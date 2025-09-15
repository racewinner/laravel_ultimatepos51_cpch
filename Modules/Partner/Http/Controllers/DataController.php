<?php

namespace Modules\Partner\Http\Controllers;

use App\Utils\ModuleUtil;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Menu;

class DataController extends Controller
{
    public function superadmin_package()
    {
        return [
            [
                'name' => 'partner_module',
                'label' => __('partner::lang.partner_module'),
                'default' => false,
            ],
        ];
    }

    /**
     * Defines user permissions for the module.
     *
     * @return array
     */
    public function user_permissions()
    {
        return [
            [
                'value' => 'partner.manage',
                'label' => __('partner::lang.partner_permission'),
                'default' => false,
            ],
            [
                'value' => 'locality.manage',
                'label' => __('partner::lang.locality_permission'),
                'default' => false,
            ],
            [
                'value' => 'partner_category.manage',
                'label' => __('partner::lang.partner_category_permission'),
                'default' => false,
            ],
            [
                'value' => 'marital_status.manage',
                'label' => __('partner::lang.marital_status_permission'),
                'default' => false,
            ],
            [
                'value' => 'book_category.manage',
                'label' => __('partner::lang.book_category_permission'),
                'default' => false,
            ],
            [
                'value' => 'partner_receipt.show',
                'label' => __('partner::lang.receipt_show_permission'),
                'default' => true,
            ],
            [
                'value' => 'partner_receipt.issue',
                'label' => __('partner::lang.issue_receipt'),
                'default' => true,
            ],
        ];
    }

    /**
     * Adds mercadolibre menus
     *
     * @return null
     */
    public function modifyAdminMenu()
    {
        $module_util = new ModuleUtil();

        $business_id = session()->get('user.business_id');
        $is_partner_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'partner_module', 'superadmin_package');

