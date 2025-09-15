@extends('layouts.app')
@section('title', __('partner::lang.partner') . ' '. __('business.dashboard'))

<?php
if(empty($transaction)) {
    $formUrl = action([\Modules\Partner\Http\Controllers\PartnerPaymentController::class, 'store']);
    $headerTitle = __('purchase.add_payment');
} else {
    $formUrl = action([\Modules\Partner\Http\Controllers\PartnerPaymentController::class, 'update'], [$transaction?->id]);
    $headerTitle = __('purchase.edit_payment');
}
?>

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>{{$headerTitle}}</h1>
</section>

<section class="content no-print">
    {!! Form::open([
        'url' => $formUrl, 
        'method' => empty($transaction) ? 'post' : 'put', 
        'id' => 'editTransactionForm', 
        'files' => true,
    ]) !!}

    <input type='hidden' name='final_total' value='0' />

    @component('components.widget', ['class' => 'box-primary'])
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('partner_id', __('partner::lang.partner') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-user"></i>
                    </span>
                    {!! Form::select('partner_id', 
                        !empty($partner) ? [$partner->id => $partner->display_name] : [], 
                        $partner?->id, 
                        ['class' => 'form-control', 'placeholder' => __('messages.please_select') , 'required', 'id' => 'partner_id']
                    ) !!}
                    <span class="input-group-btn">
                        <a  href="{{action([\Modules\Partner\Http\Controllers\PartnerController::class, 'create'])}}"
                            class="btn btn-default bg-white btn-flat" data-name=""
                        >
                            <i class="fa fa-plus-circle text-primary fa-lg"></i>
                        </a>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="row partner-detail {{empty($partner) ? 'hidden' : ''}}">
        <div class="col-md-3">
            <label>@lang('partner::lang.idcard')</label>
            {!! Form::text('id_card_number', $partner?->id_card_number, ['class' => 'form-control', 'id' => 'id_card_number', 'readonly']) !!}
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('address', __('partner::lang.address') . ':') !!}
                {!! Form::text('address', $partner?->address, ['class' => 'form-control', 'id'=>'address', 'readonly']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('entre', __('partner::lang.entre') . ':') !!}
                {!! Form::text('entre', $partner?->entre, ['class' => 'form-control', 'id'=>'entre', 'readonly']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('locality', __('partner::lang.locality') . ':') !!}
                {!! Form::text('locality', $partner?->locality->name, ['class' => 'form-control', 'id' => 'locality', 'readonly']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('telephone', __('partner::lang.telephone') . ':') !!}
                {!! Form::text('telephone', $partner?->telephone, ['class' => 'form-control', 'id'=>'telephone', 'readonly']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('handphone', __('partner::lang.handphone') . ':') !!}
                {!! Form::text('handphone', $partner?->handphone, ['class' => 'form-control', 'id'=>'handphone', 'readonly']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('date_admission', __('partner::lang.date_admission') . ':*') !!}
                {!! Form::text('date_admission', $partner?->date_admission, ['class' => 'form-control', 'id'=>'date_admission', 'readonly']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('email', __('partner::lang.email') . ':*') !!}
                {!! Form::text('email', $partner?->email, ['class' => 'form-control', 'id'=>'email', 'readonly']) !!}
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('ref_no', __('purchase.ref_no').':') !!}
                @show_tooltip(__('lang_v1.leave_empty_to_autogenerate'))
                {!! Form::text('ref_no', $transaction?->ref_no, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('transaction_date', __('lang_v1.payment_date') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    {!! Form::text('transaction_date', @format_datetime(!empty($transaction) ? $transaction->transaction_date : 'now'), ['class' => 'form-control', 'readonly', 'required']) !!}
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('currency_id', __('business.currency') . ':*') !!}
                <div class="input-group">
                <span class="input-group-addon">
                    <i class="fas fa-money-bill-alt"></i>
                </span>
                <select name='currency_id' class='form-control select2' required {{!empty($transaction) ? 'disabled' : ''}}>
                    @foreach ($currencies as $currency)
                    <option value={{ $currency->id }} 
                        {{ $currency->id == $transaction?->currency_id ? "selected" : "" }}
                        data-thousand-separator={{ empty($currency->thousand_separator) ? "NA" : $currency->thousand_separator }}
                        data-decimal-separator={{ empty($currency->decimal_separator) ? "NA" : $currency->decimal_separator}}
                        data-symbol={{ $currency->symbol }}
                    > 
                        {{ $currency->name }}
                    </option>
                    @endforeach
                </select>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('nation_exchg_rate', __('lang_v1.currency_exchange_rate') . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fas fa-money-bill-alt"></i>
                </span>
                {!! Form::number(
                    'nation_exchg_rate', 
                    !empty($transaction) ? $transaction?->nation_exchg_rate : 1.0, 
                    ['class' => 'form-control', 'placeholder' => __('lang_v1.currency_exchange_rate'), 'readonly' => !empty($transaction)]) 
                !!}
              </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
            {!! Form::label('observation', __('partner::lang.observation') . ':') !!}
            {!! Form::text('observation', $transaction?->observation, ['class' => 'form-control']) !!}
            </div>
        </div>
    </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary'])
    <div class="d-flex justify-content-end w-100 mb-4" >
        <div style="width: 500px;" class="input-group">
            {!! Form::select('search_service', $services,null, ['class' => 'form-control', 'placeholder' => __('partner::lang.select_service_for_payment') , 'id' => 'search_service']) !!}
            <span class="input-group-btn">
                <a  href="{{action([\Modules\Partner\Http\Controllers\ServiceController::class, 'index'])}}"
                    class="btn btn-default bg-white btn-flat" data-name=""
                >
                    <i class="fa fa-plus-circle text-primary fa-lg"></i>
                </a>
            </span>
        </div>
        <button type="button" class="btn btn-primary ms-4" id="add_payment">Add</button>
    </div>
    
    <div class="row">
        <div class="table-responsive">
            <table class="table table-condensed table-bordered table-th-green text-center table-striped" id="payment_table">
                <thead>
                    <tr>
                        <th style='width: 50px;'>#</th>
                        <th style='width: 200px;'>@lang('lang_v1.service')</th>
                        <th style='width: 150px;'>@lang('lang_v1.quantity')</th>
                        <th style='width: 150px;'>@lang('purchase.unit_cost')</th>
                        <th>@lang('messages.detail')</th>
                        <th style='width: 150px;'>@lang('sale.total_amount')</th>
                        <th style='width: 100px;'>@lang('messages.action')</th>
                    </tr>
                </thead>
                <tbody>
                @if(!empty($transaction->payment_lines) && count($transaction->payment_lines) > 0)
                    @foreach($transaction->payment_lines as $index => $pl)
                    @include('partner::partner_payment.partials.payment_row', ['row_number'=>$index, 'payment' => $pl])
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-end align-items-center mt-4">
        <label class="m-0 me-2">@lang('sale.total_amount'):</label>
        <span class="final-total" style="font-size: 150%; font-weight:bold;">0.0</span>
    </div>
    @endcomponent

    <div class="d-flex justify-content-end mt-4">
        <button type="submit" class="btn btn-primary me-4" id="button-save">@lang( 'messages.save' )</button>
    </div>

    {!! Form::close() !!}
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    //Date picker
    $('#transaction_date').datetimepicker({
        format: moment_date_format + ' ' + moment_time_format,
        ignoreReadonly: true,
    });

    initMonthPicker('.month-range-picker');

    $(document).on('click', 'button#add_payment', function() {
        service_id = $("#search_service").val();
        if(!service_id) return;

        const row_number = $("table#payment_table tbody tr").length;
        $.ajax({
            method: 'GET',
            url: `/partner/partner_payments/new_payment_row?row_number=${row_number}&service_id=${service_id}`,
            success: function(result) {
                $('table#payment_table tbody').append(result);
                initMonthPicker('.month-range-picker');
                update_finaltotal();
            }
        });
    })

    $(document).on('click', '.remove_row', function(e) {
        const tr = $(e.target).closest('tr');
        $(tr).remove();
        update_finaltotal();
    })

    $(document).on('change', '.payment_unit_cost, .payment_qty', function(e) {
        update_subtotal($(e.target).closest('tr'));
        update_finaltotal();
    })

    $(document).on('submit', 'form#editTransactionForm', function(e) {
        const row_number = $("table#payment_table tbody tr").length;
        if(row_number == 0) {
            toastrSwal("No added payment", 'warning');
            return false;
        }
        return true;
    })

    $("#search_service").select2({
        ajax: {
            url: '/partner/services/get_services',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term, // search term
                    partner_id: $("#partner_id").val()
                };
            },
            processResults: function(data) {
                return {
                    results: data.map(function(item) {
                        return {
                            ...item,
                            text: `${item.name}`
                        }
                    }),
                };
            },
        },
        minimumInputLength: 0,
        escapeMarkup: function(m) {
            return m;
        },
        templateResult: function(data) {
            if (!data.id) {
                return data.text;
            }
            return `${data.name}`;
        },
        language: {
            noResults: function() {
                return "<span>No found service</span>";
            },
        },
    });

    //get partners
    $('#partner_id').select2({
        ajax: {
            url: '/partner/partners/get_partners',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term, // search term
                    page: params.page,
                };
            },
            processResults: function(data) {
                return {
                    results: data.map(function(item) {
                        return {
                            ...item,
                            text: `${item.surname} ${item.name}`
                        }
                    }),
                };
            },
        },
        minimumInputLength: 1,
        escapeMarkup: function(m) {
            return m;
        },
        templateResult: function(data) {
            if (!data.id) {
                return data.text;
            }
            return `${data.surname} ${data.name} (${data.id_card_number})`;
        },
        language: {
            noResults: function() {
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
        var data = e.params.data;
        $(".partner-detail").removeClass("hidden");

        $(".partner-detail #id_card_number").val(data.id_card_number);
        $(".partner-detail #address").val(data.address);
        $(".partner-detail #entre").val(data.entre);
        $(".partner-detail #locality").val(data.locality?.name);
        $(".partner-detail #telephone").val(data.telephone);
        $(".partner-detail #handphone").val(data.handphone);
        $(".partner-detail #date_admission").val(data.date_admission);
        $(".partner-detail #email").val(data.email);
    })

    update_finaltotal();
})

function update_subtotal(row) {
    const qty = parseFloat($(row).find(".payment_qty").val());
    const unit_cost = parseFloat($(row).find(".payment_unit_cost").val());
    const subtotal = qty * unit_cost;
    $(row).find(".payment-subtotal").val(subtotal.toFixed(2));
}

function update_finaltotal() {
    let total_amount = 0.0;
    const trs = $('table#payment_table tbody tr');
    for(let i=0; i<trs.length; i++) {
        total_amount += parseFloat($(trs[i]).find('.payment-subtotal').val());
    }
    $(".final-total").text(total_amount.toFixed(2));
    $("input[name='final_total']").val(total_amount.toFixed(2));
}

</script>
@endsection