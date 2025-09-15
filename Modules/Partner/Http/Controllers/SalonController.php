<?php

namespace Modules\Partner\Http\Controllers;

use App\Business;

use Modules\Partner\Entities\Salon;
use Modules\Partner\Utils\PartnerUtil;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SalonController extends Controller
{
    protected $partnerUtil;

    public function __construct(PartnerUtil $partnerUtil) {
        $this->partnerUtil = $partnerUtil;
    }

    public function index() {
        // if (! auth()->user()->can('salon.permission')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $rows = Salon::where('business_id', $business_id);
            $output = Datatables::of($rows)
                ->addColumn('action', function($row) {
                    $html = "<div class='d-flex align-items-center'>";
                    $html .= '<li class="btn btn-primary px-4 py-1">'
                            . '<a href="#" style="color:white !important;" data-href="'
                            . action([\Modules\Partner\Http\Controllers\SalonController::class, 'edit'], [$row->id])
                            . '" class="btn-modal" data-container=".salon-modal">'
                            . '<i class="fas fa-edit" style="margin-right: 5px !important;" aria-hidden="true"></i>'
                            . __('messages.edit').'</a></li>';
                    
                    $html .= '<li class="btn btn-danger px-4 py-1 ms-4">'
                        . '<a class="delete-salon" style="color:white !important;" href="'
                        . action([\Modules\Partner\Http\Controllers\SalonController::class, 'destroy'], [$row->id])
                        . '"><i class="fas fa-trash" style="margin-right: 5px !important"></i>'
                        . __('messages.delete').'</a></li>';
                    
                    $html .= "</div>";
                    return $html;
                })
                ->removeColumn('id')
                ->editColumn('open', function($row) {
                    return $row->open ? __("messages.yes") : __("messages.no");
                })
                ->addColumn('daytime', function($row) {
                    return $row->daytime_from . " ~ " . $row->daytime_to;
                })
                ->addColumn('nighttime', function($row) {
                    return $row->nighttime_from . " ~ " . $row->nighttime_to;
                })
                ->rawColumns(['action', 'open', 'daytime', 'nighttime'])
                ->make(true);
            return $output;
        }

        return view("partner::salon.index");
    }

    public function create() {
        // if (! auth()->user()->can('salon.permission')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $salon = null;
        return view("partner::salon.edit", compact('salon'));
    }

    public function edit($id) {
        // if (! auth()->user()->can('salon.permission')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $salon = Salon::findOrFail($id);
        return view("partner::salon.edit", compact('salon'));
    }

    public function store(Request $request) {
        // if (! auth()->user()->can('salon.permission')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $business_id = request()->session()->get('user.business_id');

        try {
            $data = $request->except(['_token']);
            $data['business_id'] = $business_id;

            // To check whether the same salon exists.
            $exist = Salon::where('name', $data['name'])->where('business_id', $business_id)->first();
            if(!empty($exist)) {
                return response()->json([
                    'success' => 0,
                    'msg' => __('messages.same_exist')
                ]);
            }

            DB::beginTransaction();

            $salon = Salon::create($data);

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
        // if (! auth()->user()->can('salon.permission')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $business_id = request()->session()->get('user.business_id');

        try {
            $salon = Salon::findOrFail($id);
            $data = request()->except(['_token']);
            $data['business_id'] = $business_id;

            // To check if the same salon already exists.
            $exist = Salon::where('business_id', $business_id)
                ->where('id', '!=', $id)
                ->where('name', $data['name'])
                ->first();
            if(!empty($exist)) {
                return response()->json([
                    'success' => 0,
                    'msg'=>'The same salon already exists'
                ]);
            }

            DB::beginTransaction();

            $salon->update($data);

            $output = ['success' => 1,
                'msg' => __('messages.update_success'),
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
        // if (! auth()->user()->can('salon.permission')) {
        //     abort(403, 'Unauthorized action.');
        // }
        
        try {
            Salon::find($id)->delete();

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

    public function show($id) {
        try {
            $salon = Salon::find($id);
            $output = [
                'success' => 1,
                'salon' => $salon,
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