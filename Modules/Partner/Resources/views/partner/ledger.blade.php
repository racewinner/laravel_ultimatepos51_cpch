@extends('layouts.app')
@section('title', __('partner::lang.partner') . ' ' . __('business.dashboard'))

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1>@lang('partner::lang.partner_ledger')</h1>
    </section>

    <section class="content no-print">
        @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.partner_information') . '</h4>'])
        @include('partner::partner.partials.partner_maininfo', ['partner' => $partner])
        @endcomponent

        @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.partner_summary') . '</h4>'])
        <div class="row">
            <div class="col-md-4">
                <label>@lang('account.debit'):</label>
                <span
                    class="fw-bold ms-4">{{ \App\Utils\Util::format_currency($partner_summary['total_debit'], $partner->currency) }}</span>
            </div>
            <div class="col-md-4">
                <label>@lang('account.credit'):</label>
                <span
                    class="fw-bold ms-4">{{ \App\Utils\Util::format_currency($partner_summary['total_credit'], $partner->currency) }}</span>
            </div>
            <div class="col-md-4">
                <label>@lang('lang_v1.balance'):</label>
                <span
                    class="fw-bold ms-4 error">{{ \App\Utils\Util::format_currency($partner_summary['total_balance'], 0) }}</span>
            </div>
        </div>
        @endcomponent

        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-tabs nav-justified">
                    <li>
                        <a href="#service_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-life-bouy"
                                aria-hidden="true"></i> @lang('lang_v1.service')</a>
                    </li>
                    <li class="active">
                        <a href="#receipt_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-file-excel-o"
                                aria-hidden="true"></i> @lang('partner::lang.partner_statement')</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane p-20" id="service_tab">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('partner::lang.name')</th>
                                    <th>@lang('purchase.unit_cost')</th>
                                    <th>@lang('business.currency')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($partner->services as $service)
                                    <tr>
                                        <td>{{ $service->name }}</td>
                                        <td>{{ $service->unit_cost }}</td>
                                        <td>{{ $service->currency->symbol }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="tab-pane p-20 active" id="receipt_tab">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('filter_month_range', __('report.month_range') . ':') !!}
                                    {!! Form::text('filter_month_range', null, ['class' => 'form-control input-sm month-range-picker', 'id' => 'filter_month_range', 'readonly']) !!}
                                </div>
                            </div>
                        </div>

                        <table class="table table-bordered table-striped ajax_view mt-20" id="receipt_table"
                            style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang('lang_v1.date')</th>
                                    <th>@lang('invoice.document_type')</th>
                                    <th>@lang('partner::lang.services')</th>
                                    <th>@lang('lang_v1.months')</th>
                                    @if(empty($partner->leave))
                                    <th>@lang('purchase.ref_no')</th>
                                    @endif
                                    <th>@lang('business.currency')</th>
                                    <th>@lang('partner::lang.debit')</th>
                                    <th>@lang('partner::lang.credit')</th>
                                    <th>@lang('lang_v1.balance')</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <td colspan="{{ empty($partner->leave) ? 6 : 5 }}" class="text-center"><strong>@lang('sale.total'):</strong></td>
                                    <td class="footer_total_debit"></td>
                                    <td class="footer_total_credit"></td>
                                    <td class="footer_total_balance"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('javascript')
    <script>
        let receipt_table = null;

        function initReceiptTable() {
            receipt_table = $('#receipt_table').DataTable({
                processing: true,
                serverSide: true,
                scrollY: "75vh",
                scrollX: true,
                scrollCollapse: true,
                ajax: {
                    url: `/partner/receipts/ledger/{{$partner->id}}`,
                    data: function (d) {
                        if ($('#filter_month_range').val()) {
                            var start = $('#filter_month_range').data('daterangepicker').startDate.format('YYYY-MM-01');
                            var end = $('#filter_month_range').data('daterangepicker').endDate.format('YYYY-MM-01');
                            d.start_month = start;
                            d.end_month = end;
                        }

                        d = __datatable_ajax_callback(d);
                    },
                },
                columns: [
                    { data: 'transaction_date', name: 'created_at', orderable: false, searchable: false },
                    { data: 'document', name: 'paid', orderable: false, searchable: false },
                    { data: 'services', orderable: false, searchable: false },
                    { data: 'period', name: 'to_month', orderable: false, searchable: false },
@if(empty($partner->leave))
                    { data: 'ref_no', name: 'ref_no', orderable: false, searchable: false },
@endif
                    { data: 'currency.symbol', orderable: false, searchable: false },
                    { data: 'debit', name: 'amount', orderable: false, searchable: false },
                    { data: 'credit', name: 'amount', orderable: false, searchable: false },
                    { data: 'balance', orderable: false, searchable: false }
                ],
                footerCallback: function (row, data, start, end, display) {
                    if (data.length > 0) {
                        let footer_total_debit = 0;
                        let footer_total_credit = 0;
                        let footer_total_balance = 0;

                        for (let r in data) {
                            footer_total_debit += floatV(data[r].debit);
                            footer_total_credit += floatV(data[r].credit);
                            footer_total_balance = data[r].balance;
                        }

                        $(".footer_total_debit").html(footer_total_debit.toFixed(2));
                        $(".footer_total_credit").html(footer_total_credit.toFixed(2));
                        $(".footer_total_balance").html(footer_total_balance);
                    }
                }
            });
        }

        $(document).ready(function () {
            $('a[href="#receipt_tab"]').on('shown.bs.tab', function (e) {
                if (!receipt_table) {
                    initReceiptTable();
                } else {
                    receipt_table.ajax.reload();
                }
            });

            initReceiptTable();

            const currentYear = new Date().getFullYear();
            const currentMonth = new Date().getMonth() + 1;
            initMonthPicker('.month-range-picker', {
                startMonth: `01/${currentYear}`,
                endMonth: `${currentMonth}/${currentYear}`,
                onChange: function (from_month, to_month) {
                    if (receipt_table) {
                        receipt_table.ajax.reload();
                    }
                }
            });
        })
    </script>
@endsection