        Menu::modify('admin-sidebar-menu', function ($menu) {
            $menu->dropdown(
                __('partner::lang.partner_management'),
                function ($sub) {
                    if (auth()->user()->can('partner.permission')) {
                        $sub->url(
                            action([\Modules\Partner\Http\Controllers\PartnerController::class, 'create']),
                            __('partner::lang.add_partner'),
                            ['icon' => 'fa fa-handshake', 'active' => (request()->segment(1) == 'partner') && (request()->segment(2) == 'partners') && (request()->segment(3) == 'create')]
                        );
                        $sub->url(
                            action([\Modules\Partner\Http\Controllers\PartnerController::class, 'index']),
                            __('partner::lang.list_partners'),
                            ['icon' => 'fa fa-handshake', 'active' => (request()->segment(1) == 'partner') && (request()->segment(2) == 'partners' && request()->segment(3) == null)]
                        );
                        if(auth()->user()->can('partner_receipt.issue')) {
                            $sub->url(
                                action([\Modules\Partner\Http\Controllers\PartnerReceiptController::class, 'index'], ['issue_receipt' => 1]),
                                __('partner::lang.issue_receipt'),
                                ['icon' => 'fa fa-handshake']
                            );
                        }
                        $sub->url(
                            action([\Modules\Partner\Http\Controllers\PartnerReceiptController::class, 'index']),
                            __('partner::lang.list_receipts'),
                            ['icon' => 'fa fa-handshake', 'active' => (request()->segment(1) == 'partner') && (request()->segment(2) == 'receipts' && request()->segment(3) == null)]
                        );
                    }
                },
                ['icon' => 'fa fa-handshake', 'id' => 'partner']
            )->order(100);

            $menu->dropdown(
                __('partner::lang.partner_booking'),
                function ($sub) {
                    $sub->url(
                        action([\Modules\Partner\Http\Controllers\PartnerBookingController::class, 'index']),
                        __('partner::lang.list_bookings'),
                        ['icon' => 'fa fa-handshake', 'active' => (request()->segment(1) == 'partner') && (request()->segment(2) == 'bookings' && request()->segment(3) == null)]
                    );
                },
                ['icon' => 'fa fa-calendar-check', 'id' => 'partner']
            )->order(105);

            $menu->dropdown(
                __('partner::lang.auxiliaries'),
                function ($sub) {
                    if (auth()->user()->can('locality.permission')) {
                        $sub->url(
                            action([\Modules\Partner\Http\Controllers\LocalityController::class, 'index']),
                            __('partner::lang.list_localities'),
                            ['icon' => 'fa fa-handshake', 'active' => (request()->segment(1) == 'partner') && (request()->segment(2) == 'localities')]
                        );
                    }
                    if (auth()->user()->can('partner_category.permission')) {
                        $sub->url(
                            action([\Modules\Partner\Http\Controllers\PartnerCategoryController::class, 'index']),
                            __('partner::lang.list_partner_categories'),
                            ['icon' => 'fa fa-handshake', 'active' => (request()->segment(1) == 'partner') && (request()->segment(2) == 'partner_categories')]
                        );
                    }
                    if (auth()->user()->can('marital_status.permission')) {
                        $sub->url(
                            action([\Modules\Partner\Http\Controllers\MaritalStatusController::class, 'index']),
                            __('partner::lang.list_marital_statuses'),
                            ['icon' => 'fa fa-handshake', 'active' => (request()->segment(1) == 'partner') && (request()->segment(2) == 'marital_statuses')]
                        );
                    }
                    $sub->url(
                        action([\Modules\Partner\Http\Controllers\BookCategoryController::class, 'index']),
                        __('partner::lang.list_book_categories'),
                        ['icon' => 'fa fa-handshake', 'active' => (request()->segment(1) == 'partner') && (request()->segment(2) == 'book_categories')]
                    );
                    $sub->url(
                        action([\Modules\Partner\Http\Controllers\PartnerLeaveTypeController::class, 'index']),
                        __('partner::lang.list_partner_leave_types'),
                        ['icon' => 'fa fa-handshake', 'active' => (request()->segment(1) == 'partner') && (request()->segment(2) == 'partner_leave_types')]
                    );
                    $sub->url(
                        action([\Modules\Partner\Http\Controllers\PartnerLeaveReasonController::class, 'index']),
                        __('partner::lang.list_partner_leave_reasons'),
                        ['icon' => 'fa fa-handshake', 'active' => (request()->segment(1) == 'partner') && (request()->segment(2) == 'partner_leave_reasons')]
                    );
                    $sub->url(
                        action([\Modules\Partner\Http\Controllers\PartnerAdmissionReasonController::class, 'index']),
                        __('partner::lang.list_partner_admission_reasons'),
                        ['icon' => 'fa fa-handshake', 'active' => (request()->segment(1) == 'partner') && (request()->segment(2) == 'partner_admission_reasons')]
                    );
                    $sub->url(
                        action([\Modules\Partner\Http\Controllers\PartnerReturnReasonController::class, 'index']),
                        __('partner::lang.list_partner_return_reasons'),
                        ['icon' => 'fa fa-handshake', 'active' => (request()->segment(1) == 'partner') && (request()->segment(2) == 'partner_return_reasons')]
                    );
                    $sub->url(
                        action([\Modules\Partner\Http\Controllers\ServiceController::class, 'index']),
                        __('partner::lang.list_services'),
                        ['icon' => 'fa fa-handshake', 'active' => (request()->segment(1) == 'partner') && (request()->segment(2) == 'services')]
                    );
                    $sub->url(
                        action([\Modules\Partner\Http\Controllers\ZoneController::class, 'index']),
                        __('partner::lang.list_zones'),
                        ['icon' => 'fa fa-handshake', 'active' => (request()->segment(1) == 'partner') && (request()->segment(2) == 'zones')]
                    );
                    $sub->url(
                        action([\Modules\Partner\Http\Controllers\RadioController::class, 'index']),
                        __('partner::lang.list_radios'),
                        ['icon' => 'fa fa-handshake', 'active' => (request()->segment(1) == 'partner') && (request()->segment(2) == 'radios')]
                    );
                    $sub->url(
                        action([\Modules\Partner\Http\Controllers\SalonController::class, 'index']),
                        __('partner::lang.list_salons'),
                        ['icon' => 'fa fa-handshake', 'active' => (request()->segment(1) == 'partner') && (request()->segment(2) == 'salons')]
                    );
                },
                ['icon' => 'fa fa-database', 'id' => 'partner']
            )->order(110);
        });
    }
}
