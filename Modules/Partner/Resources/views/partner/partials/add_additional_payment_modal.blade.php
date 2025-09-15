<div class="modal-dialog modal-lg" role="document" style="width: 1000px;">
    <input type='hidden' name='partner_id' value="{{$partner->id}}" />
    <input type="hidden" name="additional_fee_service_ids" value="{{ $partner->additional_fee_service_ids }}" />

    <div class="modal-content">
        {!! Form::open([
            'url' => action([\Modules\Partner\Http\Controllers\PartnerReceiptController::class, 'postIssueReceipt']),
            'method' => 'post',
        ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('partner::lang.add_additional_payment')</h4>
        </div>

        <div class="modal-body">
            @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.partner_information') . '</h4>'])
                @include("partner::partner.partials.partner_shortinfo", ['partner' => $partner])
            @endcomponent

            @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.additional_fee_services') . '</h4>'])
            <div class="row">
                @foreach ($partner->additional_fee_services as $service)
                <div class="col-md-3">{{ $service->name }}</div>
                @endforeach
            </div>
            @endcomponent

            @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('invoice.receipt') . '</h4>'])
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
            </div>
            @endcomponent
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" >@lang('invoice.issue')</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
        </div>

        {!! Form::close() !!}
    </div>
</div>
