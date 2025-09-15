<?php

namespace Modules\Partner\Http\Controllers;

use App\Business;

use Modules\Partner\Entities\Partner;
use Modules\Partner\Entities\Locality;
use Modules\Partner\Utils\PartnerUtil;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class LocalityController extends Controller
{
    protected $partnerUtil;

    public function __construct(PartnerUtil $partnerUtil) {
        $this->partnerUtil = $partnerUtil;
    }

    public function index() {
        if (! auth()->user()->can('locality.permission')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $localities = Locality::where('business_id', $business_id);
            $output = Datatables::of($localities)
                ->addColumn('action', function($row) {
                    $html = "<div class='d-flex align-items-center'>";
                    $html .= '<li class="btn btn-primary px-4 py-1">'
                            . '<a href="#" style="color:white !important;" data-href="'
                            . action([\Modules\Partner\Http\Controllers\LocalityController::class, 'edit'], [$row->id])
                            . '" class="btn-modal" data-container=".locality-modal">'
                            . '<i class="fas fa-edit" style="margin-right: 5px !important;" aria-hidden="true"></i>'
                            . __('messages.edit').'</a>
                    </li>';
                    
                    $html .= '<li class="btn btn-danger px-4 py-1 ms-4">'
                        . '<a class="delete-locality" style="color:white !important;" href="'
                        . action([\Modules\Partner\Http\Controllers\LocalityController::class, 'destroy'], [$row->id])
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

        return view("partner::locality.index");
    }

    public function create() {
        if (! auth()->user()->can('locality.permission')) {
            abort(403, 'Unauthorized action.');
        }

        $locality = null;
        return view("partner::locality.edit", compact('locality'));
    }

    public function edit($id) {
        if (! auth()->user()->can('locality.permission')) {
            abort(403, 'Unauthorized action.');
        }

        $locality = Locality::findOrFail($id);
        return view("partner::locality.edit", compact('locality'));
    }

    public function store(Request $request) {
        if (! auth()->user()->can('locality.permission')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        try {
            $data = $request->only(['name', 'department_code']);
            $data['business_id'] = $business_id;

            // To check whether the same locality exists.
            $exist = Locality::where('name', $data['name'])->where('business_id', $business_id)->first();
            if(!empty($exist)) {
                return response()->json([
                    'success' => 0,
                    'msg' => __('messages.same_exist')
                ]);
            }

            DB::beginTransaction();

            $locality = Locality::create($data);

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
        if (! auth()->user()->can('locality.permission')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        try {
            $locality = Locality::findOrFail($id);
            $data = request()->only(['name', 'department_code']);
            $data['business_id'] = $business_id;

            // To check if the same locality already exists.
            $exist = Locality::where('business_id', $business_id)
                ->where('id', '!=', $id)
                ->where('name', $data['name'])
                ->first();
            if(!empty($exist)) {
                return response()->json([
                    'success' => 0,
                    'msg'=>'The same locality already exists'
                ]);
            }

            DB::beginTransaction();

            $locality->update($data);

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
        if (! auth()->user()->can('locality.permission')) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            // To check whether it is in use.
            $count = Partner::where('locality_id', $id)->count();
            if($count > 0) {
                return response()->json([
                    'success' => 0,
                    'msg' => 'This locality is in use now'
                ]);
            }

            Locality::find($id)->delete();

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