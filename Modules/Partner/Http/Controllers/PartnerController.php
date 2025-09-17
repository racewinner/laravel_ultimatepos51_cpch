<?php

namespace Modules\Partner\Http\Controllers;

use App\Business;
use App\Utils\Util;
use App\Utils\BusinessUtil;

use Modules\Partner\Entities\Partner;
use Modules\Partner\Entities\PartnerCategory;
use Modules\Partner\Entities\PartnerReceipt;
use Modules\Partner\Entities\Locality;
use Modules\Partner\Entities\Zone;
use Modules\Partner\Entities\Radio;
use Modules\Partner\Entities\MaritalStatus;
use Modules\Partner\Entities\BookCategory;
use Modules\Partner\Entities\PartnerLeave;
use Modules\Partner\Entities\PartnerTransaction;
use Modules\Partner\Entities\PartnerLeaveType;
use Modules\Partner\Entities\PartnerLeaveReason;
use Modules\Partner\Entities\PartnerReturnReason;
use Modules\Partner\Entities\PartnerAdmissionReason;
use Modules\Partner\Entities\PartnerHistory;
use Modules\Partner\Utils\PartnerUtil;
use Modules\Partner\Utils\PartnerTransactionUtil;
use Modules\Partner\Http\Controllers\PartnerHistoryController;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Pdf;
use \Carbon\Carbon;

class PartnerController extends Controller
{
    protected $partnerUtil;
    protected $businessUtil;
    protected $ptUtil;

    public function __construct(PartnerUtil $partnerUtil, PartnerTransactionUtil $ptUtil, BusinessUtil $businessUtil)
    {
        $this->partnerUtil = $partnerUtil;
        $this->ptUtil = $ptUtil;
        $this->businessUtil = $businessUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (!auth()->user()->can('partner.permission')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $partners = $this->partnerUtil->getListPartners($business_id);

            if (!empty(request()->get('partner_category'))) {
                $partners->where('partner_category_id', request()->get('partner_category'));
            }
            if (!empty(request()->get('locality'))) {
                $partners->where('locality_id', request()->get('locality'));
            }
            if (!empty(request()->get('marital_status'))) {
                $partners->where('marital_status_id', request()->get('marital_status'));
            }
            if (!empty(request()->get('book_category'))) {
                $partners->where('cat_book_id', request()->get('book_category'));
            }
            if (!empty(request()->get('admission_reason'))) {
                $partners->where('admission_reason_id', request()->get('admission_reason'));
            }
            if (request()->filled('sign_policy')) {
                $partners->where('sign_policy', request()->get('sign_policy'));
            }

            // $start_date = request()->get('start_date');
            // $end_date = request()->get('end_date');
            // if (!empty($start_date) && !empty($end_date)) {
            //     $partners->whereBetween(DB::raw('date(date_admission)'), [$start_date, $end_date]);
            // }

            $output = Datatables::of($partners)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                data-toggle="dropdown" aria-expanded="false">' .
                        __('messages.actions') .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                    $html .= '<li><a href="#" data-href="' . action([PartnerController::class, 'show'], [$row->id]) . '" class="btn-modal" data-container=".partner-modal"><i class="fas fa-eye" aria-hidden="true"></i>' . __('messages.view') . '</a></li>';
                    if (empty($row->leave)) {
                        $html .= '<li><a href="' . action([PartnerController::class, 'edit'], [$row->id]) . '"><i class="fas fa-edit" aria-hidden="true"></i>' . __('messages.edit') . '</a></li>';
                        $html .= '<li><a href="#" data-href="'
                            . action([PartnerController::class, 'getAddAdditionalPayment'], [$row->id])
                            . '" class="btn-modal" data-container=".add-additional-payment-modal">'
                            . '<i class="fas fa-file-alt" aria-hidden="true"></i>'
                            . __('partner::lang.add_additional_payment') . '</a></li>';                        
                        $html .= '<li><a href="#" data-href="'
                            . action([PartnerController::class, 'getIssueReceipt'], [$row->id])
                            . '" class="btn-modal" data-container=".issue-receipt-modal">'
                            . '<i class="fas fa-file-alt" aria-hidden="true"></i>'
                            . __('partner::lang.issue_receipt') . '</a></li>';
                        $html .= '<li><a href="' . action([PartnerController::class, 'getLeave'], [$row->id]) . '"><i class="fas fa-trash"></i>' . __('messages.delete') . '</a></li>';
                    } else {
                        $html .= '<li><a href="' . action([PartnerController::class, 'getReEntry'], [$row->id]) . '"><i class="fas fa-undo" aria-hidden="true"></i>' . __('partner::lang.reEntry') . '</a></li>';
                    }
                    $html .= '<li><a href="' . action([PartnerController::class, 'showLedger'], [$row->id]) . '"><i class="fas fa-scroll"></i>' . __('lang_v1.ledger') . '</a></li>';
                    $html .= '<li><a href="' . action([PartnerHistoryController::class, 'index'], [$row->id]) . '"><i class="fas fa-history"></i>' . __('partner::lang.history') . '</a></li>';
                    $html .= '<li><a href="#" class="print-invoice" data-href="/partner/partners/' . $row->id . '/print"><i class="fa fa-print" aria-hidden="true"></i>' . __("messages.print") . '</a></li>';
                    $html .= '</ul></div>';

                    return $html;
                })
                ->addColumn('bulk_chkbox', function ($row) {
                    return '<input type="checkbox" class="row-select" value="' . $row->id . '">';
                })
                ->addColumn('last_payment', function ($row) use ($business_id) {
                    $html = '';
                    $last_payment = $this->ptUtil->getLastPayment($row->id);
                    if(!empty($last_payment)) {
                        $html = $last_payment['month'];
                        // $html .= '(' . Util::format_currency($last_payment['amount'], $last_payment['currency']) . ')';
                    }
                    return $html;
                })
                ->addColumn('marital_status', function ($row) {
                    return $row->maritalStatus?->status;
                })
                ->editColumn('book_category', function ($row) {
                    return $row->bookCategory?->name ?? '';
                })
                ->removeColumn('id')
                ->editColumn('surname', function ($row) {
                    $html = "<div>$row->surname</div>";
                    if (!empty($row->leave)) {
                        $html .= "<div class='partner-left'>" . __('messages.deleted') . "</div>";
                    }
                    return $html;
                })
                ->editColumn('id_card', function ($row) {
                    $html = '<span style="margin-right:5px;">' . $row->id_card_number . '</span>';
                    if (!empty($row->id_card)) {
                        $html .= '<a href="' . url('uploads/documents/' . $row->id_card) . '" target="__blank"><i class="fas fa-download" aria-hidden="true"></i></a>';
                    }
                    return $html;
                })
                ->editColumn('partner_category', function ($row) {
                    return $row->category?->detail;
                })
                ->editColumn('locality', function ($row) {
                    return $row->locality?->name;
                })
                ->rawColumns(['action', 'id_card', 'surname', 'bulk_chkbox', 'last_payment'])
                ->make(true);

