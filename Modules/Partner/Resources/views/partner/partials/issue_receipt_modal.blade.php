<div class="modal-dialog modal-lg" role="document" style="width: 1000px;">
    <div class="modal-content">
        {!! Form::open([
            'url' => action([\Modules\Partner\Http\Controllers\PartnerReceiptController::class, 'postIssueReceipt']),
            'method' => 'post',
        ]) !!}
        <input type='hidden' name='partner_id' value="{{$partner->id}}" />
        
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('partner::lang.issue_receipt')</h4>
        </div>

        <div class="modal-body">
            @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.partner_information') . '</h4>'])
                @include("partner::partner.partials.partner_shortinfo", ['partner' => $partner])
            @endcomponent

            <div class="row">
                <div class="col-md-6">
                    @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.services') . '</h4>'])
                    <table class="table table-bordered table-striped ajax_view" id="services_table"
                        style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang('partner::lang.name')</th>
                                <th>@lang('purchase.unit_cost')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($partner->fee_services as $service)
                                <tr>
                                    <td>{{$service->name}}</td>
                                    <td>{{ \App\Utils\Util::format_currency($service->unit_cost, $service->currency) }}</td>
                                </tr>
                            @endforeach
                            @foreach($partner->additional_fee_services as $service)
                                <tr>
                                    <td>{{$service->name}}</td>
                                    <td>{{ \App\Utils\Util::format_currency($service->unit_cost, $service->currency) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endcomponent
                </div>
                <div class="col-md-6">
                    @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('lang_v1.payment') . '</h4>'])
                    <div class="form-group">
                        {!! Form::label('last_payment', __('partner::lang.last_month_payment') . ':') !!}
                        <div class="d-flex">
                            {!! Form::text('last_pay_month', !empty($last_payment) ? $last_payment['month'] : '', ['class' => 'form-control disabled']) !!}
                            {!! Form::text('last_pay_amount', !empty($last_payment) ? \App\Utils\Util::format_currency($last_payment['amount'], $last_payment['currency']) : '', ['class' => 'form-control disabled']) !!}
                        </div>
                    </div>
                    @endcomponent
                </div>
            </div>

            @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.unpaid_receipts') . '</h4>'])
            <table class="table table-bordered table-striped unpaid-receipts">
                <thead>
                    <tr>
                        <th>@lang('lang_v1.months')</th>
                        <th>@lang('purchase.ref_no')</th>
                        <th>@lang('business.currency')</th>
                        <th>@lang('sale.amount')</th>
                        <th>@lang('lang_v1.issuer')</th>
                    </tr>
                </thead>
                <tbody>
                    @if($unpaid_receipts->count() > 0)
                        @foreach ($unpaid_receipts as $receipt)
                            <tr data-receipt-id="{{ $receipt->id }}">
                                <td class="d-flex align-items-center">
                                    <!-- <input type="checkbox" class="sel_unpaid_receipt m-0" data-receipt-id="{{ $receipt->id }}" /> -->
                                    <span class="ms-2">{{ $receipt->period }}</span>
                                </td>
                                <td>{{ $receipt->ref_no }}</td>
                                <td>{{ $receipt->currency?->symbol ?? '' }}</td>
                                <td>{{ $receipt->amount }}</td>
                                <td>{{ $receipt->editor->display_name }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center">@lang('partner::messages.no_unpaid_receipts')</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <!-- @if($unpaid_receipts->count() > 0)
                <div class="d-flex justify-content-end mt-4">
                    <button type="button" class="btn btn-success"
                        id="btn_settle_unpaidReceipts">@lang("lang_v1.pay")</button>
                </div>
            @endif -->
            @endcomponent
            
            @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('invoice.receipt') . '</h4>'])
            @if(!empty($issue_months))
                <div class="row">
                    <div class="col-md-6">
                        {!! Form::label('issue_months', __('lang_v1.months') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            <input type='text' class='form-control input-sm month-range-picker' name='issue_months' id="issue_months"
                                data-start-month="{{ $issue_months['start_month'] }}" data-end-month="{{ $issue_months['end_month'] }}" required />
                        </div>
                    </div>
                    <div class="col-md-6" style="padding-top: 15px;">
                        <div class="checkbox">
                            {!! Form::label('paid', __('lang_v1.paid') . ':') !!}
                            {!! Form::checkbox('paid', 1, null, ['class' => 'input-icheck m-0']) !!}
                        </div>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-12 text-center">
                        @lang('partner::messages.no_month_to_issue_receipt')
                    </div>
                </div>
            @endif
            @endcomponent
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" {{ empty($issue_months) ? 'disabled' : '' }}>@lang('invoice.issue')</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>