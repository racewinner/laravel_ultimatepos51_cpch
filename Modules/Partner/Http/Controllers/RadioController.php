<?php

namespace Modules\Partner\Http\Controllers;

use App\Business;

use Modules\Partner\Entities\Partner;
use Modules\Partner\Entities\PartnerReturn;
use Modules\Partner\Entities\Radio;
use Modules\Partner\Utils\PartnerUtil;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RadioController extends Controller
{
    protected $partnerUtil;

    public function __construct(PartnerUtil $partnerUtil) {
        $this->partnerUtil = $partnerUtil;
    }

    public function index() {
        // if (! auth()->user()->can('radio.permission')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $rows = Radio::where('business_id', $business_id);
            $output = Datatables::of($rows)
                ->addColumn('action', function($row) {
                    $html = "<div class='d-flex align-items-center'>";
                    $html .= '<li class="btn btn-primary px-4 py-1">'
                            . '<a href="#" style="color:white !important;" data-href="'
                            . action([\Modules\Partner\Http\Controllers\RadioController::class, 'edit'], [$row->id])
                            . '" class="btn-modal" data-container=".radio-modal">'
                            . '<i class="fas fa-edit" style="margin-right: 5px !important;" aria-hidden="true"></i>'
                            . __('messages.edit').'</a>
                    </li>';
                    
                    $html .= '<li class="btn btn-danger px-4 py-1 ms-4">'
                        . '<a class="delete-radio" style="color:white !important;" href="'
                        . action([\Modules\Partner\Http\Controllers\RadioController::class, 'destroy'], [$row->id])
                        . '"><i class="fas fa-trash" style="margin-right: 5px !important"></i>'
                        . __('messages.delete').'</a></li>';
                    
                    $html .= "</div>";
                    return $html;
                })
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
            return $output;
        }

        return view("partner::radio.index");
    }

    public function create() {
        // if (! auth()->user()->can('radio.permission')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $radio = null;
        return view("partner::radio.edit", compact('radio'));
    }

    public function edit($id) {
        // if (! auth()->user()->can('radio.permission')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $radio = Radio::findOrFail($id);
        return view("partner::radio.edit", compact('radio'));
    }

    public function store(Request $request) {
        // if (! auth()->user()->can('radio.permission')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $business_id = request()->session()->get('user.business_id');

        try {
            $data = $request->only(['name', 'department_code']);
            $data['business_id'] = $business_id;

            // To check whether the same radio exists.
            $exist = Radio::where('name', $data['name'])->where('business_id', $business_id)->first();
            if(!empty($exist)) {
                return response()->json([
                    'success' => 0,
                    'msg' => __('messages.same_exist')
                ]);
            }

            DB::beginTransaction();

            $radio = Radio::create($data);

            $output = ['success' => 1,
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
        // if (! auth()->user()->can('radio.permission')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $business_id = request()->session()->get('user.business_id');

        try {
            $radio = Radio::findOrFail($id);
            $data = request()->only(['name', 'department_code']);
            $data['business_id'] = $business_id;

            // To check if the same radio already exists.
            $exist = Radio::where('business_id', $business_id)
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

            $radio->update($data);

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
        // if (! auth()->user()->can('radio.permission')) {
        //     abort(403, 'Unauthorized action.');
        // }
        
        try {
            // To check whether it is in use.
            $count = Partner::where('radio_id', $id)->count();
            if($count > 0) {
                return response()->json([
                    'success' => 0,
                    'msg' => __('messages.now_in_use')
                ]);
            }

            Radio::find($id)->delete();

            $output = ['success' => 1,
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