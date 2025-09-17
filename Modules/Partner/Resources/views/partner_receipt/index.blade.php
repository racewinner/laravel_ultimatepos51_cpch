@extends('layouts.app')
@section('title', __('partner::lang.partner') . ' ' . __('business.dashboard'))

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1>@lang('invoice.receipt')</h1>
    </section>

    <!-- Main content -->
    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('filter_month_range', __('report.month_range') . ':') !!}
                    {!! Form::text('filter_month_range', null, ['class' => 'form-control input-sm month-range-picker', 'id' => 'filter_month_range', 'readonly']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('partner_category', __('partner::lang.partner_category') . ':') !!}
                    <div class="d-flex align-items-center">
                        {!! Form::select('filter_from_partner_category', $partner_categories, null, ['class' => 'form-control ', 'id'=>'filter_from_partner_category', 'placeholder' => __('messages.please_select')]) !!}
                        <span class="px-1">~</span>
                        {!! Form::select('filter_to_partner_category', $partner_categories, null, ['class' => 'form-control ', 'id'=>'filter_to_partner_category', 'placeholder' => __('messages.please_select')]) !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('radio_id', __('partner::lang.radio') . ':') !!}
                    <div class="d-flex align-items-center">
                        {!! Form::select('filter_from_radio_id', $radios, '', ['class' => 'form-control ', 'placeholder' => __('messages.please_select'), 'id' => 'filter_from_radio_id']) !!}
                        <span class="px-1">~</span>
                        {!! Form::select('filter_to_radio_id', $radios, '', ['class' => 'form-control ', 'placeholder' => __('messages.please_select'), 'id' => 'to_radio_id']) !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('zone_id', __('partner::lang.zone') . ':') !!}
                    <div class="d-flex align-items-center">
                        {!! Form::select('filter_from_zone_id', $zones, '', ['class' => 'form-control ', 'placeholder' => __('messages.please_select'), 'id' => 'filter_from_zone_id']) !!}
                        <span class="px-1">~</span>
                        {!! Form::select('filter_to_zone_id', $zones, '', ['class' => 'form-control ', 'placeholder' => __('messages.please_select'), 'id' => 'filter_to_zone_id']) !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('route_id', __('partner::lang.route') . ':') !!}
                    <div class="d-flex align-items-center">
                        {!! Form::number('filter_from_route_id', '', ['class' => 'form-control', 'id' => 'filter_from_route_id']) !!}
                        <span class="px-1">~</span>
                        {!! Form::number('filter_to_route_id', '', ['class' => 'form-control ', 'id' => 'filter_to_route_id']) !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('filter_partner_name', __('partner::lang.partner_name') . ':') !!}
                    {!! Form::text('filter_partner_name', null, ['class' => 'form-control input-sm', 'id' => 'filter_partner_name']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('filter_partner_idcard', __('partner::lang.partner_idcard') . ':') !!}
                    {!! Form::text('filter_partner_idcard', null, ['class' => 'form-control input-sm', 'id' => 'filter_partner_idcard']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('receipt_status', __('partner::lang.status') . ':') !!}
                    {!! Form::select('receipt_status', $receipt_status, null, ['class' => 'form-control input-sm', 'id'=>'filter_receipt_status', 'placeholder' => __('messages.please_select')]) !!}
                </div>
            </div>
        </div>
        @endcomponent

        @component('components.widget', ['class' => 'box-primary', 'title' => __('partner::lang.list_receipts')])
        @slot('tool')
        <div class="box-tools">
            @if(auth()->user()->can('partner_receipt.issue'))
            <a href="#" id="btn_issue_receipt" class="btn btn-block btn-primary btn-modal"
                data-href="{{ action([\Modules\Partner\Http\Controllers\PartnerReceiptController::class, 'getIssueReceipt']) }}"
                data-container=".bulk-issue-receipts-modal">
                <i class="fa fa-plus"></i> @lang('invoice.issue')</a>
            </a>
            @endif
        </div>
        @endslot

        <table class="table table-bordered table-striped ajax_view" id="receipt_table" style="width: 100%;">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all-row" data-table-id="receipt_table"></th>
                    <th>@lang('messages.action')</th>
                    <th>@lang('lang_v1.months')</th>
                    <th>@lang('partner::lang.partner')</th>
                    <th>@lang('purchase.ref_no')</th>
                    <th>@lang('business.currency')</th>
                    <th>@lang('sale.amount')</th>
                    <th>@lang('lang_v1.type')</th>
                    <th>@lang('lang_v1.paid')</th>
                    <th>@lang('lang_v1.issuer')</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="9">
                        {!! Form::button(__('partner::lang.bulk_settle'), array('class' => 'btn btn-xs btn-primary', 'id' => 'btn_bulk_settle')) !!}
                        {!! Form::button(__('partner::lang.bulk_print_receipt'), array('class' => 'btn btn-xs btn-success', 'id' => 'btn_bulk_print_receipt', 'data-type' => 'receipt')) !!}
                        {!! Form::button(__('partner::lang.bulk_print_payment'), array('class' => 'btn btn-xs btn-success', 'id' => 'btn_bulk_print_payment', 'data-type' => 'payment')) !!}
                    </td>
                </tr>
            </tfoot>
        </table>
        @endcomponent

        <div class="modal fade bulk-issue-receipts-modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

        <div class="modal fade unsettled-receipts-modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

        @include('partner::partner.partials.bulk_print')
    </section>
@endsection

@section('javascript')
    <script src="{{ asset('js/partner/partner_receipt.js?v=' . $asset_v) }}"></script>

    <script>
        var receipt_table;

        function initReceiptTable() {
            receipt_table = $('#receipt_table').DataTable({
                processing: true,
                serverSide: true,
                scrollY: "75vh",
                scrollX: true,
                scrollCollapse: true,
                ajax: {
                    url: '/partner/receipts',
                    data: function (d) {
                        if ($('#filter_month_range').val()) {
                            var start = $('#filter_month_range').data('daterangepicker').startDate.format('YYYY-MM-01');
                            var end = $('#filter_month_range').data('daterangepicker').endDate.format('YYYY-MM-01');
                            d.start_month = start;
                            d.end_month = end;
                        }

                        if($('#filter_from_partner_category').val()) d.from_partner_category = $("#filter_from_partner_category").val();
                        if($('#filter_to_partner_category').val()) d.to_partner_category = $("#filter_to_partner_category").val();

                        if ($('#filter_from_zone_id').val()) d.from_zone_id = $('#filter_from_zone_id').val();
                        if ($('#filter_to_zone_id').val()) d.to_zone_id = $('#filter_to_zone_id').val();

                        if ($('#filter_from_radio_id').val()) d.from_radio_id = $('#filter_from_radio_id').val();
                        if ($('#filter_to_radio_id').val()) d.to_radio_id = $('#to_radio_id').val();

                        if ($('#filter_from_route_id').val()) d.from_route_id = $('#filter_from_route_id').val();
                        if ($('#filter_to_route_id').val()) d.to_route_id = $('#filter_to_route_id').val();

                        if ($('#filter_partner_name').val()) d.partner_name = $('#filter_partner_name').val();
                        if ($('#filter_partner_idcard').val()) d.partner_idcard = $('#filter_partner_idcard').val();

                        if($('#filter_receipt_status').val()) d.receipt_status = $('#filter_receipt_status').val();

                        d = __datatable_ajax_callback(d);
                    },
                },
                columns: [
                    { data: 'bulk_chkbox', orderable: false, searchable: false },
                    { data: 'action', orderable: false, searchable: false },
                    { data: 'period', name: 'to_month', searchable: false },
                    { data: 'partner_name', name: 'partner.name' },
                    { data: 'ref_no', name: 'ref_no' },
                    { data: 'currency.code', searchable: false },
                    { data: 'amount', name: 'amount', searchable: false },
                    { data: 'type', name: 'additional_payment', searchable: false },
                    { data: 'paid', name: 'paid', searchable: false },
                    { data: 'editor.username', searchable: false }
                ]
            });
        }

        $(document).ready(function () {
            //Date range as a button
            $('#filter_date_range').daterangepicker(
                dateRangeSettings,
                function (start, end) {
                    $('#filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));

                    if(!receipt_table) initReceiptTable();
                    receipt_table.ajax.reload();
                }
            );
            $('#filter_date_range').on('cancel.daterangepicker', function (ev, picker) {
                $('#filter_date_range').val('');

                if(!receipt_table) initReceiptTable();
                receipt_table.ajax.reload();
            });
            $(document).on('change', '#filter_from_partner_category, #filter_to_partner_category, #filter_from_zone_id, #filter_to_zone_id, #filter_from_radio_id, #filter_to_radio_id, #filter_from_route_id, #filter_to_route_id, #filter_partner_name, #filter_partner_idcard, #filter_receipt_status', function () {
                if(!receipt_table) initReceiptTable();
                receipt_table.ajax.reload();
            })
            $(document).on('click', 'a.undelete-receipt', function (e) {
                e.preventDefault();
                e.stopPropagation();

                swal({
                    text: "@lang('partner::messages.confirm_undelete_receipt')",
                    icon: "warning",
                    buttons: {
                        cancel: "@lang('messages.no')",
                        confirm: "@lang('messages.yes')"
                    },
                    dangerMode: false,
                }).then((result) => {
                    if(result) {
                        const receipt_id = $(e.target).closest('a').data('id');
                        $.ajax({
                            method: 'put',
                            url: `/partner/receipts/${receipt_id}/undelete`,
                            dataType: 'json',
                            success: function (result) {
                                if (result.success == true) {
                                    toastrSwal(result.msg);

                                    if(!receipt_table) initReceiptTable();
                                    receipt_table.ajax.reload();
                                } else {
                                    toastrSwal(result.msg, 'error');
                                }
                            }
                        })
                    }
                });
                
            })
            $(document).on('click', 'a.delete-receipt', function (e) {
                e.preventDefault();
                e.stopPropagation();

                swal({
                    text: "@lang('messages.confirm_delete')",
                    icon: "warning",
                    buttons: {
                        cancel: "@lang('messages.no')",
                        confirm: "@lang('messages.yes')"
                    },
                    dangerMode: false,
                }).then((result) => {
                    const receipt_id = $(e.target).closest('a').data('id');
                    if(result) {
                        $.ajax({
                            method: 'DELETE',
                            url: `/partner/receipts/${receipt_id}}`,
                            dataType: 'json',
                            success: function (result) {
                                if (result.success == true) {
                                    toastrSwal(result.msg);

                                    if(!receipt_table) initReceiptTable();
                                    receipt_table.ajax.reload();
                                } else {
                                    toastrSwal(result.msg, 'error');
                                }
                            }
                        })
                    }
                });
            });

            $(document).on('shown.bs.modal', '.bulk-issue-receipts-modal', function (e) {
                initMonthPicker('.bulk-issue-receipts-modal .month-range-picker', {
                    drops: 'up'
                });

                $('.bulk-issue-receipts-modal input.input-icheck').iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                });

                $('.bulk-issue-receipts-modal #partner_id').select2({
                    dropdownParent: $(".bulk-issue-receipts-modal"),
                    ajax: {
                        url: '/partner/partners/get_partners',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term, // search term
                                page: params.page,
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data.map(function (item) {
                                    return {
                                        ...item,
                                        text: `${item.surname} ${item.name}`
                                    }
                                }),
                            };
                        },
                    },
                    minimumInputLength: 1,
                    escapeMarkup: function (m) {
                        return m;
                    },
                    templateResult: function (data) {
                        if (!data.id) {
                            return data.text;
                        }
                        return `${data.surname} ${data.name} (${data.id_card_number})`;
                    },
                    language: {
                        noResults: function () {
                            var name = $('#partner_id')
                                .data('select2')
                                .dropdown.$search.val();
                            return (
                                '<a href="/partner/partners/create" class="btn btn-link add_new_partner"><i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i>&nbsp; ' +
                                __translate('add_name_as_new_partner', { name: name }) +
                                '</a>'
                            );
                        },
                    },
                }).on('select2:select', function (e) {
                })
            });

            $(document).on('receipts_updated', '.bulk-issue-receipts-modal', function(e) {
                if(!receipt_table) initReceiptTable();
                receipt_table.ajax.reload();
            })

            $(document).on('click', '.receipt-settle', function (e) {
                e.preventDefault();

                const receipt_id = $(e.target).closest('a').data('id');

                // To check whether there is prev-month when was not made payment
                $.ajax({
                    url: `/partner/receipts/${receipt_id}/prev_unpaid`,
                    method: 'get',
                    dataType: 'json',
                    success: function (result) {
                        if(result.success == 0) {
                            toastrSwal(result.msg, 'warning');
                        } else {
                            swal({
                                text: "@lang('partner::messages.confirm_settle_receipt')",
                                icon: "warning",
                                buttons: {
                                    cancel: "@lang('messages.no')",
                                    confirm: "@lang('messages.yes')"
                                },
                                dangerMode: false,
                            }).then((willSettle) => {
                                if (willSettle) {
                                    const url = `/partner/receipts/${receipt_id}/settle`;
                                    $.ajax({
                                        url,
                                        method: 'get',
                                        dataType: 'json',
                                        success: function (result) {
                                            if (result.success == 1) {
                                                if(!receipt_table) initReceiptTable();
                                                receipt_table.ajax.reload();

                                                toastrSwal(result.msg, 'success', function() {
                                                    showUnsettledReceipts(result.partner_id);

                                                    setTimeout(() => {
                                                        window.open(`/partner/receipts/${receipt_id}/print?type=payment`, "_blank");
                                                    }, 1000);
                
                                                });
                                            } else {
                                                toastrSwal(result.msg, 'error');
                                            }
                                        }
                                    })
                                }
                            })
                        }
                    }
                })
            })

            $(document).on('click', '#btn_bulk_print_receipt, #btn_bulk_print_payment', function (e) {
                e.preventDefault();

                let selected_ids = getSelectedRows();
                const type = $(e.target).data('type');

                if (selected_ids.length > 0) {
                    $form = $("#form_bulk_print");
                    $form.find("input[name='type']").val(type);
                    $form.find("input[name='selected_ids']").val(selected_ids.join(','));
                    $form.trigger('submit');
                }
            })

            async function check_pre_unpaied (selected_ids, unselected_ids) {
                let selected_objs = selected_ids.map(itm => ({id: itm, to_del: 0}))

                for (var i = 0; i < selected_ids.length; i++) {
                  let selected_id = selected_ids[i];
                  let prev_selected_id = parseInt(selected_id) - 1;
                  let selected_idx = selected_ids.indexOf(prev_selected_id.toString());
                  if (selected_idx != -1) 
                      selected_objs[i].to_del = 1
                }

                selected_objs = selected_objs.filter(itm => itm.to_del == 0);
                selected_ids = selected_objs.map(itm => itm.id);

                let pre_unpaied_detect = false;
                let result_msg = '';

                for (var j = 0; j < selected_ids.length; j++) {
                    await $.ajax({
                        url: `/partner/receipts/${selected_ids[j]}/prev_unpaid`,
                        method: 'get',
                        dataType: 'json',
                        success: function (result) {
                            if(result.success == 0) {
                                pre_unpaied_detect = true;
                                pre_unpaied_id = selected_ids[j];
                                result_msg = result.msg;
                            } 
                        }
                    })

                    if (pre_unpaied_detect)
                        break;
                }
                
                return {pre_unpaied_detect, pre_unpaied_id, result_msg}
            }

            $(document).on('click', '#btn_bulk_settle', async function (e) {
                e.preventDefault();

                let selected_ids = getSelectedRows();
                let unselected_ids = getUnSelectedRows();

                //check the pre-unpaied
                const { pre_unpaied_detect, pre_unpaied_id, result_msg } = await check_pre_unpaied(selected_ids, unselected_ids);
                let pre_unpaied_id4iterator = parseInt(pre_unpaied_id);
                let cnt_iterator = 0;

                let seg_unselected_idx = unselected_ids.indexOf((pre_unpaied_id - 1).toString());
                let seg_unselected_ids = unselected_ids.filter((itm, idx) => idx <= seg_unselected_idx)
                debugger
                while (1) {
                  pre_unpaied_id4iterator--;
                  cnt_iterator++;
                  if (seg_unselected_ids.includes((pre_unpaied_id4iterator - 1).toString()) == false) 
                      break;
                }

                debugger
                let jq_el_before_unpaied = $("input[value='"+pre_unpaied_id+"']").parent().parent()
                for(let i = 0; i < cnt_iterator; i++) {
                  jq_el_before_unpaied = jq_el_before_unpaied.prev();
                }
                let msg = jq_el_before_unpaied ? jq_el_before_unpaied.children().get(2).children[0].innerHTML : undefined;

                if (pre_unpaied_detect) {
                    if (!msg)
                      toastrSwal(result_msg, 'warning');
                    else {
                      let new_msg = result_msg.slice(0, -7);
                      new_msg += msg;
                      toastrSwal(new_msg, 'warning');
                    }
                    return;
                }

                if (selected_ids.length > 0) {
                    swal({
                        text: "@lang('partner::messages.confirm_create_payment')",
                        icon: "warning",
                        buttons: {
                            cancel: "@lang('messages.no')",
                            confirm: "@lang('messages.yes')"
                        },
                        dangerMode: false,
                    }).then((willPay) => {
                        if (willPay) {
                            const data = {
                                selected_ids,
                            };
                            $.ajax({
                                url: '/partner/receipts/bulk_settle',
                                data,
                                method: 'post',
                                dataType: 'json',
                                success: function (result) {
                                    if (result.success == true) {
                                        toastrSwal(result.msg, 'success', function() {
                                            if(result.new_payment_ref_nos?.length > 0) {
                                                printReceipts(result.new_payment_ref_nos, 1);                                            
                                            }
                                        });

                                        if(!receipt_table) initReceiptTable();
                                        receipt_table.ajax.reload();
                                    } else {
                                        toastrSwal(result.msg, 'error');
                                    }
                                }
                            })
                        }
                    })
                }
            })

            $(document).on('hidden.bs.modal', '.unsettled-receipts-modal', function(e) {
                if(receipt_table) receipt_table.ajax.reload();
            })

            const currentYear = new Date().getFullYear();
            const currentMonth = new Date().getMonth() + 1;
            initMonthPicker('.month-range-picker', {
                startMonth: `01/${currentYear}`,
                endMonth: `${currentMonth}/${currentYear}`,
                onChange: function (from_month, to_month) {
                    if(!receipt_table) initReceiptTable();
                    receipt_table.ajax.reload();
                }
            });

            // if this request is from 'new receipt', it would open issue_receipt modal.
            @if(!empty($issue_receipt))
                $("#btn_issue_receipt").trigger('click');
            @endif

            @if($receipt_show_permission)
            initReceiptTable();
            @endif
        })
    </script>
@endsection