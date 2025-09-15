<?php
$formUrl = empty($radio) ? 
    action([\Modules\Partner\Http\Controllers\RadioController::class, 'store']) :
    action([\Modules\Partner\Http\Controllers\RadioController::class, 'update'], [$radio?->id]);
$title = empty($radio) ? __('partner::lang.add_radio') : __('partner::lang.edit_radio');
?>

<div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => $formUrl, 'method' => empty($radio) ? 'post' : 'put', 'id' => 'editRadioForm', 'files' => true ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">{{ $title }}</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-sm-12">
                    {!! Form::label('name', __('partner::lang.name') . ':') !!}
                    {!! Form::text('name', $radio?->name, ['class' => 'form-control', 'required']) !!}
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-save-radio">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>
