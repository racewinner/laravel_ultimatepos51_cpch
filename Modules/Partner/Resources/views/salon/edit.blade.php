<?php
$formUrl = empty($salon) ? 
    action([\Modules\Partner\Http\Controllers\SalonController::class, 'store']) :
    action([\Modules\Partner\Http\Controllers\SalonController::class, 'update'], [$salon?->id]);
$title = empty($salon) ? __('partner::lang.add_salon') : __('partner::lang.edit_salon');
?>

<div class="modal-dialog modal-md" role="document">
    {!! Form::open(['url' => $formUrl, 'method' => empty($salon) ? 'post' : 'put', 'id' => 'editSalonForm', 'files' => true ]) !!}
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">{{ $title }}</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-sm-12 mb-4">
                    {!! Form::label('name', __('partner::lang.name') . ':') !!}
                    {!! Form::text('name', $salon?->name, ['class' => 'form-control', 'placeholder' => __('partner::lang.name'), 'required']) !!}
                </div>

                <div class="col-sm-6 mb-4">
                    {!! Form::label('people_number', __('partner::lang.people_number') . ':') !!}
                    {!! Form::text('people_number', $salon?->people_number, ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="col-sm-6 mb-4">
                    {!! Form::label('open', __('partner::lang.open') . ':') !!}
                    <div class="checkbox">
                        <label class='p-0'>
                            {!! Form::checkbox('open', 1, $salon->open ? true : false, ['class' => 'input-icheck m-0']) !!}
                        </label>
                    </div>
                </div>

                <div class="col-sm-12 mb-4">
                    {!! Form::label('daytime', __('partner::lang.daytime') . ':') !!}
                    <div class="d-flex align-items-center">
                        <div class="timepicker" data-default-time="{{ $salon->daytime_from ?? '09:00 AM' }}">
                            <input type="hidden" class="selected-time" name="daytime_from" />
                        </div>
                        
                        <span class="mx-10">~</span>

                        <div class="timepicker" data-default-time="{{ $salon->daytime_to ?? '05:30 PM' }}">
                            <input type="hidden" class="selected-time" name="daytime_to" />
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 mb-8">
                    {!! Form::label('nighttime', __('partner::lang.nighttime') . ':') !!}
                    <div class="d-flex align-items-center">
                        <div class="timepicker" data-default-time="{{ $salon->nighttime_from ?? '09:00 PM' }}">
                            <input type="hidden" class="selected-time" name="nighttime_from" />
                        </div>
                        
                        <span class="mx-10">~</span>

                        <div class="timepicker" data-default-time="{{ $salon->nighttime_to ?? '03:00 AM' }}">
                            <input type="hidden" class="selected-time" name="nighttime_to" />
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    {!! Form::label('price_for_partner', __('partner::lang.price_for_partner') . ':') !!}
                    {!! Form::number('price_for_partner', $salon->price_for_partner ?? 0, ['class' => 'form-control']) !!}
                </div>
                <div class="col-sm-6">
                    {!! Form::label('price_for_no_partner', __('partner::lang.price_for_no_partner') . ':') !!}
                    {!! Form::number('price_for_no_partner', $salon->price_for_no_partner ?? 0, ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-save-salon">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
    </div>
    {!! Form::close() !!}
</div>
