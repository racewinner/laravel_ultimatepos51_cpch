<?php
$formUrl = empty($partner_admission_reason) ? 
    action([\Modules\Partner\Http\Controllers\PartnerAdmissionReasonController::class, 'store']) :
    action([\Modules\Partner\Http\Controllers\PartnerAdmissionReasonController::class, 'update'], [$partner_admission_reason?->id]);
$title = empty($partner_admission_reason) ? __('partner::lang.add_partner_admission_reason') : __('partner::lang.edit_partner_admission_reason');
?>

<div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">{{ $title }}</h4>
        </div>

        <div class="modal-body">
            {!! Form::open(['url' => $formUrl, 'method' => empty($partner_admission_reason) ? 'post' : 'put', 'id' => 'editPartnerAdmissionReasonForm', 'files' => true ]) !!}
            <div class="row">
                <div class="col-sm-12">
                    {!! Form::label('name', __('partner::lang.name') . ':') !!}
                    {!! Form::text('name', $partner_admission_reason?->name, ['class' => 'form-control', 'placeholder' => __('partner::lang.name'), 'required']) !!}
                </div>
            </div>
            {!! Form::close() !!}
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-save-partner-admission-reason">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
    </div>
</div>
