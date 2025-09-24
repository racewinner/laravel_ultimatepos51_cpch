<?php
use \Modules\Partner\Http\Controllers\PartnerController;
use \Modules\Partner\Http\Controllers\ServiceController;
use \Modules\Partner\Http\Controllers\LocalityController;
use \Modules\Partner\Http\Controllers\SalonController;
use \Modules\Partner\Http\Controllers\MaritalStatusController;
use \Modules\Partner\Http\Controllers\PartnerCategoryController;
use \Modules\Partner\Http\Controllers\PartnerHistoryController;
use \Modules\Partner\Http\Controllers\PartnerReceiptController;
use \Modules\Partner\Http\Controllers\PartnerPaymentController;
use \Modules\Partner\Http\Controllers\PartnerBookingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('web', 'authh', 'auth', 'SetSessionData', 'language', 'timezone', 'AdminSidebarMenu')->prefix('partner')->group(function () {
    Route::get('partners/get_partners', [PartnerController::class, 'getPartners']);
    Route::get('partners/{id}/leave', [PartnerController::class, 'getLeave']);
    Route::post('partners/{id}/leave', [PartnerController::class, 'postLeave']);
    Route::get('partners/{id}/print', [PartnerController::class, 'print']);
    Route::get('partners/{id}/print_leave', [PartnerController::class, 'print_leave']);

    Route::get('partners/{id}/reEntry', [PartnerController::class, 'getReEntry']);
    Route::get('partners/{id}/ledger', [PartnerController::class, 'showLedger']);
    Route::get('partners/{id}/issue_receipt', [PartnerController::class, 'getIssueReceipt']);
    Route::get('partners/{id}/add_additional_payment', [PartnerController::class, 'getAddAdditionalPayment']);
    Route::get('partners/{id}/unsettled', [PartnerController::class, 'showUnsettledReceipts']);
    Route::post('partners/bulk_action', [PartnerController::class, 'bulkAction']);
    Route::post('partners/bulk_update', [PartnerController::class, 'bulkUpdate']);
    Route::resource('partners', PartnerController::class);

    Route::resource('localities', LocalityController::class);

    Route::get('services/get_services', [ServiceController::class, 'getServices']);
    Route::resource('services', ServiceController::class);

    Route::resource('marital_statuses', MaritalStatusController::class);

    Route::get('/partner_categories/{id}/services', [PartnerCategoryController::class, 'showServices']);
    Route::resource('partner_categories', PartnerCategoryController::class);

    Route::resource('book_categories', BookCategoryController::class);

    Route::resource('partner_leave_types', PartnerLeaveTypeController::class);

    Route::resource('partner_leave_reasons', PartnerLeaveReasonController::class);

    Route::resource('partner_return_reasons', PartnerReturnReasonController::class);

    Route::resource('partner_admission_reasons', PartnerAdmissionReasonController::class);

    Route::resource('zones', ZoneController::class);

    Route::resource('radios', RadioController::class);

    Route::resource('salons', SalonController::class);

    Route::get('bookings/get-todays-bookings', [PartnerBookingController::class, 'getTodayBookings']);
    Route::resource('bookings', PartnerBookingController::class);

    Route::get('receipts/{id}/print', [PartnerReceiptController::class, 'print']);
    Route::get('receipts/{id}/settle', [PartnerReceiptController::class, 'settle']);
    Route::get('receipts/{id}/prev_unpaid', [PartnerReceiptController::class, 'checkPrevUnpaid']);
    Route::get('receipts/issue', [PartnerReceiptController::class, 'getIssueReceipt']);
    Route::get('receipts/ledger/{partner_id}', [PartnerReceiptController::class, 'getLedger']);
    Route::post('receipts/issue', [PartnerReceiptController::class, 'postIssueReceipt']);
    Route::post('receipts/bulk_settle', [PartnerReceiptController::class, 'bulkSettle']);
    Route::post('receipts/bulk_print', [PartnerReceiptController::class, 'bulkPrint']);
    Route::delete('receipts/unpaid/{partner_id}', [PartnerReceiptController::class, 'removeUnpaid']);
    Route::put('receipts/{id}/undelete', [PartnerReceiptController::class, 'unDelete']);
    Route::resource('receipts', PartnerReceiptController::class);

    Route::get('partner_histories/{partner_id}', [PartnerHistoryController::class, 'index']);

    Route::get('/partner_payments/new_payment_row', [PartnerPaymentController::class, 'new_payment_row']);
    Route::get('/partner_payments/{transaction_id}/print_receipt', [PartnerPaymentController::class, 'printReceipt']);
    Route::resource('partner_payments', PartnerPaymentController::class);
});
