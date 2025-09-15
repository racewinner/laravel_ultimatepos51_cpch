<?php

namespace Modules\Partner\Http\Controllers;

use App\Business;

use Modules\Partner\Entities\Partner;
use Modules\Partner\Entities\MaritalStatus;
use Modules\Partner\Utils\PartnerUtil;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MaritalStatusController extends Controller
{
    protected $partnerUtil;

    public function __construct(PartnerUtil $partnerUtil) {
        $this->partnerUtil = $partnerUtil;
    }

    public function index() {
        if (! auth()->user()->can('marital_status.permission')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $maritalStatuses = MaritalStatus::where('business_id', $business_id);
            $output = Datatables::of($maritalStatuses)
                ->addColumn('action', function($row) {
                    $html = '<div class="d-flex align-items-center">';
                    $html = '<a href="#" class="btn btn-primary btn-modal px-4 py-1" style="color:white !important;"'
                        . 'data-href="' . action([\Modules\Partner\Http\Controllers\MaritalStatusController::class, 'edit'], [$row->id]) . '" '
                        . 'data-container=".marital_status_modal">'
                        . '<i class="fas fa-edit" style="margin-right: 5px !important;" aria-hidden="true"></i>'
                        . __('messages.edit')
                        .'</a>';

                    $html .= '<a class="btn btn-danger px-4 py-1 ms-4 delete-marital-status" style="color:white !important;"'
                        . 'href="' . action([\Modules\Partner\Http\Controllers\MaritalStatusController::class, 'destroy'], [$row->id]) . '">'
                        . '<i class="fas fa-trash" style="margin-right: 5px !important"></i>'
                        . __('messages.delete')
                        .'</a>';
                    $html .= "</div>";
                    return $html;
                })
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);

            return $output;
        }

        return view("partner::marital_status.index");
    }

    public function create() {
        if (! auth()->user()->can('marital_status.permission')) {
            abort(403, 'Unauthorized action.');
        }

        $marital_status = null;
        return view("partner::marital_status.edit", compact('marital_status'));
    }

    public function edit($id) {
        if (! auth()->user()->can('marital_status.permission')) {
            abort(403, 'Unauthorized action.');
        }

        $marital_status = MaritalStatus::findOrFail($id);
        return view("partner::marital_status.edit", compact('marital_status'));
    }

    public function store(Request $request) {
        if (! auth()->user()->can('marital_status.permission')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        try {
            $data = $request->only(['status']);
            $data['business_id'] = $business_id;

            // To check whether the same marital_status exists.
            $exist = MaritalStatus::where('status', $data['status'])->where('business_id', $business_id)->first();
            if(!empty($exist)) {
                return response()->json([
                    'success' => 0,
                    'msg' => __('messages.same_exist')
                ]);
            }

            DB::beginTransaction();

            $newOne = MaritalStatus::create($data);

            $output = [
                'success' => 1,
                'msg' => __('messages.add_success'),
            ];

            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return response()->json($output);
    }

    public function update($id) {
        if (! auth()->user()->can('marital_status.permission')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        try {
            $marital_status = MaritalStatus::findOrFail($id);
            $data = request()->only(['status']);
            $data['business_id'] = $business_id;

            $exist = MaritalStatus::where('business_id', $business_id)
                ->where('id', '!=', $id)
                ->where('status', $data['status'])
                ->first();
            if(!empty($exist)) {
                return response()->json([
                    'success' => 0,
                    'msg' => __('messages.same_exist')
                ]);
            }

            DB::beginTransaction();

            $marital_status->update($data);

            $output = [
                'success' => 1,
                'msg' => __('messagesupdate_success'),
            ];

            DB::commit();

        }catch(\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return response()->json($output);
    }

    public function destroy($id) {
        if (! auth()->user()->can('marital_status.permission')) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            // To check whether it is in use.
            $count = Partner::where('marital_status_id', $id)->count();
            if($count > 0) {
                return response()->json([
                    'success' => 0,
                    'msg' => 'This marital status is in use now'
                ]);
            }
                        
            MaritalStatus::find($id)->delete();

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