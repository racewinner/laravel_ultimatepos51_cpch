<?php
$formUrl = empty($service) ?
    action([\Modules\Partner\Http\Controllers\ServiceController::class, 'store']) :
    action([\Modules\Partner\Http\Controllers\ServiceController::class, 'update'], [$service?->id]);
$title = empty($service) ? __('partner::lang.add_service') : __('partner::lang.edit_service');
?>

<div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => $formUrl, 'method' => empty($service) ? 'post' : 'put', 'id' => 'editServiceForm', 'files' => true]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">{{ $title }}</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-sm-12">
                    {!! Form::label('name', __('partner::lang.name') . ':*') !!}
                    {!! Form::text('name', $service?->name, ['class' => 'form-control', 'placeholder' => __('partner::lang.name'), 'required']) !!}
                </div>
                <div class="col-sm-12 mt-4">
                    {!! Form::label('unit_cost', __('purchase.unit_cost') . ':*') !!}
                    {!! Form::number('unit_cost', $service?->unit_cost ?? 0, ['class' => 'form-control', 'placeholder' => __('purchase.unit_cost'), 'required']) !!}
                </div>
                <div class="col-sm-12 mt-4">
                    {!! Form::label('currency_id', __('business.currency') . ':*') !!}
                    {!! Form::select('currency_id', $currencies, $service?->currency_id, ['class' => 'form-control', 'placeholder' => 'Select Currency', 'required']) !!}
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-save-service">@lang('messages.save')</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>