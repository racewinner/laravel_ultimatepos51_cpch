<?php

namespace Modules\Partner\Http\Controllers;

use App\Business;

use Modules\Partner\Entities\Partner;
use Modules\Partner\Entities\PartnerCategory;
use Modules\Partner\Entities\PartnerReceipt;
use Modules\Partner\Entities\Service;
use Modules\Partner\Entities\Radio;
use Modules\Partner\Entities\Zone;
use Modules\Partner\Entities\Locality;
use Modules\Partner\Utils\PartnerUtil;
use Modules\Partner\Utils\PartnerTransactionUtil;

use App\Utils\TransactionUtil;
use App\Utils\BusinessUtil;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use \Carbon\Carbon;
use Pdf;

class PartnerReceiptController extends Controller
{
    protected $transactionUtil;
    protected $ptUtil;
    protected $businessUtil;

    public function __construct(TransactionUtil $transactionUtil, BusinessUtil $businessUtil, PartnerTransactionUtil $ptUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
        $this->ptUtil = $ptUtil;
    }

    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {

            $query = PartnerReceipt::leftjoin('partners', 'partners.id', '=', 'partner_receipts.partner_id')
                ->where('business_id', $business_id)
                ->whereDoesntHave('partner.leave')
                ->select('partner_receipts.*', 'surname', 'name', 'partners.id_card_number');

            if (!empty(request()->start_month) && !empty(request()->end_month)) {
                $start = request()->start_month;
                $end = request()->end_month;
                $query->whereDate('partner_receipts.from_month', '>=', $start)
                    ->whereDate('partner_receipts.to_month', '<=', $end);
            }

            if(!empty(request()->from_partner_category)) {
                $query->where('partners.partner_category_id', '>=', request()->from_partner_category);
            }
            if(!empty(request()->to_partner_category)) {
                $query->where('partners.partner_category_id', '<=', request()->to_partner_category);
            }

            if (!empty(request()->from_zone_id)) {
                $query->where('partners.zone_id', '>=', request()->from_zone_id);
            }
            if (!empty(request()->to_zone_id)) {
                $query->where('partners.zone_id', '<=', request()->to_zone_id);
            }

            if (!empty(request()->from_radio_id)) {
                $query->where('partners.radio_id', '>=', request()->from_radio_id);
            }
            if (!empty(request()->to_radio_id)) {
                $query->where('partners.radio_id', '<=', request()->to_radio_id);
            }

            if (!empty(request()->from_route_id)) {
                $query->where('partners.route_id', '>=', request()->from_route_id);
            }
            if (!empty(request()->to_route_id)) {
                $query->where('partners.route_id', '<=', request()->to_route_id);
            }

            if (!empty(request()->partner_name)) {
                $partner_name = request()->partner_name;
                $query->where(function ($query) use ($partner_name) {
                    $query->where('surname', 'like', '%' . $partner_name . '%')
                        ->orWhere('name', 'like', '%' . $partner_name . '%');
                });
            }

            if (!empty(request()->partner_idcard)) {
                $partner_idcard = request()->partner_idcard;
                $query->where(function ($query) use ($partner_idcard) {
                    $query->where('id_card_number', 'like', '%' . $partner_idcard . '%');
                });
            }

            if(!empty(request()->receipt_status)) {
                if(request()->receipt_status == 'deleted') {
                    $query->where('deleted', 1);
                } else if(request()->receipt_status == 'undeleted') {
                    $query->where(function ($query) {
                        $query->whereNull('deleted')->orWhere('deleted', '<>', 1);
                    });
                }
            }

            $query = $query->with(['partner', 'currency', 'editor'])->orderBy('created_at', 'desc')->orderBy('from_month', 'asc')->get();

            $output = Datatables::of($query)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                            data-toggle="dropdown" aria-expanded="false">' .
                        __('messages.actions') .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                    $html .= '<li><a href="/partner/receipts/xprint/' . $row->id . '?type=receipt" target="_blank"><i class="fas fa-eye" aria-hidden="true"></i>' . __('partner::lang.view_receipt') . '</a></li>';
                    
                    if(empty($row->partner->leave)) {
                        $html .= '<li><a target="_blank" href="/partner/receipts/' . $row->id . '/print?type=receipt"><i class="fa fa-print" aria-hidden="true"></i>' . __("partner::lang.print_receipt") . '</a></li>';
                    }

                    if ($row->paid == 1) {
                        $html .= '<li><a href="/partner/receipts/xprint/' . $row->id . '?type=payment" target="_blank"><i class="fas fa-eye" aria-hidden="true"></i>' . __('partner::lang.view_payment') . '</a></li>';
                        if(empty($row->partner->leave)) {
                            $html .= '<li><a target="_blank" href="/partner/receipts/' . $row->id . '/print?type=payment"><i class="fa fa-print" aria-hidden="true"></i>' . __("partner::lang.print_payment") . '</a></li>';
                        }
                    } else {
                        if($row->deleted != 1) {
                            $html .= '<li><a href="#" data-id="' . $row->id . '" class="receipt-settle"><i class="fas fa-money-bill-alt" aria-hidden="true"></i>' . __('partner::lang.make_payment') . '</a></li>';
                        }
                    }

                    if($row->deleted == 1) {
                        $html .= '<li><a data-id="' . $row->id . '" class="undelete-receipt"><i class="fas fa-undo"></i>' . __('lang_v1.undelete') . '</a></li>';
                    } else {
                        $html .= '<li><a data-id="' . $row->id . '" class="delete-receipt"><i class="fas fa-trash""></i>' . __('messages.delete') . '</a></li>';
                    }

                    return $html;
                })
                ->removeColumn('id')
                ->addColumn('partner_name', function ($row) {
                    return $row->partner->display_name;
                })
                ->addColumn('type', function($row) {
                    return $row->additional_payment ? "<span class='additional-payment'>Pago adicional</span>" : '<span>Normal</span>';
                })
                ->addColumn('bulk_chkbox', function ($row) {
                    return '<input type="checkbox" class="row-select" value="' . $row->id . '">';
                })
                ->addColumn('period', function ($row) {
                    $html = '<span>'.$row->period.'</span>';
                    if($row->deleted == 1) {
                        $html .= "<span class='ms-1 deleted-receipt'>".__('lang_v1.deleted')."</span>";
                    }
                    return $html;

                })
                ->editColumn('paid', function ($row) {
                    if ($row->paid) {
                        return '<span class="label bg-light-green" _msttexthash="42653" _msthash="385">' . $row->paid_on . '</span>';
                    } else {
                        return '<span class="label bg-danger" _msttexthash="42653" _msthash="385">' . __('lang_v1.unpaid') . '</span>';
                    }
                })
                ->editColumn('from_month', function ($row) {
                    return Carbon::parse($this->from_month)->format('m/Y');
                })
                ->editColumn('to_month', function ($row) {
                    return Carbon::parse($this->to_month)->format('m/Y');
                })
                ->editColumn('amount', function ($row) {
                  $service_rows = Service::all()->toArray();
                  $service_amounts = array_map(function($item) {
                    return [
                        $item['id'] => $item['unit_cost']
                      ];
                  }, $service_rows);

                  $itm_service_ids = $row->service_ids;
                  $my_service_array = explode(',', $itm_service_ids);
                  $real_amount = 0;

                  foreach ($my_service_array as $itmdt) {
                    foreach ($service_amounts as $item) {

                      $keys = array_keys($item);
                      $values = array_values($item);
                      $key = $keys[0];
                      $value = $values[0];

                      if(intval($itmdt) == $key) {
                        $real_amount += $value;
                      }
                    }
                  }
                  return $real_amount;
                })
                ->rawColumns(['action', 'partner_name', 'period', 'paid', 'bulk_chkbox', 'type', 'from_month', 'to_month'])
                ->make(true);
                
            return $output;

        }

        $zones = Zone::allZones($business_id);
        $radios = Radio::allRadios($business_id);
        $issue_receipt = request()->get('issue_receipt');
        $receipt_show_permission = auth()->user()->can('partner_receipt.show');
        $partner_categories = PartnerCategory::allPartnerCategoriesDropdown($business_id);
        $receipt_status = [
            'deleted' => __('partner::lang.only_deleted'),
            'undeleted' => __('partner::lang.only_undeleted'),
        ];

        return view('partner::partner_receipt.index', compact('zones', 'radios', 'issue_receipt', 
            'receipt_show_permission', 'partner_categories', 'receipt_status'));
    }

    public function getLedger($partner_id)
    {
        if (request()->ajax()) {
            $partner = Partner::findOrFail($partner_id);

            $overdue_debit = 0;
            $overdue_credit = 0;
            $overdue_balance = 0;
            $start = null;
            $end = null;

            if (!empty(request()->start_month) && !empty(request()->end_month)) {
                $start = request()->start_month;
                $end = request()->end_month;
                
                $query = PartnerReceipt::where('partner_id', $partner_id)
                    ->whereDate('from_month', '<', $start)
                    ->where(function ($query) {
                        $query->whereNull('deleted')->orWhere('deleted', '<>', 1);
                    });

                $q1 = $query;
                $overdue_debit = $q1->sum('amount');

                $q2 = $query;
                $overdue_credit = $q2->where('paid', 1)->sum('amount');
                $overdue_balance = $overdue_debit - $overdue_credit;
            }

            // To build query
            $r_query = PartnerReceipt::where('partner_id', $partner_id)
                ->where(function ($query) {
                    $query->whereNull('deleted')->orWhere('deleted', '<>', 1);
                });
            if (!empty($start) && !empty($end)) {
                $r_query->whereDate('from_month', '>=', $start)->whereDate('to_month', '<=', $end);
            }
            $r_query->orderBy('created_at', 'desc');

            $p_query = $r_query;

            // To get receipts
            $r_query->select('*', 'created_at as transaction_date');
            $receipts = $r_query->get();

            // To get payments
            $p_query->where('paid', 1)->select('*', 'paid_on as transaction_date');
            $payments = $p_query->get();

            // To get service informations
            $service_rows = Service::all()->toArray();
            $service_amounts = array_map(function($item) {
              return [
                  $item['id'] => $item['unit_cost']
                ];
            }, $service_rows);

            $rows = [];
            foreach ($receipts as $r) {
                $itm_service_ids = $r->service_ids;
                $my_service_array = explode(',', $itm_service_ids);
                $real_amount = 0;

                foreach ($my_service_array as $itmdt) {
                  foreach ($service_amounts as $item) {

                    $keys = array_keys($item);
                    $values = array_values($item);
                    $key = $keys[0];
                    $value = $values[0];

                    if(intval($itmdt) == $key) {
                      $real_amount += $value;
                    }
                  }
                }

                $rows[] = [
                    'id' => $r->id,
                    'transaction_date' => $r->transaction_date,
                    'services' => $r->services,
                    'document' => __('invoice.receipt'),
                    'credit' => 0,
                    'debit'  => $real_amount,//$r->amount,
                    'ref_no' => $r->ref_no,
                    'months' => $r->months,
                    'period' => $r->period,
                    'currency' => $partner->currency ?? session("currency"),
                ];
            }
            foreach ($payments as $p) {
                $itm_service_ids = $p->service_ids;
                $my_service_array = explode(',', $itm_service_ids);
                $real_amount = 0;

                foreach ($my_service_array as $itmdt) {
                  foreach ($service_amounts as $item) {

                    $keys = array_keys($item);
                    $values = array_values($item);
                    $key = $keys[0];
                    $value = $values[0];

                    if(intval($itmdt) == $key) {
                      $real_amount += $value;
                    }
                  }
                }

                $rows[] = [
                    'id' => $p->id,
                    'transaction_date' => $p->transaction_date,
                    'services' => $p->services,
                    'document' => __('lang_v1.payment'),
                    'credit' => $real_amount,//$r->amount,
                    'debit' => 0,
                    'ref_no' => $r->payment_ref_no,
                    'months' => $r->months,
                    'period' => $r->period,
                    'currency' => $partner->currency ?? session("currency"),
                ];
            }
            usort($rows, function ($a, $b) {
                return strtotime($b->transaction_date) - strtotime($a->transaction_date);
            });

            $ledgers = [];
            $ledgers[] = [
                'id' => '',
                'transaction_date' => '',
                'services' => [],
                'document' => __('lang_v1.overdue'),
                'debit' => $overdue_debit,
                'credit' => $overdue_credit,
                'balance' => $overdue_balance,
                'period' => '',
                'months' => '',
                'ref_no' => '',
                'currency' => $partner->currency ?? session("currency"),
            ];
            $balance = $overdue_balance;
            foreach ($rows as $r) {
                $balance += $r['debit'] - $r['credit'];
                $ledgers[] = [
                    ...$r,
                    'balance' => \App\Utils\Util::format_currency($balance, $partner->currency, false)
                ];
            }

            return DataTables::of($ledgers)
                ->editColumn('ref_no', function($row) {
                    if(empty($row['id'])) {     // if total record?
                        return $row['ref_no'];
                    } else {
                        $html = "<a href='/partner/receipts/xprint/" . $row['id'] . "?type=" . ($row['debit'] > 0 ? 'receipt' : 'payment') . "' target='_blank'>";
                        $html .= $row['ref_no'];
                        $html .= "</a>";
                        return $html;
                    }
                })
                ->editColumn("services", function($row) {
                    $html = '';
                    if(!empty($row['services'])) {
                        foreach($row['services'] as $service) {
                            if(!empty($html)) $html .= ",";
                            $html .= $service->name;
                        }
                    }
                    return $html;
                })
                ->rawColumns(['ref_no', 'services'])
                ->make(true);
        }
    }

    public function destroy($id)
    {
        try {
            $receipt = PartnerReceipt::findOrFail($id);
            $receipt->deleted = 1;
            $receipt->save();
            
            $output = [
                'success' => 1,
                'msg' => __('messages.delete_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return response()->json($output);
    }

    public function unDelete($id)
    {
        try {
            $receipt = PartnerReceipt::findOrFail($id);
            $receipt->deleted = 0;
            $receipt->save();
            
            $output = [
                'success' => 1,
                'msg' => __('messages.restored_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return response()->json($output);
    }

    public function getIssueReceipt()
    {
        $business_id = request()->session()->get('user.business_id');

        $partner_categories = PartnerCategory::allPartnerCategoriesDropdown($business_id);

        $localities = Locality::allLocalitiesDropdown($business_id);

        $zones = Zone::allZones($business_id);

        $radios = Radio::allRadios($business_id);

        return view("partner::partner_receipt.partials.issue_receipt_modal", compact('partner_categories', 'localities', 'zones', 'radios', 'routes'));
    }

    public function postIssueReceipt()
    {
        $business_id = request()->session()->get('user.business_id');

        $partner_id = request()->partner_id;
        $partner_category_from = request()->partner_category_from;
        $partner_category_to = request()->partner_category_to;
        $locality_id = request()->locality_id;
        $from_zone_id = request()->from_zone_id;
        $to_zone_id = request()->to_zone_id;
        $from_radio_id = request()->from_radio_id;
        $to_radio_id = request()->to_radio_id;
        $from_route_id = request()->from_route_id;
        $to_route_id = request()->to_route_id;
        $issue_months = request()->issue_months;
        $paid = request()->paid ?? 0;
        $ignore_leave = request()->ignore_leave ?? 0;
        $additional_payment = request()->additional_payment ?? 0;

        $query = Partner::where('business_id', $business_id);

        if($ignore_leave != 1) {
            $query->whereDoesntHave('leave');
        }

        if (!empty($partner_id)) {
            $query->where('id', $partner_id);
        }

        if (!empty($partner_category_from)) {
            $query->where('partner_category_id', '>=', $partner_category_from);
        }
        if (!empty($partner_category_to)) {
            $query->where('partner_category_id', '<=', $partner_category_to);
        }

        if (!empty($locality_id)) {
            $query->where('locality_id', $locality_id);
        }

        if (!empty($from_zone_id)) {
            $query->where('zone_id', '>=', $from_zone_id);
        }
        if (!empty($to_zone_id)) {
            $query->where('zone_id', '<=', $to_zone_id);
        }

        if (!empty($from_radio_id)) {
            $query->where('radio_id', '>=', $from_radio_id);
        }
        if (!empty($to_radio_id)) {
            $query->where('radio_id', '<=', $to_radio_id);
        }

        if (!empty($from_route_id)) {
            $query->where('route_id', '>=', $from_route_id);
        }
        if (!empty($to_route_id)) {
            $query->where('route_id', '<=', $to_route_id);
        }

        try {
            $newly_issued_count = 0;
            $already_issued_count = 0;
            $no_fee_service_partners = 0;
            $new_receipt_ref_nos = [];

            list($from_month, $to_month) = explode('-', $issue_months);
            
            $from_month = \DateTime::createFromFormat('m/Y', trim($from_month));
            if($from_month != false) $from_month = new \DateTime($from_month->format('Y-m-01'));

            $to_month = \DateTime::createFromFormat('m/Y', trim($to_month));
            if($to_month != false) $to_month = new \DateTime($to_month->format('Y-m-01'));

            $partners = $query->get();

            if (!empty($partners)) {
                DB::beginTransaction();

                foreach ($partners as $partner) {
                    // To check whether the partner has fee_service
                    if ($partner->fee_services->count() == 0) {
                        $no_fee_service_partners++;
                        continue;
                    }

                    $date_admission = Carbon::parse($partner->date_admission);

                    if(empty($additional_payment)) {
                        $s_ids = [];
                        foreach ($partner->fee_services as $service) {
                            $s_ids[] = $service->id;
                        }
                        $service_ids = implode(',', $s_ids);
                    } else {
                        $service_ids = $partner->additional_fee_service_ids;
                    }
                    
                    $m = $from_month;
                    $new_payment_ref_no = '';
                    while ($m <= $to_month) {
                        $m1 = Carbon::parse($m)->addMonth(); 
                        
                        if($m1 > $date_admission) {
                            if ($this->ptUtil->hasReceiptIssued($partner->id, $m, $additional_payment)) {
                                $already_issued_count++;
                            } else {
                                $ref_count = $this->ptUtil->setAndGetReferenceCount('partner_receipt', $business_id);

                                $data = [
                                    'partner_id' => $partner->id,
                                    'from_month' => $m->format('Y-m-01'),
                                    'to_month' => $m->format('Y-m-01'),
                                    'months' => 1,
                                    'additional_payment' => $additional_payment,
                                    'service_ids' => $service_ids,
                                    'amount' => $partner->monthly_fee * 1,
                                    'currency_id' => $partner->currency->id,
                                    'ref_no' => $this->ptUtil->generateReferenceNumber('partner_receipt', $ref_count, $business_id, 'PRR'),
                                    'paid' => $paid,
                                    'editor_id' => auth()->user()->id,
                                ];
                                if ($paid == "1") {
                                    if(empty($new_payment_ref_no)) {
                                        $new_payment_ref_no = 'PAY-' . $data['ref_no'];
                                        $new_receipt_ref_nos[] = $new_payment_ref_no;
                                    }
                                    $data['paid_on'] = date('Y-m-d H:i');
                                    $data['payment_ref_no'] = $new_payment_ref_no;
                                } else {
                                    $new_receipt_ref_nos[] = $data['ref_no'];
                                }
            
                                PartnerReceipt::create($data);
            
                                $newly_issued_count++;
                            }
                        }

                        $m = $m1;
                    }
                }

                DB::commit();
            }

            $msg = [];

            if (empty($partners)) {
                $msg[] = [
                    'type' => 'warning',
                    'message' => __('partner::messages.no_found_matching_partner')
                ];
            } else {
                if($newly_issued_count > 0) {
                    $msg[] = [
                        'type' => 'success',
                        'message' => sprintf(__('partner::messages.n_receipts_issued'), $newly_issued_count),
                    ];
                }

                if($already_issued_count > 0) {
                    $msg[] = [
                        'type' => 'warning',
                        'message' => sprintf(__('partner::messages.n_receipts_already_issued_before'), $already_issued_count)
                    ];
                }
    
                if ($no_fee_service_partners) {
                    $msg[] = [
                        'type' => 'error',
                        'message' => sprintf(__('partner::messages.n_partners_has_no_fee_service'), $no_fee_service_partners),
                    ];
                }
            }

            $output = [
                'success' => 1,
                'newly_issued_count' => $newly_issued_count,
                'already_issued_count' => $already_issued_count,
                'no_fee_service_partners' => $no_fee_service_partners,
                'new_receipt_ref_nos' => $new_receipt_ref_nos,
                'msg' => $msg,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return response()->json($output);
    }

    public function print($id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $business_details = $this->businessUtil->getDetails($business_id);
            $user = auth()->user();
            $type = request()->get('type');

            $receipt = PartnerReceipt::with(['currency', 'partner'])->findOrFail($id);
            $receipt_groups = [
                $type == 'receipt' ? $receipt->ref_no : $receipt->payment_ref_no => [$receipt]
            ];

            // logo image
            $imageUrl = public_path('/images/partner/mark5.png');
            $imageData = file_get_contents($imageUrl);
            $base64 = base64_encode($imageData);
            $base64Logo = 'data:image/jpeg;base64,' . $base64;

            $pdf = PDF::loadView(
                'partner::partner_receipt.partials.ticket_pdf',
                compact('business_details', 'receipt_groups', 'user', 'partner', 'base64Logo', 'type', 'receipt')
            );

            $pdf->setPaper([0, 0, 265, 1100]);
            $pdfContent = $pdf->output();

            // Send as a downloadable file response
            $filename = $type . '_(' . $receipt->partner->display_name . ')_(' . $receipt->period . ').pdf';
            return response($pdfContent, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            abort(500, __('messages.something_went_wrong'));
        }
    }

    public function xprint($id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $businessDetails = $this->businessUtil->getDetails($business_id);
            $user = auth()->user();
            $type = request()->get('type');

            $receipt = PartnerReceipt::with(['currency', 'partner'])->findOrFail($id);
            $receiptGroups = [
                $type == 'receipt' ? $receipt->ref_no : $receipt->payment_ref_no => [$receipt]
            ];

            // logo image
            $imageUrl = public_path('/images/partner/mark5.png');
            $imageData = file_get_contents($imageUrl);
            $base64 = base64_encode($imageData);
            $base64Logo = 'data:image/jpeg;base64,' . $base64;

            // $pdf = PDF::loadView(
            //     'partner::partner_receipt.partials.ticket_pdf',
            //     compact('business_details', 'receipt_groups', 'user', 'partner', 'base64Logo', 'type', 'receipt')
            // );

            // $pdf->setPaper([0, 0, 265, 1100]);
            // $pdfContent = $pdf->output();

            // // Send as a downloadable file response
            // $filename = $type . '_(' . $receipt->partner->display_name . ')_(' . $receipt->period . ').pdf';
            return view(
                'partner::partner_receipt.xprint',
                compact('businessDetails', 'receiptGroups', 'user', 'base64Logo', 'type', 'receipt')
          );
      } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            abort(500, __('messages.something_went_wrong'));
        }
    }

    public function bulkPrint()
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $business_details = $this->businessUtil->getDetails($business_id);
            $user = auth()->user();

            $type = request()->input('type');
            // $ref_nos = explode(',', request()->input('ref_nos'));
            $ref_nos = explode(',', request()->input('ref_nos'));

            // To get receipts
            if($type == 'receipt') {
                $query = PartnerReceipt::whereIn('ref_no', $ref_nos);
            } else {
                $query = PartnerReceipt::whereIn('payment_ref_no', $ref_nos)->where('paid', 1);
            }
            $query->where(function ($query) {
                    $query->whereNull('deleted')->orWhere('deleted', '<>', 1);
                })->with(['currency', 'partner']);
            $receipts = $query->get();

            // To group receipts by ref_no
            $receipt_groups = $receipts->groupBy($type == 'receipt' ? 'ref_no' : 'payment_ref_no');

            // logo image
            $imageUrl = public_path('/images/partner/mark5.png');
            $imageData = file_get_contents($imageUrl);
            $base64 = base64_encode($imageData);
            $base64Logo = 'data:image/jpeg;base64,' . $base64;

            $pdf = PDF::loadView(
                'partner::partner_receipt.partials.ticket_pdf',
                compact('business_details', 'receipt_groups', 'user', 'partner', 'base64Logo', 'type')
            );

            $pdf->setPaper([0, 0, 265, 600 * $receipts->count()]);
            $pdfContent = $pdf->output();

            // Send as a downloadable file response
            $filename = $type . '_(' . date('Y-m-d') . ').pdf';
            return response($pdfContent, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            abort(500, __('messages.something_went_wrong'));
        }
    }

    public function show($id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $business_details = $this->businessUtil->getDetails($business_id);
            $user = auth()->user();
            $type = request()->get('type');

            $receipt = PartnerReceipt::with(['currency', 'partner'])->findOrFail($id);
            $receipts = [$receipt];

            // logo image
            $imageUrl = public_path('/images/partner/mark5.png');
            $imageData = file_get_contents($imageUrl);
            $base64 = base64_encode($imageData);
            $base64Logo = 'data:image/jpeg;base64,' . $base64;

            return view(
                'partner::partner_receipt.partials.ticket_pdf',
                compact('business_details', 'receipts', 'user', 'base64Logo', 'type')
            );
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            abort(500, __('messages.something_went_wrong'));
        }
    }

    public function checkPrevUnpaid($id) 
    {
        try {
            $receipt = PartnerReceipt::findOrFail($id);
            $unpaid = PartnerReceipt::where('to_month', '<', $receipt->from_month)
                ->where('partner_id', $receipt->partner_id)
                ->where('paid', 0)->first();
            if(empty($unpaid)) {
                return response()->json([
                    'success' => 1
                ]);          
            } else {
                return response()->json([
                    'success' => 0,
                    'msg' => sprintf(__('partner::messages.exist_prev_unpaid'), Carbon::parse($unpaid->to_month)->format('m/Y'))
                ]);
            }
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            abort(500, __('messages.something_went_wrong'));
        }
    }

    public function settle($id)
    {
        try {
            $receipt = PartnerReceipt::findOrFail($id);
            $receipt->paid = 1;
            $receipt->paid_on = date('Y-m-d H:i');
            $receipt->payment_ref_no = 'PAY-' . $receipt->ref_no;
            $receipt->save();

            $output = [
                'success' => 1,
                'partner_id' => $receipt->partner_id,
                'new_payment_ref_nos' => [$receipt->payment_ref_no],
                'msg' => __('partner::messages.paid_success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return response()->json($output);
    }

    public function bulkSettle()
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $selected_ids = request()->selected_ids;
            $ignore_leave = request()->ignore_leave;

            // To build query
            $query = PartnerReceipt::whereIn('id', $selected_ids)
                ->where(function ($query) {
                    $query->whereNull('deleted')->orWhere('deleted', '<>', 1);
                })->where('paid', 0);
            if( !(!empty($ignore_leave) && $ignore_leave == 1) ) {
                $query->whereDoesntHave('partner.leave');
            }

            // To get receipts
            $q = $query;
            $total_receipts = $q->get();

            // To make payments
            $new_payment_ref_nos = [];
            if($total_receipts->count() > 0) {
                $receipt_groups = $total_receipts->groupBy('partner_id');

                DB::beginTransaction();

                foreach($receipt_groups as $partner_id => $receipts) {
                    $r = $receipts->first();
                    $payment_ref_no = 'PAY-' . $r->ref_no;
                    $new_payment_ref_nos[] = $payment_ref_no;

                    foreach($receipts as $r) {
                        $r->payment_ref_no = $payment_ref_no;
                        $r->paid = 1;
                        $r->paid_on = date('Y-m-d H:i');
                        $r->save();
                    }
                }

                DB::commit();
            }

            $output = [
                'success' => 1,
                'msg' => sprintf(__('partner::messages.n_payment_made'), $total_receipts->count()),
                'new_payment_ref_nos' => $new_payment_ref_nos
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return response()->json($output);
    }

    public function removeUnpaid($partner_id) {
        try {
            DB::beginTransaction();
            
            PartnerReceipt::where('partner_id', $partner_id)->where('paid', 0)->delete();

            DB::commit();

            $output = [
                'success'=> 1,
                'msg' => __('messages.delete_success')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }      

        return response()->json($output);
    }
}