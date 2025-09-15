<?php

namespace Modules\Partner\Http\Controllers;

use App\Business;

use Modules\Partner\Entities\Partner;
use Modules\Partner\Entities\Service;
use Modules\Partner\Entities\PartnerTransactionPayment;
use Modules\Partner\Utils\PartnerUtil;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ServiceController extends Controller
{
    protected $partnerUtil;

    public function __construct(PartnerUtil $partnerUtil) {
        $this->partnerUtil = $partnerUtil;
    }

    public function index() {
        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $services = Service::where('business_id', $business_id);
            $output = Datatables::of($services)
                ->addColumn('action', function($row) {
                    $html = "<div class='d-flex align-items-center'>";
                    $html .= '<li class="btn btn-primary px-4 py-1">'
                            . '<a href="#" style="color:white !important;" data-href="'
                            . action([\Modules\Partner\Http\Controllers\ServiceController::class, 'edit'], [$row->id])
                            . '" class="btn-modal" data-container=".service_modal">'
                            . '<i class="fas fa-edit" style="margin-right: 5px !important;" aria-hidden="true"></i>'
                            . __('messages.edit').'</a>
                    </li>';
                    
                    $html .= '<li class="btn btn-danger px-4 py-1 ms-4">'
                        . '<a class="delete-service" style="color:white !important;" href="'
                        . action([\Modules\Partner\Http\Controllers\ServiceController::class, 'destroy'], [$row->id])
                        . '"><i class="fas fa-trash" style="margin-right: 5px !important"></i>'
                        . __('messages.delete').'</a></li>';
                    
                    $html .= "</div>";
                    return $html;
                })
                ->removeColumn('id')
                ->addColumn('currency', function($row) {
                    return $row->currency->symbol;
                })
                ->rawColumns(['action'])
                ->make(true);
            return $output;
        }

        return view("partner::service.index");
    }

    public function getServices() {
        if (request()->ajax()) {
            $term = request()->q;
            $partner_id = request()->partner_id;
            if (empty($partner_id)) {
                return json_encode([]);
            }

            $business_id = request()->session()->get('user.business_id');
            $partner = Partner::findOrFail($partner_id);

            $query = Service::where('business_id', $business_id)->whereIn('id', explode(',', $partner->category->service_ids));
            if(!empty($term)) {
                $query->where('name', 'like', '%'.$term.'%');
            }
            $services = $query->get();

            return json_encode($services);
        }
    }

    public function create() {
        $business_id = request()->session()->get('user.business_id');
        $business = Business::findOrFail($business_id);
        $currencies = $business->currenciesDropdown();

        $service = null;
        return view("partner::service.edit", compact('service', 'currencies'));
    }

    public function edit($id) {
        $business_id = request()->session()->get('user.business_id');
        $business = Business::findOrFail($business_id);
        $currencies = $business->currenciesDropdown();

        $service = Service::findOrFail($id);

        return view("partner::service.edit", compact('service', 'currencies'));
    }

    public function store(Request $request) {
        $business_id = request()->session()->get('user.business_id');

        try {
            $data = $request->except('_token');
            $data['business_id'] = $business_id;

            // To check whether the same service exists.
            $exist = Service::where('name', $data['name'])->where('business_id', $business_id)->first();
            if(!empty($exist)) {
                return response()->json([
                    'success' => 0,
                    'msg' => __('messages.same_exist')
                ]);
            }

            DB::beginTransaction();

            $service = Service::create($data);

            $output = [
                'success' => 1,
                'msg' => __('messages.add_success'),
            ];

            DB::commit();
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

    public function update($id) {
        $business_id = request()->session()->get('user.business_id');

        try {
            $service = Service::findOrFail($id);
            $data = request()->except('_token');

            // To check if the same service already exists.
            $exist = Service::where('business_id', $business_id)
                ->where('id', '!=', $id)
                ->where('name', $data['name'])
                ->first();
            if(!empty($exist)) {
                return response()->json([
                    'success' => 0,
                    'msg'=>__('messages.same_exist')
                ]);
            }

            DB::beginTransaction();

            $service->update($data);

            $output = [
                'success' => 1,
                'msg' => __('messages.update_success'),
            ];


            DB::commit();

        }catch(\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return response()->json($output);
    }

    public function destroy($id) {
        try {
            // To check whether it is in use now.
            $count = PartnerTransactionPayment::where('service_id', $id)->count();
            if($count > 0) {
                return response()->json([
                    'success' => 0,
                    'msg' => __("messages.now_in_use")
                ]);
            }

            Service::find($id)->delete();

            $output = [
                'success' => 1,
                'msg' => __('messages.delete_success'),
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
}