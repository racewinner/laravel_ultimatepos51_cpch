@extends('layouts.app')
@section('title', __('partner::lang.partner') . ' ' . __('business.dashboard'))

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1>@lang('lang_v1.payment')</h1>
    </section>

    <!-- Main content -->
    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('filter_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']) !!}
                </div>
            </div>
        </div>
        @endcomponent

        @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.list_payments')])
        @slot('tool')
        <div class="box-tools">
            <a class="btn btn-block btn-primary"
                href="{{action([\Modules\Partner\Http\Controllers\PartnerPaymentController::class, 'create'])}}">
                <i class="fa fa-plus"></i> @lang('messages.add')</a>
            </a>
        </div>
        @endslot

        <table class="table table-bordered table-striped ajax_view" id="pt_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang('messages.action')</th>
                    <th>@lang('lang_v1.paid_on')</th>
                    <th>@lang('partner::lang.partner')</th>
                    <th>@lang('purchase.ref_no')</th>
                    <th>@lang('business.currency')</th>
                    <th>@lang('sale.amount')</th>
                    <th>@lang('partner::lang.observation')</th>
                    <th>@lang('lang_v1.issuer')</th>
                </tr>
            </thead>
        </table>
        @endcomponent
    </section>
@endsection

@section('javascript')
    <script>
        $(document).ready(function () {
            //Date range as a button
            $('#filter_date_range').daterangepicker(
                dateRangeSettings,
                function (start, end) {
                    $('#filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                    pt_table.ajax.reload();
                }
            );
            $('#filter_date_range').on('cancel.daterangepicker', function (ev, picker) {
                $('#filter_date_range').val('');
                pt_table.ajax.reload();
            });
            $(document).on('click', 'a.delete-partner-payment', function (e) {
                e.preventDefault();
                if (window.confirm("Are you sure to delete?") == false) return;

                const url = $(e.target).closest("a").attr("href");
                $.ajax({
                    method: 'DELETE',
                    url,
                    dataType: 'json',
                    success: function (result) {
                        if (result.success == true) {
                            toastrSwal(result.msg);
                            pt_table.ajax.reload();
                        } else {
                            toastrSwal(result.msg, 'error');
                        }
                    }
                })
            });

            pt_table = $('#pt_table').DataTable({
                processing: true,
                serverSide: true,
                scrollY: "75vh",
                scrollX: true,
                scrollCollapse: true,
                ajax: {
                    url: '/partner/partner_payments',
                    data: function (d) {
                        if ($('#filter_date_range').val()) {
                            var start = $('#filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                            var end = $('#filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                            d.start_date = start;
                            d.end_date = end;
                        }

                        d = __datatable_ajax_callback(d);
                    },
                },
                columns: [
                    { data: 'action', orderable: false, searchable: false },
                    { data: 'transaction_date', searchable: false },
                    { data: 'partner_name', name: 'partner.name' },
                    { data: 'ref_no', name: 'ref_no' },
                    { data: 'currency.code', searchable: false },
                    { data: 'final_total', name: 'final_total' },
                    { data: 'observation', name: 'observation' },
                    { data: 'creator.username', searchable: false }
                ]
            });
        })
    </script>
@endsection