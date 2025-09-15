<?php

namespace Modules\Partner\Http\Controllers;

use App\Business;

use Modules\Partner\Entities\Partner;
use Modules\Partner\Entities\PartnerBooking;
use Modules\Partner\Entities\Salon;
use Modules\Partner\Utils\PartnerUtil;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PartnerBookingController extends Controller
{
    protected $partnerUtil;

    public function __construct(PartnerUtil $partnerUtil)
    {
        $this->partnerUtil = $partnerUtil;
    }

    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $start_date = request()->start;
            $end_date = request()->end;
            $query = PartnerBooking::where('business_id', $business_id)
                ->whereBetween(DB::raw('date(booking_start)'), [$start_date, $end_date])
                ->with(['partner', 'salon']);

            $bookings = $query->get();

            $events = [];

            foreach ($bookings as $booking) {
                if (empty($booking->partner) || empty($booking->salon)) {
                    continue;
                }

                $backgroundColor = '#3c8dbc';
                $borderColor = '#3c8dbc';

                if ($booking->booking_status == 'completed') {
                    $backgroundColor = '#00a65a';
                    $borderColor = '#00a65a';
                } else if ($booking->booking_status == 'cancelled') {
                    $backgroundColor = '#f56954';
                    $borderColor = '#f56954';
                } else if ($booking->booking_status == 'waiting') {
                    $backgroundColor = '#FFAD46';
                    $borderColor = '#FFAD46';
                }

                $title = $booking->partner->display_name . ' - ' . $booking->salon->name;
                $title_html = $booking->partner->display_name . '<br/>' . $booking->salon->name;
                $events[] = [
                    'title' => $title,
                    'title_html' => $title_html,
                    'start' => $booking->booking_start,
                    'end' => $booking->booking_end,
                    'partner_name' => $booking->partner->display_name,
                    'salon_name' => $booking->salon->name,
                    'url' => action([PartnerBookingController::class, 'show'], [$booking->id]),
                    'backgroundColor' => $backgroundColor,
                    'borderColor' => $borderColor,
                    'allDay' => false,
                    'event_type' => 'bookings',
                ];
            }

            return $events;
        }

        $salons = Salon::allSalons($business_id);

        return view("partner::partner_booking.index", compact('salons'));
    }

    public function store()
    {
        if (request()->ajax()) {
            $user_id = request()->session()->get('user.id');
            $business_id = request()->session()->get('user.business_id');

            $input = request()->input();
            $booking_start = $this->partnerUtil->uf_date($input['booking_start'], true);
            $booking_end = $this->partnerUtil->uf_date($input['booking_end'], true);

            //Check if booking is available for the required input
            $query = PartnerBooking::where('business_id', $business_id)
                ->where('salon_id', $input['salon_id'])
                ->where(function ($q) use ($booking_start, $booking_end) {
                    $q->where(function ($q1) use ($booking_start, $booking_end) {
                        $q1->where('booking_start', '<=', $booking_start)->where('booking_end', '>=', $booking_start);
                    })->orWhere(function ($q1) use ($booking_start, $booking_end) {
                        $q1->where('booking_start', '<=', $booking_end)->where('booking_end', '>=', $booking_end);
                    });
                });
            $existing_booking = $query->first();
            if (!empty($existing_booking)) {
                $time_range = $this->partnerUtil->format_date($existing_booking->booking_start, true) . ' ~ ' .
                    $this->partnerUtil->format_date($existing_booking->booking_end, true);

                $output = [
                    'success' => 0,
                    'msg' => trans(
                        'restaurant.booking_not_available',
                        [
                            'customer_name' => $existing_booking->partner->display_name,
                            'booking_time_range' => $time_range,
                        ]
                    ),
                ];
            } else {
                $input['business_id'] = $business_id;
                $input['created_by'] = $user_id;
                $input['booking_start'] = $booking_start;
                $input['booking_end'] = $booking_end;
                $input['booking_status'] = $input['booking_status'] ?? 'booked';


                $booking = PartnerBooking::create($input);

                $output = [
                    'success' => 1,
                    'msg' => trans('messages.add_success'),
                ];

                //Send notification to customer
                if (isset($input['send_notification']) && $input['send_notification'] == 1) {
                    $output['send_notification'] = 1;
                    $output['notification_url'] = action([\App\Http\Controllers\NotificationController::class, 'getTemplate'], ['transaction_id' => $booking->id, 'template_for' => 'new_partner_booking']);
                }
            }

            return response()->json($output);
        }
    }

    public function getTodayBookings()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');
            $today = \Carbon::now()->format('Y-m-d');
            $query = PartnerBooking::where('business_id', $business_id)
                ->where('booking_status', 'booked')
                ->whereDate('booking_start', $today)
                ->with(['partner', 'salon', 'creator']);

            return DataTables::of($query)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                data-toggle="dropdown" aria-expanded="false">' .
                        __('messages.actions') .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                    $html .= '<li><a href="#" data-href="' . action([PartnerBookingController::class, 'show'], [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>' . __('messages.view') . '</a></li>';
                    return $html;
                })
                ->editColumn('partner', function ($row) {
                    return $row->partner?->display_name ?? '';
                })
                ->editColumn('salon', function ($row) {
                    return $row->salon?->name ?? '';
                })
                ->editColumn('provisional', function ($row) {
                    return $row->provisional ? __('messages.yes') : __('messages.no');
                })
                ->editColumn('confirmed', function ($row) {
                    return $row->confirmed ? __('messages.yes') : __('messages.no');
                })
                ->editColumn('creator', function ($row) {
                    return $row->creator->display_name;
                })
                ->removeColumn('id')
                ->rawColumns(['action', 'provisional', 'confirmed', 'creator'])
                ->make(true);
        }
    }

    public function show($id)
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $booking = PartnerBooking::with(['partner', 'salon'])->findOrFail($id);

            $booking_start = $this->partnerUtil->format_date($booking->booking_start, true);
            $booking_end = $this->partnerUtil->format_date($booking->booking_end, true);

            $booking_statuses = [
                'waiting' => __('lang_v1.waiting'),
                'booked' => __('restaurant.booked'),
                'completed' => __('restaurant.completed'),
                'cancelled' => __('restaurant.cancelled'),
            ];

            return view('partner::partner_booking.show', compact('booking', 'booking_start', 'booking_end', 'booking_statuses'));
        }
    }

    public function update($id)
    {
        try {
            $booking = PartnerBooking::findOrFail($id);

            $booking->booking_status = request()->booking_status;
            $booking->save();

            $output = [
                'success' => 1,
                'msg' => __('messages.update_success')
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

    public function destroy($id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $booking = PartnerBooking::where('business_id', $business_id)->where('id', $id)->delete();
            $output = [
                'success' => 1,
                'msg' => trans('lang_v1.deleted_success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }
}