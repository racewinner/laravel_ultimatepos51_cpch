<?php

namespace Modules\Partner\Http\Controllers;

use App\Business;

use Modules\Partner\Entities\Partner;
use Modules\Partner\Entities\PartnerCategory;
use Modules\Partner\Entities\Service;
use Modules\Partner\Utils\PartnerUtil;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PartnerCategoryController extends Controller
{
    protected $partnerUtil;

    public function __construct(PartnerUtil $partnerUtil) {
        $this->partnerUtil = $partnerUtil;
    }

    public function index() {
        if (! auth()->user()->can('partner_category.permission')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $rows = PartnerCategory::where('business_id', $business_id);
            $output = Datatables::of($rows)
                ->addColumn('action', function($row) {
                    $html = "<div class='d-flex align-items-center'>";
                    
                    $html .= '<li class="btn btn-primary px-4 py-1">'
                            . '<a style="color:white !important;" href="'
                            . action([\Modules\Partner\Http\Controllers\PartnerCategoryController::class, 'edit'], [$row->id])
                            . '" >'
                            . '<i class="fas fa-edit" style="margin-right: 5px !important;" aria-hidden="true"></i>'
                            . __('messages.edit').'</a>
                    </li>';
                
                    $html .= '<li class="btn btn-danger px-4 py-1 ms-4">'
                        . '<a class="delete-partner-category" style="color:white !important;" href="'
                        . action([\Modules\Partner\Http\Controllers\PartnerCategoryController::class, 'destroy'], [$row->id])
                        . '"><i class="fas fa-trash" style="margin-right: 5px !important"></i>'
                        . __('messages.delete').'</a></li>';
                    
                    $html .= "</div>";
                    return $html;
                })
                ->editColumn('impression', function($row) {
                    return $row['impression'] == 1 ? 'Yes' : 'No';
                })
                ->editColumn('vote', function($row) {
                    return $row['vote'] == 1 ? 'Yes' : 'No';
                })
                ->editColumn('assembly', function($row) {
                    return $row['assembly'] == 1 ? 'Yes' : 'No';
                })
                ->editColumn('reserve', function($row) {
                    return $row['reserve'] == 1 ? 'Yes' : 'No';
                })
                ->editColumn('sport', function($row) {
                    return $row['sport'] == 1 ? 'Yes' : 'No';
                })
                ->editColumn('other', function($row) {
                    return $row['other'] == 1 ? 'Yes' : 'No';
                })
                ->removeColumn('id')
                ->rawColumns(['action', 'impression', 'vote', 'assembly', 'reserve', 'sport', 'other'])
                ->make(true);
            return $output;
        }

        return view("partner::partner_category.index");
    }

    public function create() {
        if (! auth()->user()->can('partner_category.permission')) {
            abort(403, 'Unauthorized action.');
        }
        
        $services = Service::all();
        $partner_category = null;
        return view("partner::partner_category.edit", compact('partner_category', 'services'));
    }

    public function edit($id) {
        if (! auth()->user()->can('partner_category.permission')) {
            abort(403, 'Unauthorized action.');
        }
        
        $services = Service::all();
        $partner_category = PartnerCategory::findOrFail($id);
        return view("partner::partner_category.edit", compact('partner_category', 'services'));
    }

    public function store(Request $request) {
        if (! auth()->user()->can('partner_category.permission')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        try {
            $data = request()->except('_token');
            $data['business_id'] = $business_id;

            // To check whether the same partner_category exists.
            $exist = PartnerCategory::where('detail', $data['detail'])->where('business_id', $business_id)->first();
            if(!empty($exist)) {
                return response()->json([
                    'success' => 0,
                    'msg' => __('messages.same_exist')
                ]);
            }

            DB::beginTransaction();

            $partner_category = PartnerCategory::create($data);

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
        if (! auth()->user()->can('partner_category.permission')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        try {
            $partner_category = PartnerCategory::findOrFail($id);
            $data = request()->except('_token');
            $data['business_id'] = $business_id;
            if(empty($data['impression'])) $data['impression'] = 0;
            if(empty($data['vote'])) $data['vote'] = 0;

            // To check if the same partner_category already exists.
            $exist = PartnerCategory::where('business_id', $business_id)
                ->where('id', '!=', $id)
                ->where('detail', $data['detail'])
                ->first();
            if(!empty($exist)) {
                return response()->json([
                    'success' => 0,
                    'msg'=>'The same partner category already exists'
                ]);
            }

            DB::beginTransaction();

            $partner_category->update($data);

            $output = [
                'success' => 1,
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
        if (! auth()->user()->can('partner_category.permission')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // To check whether it is in use.
            $count = Partner::where('partner_category_id', $id)->count();
            if($count > 0) {
                return response()->json([
                    'success' => 0,
                    'msg' => 'This category is in use now'
                ]);
            }
            
            PartnerCategory::find($id)->delete();

            $output = [
                'success' => 1,
                'msg' => __('messages.delete_success'),
            ];
        } catch(\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return response()->json($output);
    }

    public function show($id) {
        try {
            $row = PartnerCategory::findOrFail($id);
            return response()->json([
                'success' => 1,
                'partner_category' => $row
            ]);
        } catch(\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }
    }

    public function showServices($id) {
        try {
            $partner_category = PartnerCategory::findOrFail($id);
            $services = Service::whereIn('id', $partner_category->services)->get();
            return response()->json([
                'success' => 1,
                'services' => $services
            ]);
        } catch(\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }
    }
}