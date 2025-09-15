<div class="modal-dialog modal-md" role="document" style="width: 1000px;">
    <div class="modal-content">
        {!! Form::open([
            'url' => action([\Modules\Partner\Http\Controllers\PartnerReceiptController::class, 'postIssueReceipt']),
            'method' => 'post',
        ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('partner::lang.issue_receipt')</h4>
        </div>

        <div class="modal-body">
            @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.partner') . '</h4>'])
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('partner_id', __('partner::lang.partner') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user"></i>
                            </span>
                            {!! Form::select('partner_id', [], null, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'id' => 'partner_id']) !!}
                            <span class="input-group-btn">
                                <a href="{{action([\Modules\Partner\Http\Controllers\PartnerController::class, 'create'])}}"
                                    class="btn btn-default bg-white btn-flat" data-name="">
                                    <i class="fa fa-plus-circle text-primary fa-lg"></i>
                                </a>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('partner_category', __('partner::lang.partner_category') . ':') !!}
                        <div class="d-flex align-items-center">
                            {!! Form::select('partner_category_from', $partner_categories, null, ['class' => 'form-control ', 'placeholder' => __('messages.please_select')]) !!}
                            <span class="px-1">~</span>
                            {!! Form::select('partner_category_to', $partner_categories, null, ['class' => 'form-control ', 'placeholder' => __('messages.please_select')]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('radio_id', __('partner::lang.radio') . ':') !!}
                        <div class="d-flex align-items-center">
                            {!! Form::select('from_radio_id', $radios, '', ['class' => 'form-control ', 'placeholder' => __('messages.please_select')]) !!}
                            <span class="px-1">~</span>
                            {!! Form::select('to_radio_id', $radios, '', ['class' => 'form-control ', 'placeholder' => __('messages.please_select')]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('zone_id', __('partner::lang.zone') . ':') !!}
                        <div class="d-flex align-items-center">
                            {!! Form::select('from_zone_id', $zones, '', ['class' => 'form-control ', 'placeholder' => __('messages.please_select')]) !!}
                            <span class="px-1">~</span>
                            {!! Form::select('to_zone_id', $zones, '', ['class' => 'form-control ', 'placeholder' => __('messages.please_select')]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('route_id', __('partner::lang.route') . ':') !!}
                        <div class="d-flex align-items-center">
                            {!! Form::number('from_route_id', '', ['class' => 'form-control ']) !!}
                            <span class="px-1">~</span>
                            {!! Form::number('to_route_id', '', ['class' => 'form-control ']) !!}
                        </div>
                    </div>
                </div>
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
                        <input type='text' class='form-control input-sm month-range-picker' name='issue_months'
                            required />
                    </div>
                </div>
                <div class="col-md-6" style="padding-top: 15px;">
                    <div class="checkbox">
                        {!! Form::label('paid', __('lang_v1.paid') . ':') !!}
                        {!! Form::checkbox('paid', 1, null, ['class' => 'input-icheck m-0']) !!}
                    </div>
                </div>
            </div>
            @endcomponent
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang('invoice.issue')</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>