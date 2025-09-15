<?php

namespace Modules\Partner\Http\Controllers;

use App\Business;
use App\Utils\TransactionUtil;
use App\Utils\BusinessUtil;
use Modules\Partner\Utils\PartnerTransactionUtil;
use Modules\Partner\Entities\PartnerTransaction;
use Modules\Partner\Entities\Service;
use Modules\Partner\Entities\Partner;
use Modules\Partner\Entities\PartnerTransactionPayment;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use \biller\bcu\Cotizaciones;
use Pdf;

class PartnerPaymentController extends Controller
{
    protected $ptUtil;
    protected $transactionUtil;
    protected $businessUtil;

    public function __construct(PartnerTransactionUtil $ptUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil) {
        $this->ptUtil = $ptUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
    }

    public function index() {
        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $query = $this->ptUtil->getListPartnerPayments($business_id);

            if (! empty(request()->start_date) && ! empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $query->whereDate('pt.transaction_date', '>=', $start)
                            ->whereDate('pt.transaction_date', '<=', $end);
            }

            $query = $query->with(["currency",'creator'])->orderBy('transaction_date', 'desc')->get();
            $output = Datatables::of($query)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                            data-toggle="dropdown" aria-expanded="false">'.
                            __('messages.actions').
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                    $html .= '<li><a href="#" data-href="'.action([\Modules\Partner\Http\Controllers\PartnerPaymentController::class, 'show'], [$row->id]).'" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>'.__('messages.view').'</a></li>';
                    $html .= '<li><a href="'.action([\Modules\Partner\Http\Controllers\PartnerPaymentController::class, 'edit'], [$row->id]).'"><i class="fas fa-edit" aria-hidden="true"></i>'.__('messages.edit').'</a></li>';
                    $html .= '<li><a class="delete-partner-payment" href="'
                        . action([\Modules\Partner\Http\Controllers\PartnerPaymentController::class, 'destroy'], [$row->id])
                        . '"><i class="fas fa-trash" style="margin-right: 5px !important"></i>'
                        . __('messages.delete').'</a></li>';
                    $html .= '<li><a target="__blank" href="'.action([\Modules\Partner\Http\Controllers\PartnerPaymentController::class, 'printReceipt'], [$row->id]).'"><i class="fa fa-print" aria-hidden="true"></i>'.__("messages.print").'</a></li>';
                    return $html;
                })
                ->removeColumn('id')
                ->addColumn('partner_name', function($row) {
                    return $row->surname . ' ' . $row->name;
                })
                ->addColumn('pay_months', function($row) {
                    return $row->pay_months;
                })
                ->rawColumns(['action', 'partner_name'])
                ->make(true);

            return $output;
        }

        return view("partner::partner_payment.index");
    }

    public function create() {
        $business_id = request()->session()->get('user.business_id');
        $business = Business::find($business_id);

        $currencies = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $services = Service::allServicesDropdown($business_id);

        // partner for payment
        $partner_id = request()->get('partner_id');
        if(!empty($partner_id)) {
            $partner = Partner::find($partner_id);
        }

        return view("partner::partner_payment.edit", compact('business', 'currencies', 'services', 'partner'));
    }

    public function edit($id) {
        try {
            $business_id = request()->session()->get('user.business_id');
            $business = Business::find($business_id);
    
            $currencies = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            $services = Service::allServicesDropdown($business_id);
    
            $transaction = PartnerTransaction::with('payment_lines')->findOrFail($id);
            $partner = $transaction->partner;

            return view("partner::partner_payment/edit", compact('business', 'currencies', 'transaction', 'services', 'partner'));
        } catch(\Exception $e) {
            abort(500, "Something went wrong.");
        }
    }

    public function store() {
        try {
            $business_id = request()->session()->get('user.business_id');
            $data = request()->except(['_token']); 

            $tr_currency = \App\Currency::findOrFail($data['currency_id']);

            $data['type'] = 'payment';
            $data['created_by'] = auth()->user()->id;
            $data['business_id'] = $business_id;
            $data['transaction_date'] = $this->ptUtil->uf_date($data['transaction_date'], true);
            $data['final_total'] = $this->ptUtil->num_uf($data['final_total'], $tr_currency);

            DB::beginTransaction();

            // ref_no
            if(empty($data['ref_no'])) {
                $ref_count = $this->ptUtil->setAndGetReferenceCount('partner_payment', $business_id);
                $data['ref_no'] = $this->ptUtil->generateReferenceNumber('partner_payment', $ref_count, $business_id, 'PAP');
            }

            // To insert a transaction
            $transaction = PartnerTransaction::create($data);

            // To add payment lines
            $this->ptUtil->createOrUpdatePaymentLines($transaction, $data['payments']);

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => 'Payment was made successfully'
            ];
        } catch(\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect()->action([\Modules\Partner\Http\Controllers\PartnerPaymentController::class, 'index'])->with('status', $output);
    }

    public function update($id) {
        try {
            $business_id = request()->session()->get('user.business_id');
            $data = request()->except(['_token']); 

            $data['type'] = 'payment';
            $data['created_by'] = auth()->user()->id;
            $data['business_id'] = $business_id;
            $data['transaction_date'] = $this->ptUtil->uf_date($data['transaction_date'], true);

            DB::beginTransaction();

            // ref_no
            if(empty($data['ref_no'])) {
                $ref_count = $this->ptUtil->setAndGetReferenceCount('partner_payment', $business_id);
                $data['ref_no'] = $this->ptUtil->generateReferenceNumber('partner_payment', $ref_count, $business_id, 'PAP');
            }

            // To insert a transaction
            $transaction = PartnerTransaction::findOrFail($id);
            $transaction->update($data);

            // To add payment lines
            $this->ptUtil->createOrUpdatePaymentLines($transaction, $data['payments']);

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => 'Payment was updated successfully'
            ];
        } catch(\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect()->action([\Modules\Partner\Http\Controllers\PartnerPaymentController::class, 'index'])->with('status', $output);
    }

    public function destroy($id) {
        try {
            $transaction = PartnerTransaction::findOrFail($id);
            $transaction->payment_lines()->delete();
            $transaction->delete();
            $output = [
                'success' => 1,
                'msg' => 'Payment was deleted succssfully'
            ];
        } catch(\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return response()->json($output);
    }

    public function new_payment_row() {
        $row_number = (string)request()->input('row_number');
        $service_id = (string)request()->input('service_id');
        $service = Service::findOrFail($service_id);

        return view("partner::partner_payment.partials.payment_row", compact('row_number', 'service'));
    }

    public function printReceipt($transaction_id) {
        try {
            $business_id = request()->session()->get('user.business_id');
            $business_details = $this->businessUtil->getDetails($business_id);
            $user = auth()->user();
            
            $transaction = PartnerTransaction::with(['currency'])->findOrFail($transaction_id);
            if(empty($transaction)) {
                abort(404, 'The transaction does not exist');
            }
            $partner = Partner::findOrFail($transaction->partner_id);

            // return view('partner::partner_payment.partials.ticket_pdf', compact('business_details', 'transaction', 'user', 'partner'));

            $pdf = PDF::loadView('partner::partner_payment.partials.ticket_pdf', compact('business_details', 'transaction', 'user', 'partner'));
            $pdf->setPaper([0, 0, 265, 1100]);
            $pdfContent = $pdf->output();
            
            // Send as a downloadable file response
            return response($pdfContent, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="receipt_' . $transaction_id . '_' . $transaction->ref_no . '.pdf"');
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            abort(500, __('messages.something_went_wrong'));
        }
    }
}