<div class="modal-dialog modal-md" role="document" style="width: 1000px;">
    <input type="hidden" name="partner_id" value="{{ $partner->id }}" />
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('partner::lang.issue_receipt')</h4>
        </div>

        <div class="modal-body">
            @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.partner') . '</h4>'])
                @include("partner::partner.partials.partner_shortinfo", ['partner' => $partner])
            @endcomponent

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
                                    <input type="checkbox" class="sel_unpaid_receipt m-0" data-receipt-id="{{ $receipt->id }}" />
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

            @if($unpaid_receipts->count() > 0)
                <div class="d-flex justify-content-end mt-4">
                    <button type="button" class="btn btn-success"
                        id="btn_settle_unpaidReceipts">@lang("lang_v1.pay")</button>
                </div>
            @endif
            @endcomponent

            @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.unissued_receipts') . '</h4>'])
            @if(empty($unissued_receipts))
                <div class="d-flex justify-content-center">@lang('partner::messages.no_unissued_receipts')</div>
            @else
                <div class="row">
                    <div class="col-md-6">
                        {!! Form::label('issue_months', __('lang_v1.months') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            <input type='text' id='issue_months' name='issue_months' class='form-control input-sm month-range-picker'
                                data-start-month="{{ $unissued_receipts['start_month'] }}"
                                data-end-month="{{ $unissued_receipts['end_month'] }}" required />
                        </div>
                    </div>
                    <div class="col-md-6" style="padding-top: 15px;">
                        <div class="checkbox">
                            {!! Form::label('paid', __('lang_v1.paid') . ':') !!}
                            {!! Form::checkbox('paid', 1, null, ['class' => 'input-icheck m-0']) !!}
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button class="btn btn-success" id="btn_issue_receipts_for_issue_and_reenter">@lang("invoice.issue_and_reenter")</button>
                </div>
            @endif
            @endcomponent

            <div class="d-flex justify-content-end mt-4 pe-4">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerca</button>
                @if(!empty($partner->leave))
                <button class="btn btn-primary ms-4" id="btn_reEntry">@lang('partner::lang.reEntry')</button>
                @endif
            </div>
        </div>
    </div>
</div>