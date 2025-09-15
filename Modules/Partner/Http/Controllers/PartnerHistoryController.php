<?php

namespace Modules\Partner\Http\Controllers;

use App\Business;

use Modules\Partner\Entities\Partner;
use Modules\Partner\Entities\PartnerHistory;
use Modules\Partner\Utils\PartnerUtil;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PartnerHistoryController extends Controller
{
    protected $partnerUtil;

    public function __construct(PartnerUtil $partnerUtil)
    {
        $this->partnerUtil = $partnerUtil;
    }

    public function index($partner_id)
    {
        if (request()->ajax()) {
            $histories = PartnerHistory::with('editor')->where('partner_id', $partner_id);
            $histories->orderBy('created_at', 'DESC');

            $output = Datatables::of($histories)
                ->removeColumn('id')
                ->editColumn('data_change', function ($row) {
                    $html = '<div>';
                    if (!empty($row->data_change)) {
                        $data_change = json_decode($row->data_change, true);
                        foreach ($data_change as $dc) {
                            $html .= $dc['field'] . " (" . $dc['old_value'] . " => " . $dc['new_value'] . "),  ";
                        }
                    }
                    $html .= "</div>";
                    return $html;
                })
                ->editColumn('action_type', function ($row) {
                    return __('partner::lang.' . $row->action_type);
                })
                ->editColumn('created_at', '{{@format_datetime($created_at, true)}}')
                ->addColumn('editor', function ($row) {
                    return $row->editor->display_name;
                })
                ->rawColumns(['data_change'])
                ->make(true);

            return $output;
        }

        $partner = Partner::findOrFail($partner_id);
        return view('partner::partner_history.index', compact('partner'));
    }
}