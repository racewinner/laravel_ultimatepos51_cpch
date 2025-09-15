<div class="modal-dialog modal-md" role="document" style="width:1000px;">
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

            @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.additional_fee_services') . '</h4>'])
            <div class="row">
                @foreach ($partner->additional_fee_services as $service)
                <div class="col-md-4">
                    {{ $service->name }}
                </div>
                @endforeach
            </div>
            @endcomponent

            @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.additional_fee_services') . '</h4>'])
            <div class="row">
                <div class="col-md-6">
                    {!! Form::label('issue_months', __('lang_v1.months') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        <input type='text' name='issue_months' class='form-control input-sm month-range-picker'
                            data-start-month="{{ $issue_months['start_month'] }}"
                            data-end-month="{{ $issue_months['end_month'] }}" required />
                    </div>
                </div>
            </div>
            @endcomponent
        </div>
    </div>
</div>