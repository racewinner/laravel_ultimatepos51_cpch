<div class="modal-dialog modal-md" role="document" style="width: 800px;">
    {!! Form::open([
    'method' => 'post',
    'url' => action([\Modules\Partner\Http\Controllers\PartnerBookingController::class, 'store']),
    'id' => 'add_booking_form'
]) !!}
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('restaurant.add_booking')</h4>
        </div>

        <div class="modal-body">
            @component('components.widget', ['class' => 'box-primary', 'id' => 'partner-section'])
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('partner_id', __('partner::lang.partner') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user"></i>
                            </span>
                            {!! Form::select('partner_id', [], null, ['class' => 'form-control w-100', 'placeholder' => __('messages.please_select'), 'id' => 'partner_id', 'required']) !!}
                            <span class="input-group-btn">
                                <a href="{{action([\Modules\Partner\Http\Controllers\PartnerController::class, 'create'])}}"
                                    class="btn btn-default bg-white btn-flat" data-name="">
                                    <i class="fa fa-plus-circle text-primary fa-lg"></i>
                                </a>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 d-flex justify-content-end">
                    <i class="fa fa-angle-up expand-tag cursor-pointer hidden" aria-hidden="true" style="margin-top: 30px; font-size: 20px;"></i>
                </div>
            </div>

            <div class="row partner-detail hidden">
                <div class="col-md-6">
                    <label>@lang('partner::lang.idcard')</label>
                    {!! Form::text('id_card_number', $partner?->id_card_number, ['class' => 'form-control', 'id' => 'id_card_number', 'readonly']) !!}
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('email', __('partner::lang.email') . ':*') !!}
                        {!! Form::text('email', $partner?->email, ['class' => 'form-control', 'id' => 'email', 'readonly']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('address', __('partner::lang.address') . ':') !!}
                        {!! Form::text('address', $partner?->address, ['class' => 'form-control', 'id' => 'address', 'readonly']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('entre', __('partner::lang.entre') . ':') !!}
                        {!! Form::text('entre', $partner?->entre, ['class' => 'form-control', 'id' => 'entre', 'readonly']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('telephone', __('partner::lang.telephone') . ':') !!}
                        {!! Form::text('telephone', $partner?->telephone, ['class' => 'form-control', 'id' => 'telephone', 'readonly']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('handphone', __('partner::lang.handphone') . ':') !!}
                        {!! Form::text('handphone', $partner?->handphone, ['class' => 'form-control', 'id' => 'handphone', 'readonly']) !!}
                    </div>
                </div>
            </div>
            @endcomponent

            @component('components.widget', ['class' => 'box-primary', 'id' => 'salon-section'])
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('salon_id', __('partner::lang.salon') . ':*') !!}
                        {!! Form::select('salon_id', $salons, null, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'id' => 'salon_id', 'required']) !!}
                    </div>
                </div>
                <div class="col-md-6 d-flex justify-content-end">
                    <i class="fa fa-angle-up expand-tag cursor-pointer hidden" aria-hidden="true" style="margin-top: 30px; font-size: 20px;"></i>
                </div>
            </div>

            <div class="row salon-detail hidden">
                <div class="col-sm-12">
                    {!! Form::label('daytime', __('partner::lang.daytime') . ':') !!}
                    <div class="d-flex align-items-center">
                        {!! Form::text('daytime_from', '', ['readonly', 'class' => 'form-control']) !!}
                        <span class="mx-10">~</span>
                        {!! Form::text('daytime_to', '', ['readonly', 'class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-sm-12">
                    {!! Form::label('nighttime', __('partner::lang.nighttime') . ':') !!}
                    <div class="d-flex align-items-center">
                        {!! Form::text('nighttime_from', '', ['readonly', 'class' => 'form-control']) !!}
                        <span class="mx-10">~</span>
                        {!! Form::text('nighttime_to', '', ['readonly', 'class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-sm-6">
                    {!! Form::label('price_for_partner', __('partner::lang.price_for_partner') . ':') !!}
                    {!! Form::number('price_for_partner', $salon->price_for_partner ?? 0, ['class' => 'form-control', 'readonly']) !!}
                </div>
                <div class="col-sm-6">
                    {!! Form::label('price_for_no_partner', __('partner::lang.price_for_no_partner') . ':') !!}
                    {!! Form::number('price_for_no_partner', $salon->price_for_no_partner ?? 0, ['class' => 'form-control', 'readonly']) !!}
                </div>
            </div>
            @endcomponent

            @component('components.widget', ['class' => 'box-primary'])
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('cost', __('partner::lang.cost') . ':*') !!}
                        {!! Form::Number('cost', '', ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('booking_start', __('restaurant.start_time') . ':*') !!}
                        <div class='input-group date'>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                            {!! Form::text('booking_start', null, ['class' => 'form-control', 'placeholder' => __('restaurant.start_time'), 'required', 'id' => 'start_time']) !!}
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('booking_end', __('restaurant.end_time') . ':*') !!}
                        <div class='input-group date'>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                            {!! Form::text('booking_end', null, ['class' => 'form-control', 'placeholder' => __('restaurant.end_time'), 'required', 'id' => 'end_time']) !!}
                        </div>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('booking_note', __('brand.note') . ':') !!}
                        {!! Form::textarea('booking_note', null, ['class' => 'form-control', 'placeholder' => __('brand.note'), 'rows' => 3]) !!}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <div class="checkbox">
                            <label class='p-0'>
                                {!! Form::checkbox('provisional', 1, false, ['class' => 'input-icheck m-0']) !!}
                                @lang('partner::lang.provisional_booking')
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="checkbox">
                            <label class='p-0'>
                                {!! Form::checkbox('confirmed', 1, false, ['class' => 'input-icheck m-0']) !!}
                                @lang('partner::lang.confirmed_booking')
                            </label>
                        </div>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="col-sm-12 d-none">
                    <div class="form-group">
                        <div class="checkbox">
                            {!! Form::checkbox('send_notification', 1, false, ['class' => 'input-icheck', 'id' => 'send_notification']) !!}
                            @lang('partner::messages.send_notification_to_partner')
                        </div>
                    </div>
                </div>
            </div>
            @endcomponent

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>