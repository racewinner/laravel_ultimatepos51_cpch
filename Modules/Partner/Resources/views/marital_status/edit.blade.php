<?php
$formUrl = empty($marital_status) ? 
    action([\Modules\Partner\Http\Controllers\MaritalStatusController::class, 'store']) :
    action([\Modules\Partner\Http\Controllers\MaritalStatusController::class, 'update'], [$marital_status?->id]);
$title = empty($marital_status) ? __('partner::lang.add_marital_status') : __('partner::lang.edit_marital_status');
?>

<div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">{{ $title }}</h4>
        </div>

        <div class="modal-body">
            {!! Form::open(['url' => $formUrl, 'method' => empty($marital_status) ? 'post' : 'put', 'id' => 'editMaritalStatusForm', 'files' => true ]) !!}
            <div class="row">
                <div class="col-sm-12">
                    {!! Form::label('status', __('partner::lang.status') . ':') !!}
                    {!! Form::text('status', $marital_status?->status, ['class' => 'form-control', 'placeholder' => __('partner::lang.status'), 'required']) !!}
                </div>
            </div>
            {!! Form::close() !!}
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-save-marital-status">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
    </div>
</div>