            return $output;
        }

        $partner_categories = PartnerCategory::allPartnerCategoriesDropdown($business_id);

        $localities = Locality::allLocalitiesDropdown($business_id);

        $marital_statuses = MaritalStatus::allMaritalStatuses($business_id);

        $book_categories = BookCategory::allBookCategoriesDropdown($business_id);

        $admission_reasons = PartnerAdmissionReason::allAdmissionReasons($business_id);

        $sign_policies = ['0' => 'No', '1' => 'Yes'];

        $payment_partner_id = request()->get('payment_partner_id');

        $print_partner_id = request()->get('print_partner_id');

        $partner_debt = $this->ptUtil->getDebt($business_id);

        return view('partner::partner.index', compact(
            'partner_categories',
            'localities',
            'marital_statuses',
            'book_categories',
            'admission_reasons',
            'sign_policies',
            'payment_partner_id',
            'print_partner_id',
        ));
    }

    public function getPartners()
    {
        if (request()->ajax()) {
            $term = request()->q;
            if (empty($term)) {
                return json_encode([]);
            }

            $business_id = request()->session()->get('user.business_id');
            $query = Partner::where('business_id', $business_id)->doesntHave('leave');
            $partners = $query->where(function ($query) use ($term) {
                $query->where('surname', 'like', '%' . $term . '%')
                    ->orWhere('name', 'like', '%' . $term . '%')
                    ->orWhere('id_card_number', 'like', '%' . $term . '%');
            })
                ->with(['locality'])->get();
            return json_encode($partners);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');

        $partner = null;

        $user = auth()->user();

        $partner_categories = PartnerCategory::allPartnerCategoriesDropdown($business_id);

        $localities = Locality::allLocalitiesDropdown($business_id);

        $marital_statuses = MaritalStatus::allMaritalStatuses($business_id);

        $book_categories = BookCategory::allBookCategoriesDropdown($business_id);

        $admission_reasons = PartnerAdmissionReason::allAdmissionReasons($business_id);

        $zones = Zone::allZones($business_id);

        $radios = Radio::allRadios($business_id);

        $sign_policies = ['0' => 'No', '1' => 'Yes'];

        $action = 'create';

        return view('partner::partner.edit', compact(
            'marital_statuses',
            'partner',
            'partner_categories',
            'localities',
            'book_categories',
            'sign_policies',
            'admission_reasons',
            'user',
            'zones',
            'radios',
            'action'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('partner.permission')) {
            abort(403, 'Unauthorized action.');
        }

        $user = auth()->user();

        try {
            $data = $request->except(['_token']);

            // To check whether the same id_card_number already exists
            $count = Partner::where('id_card_number', $data['id_card_number'])->count();
            if ($count > 0) {
                return response()->json([
                    'success' => 0,
                    'msg' => __('partner::messages.same_idcard_exist')
                ]);
            }

            $data['business_id'] = request()->session()->get('user.business_id');
            if(!empty($data['additional_fee_services']) && count($data['additional_fee_services']) > 0)
                $data['additional_fee_service_ids'] = implode(',', $data['additional_fee_services']);
            if (!empty($data['date_admission']))
                $data['date_admission'] = $this->partnerUtil->uf_date($data['date_admission']);
            if (!empty($data['dob']))
                $data['dob'] = $this->partnerUtil->uf_date($data['dob']);
            if (!empty($data['date_expire_book']))
                $data['date_expire_book'] = $this->partnerUtil->uf_date($data['date_expire_book']);
            if (!empty($data['entered_at']))
                $data['entered_at'] = $this->partnerUtil->uf_date($data['entered_at']);
            if (!empty($data['accepted_at']))
                $data['accepted_at'] = $this->partnerUtil->uf_date($data['accepted_at']);
            if (!empty($data['application_submission_date']))
                $data['application_submission_date'] = $this->partnerUtil->uf_date($data['application_submission_date']);

            // upload identity card
            $data['id_card'] = $this->partnerUtil->uploadFile($request, 'id_card', 'documents');

            DB::beginTransaction();

            // To create a new partner
            $partner = Partner::create($data);

            // To add partner_change_history
            PartnerHistory::create([
                'partner_id' => $partner->id,
                'action_type' => 'create',
                'old_date' => '',
                'new_data' => json_encode($partner),
                'editor_id' => $user->id,
            ]);

            // receipt issue months
            $today = Carbon::now();
            $date_admission = Carbon::parse($partner->date_admission);
            if($date_admission < $today) {
                $issue_months = [
                    'start_month' => $date_admission->format('m/Y'),
                    'end_month' => $today->format('m/Y'),
                ];
            } else {
                $issue_months = [
                    'start_month' => $date_admission->format('m/Y'),
                    'end_month' => $date_admission->format('m/Y'),
                ];
            }

            $output = [
                'success' => 1,
                'partner_id' => $partner->id,
                'msg' => __('messages.add_success'),
                'issue_months' => $issue_months,
            ];

            DB::commit();
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

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        try {
            $partner = Partner::with('leave')->find($id);
            $partner->date_admission = $this->partnerUtil->format_date($partner['date_admission']);
            $partner->application_submission_date = $this->partnerUtil->format_date($partner['application_submission_date']);
            $partner->date_expire_book = $this->partnerUtil->format_date($partner['date_expire_book']);
            $partner->dob = $this->partnerUtil->format_date($partner['dob']);
            $partner->entered_at = $this->partnerUtil->format_date($partner['entered_at']);
            $partner->accepted_at = $this->partnerUtil->format_date($partner['accepted_at']);
            $partner->debt = $this->ptUtil->getDebt($id);

            return view('partner::partner.show', compact('partner'));
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            abort(400, __('messages.something_went_wrong'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $partner = Partner::findOrFail($id);
            $partner->admission_month = \App\Utils\Util::getMonthName($partner->date_admission, 'Y-m-d');
            $partner->date_admission = $this->partnerUtil->format_date($partner->date_admission);
            $partner->application_submission_date = $this->partnerUtil->format_date($partner->application_submission_date);
            $partner->date_expire_book = $this->partnerUtil->format_date($partner->date_expire_book);
            $partner->dob = $this->partnerUtil->format_date($partner->dob);
            $partner->entered_at = $this->partnerUtil->format_date($partner->entered_at);
            $partner->accepted_at = $this->partnerUtil->format_date($partner->accepted_at);

            $partner_categories = PartnerCategory::allPartnerCategoriesDropdown($business_id);

            $localities = Locality::allLocalitiesDropdown($business_id);

            $marital_statuses = MaritalStatus::allMaritalStatuses($business_id);

            $book_categories = BookCategory::allBookCategoriesDropdown($business_id);

            $admission_reasons = PartnerAdmissionReason::allAdmissionReasons($business_id);

            $zones = Zone::allZones($business_id);

            $radios = Radio::allRadios($business_id);

            $sign_policies = ['0' => 'No', '1' => 'Yes'];

            $action = 'edit';

            return view('partner::partner.edit', compact(
                'partner',
                'marital_statuses',
                'partner_categories',
                'localities',
                'book_categories',
                'sign_policies',
                'admission_reasons',
                'admission_month',
                'zones',
                'radios',
                'action'
            ));
        } catch (\Exception $e) {
            abort(400, 'No Found Partner.');
        }
    }

    public function getReEntry($id)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $partner = Partner::findOrFail($id);

            // To check whether partner has been left
            if (empty($partner->leave)) {
                return redirect('/partner/partners')->with('status', [
                    'success' => false,
                    'msg' => __("partner::messages.partner_is_active_now"),
                ]);
            }

            $partner->admission_month = \App\Utils\Util::getMonthName($partner->date_admission, 'Y-m-d');
            $partner->date_admission = $this->partnerUtil->format_date($partner->date_admission);
            $partner->date_expire_book = $this->partnerUtil->format_date($partner->date_expire_book);
            $partner->dob = $this->partnerUtil->format_date($partner->dob);
            $partner->entered_at = $this->partnerUtil->format_date($partner->entered_at);
            $partner->accepted_at = $this->partnerUtil->format_date($partner->accepted_at);
            if (!empty($partner->leave?->leave_date)) {
                $partner->leave->leave_date = $this->partnerUtil->format_date($partner->leave->leave_date);
            }
            if (!empty($partner->leave?->death_data)) {
                $partner->leave->death_data = json_decode($partner->leave->death_data);
            }

            $partner_categories = PartnerCategory::allPartnerCategoriesDropdown($business_id);

            $localities = Locality::allLocalitiesDropdown($business_id);

            $marital_statuses = MaritalStatus::allMaritalStatuses($business_id);

            $book_categories = BookCategory::allBookCategoriesDropdown($business_id);

            $admission_reasons = PartnerAdmissionReason::allAdmissionReasons($business_id);

            $return_reasons = PartnerReturnReason::allReturnReasons($business_id);

            $zones = Zone::allZones($business_id);

            $radios = Radio::allRadios($business_id);

            $sign_policies = ['0' => 'No', '1' => 'Yes'];

            $debt = $this->ptUtil->getDebt($id);

            $action = 'reEntry';

            return view('partner::partner.edit', compact(
                'partner',
                'marital_statuses',
                'partner_categories',
                'localities',
                'book_categories',
                'sign_policies',
                'admission_reasons',
                'return_reasons',
                'admission_month',
                'debt',
                'radios',
                'zones',
                'action'
            ));
        } catch (\Exception $e) {
            abort(400, 'No Found Partner.');
        }
    }

    public function getLeave($id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');

            $partner = Partner::with('leave')->findOrFail($id);
            if (!empty($partner->leave?->leave_date)) {
                $partner->leave->leave_date = $this->partnerUtil->format_date($partner->leave->leave_date);
            }
            if (!empty($partner->leave->death_data)) {
                $partner->leave->death_data = json_decode($partner->leave->death_data);
            }

            $leave_types = PartnerLeaveType::allLeaveTypes($business_id);

            $leave_reasons = PartnerLeaveReason::allLeaveReasons($business_id);

            $last_payment = $this->ptUtil->getLastPayment($id);

            $partner->debt = $this->ptUtil->getDebt($id);

            return view('partner::partner.leave', compact('partner', 'leave_types', 'leave_reasons', 'last_payment'));
        } catch (\Exception $e) {
            abort(400, $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update($id)
    {
        if (!auth()->user()->can('partner.permission')) {
            abort(403, 'Unauthorized action.');
        }

        $user = auth()->user();

        try {
            $partner = Partner::findOrFail($id);
            $old_partner = json_decode(json_encode($partner), true);
            $action = request()->input('action');

            // To check PIN code.
            $data_fields = [
                'surname',
                'name',
                'address',
                'entre',
                'locality_id',
                'telephone',
                'handphone',
                'sign_policy',
                'email',
                'permanent_observation',
                'marital_status_id',
                'dob',
                'age',
                'return_reason_id',
                'collection_address',
                'collection_entre',
                'collection_telephone',
                'collection_handphone',
                'additional_fee_services',
            ];
            if ($action == 'reEntry' || empty(auth()->user()->enable_pin_partner) || request()->input('pin_partner') == auth()->user()->pin_partner) {
                $data_fields = [
                    ...$data_fields,
                    'date_admission',
                    'book_no',
                    'partner_category_id',
                    'cat_book_id',
                    'date_expire_book',
                    'issuance_place',
                    'accepted_at',
                    'application_submission_date',
                    'submit_partner',
                    'submit_partner_category_id',
                    'radio_id',
                    'zone_id',
                    'route_id',
                ];
            }
            $data = request()->only($data_fields);

            $data['business_id'] = request()->session()->get('user.business_id');
            if(!empty($data['additional_fee_services']) && count($data['additional_fee_services'])>0)
                $data['additional_fee_service_ids'] = implode(',', $data['additional_fee_services']);
            if (!empty($data['dob']))
                $data['dob'] = $this->partnerUtil->uf_date($data['dob']);
            if (!empty($data['date_expire_book']))
                $data['date_expire_book'] = $this->partnerUtil->uf_date($data['date_expire_book']);
            if (!empty($data['date_admission']))
                $data['date_admission'] = $this->partnerUtil->uf_date($data['date_admission']);
            if (!empty($data['application_submission_date']))
                $data['application_submission_date'] = $this->partnerUtil->uf_date($data['application_submission_date']);
            if (!empty($data['entered_at']))
                $data['entered_at'] = $this->partnerUtil->uf_date($data['entered_at']);
            if (!empty($data['accepted_at']))
                $data['accepted_at'] = $this->partnerUtil->uf_date($data['accepted_at']);

            DB::beginTransaction();

            // To update partner
            $partner->update($data);

            // if the request is for partner_return
            if ($action == 'reEntry') {
                if (!empty($partner->leave)) {
                    $partner->leave->delete();
                }
                $return_reason = PartnerReturnReason::findOrFail($data['return_reason_id']);
            }

            //
            // To add partner_change_history
            $partner->refresh();
            $new_partner = json_decode(json_encode($partner), true);

            // data change
            $diffs = [];
            $fields = array_keys($new_partner);
            foreach ($fields as $field) {
                if (in_array($field, ['id', 'created_at', 'updated_at']))
                    continue;

                $old_value = $old_partner[$field] ?? '';
                $new_value = $new_partner[$field] ?? '';

                if ($old_value != $new_value) {
                    $diffs[] = [
                        'field' => $field,
                        'old_value' => $old_partner[$field] ?? '',
                        'new_value' => $new_partner[$field] ?? '',
                    ];
                }
            }

            PartnerHistory::create([
                'partner_id' => $partner->id,
                'action_type' => $action,
                'old_data' => json_encode($old_partner),
                'new_data' => json_encode($new_partner),
                'data_change' => json_encode($diffs),
                'editor_id' => $user->id,
                'memo' => $action == 'reEntry' ? $return_reason->name : '',
            ]);

            $output = [
                'success' => 1,
                'partner_id' => $partner->id,
                'msg' => __('messages.update_success'),
            ];

            DB::commit();
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

    public function postLeave($id)
    {
        if (!auth()->user()->can('partner.permission')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $data = request()->except('_token');
            if (!empty($data['leave_date']))
                $data['leave_date'] = $this->partnerUtil->uf_date($data['leave_date']);
            if (!empty($data['death_data']))
                $data['death_data'] = json_encode($data['death_data']);

            $partner = Partner::findOrFail($id);

            DB::beginTransaction();

            $leave = $partner->leave()->updateorCreate(
                [
                    'partner_id' => $id
                ],
                $data
            );

            // To add partner_change_history
            PartnerHistory::create([
                'partner_id' => $partner->id,
                'action_type' => 'leave',
                'old_data' => json_encode($partner),
                'editor_id' => auth()->user()->id,
            ]);

            DB::commit();

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

    public function print($id)
    {
        try {
            $partner = Partner::find($id);
            $partner->date_admission = $this->partnerUtil->format_date($partner->date_admission);
            $partner->date_expire_book = $this->partnerUtil->format_date($partner['date_expire_book']);
            $partner->dob = $this->partnerUtil->format_date($partner->dob);
            $partner->entered_at = $this->partnerUtil->format_date($partner->entered_at);
            $partner->accepted_at = $this->partnerUtil->format_date($partner->accepted_at);
            $partner->newly_registered = $partner->created_at == $partner->updated_at ? true : false;
            $partner->debt = $this->ptUtil->getDebt($id);

            $receipt = [
                'is_enabled' => false,
                'print_type' => 'browser',
                'html_content' => view('partner::partner.show', compact('partner'))->render(),
                'printer_config' => [],
                'data' => [],
            ];

            $output = [
                'success' => 1,
                'receipt' => $receipt
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    public function printReceipt($id)
    {
        try {
            $partner = Partner::findOrFail($id);
            // return view('partner::partner.receipt', compact('partner'));

            $receipt = [
                'is_enabled' => false,
                'print_type' => 'browser',
                'html_content' => null,
                'printer_config' => [],
                'data' => [],
            ];

            $receipt['html_content'] = view('partner::partner.receipt', compact('partner'))->render();

            $output = [
                'success' => 1,
                'receipt' => $receipt
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            $output = [
                'success' => 1,
                'msg' => trans('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    public function bulkAction()
    {
        $action = request()->input('action');
        $sel_rows = request()->input('selected_rows');
        $business_id = request()->session()->get('user.business_id');

        try {
            if (!empty($sel_rows)) {
                $sel_ids = explode(',', $sel_rows);

                $partners = Partner::where('business_id', $business_id)
                    ->whereIn('id', $sel_ids)
                    ->get();

                if ($action == 'edit') {
                    $zones = Zone::allZones($business_id);

                    $radios = Radio::allRadios($business_id);

                    return view('partner::partner.bulk_edit', compact('partners', 'zones', 'radios'));
                }
            }
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
        }
    }

    public function bulkUpdate()
    {
        $business_id = request()->session()->get('user.business_id');
        $partners = request()->input('partners');

        try {
            DB::beginTransaction();

            foreach ($partners as $id => $updt_data) {
                // update partner
                $partner = Partner::findOrFail($id);
                $partner->update($updt_data);
            }

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('messages.update_success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect('partner/partners')->with('status', $output);
    }

    public function getIssueReceipt($id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $business_details = $this->businessUtil->getDetails($business_id);
            $partner = Partner::findOrFail($id);
            $last_payment = $this->ptUtil->getLastPayment($id);

            // issue_months
            $last_receipt = $this->ptUtil->getLastReceipt($id);
            if(!empty($last_receipt)) {
                $start_month = Carbon::createFromFormat('m/Y', $last_receipt['month'])->addMonth();
                $today = Carbon::now();
                if($start_month <= $today) {
                    $issue_months = [
                        'start_month' => $start_month->format('m/Y'),
                        'end_month' => $today->format('m/Y'),
                    ];
                } else {
                    $issue_months = [
                        'start_month' => $start_month->format('m/Y'),
                        'end_month' => $start_month->format('m/Y'),
                    ];
                }
            } else {
                $date_admission = Carbon::parse($partner->date_admission);
                $today = Carbon::now();
                if($date_admission <= $today) {
                    $issue_months = [
                        'start_month' => $date_admission->format('m/Y'),
                        'end_month' => $today->format('m/Y'),
                    ];
                } else {
                    $issue_months = [
                        'start_month' => $today->format('m/Y'),
                        'end_month' => $today->format('m/Y'),
                    ];
                }
            }

            //additional part for problem 31
            // To get receipts already issued but not paid.
            $unpaid_receipts = PartnerReceipt::where('partner_id', $id)
                ->where(function ($query) {
                    $query->whereNull('deleted')->orWhere('deleted', '<>', 1);
                })
                ->where('paid', '!=', 1)->with(['editor', 'partner', 'currency'])->get();

            // To get receipts not-issued until when the partner was cancelled.
            $last_receipt = $this->ptUtil->getLastReceipt($id);
            if (!empty($last_receipt)) {
                $unissued_start_month = Carbon::createFromFormat('m/Y', $last_receipt['month'])->addMonth();
                $today = Carbon::now();

                if ($unissued_start_month < $today) {
                    $unissued_receipts = [
                        'start_month' => "$unissued_start_month->month/$unissued_start_month->year",
                        'end_month' => "$today->month/$today->year"
                    ];
                } else {
                    $unissued_receipts = [
                        'start_month' => "$unissued_start_month->month/$unissued_start_month->year",
                        'end_month' => "$unissued_start_month->month/$unissued_start_month->year",
                    ];
                }
            }

            return view("partner::partner.partials.issue_receipt_modal", 
                compact('partner', 'last_payment', 'issue_months', 'unpaid_receipts', 'unissued_receipts'));
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
    }

    public function getAddAdditionalPayment($id) {
        try {
            $partner = Partner::findOrFail($id);

            // issue_months
            $last_receipt = $this->ptUtil->getLastReceipt($id, 1);
            if(!empty($last_receipt)) {
                $start_month = Carbon::createFromFormat('m/Y', $last_receipt['month'])->addMonth();
                $today = Carbon::now();
                if($start_month <= $today) {
                    $issue_months = [
                        'start_month' => $today->format('m/Y'),
                        'end_month' => $today->format('m/Y'),
                    ];
                } else {
                    $issue_months = [
                        'start_month' => $start_month->format('m/Y'),
                        'end_month' => $start_month->format('m/Y'),
                    ];
                }
            } else {
                $today = Carbon::now();
                $issue_months = [
                    'start_month' => $today->format('m/Y'),
                    'end_month' => $today->format('m/Y'),
                ];
            }

            return view("partner::partner.partials.add_additional_payment_modal", compact('partner', 'issue_months'));
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
    }

    public function showUnsettledReceipts($partner_id)
    {
        try {
            $partner = Partner::findOrFail($partner_id);

            // To get receipts already issued but not paid.
            $unpaid_receipts = PartnerReceipt::where('partner_id', $partner_id)
                ->where(function ($query) {
                    $query->whereNull('deleted')->orWhere('deleted', '<>', 1);
                })
                ->where('paid', '!=', 1)->with(['editor', 'partner', 'currency'])->get();

            // To get receipts not-issued until when the partner was cancelled.
            $last_receipt = $this->ptUtil->getLastReceipt($partner_id);
            if (!empty($last_receipt)) {
                $unissued_start_month = Carbon::createFromFormat('m/Y', $last_receipt['month'])->addMonth();
                $today = Carbon::now();

                if ($unissued_start_month < $today) {
                    $unissued_receipts = [
                        'start_month' => "$unissued_start_month->month/$unissued_start_month->year",
                        'end_month' => "$today->month/$today->year"
                    ];
                } else {
                    $unissued_receipts = [
                        'start_month' => "$unissued_start_month->month/$unissued_start_month->year",
                        'end_month' => "$unissued_start_month->month/$unissued_start_month->year",
                    ];
                }
            }

            return view(
                'partner::partner.partials.unsettled_receipts_modal',
                compact('partner', 'unpaid_receipts', 'unissued_receipts')
            );
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            abort(500, __('messages.something_went_wrong'));
        }
    }

    public function showLedger($id)
    {
        try {
            $partner = Partner::findOrFail($id);

            // To get total amount of receipt & payment
            $total_debit = PartnerReceipt::where('partner_id', $id)->where(function ($query) {
                    $query->whereNull('deleted')->orWhere('deleted', '<>', 1);
                })->sum('amount');
            $total_credit = PartnerReceipt::where('partner_id', $id)->where(function ($query) {
                    $query->whereNull('deleted')->orWhere('deleted', '<>', 1);
                })->where('paid', 1)->sum('amount');

            $partner_summary = [
                'total_debit' => $total_debit,
                'total_credit' => $total_credit,
                'total_balance' => $total_debit - $total_credit,
            ];

            return view('partner::partner.ledger', compact('partner', 'partner_summary'));
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            abort(400, $e->getMessage());
        }
    }
}
