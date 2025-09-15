<?php
$formUrl = empty($locality) ? 
    action([\Modules\Partner\Http\Controllers\LocalityController::class, 'store']) :
    action([\Modules\Partner\Http\Controllers\LocalityController::class, 'update'], [$locality?->id]);
$title = empty($locality) ? __('partner::lang.add_locality') : __('partner::lang.edit_locality');
?>

<div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">{{ $title }}</h4>
        </div>

        <div class="modal-body">
            {!! Form::open(['url' => $formUrl, 'method' => empty($locality) ? 'post' : 'put', 'id' => 'editLocalityForm', 'files' => true ]) !!}
            <div class="row">
                <div class="col-sm-12">
                    {!! Form::label('name', __('partner::lang.name') . ':') !!}
                    {!! Form::text('name', $locality?->name, ['class' => 'form-control', 'placeholder' => __('partner::lang.name'), 'required']) !!}
                </div>
                <div class="col-sm-12 mt-4">
                    {!! Form::label('department_code', __('partner::lang.department_code') . ':') !!}
                    {!! Form::text('department_code', $locality?->department_code, ['class' => 'form-control', 'placeholder' => __('partner::lang.department_code')]) !!}
                </div>
            </div>
            {!! Form::close() !!}
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-save-locality">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
    </div>
</div>
