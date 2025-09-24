@extends('layouts.app')
@section('title', __('partner::lang.partner') . ' ' . __('business.dashboard'))

@section('css')
<style type="text/css">
.partner-status {
    padding: 5px 20px;
    border-radius: 5px;
}
.partner-status.active {
    background-color: #00be00;
    color: white;
}
.partner-status.inactive {
    background-color: red;
    color: white;
}

</style>
@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1>@lang('partner::lang.partner')</h1>
    </section>

    <!-- Main content -->
    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="locality">@lang('partner::lang.partner_category'):</label>
                    {!! Form::select('partner_category', $partner_categories, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'partner_category', 'placeholder' => __('lang_v1.all')]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="locality">@lang('partner::lang.locality'):</label>
                    {!! Form::select('locality', $localities, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'locality', 'placeholder' => __('lang_v1.all')]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="locality">@lang('partner::lang.marital_status'):</label>
                    {!! Form::select('marital_status', $marital_statuses, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'marital_status', 'placeholder' => __('lang_v1.all')]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="locality">@lang('partner::lang.book_category'):</label>
                    {!! Form::select('book_category', $book_categories, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'book_category', 'placeholder' => __('lang_v1.all')]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="locality">@lang('partner::lang.admission_reason'):</label>
                    {!! Form::select('admission_reason', $admission_reasons, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'admission_reason', 'placeholder' => __('lang_v1.all')]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="locality">@lang('partner::lang.sign_policy'):</label>
                    {!! Form::select('sign_policy', $sign_policies, null, ['class' => 'form-control select2', 'id' => 'sign_policy', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('filter_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']) !!}
                </div>
            </div>
        </div>
        @endcomponent

        @component('components.widget', ['class' => 'box-primary', 'title' => __('partner::lang.list_partners')])
        @slot('tool')
        <div class="box-tools">
            <a class="btn btn-block btn-primary"
                href="{{action([\Modules\Partner\Http\Controllers\PartnerController::class, 'create'])}}">
                <i class="fa fa-plus"></i> @lang('messages.add')</a>
            </a>
        </div>
        @endslot

        <table class="table table-bordered table-striped ajax_view" id="partner_table" style="width: 100%;">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all-row" data-table-id="partner_table"></th>
                    <th>@lang('messages.action')</th>
                    <th>@lang('partner::lang.surname')</th>
                    <th>@lang('partner::lang.name')</th>
                    <th>@lang('partner::lang.idcard')</th>
                    <th>@lang('partner::lang.partner_category')</th>
                    <th>@lang('partner::lang.address')</th>
                    <th>@lang('partner::lang.collection_address')</th>
                    <th>@lang('messages.last_payment')</th>
                    <th>@lang('partner::lang.telephone')</th>
                    <th>@lang('partner::lang.handphone')</th>
                    <th>@lang('partner::lang.email')</th>
                    <th>@lang('partner::lang.marital_status')</th>
                    <th>@lang('partner::lang.dob')</th>
                    <th>@lang('partner::lang.age')</th>
                    <th>@lang('partner::lang.book_no')</th>
                    <th>@lang('partner::lang.cat_book')</th>
                    <th>@lang('partner::lang.date_admission')</th>
                    <th>@lang('partner::lang.date_expire_book')</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="19">
                        {!! Form::open(['url' => action([\Modules\Partner\Http\Controllers\PartnerController::class, 'bulkAction']), 'method' => 'post', 'id' => 'bulk_action_form']) !!}
                        {!! Form::hidden('action', null, ['id' => 'action']) !!}
                        {!! Form::hidden('selected_rows', null, ['id' => 'selected_rows']) !!}
                        {!! Form::button(__('lang_v1.bulk_edit'), array('class' => 'btn btn-xs btn-primary', 'id' => 'btn_bulk_action')) !!}
                        {!! Form::close() !!}
                    </td>
                </tr>
            </tfoot>
        </table>

        <div class="modal fade partner-modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>

        <div class="modal fade issue-receipt-modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>

        <div class="modal fade add-additional-payment-modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
        
        <div class="modal fade partner-leave-modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>

        @include('partner::partner.partials.bulk_print')

        @endcomponent
    </section>

    @if(!empty($print_partner_id))
      @if(!empty($print_partner_leave == 1))
        <a href="#" class="print-invoice print-partner" data-href="/partner/partners/{{ $print_partner_id }}/print_leave" target="_blank"></a>
      @else
        <a href="#" class="print-invoice print-partner" data-href="/partner/partners/{{ $print_partner_id }}/print" target="_blank"></a>
      @endif
    @endif

@endsection

@section('javascript')
    <script src="{{ asset('js/partner/partner_receipt.js?v=' . $asset_v) }}"></script>

    <script>
        $(document).ready(() => {
            $('#filter_date_range').daterangepicker(
                dateRangeSettings,
                function (start, end) {
                    $('#filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                    partner_table.ajax.reload();
                }
            );
            $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#sell_list_filter_date_range').val('');
                sale_report_table.ajax.reload();
            });
            
            partner_table = $('#partner_table').DataTable({
                processing: true,
                serverSide: true,
                scrollY: "100vh",
                scrollX: true,
                scrollCollapse: true,
                ajax: {
                    url: '/partner/partners',
                    data: function (d) {
                        d = __datatable_ajax_callback(d);

                        if ($('#partner_category').length) {
                            d.partner_category = $('#partner_category').val();
                        }
                        if ($('#locality').length) {
                            d.locality = $('#locality').val();
                        }
                        if ($('#marital_status').length) {
                            d.marital_status = $('#marital_status').val();
                        }
                        if ($('#book_category').length) {
                            d.book_category = $('#book_category').val();
                        }
                        if ($('#admission_reason').length) {
                            d.admission_reason = $('#admission_reason').val();
                        }
                        if ($('#sign_policy').length) {
                            d.sign_policy = $('#sign_policy').val();
                        }
                        if($('#filter_date_range').val()) {
                            var start = $('#filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                            var end = $('#filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                            d.start_date = start;
                            d.end_date = end;
                        }
                    },
                },
                columns: [
                    { data: 'bulk_chkbox', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                    { data: 'surname', name: 'surname' },
                    { data: 'name', name: 'name' },
                    { data: 'id_card', name: 'id_card_number' },
                    { data: 'partner_category', name: 'category.detail' },
                    { data: 'address', name: 'address' },
                    { data: 'collection_address', name: 'collection_address' },
                    { data: 'last_payment', orderable: false, searchable: false },
                    { data: 'telephone', name: 'telephone' },
                    { data: 'handphone', name: 'handphone' },
                    { data: 'email', name: 'email' },
                    { data: 'marital_status', name: 'marital_status' },
                    { data: 'dob', name: 'dob' },
                    { data: 'age', name: 'age' },
                    { data: 'book_no', name: 'book_no' },
                    { data: 'book_category', name: 'cat_book', searchable: false },
                    { data: 'date_admission', name: 'date_admission' },
                    { data: 'date_expire_book', name: 'date_expire_book' },
                ],
            });

            $(document).on('shown.bs.modal', '.partner-leave-modal', function (e) {
                $('.partner-leave-modal .date-picker').datetimepicker({
                    format: moment_date_format,
                    ignoreReadonly: true,
                });
            });

            $(document).on(
                'change',
                '#partner_category, #locality, #marital_status, #book_category, #admission_reason, #sign_policy',
                function () {
                    partner_table.ajax.reload();
                }
            );

            $(document).on('click', '#btn_bulk_action', function (e) {
                e.preventDefault();
                let selected_rows = getSelectedRows();

                if (selected_rows.length > 0) {
                    $('#bulk_action_form input#selected_rows').val(selected_rows);
                    $('#bulk_action_form input#action').val('edit');
                    $('#bulk_action_form').submit();
                }
            })

            @if(!empty($print_partner_id))
            $("a.print-partner").trigger('click');
            @endif
        })
    </script>
@endsection