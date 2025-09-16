@extends('layouts.app')
@section('title', __('partner::lang.partner') . ' ' . __('business.dashboard'))

<?php
if ($action == 'create') {
    $formUrl = action([\Modules\Partner\Http\Controllers\PartnerController::class, 'store']);
    $headerTitle = __('partner::lang.add_partner');
    $submit_button_label = __('lang_v1.create');
} else if ($action == 'edit') {
    $formUrl = action([\Modules\Partner\Http\Controllers\PartnerController::class, 'update'], [$partner?->id]);
    $headerTitle = __('partner::lang.edit_partner');
    $submit_button_label = __('messages.save');
} else if ($action == 'reEntry') {
    $formUrl = action([\Modules\Partner\Http\Controllers\PartnerController::class, 'update'], [$partner?->id]);
    $headerTitle = __('partner::lang.reEntry_partner');
    $submit_button_label = __('partner::lang.reEntry');
}
?>

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1>{{$headerTitle}}</h1>
    </section>

    <section class="content no-print">
        {!! Form::open([
        'url' => $formUrl,
        'method' => empty($partner) ? 'post' : 'put',
        'id' => 'editPartnerForm',
        'files' => true,
    ]) !!}
        <input type='hidden' name='pin_partner' />
        <input type='hidden' name='action' value='{{$action}}' />
        <input type='hidden' name="partner_id" value="{{ $partner->id ?? '' }}" />

        @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.partner_information') . '</h4>'])
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('surname', __('partner::lang.surname') . ':*') !!}
                    {!! Form::text('surname', $partner?->surname, ['class' => 'form-control text-uppercase', 'required']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('name', __('partner::lang.name') . ':*') !!}
                    {!! Form::text('name', $partner?->name, ['class' => 'form-control text-uppercase', 'required']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('id_card_number', __('partner::lang.idcard') . ':*') !!}
                    {!! Form::text('id_card_number', $partner?->id_card_number, ['class' => 'form-control text-uppercase ' . (!empty($partner) ? 'disabled' : ''), 'required']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('address', __('partner::lang.address') . ':') !!}
                    {!! Form::text('address', $partner?->address, ['class' => 'form-control text-uppercase']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('entre', __('partner::lang.entre') . ':') !!}
                    {!! Form::text('entre', $partner?->entre, ['class' => 'form-control text-uppercase']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('locality', __('partner::lang.locality') . ':') !!}
                    {!! Form::select('locality_id', $localities, $partner?->locality_id, ['class' => 'form-control', 'placeholder' => __('messages.please_select')]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('telephone', __('partner::lang.telephone') . ':') !!}
                    {!! Form::text('telephone', $partner?->telephone, ['class' => 'form-control text-uppercase']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('handphone', __('partner::lang.handphone') . ':') !!}
                    {!! Form::text('handphone', $partner?->handphone, ['class' => 'form-control text-uppercase']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <div class="d-flex align-items-center">
                        {!! Form::label('date_admission', __('partner::lang.date_admission') . ':*') !!}
                        @if($action == 'edit')
                            <button type="button" class="btn btn-primary ms-2 px-4 py-0 mb-1 btn-pin-verify" data-toggle='modal'
                                data-target='#checkPinModal' style="font-size: 80%;">Verify PIN</button>
                        @endif
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        {!! Form::text('date_admission', $partner?->date_admission, ['class' => 'form-control date-picker ' . ($action == 'edit' && auth()->user()->enable_pin_partner ? 'input-pin-verify disabled' : ''), 'required']) !!}
                        <span class="input-group-addon" id="admission-month">{{$partner?->admission_month ?? ''}}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('sign_policy', __('partner::lang.sign_policy') . ':') !!}
                    {!! Form::select('sign_policy', $sign_policies, $partner?->sign_policy, ['class' => 'form-control', 'id' => 'sign_policy', 'placeholder' => __('messages.please_select')]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('email', __('partner::lang.email') . ':') !!}
                    {!! Form::text('email', $partner?->email, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('marital_status_id', __('partner::lang.marital_status') . ':') !!}
                    {!! Form::select('marital_status_id', $marital_statuses, $partner?->marital_status_id, ['class' => 'form-control', 'id' => 'marital_status_id', 'placeholder' => __('messages.please_select')]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('dob', __('partner::lang.dob') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        {!! Form::text('dob', $partner?->dob, ['class' => 'form-control date-picker ']) !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('age', __('partner::lang.age') . ':') !!}
                    {!! Form::number('age', $partner?->age, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <div class="d-flex align-items-center">
                        {!! Form::label('partner_category_id', __('partner::lang.partner_category') . ':*') !!}
                        @if($action == 'edit')
                            <button type="button" class="btn btn-primary ms-2 px-4 py-0 mb-1 btn-pin-verify" data-toggle='modal'
                                data-target='#checkPinModal' style="font-size: 80%;">Verify PIN</button>
                        @endif
                    </div>
                    {!! Form::select(
                        'partner_category_id', 
                        $partner_categories, 
                        $partner?->partner_category_id, 
                        [
                            'class' => 'form-control ' . ($action == 'edit' && auth()->user()->enable_pin_partner ? 'input-pin-verify disabled' : ''), 
                            'required', 
                            'placeholder' => __('messages.please_select')
                        ]
                    ) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <div class="d-flex align-items-center">
                        {!! Form::label('cat_book_id', __('partner::lang.cat_book') . ':') !!}
                        @if($action == 'edit')
                            <button type="button" class="btn btn-primary ms-2 px-4 py-0 mb-1 btn-pin-verify" data-toggle='modal'
                                data-target='#checkPinModal' style="font-size: 80%;">Verify PIN</button>
                        @endif
                    </div>
                    {!! Form::select('cat_book_id', $book_categories, $partner?->cat_book_id, ['class' => 'form-control ' . ($action == 'edit' && auth()->user()->enable_pin_partner ? 'input-pin-verify disabled' : ''), 'placeholder' => __('messages.please_select')]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <div class="d-flex align-items-center">
                        {!! Form::label('book_no', __('partner::lang.book_no') . ':') !!}
                        @if($action == 'edit')
                            <button type="button" class="btn btn-primary ms-2 px-4 py-0 mb-1 btn-pin-verify" data-toggle='modal'
                                data-target='#checkPinModal' style="font-size: 80%;">Verify PIN</button>
                        @endif
                    </div>
                    {!! Form::text('book_no', $partner?->book_no, ['class' => 'form-control text-uppercase ' . ($action == 'edit' && auth()->user()->enable_pin_partner ? 'input-pin-verify disabled' : '')]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <div class="d-flex align-items-center">
                        {!! Form::label('date_expire_book', __('partner::lang.date_expire_book') . ':') !!}
                        @if($action == 'edit')
                            <button type="button" class="btn btn-primary ms-2 px-4 py-0 mb-1 btn-pin-verify" data-toggle='modal'
                                data-target='#checkPinModal' style="font-size: 80%;">Verify PIN</button>
                        @endif
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        {!! Form::text('date_expire_book', $partner?->date_expire_book, ['class' => 'form-control date-picker ' . ($action == 'edit' && auth()->user()->enable_pin_partner ? 'input-pin-verify disabled' : '')]) !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <div class="d-flex align-items-center">
                        {!! Form::label('issuance_place', __('partner::lang.issuance_place') . ':') !!}
                        @if($action == 'edit')
                            <button type="button" class="btn btn-primary ms-2 px-4 py-0 mb-1 btn-pin-verify" data-toggle='modal'
                                data-target='#checkPinModal' style="font-size: 80%;">Verify PIN</button>
                        @endif
                    </div>
                    {!! Form::text('issuance_place', $partner?->issuance_place, ['class' => 'form-control text-uppercase ' . ($action == 'edit' && auth()->user()->enable_pin_partner ? 'input-pin-verify disabled' : '')]) !!}
                </div>
            </div>
        </div>
        @endcomponent

        @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.register_information') . '</h4>'])
        <div class="row partner-register-data">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('admission_reason_id', __('partner::lang.admission_reason') . ':*') !!}
                    {!! Form::select('admission_reason_id', $admission_reasons, $partner?->admission_reason_id, ['class' => 'form-control ' . (!empty($partner) ? 'disabled' : ''), 'id' => 'admission_reason', 'required']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('collection_address', __('partner::lang.collection_address') . ':') !!}
                    {!! Form::text('collection_address', $partner?->collection_address, ['class' => 'form-control text-uppercase']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('collection_entre', __('partner::lang.collection_entre') . ':') !!}
                    {!! Form::text('collection_entre', $partner?->collection_entre, ['class' => 'form-control text-uppercase']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('collection_telephone', __('partner::lang.collection_telephone') . ':') !!}
                    {!! Form::text('collection_telephone', $partner?->collection_telephone, ['class' => 'form-control text-uppercase']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('collection_handphone', __('partner::lang.collection_handphone') . ':') !!}
                    {!! Form::text('collection_handphone', $partner?->collection_handphone, ['class' => 'form-control text-uppercase']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('permanent_observation', __('partner::lang.permanent_observation') . ':') !!}
                    {!! Form::text('permanent_observation', $partner?->permanent_observation, ['class' => 'form-control text-uppercase ']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <div class="d-flex align-items-center">
                        {!! Form::label('accepted_at', __('partner::lang.accepted_at') . ':') !!}
                        @if($action == 'edit')
                            <button type="button" class="btn btn-primary ms-2 px-4 py-0 mb-1 btn-pin-verify" data-toggle='modal'
                                data-target='#checkPinModal' style="font-size: 80%;">Verify PIN</button>
                        @endif
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        {!! Form::text('accepted_at', $partner?->accepted_at, ['class' => 'form-control date-picker ' . ($action == 'edit' && auth()->user()->enable_pin_partner ? 'input-pin-verify disabled' : '')]) !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <div class="d-flex align-items-center">
                        {!! Form::label('application_submission_date', __('partner::lang.application_submission_date') . ':') !!}
                        @if($action == 'edit')
                            <button type="button" class="btn btn-primary ms-2 px-4 py-0 mb-1 btn-pin-verify" data-toggle='modal'
                                data-target='#checkPinModal' style="font-size: 80%;">Verify PIN</button>
                        @endif
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        {!! Form::text('application_submission_date', $partner?->application_submission_date, ['class' => 'form-control date-picker ' . ($action == 'edit' && auth()->user()->enable_pin_partner ? 'input-pin-verify disabled' : '')]) !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <div class="d-flex align-items-center">
                        {!! Form::label('submit_partner', __('partner::lang.submit_partner') . ':') !!}
                        @if($action == 'edit')
                            <button type="button" class="btn btn-primary ms-2 px-4 py-0 mb-1 btn-pin-verify" data-toggle='modal'
                                data-target='#checkPinModal' style="font-size: 80%;">Verify PIN</button>
                        @endif
                    </div>
                    {!! Form::text('submit_partner', $partner?->submit_partner, ['class' => 'form-control text-uppercase ' . ($action == 'edit' && auth()->user()->enable_pin_partner ? 'input-pin-verify disabled' : '')]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <div class="d-flex align-items-center">
                        {!! Form::label('submit_partner_category_id', __('partner::lang.submit_partner_category') . ':') !!}
                        @if($action == 'edit')
                            <button type="button" class="btn btn-primary ms-2 px-4 py-0 mb-1 btn-pin-verify" data-toggle='modal'
                                data-target='#checkPinModal' style="font-size: 80%;">Verify PIN</button>
                        @endif
                    </div>
                    {!! Form::select('submit_partner_category_id', $partner_categories, $partner?->submit_partner_category_id, ['class' => 'form-control ' . ($action == 'edit' && auth()->user()->enable_pin_partner ? 'input-pin-verify disabled' : '')]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <div class="d-flex align-items-center">
                        {!! Form::label('radio_id', __('partner::lang.radio') . ':') !!}
                        @if($action == 'edit')
                            <button type="button" class="btn btn-primary ms-2 px-4 py-0 mb-1 btn-pin-verify" data-toggle='modal'
                                data-target='#checkPinModal' style="font-size: 80%;">Verify PIN</button>
                        @endif
                    </div>
                    {!! Form::select('radio_id', $radios, $partner?->radio_id, ['class' => 'form-control ' . ($action == 'edit' && auth()->user()->enable_pin_partner ? 'input-pin-verify disabled' : 'disabled'), 'placeholder' => __('messages.please_select')]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <div class="d-flex align-items-center">
                        {!! Form::label('zone_id', __('partner::lang.zone') . ':') !!}
                        @if($action == 'edit')
                            <button type="button" class="btn btn-primary ms-2 px-4 py-0 mb-1 btn-pin-verify" data-toggle='modal'
                                data-target='#checkPinModal' style="font-size: 80%;">Verify PIN</button>
                        @endif
                    </div>
                    {!! Form::select('zone_id', $zones, $partner?->zone_id, ['class' => 'form-control ' . ($action == 'edit' && auth()->user()->enable_pin_partner ? 'input-pin-verify disabled' : 'disabled'), 'placeholder' => __('messages.please_select')]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <div class="d-flex align-items-center">
                        {!! Form::label('route_id', __('partner::lang.route') . ':') !!}
                        @if($action == 'edit')
                            <button type="button" class="btn btn-primary ms-2 px-4 py-0 mb-1 btn-pin-verify" data-toggle='modal'
                                data-target='#checkPinModal' style="font-size: 80%;">Verify PIN</button>
                        @endif
                    </div>
                    {!! Form::number('route_id', $partner?->route_id, ['class' => 'form-control ' . ($action == 'edit' && auth()->user()->enable_pin_partner ? 'input-pin-verify disabled' : 'disabled')]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('referrer_name', __('partner::lang.referrer_name') . ':') !!}
                    {!! Form::text('referrer_name', empty($partner) ? $user->display_name : $partner->referrer_name, ['class' => 'form-control text-uppercase', 'placeholder' => __('partner::lang.referrer_name'), 'readonly']) !!}
                </div>
            </div>
        </div>
        @endcomponent

        @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.additional_fee_services') . '</h4>'])
        <div class="row additional_fee_services">
            @if(!empty($partner))
                <div class="col-sm-12 mb-4">
                    <button type="button" class="btn btn-primary px-4 py-0 mb-1 btn-pin-verify" data-toggle='modal'
                                data-target='#checkPinModal' style="font-size: 80%;">Verify PIN</button>
                </div>
                @foreach ($partner->not_fee_services as $service)
                <div class="col-sm-3 mb-4 {{ ($action == 'edit' && auth()->user()->enable_pin_partner) ? 'input-pin-verify disabled' : '' }}">
                    <div class="checkbox">
                        <label class='p-0'>
                            {!! Form::checkbox(
                                'additional_fee_services[]', 
                                $service->id, 
                                in_array($service->id, $partner?->additional_fee_service_id_array ?? []), 
                                ['class' => 'input-icheck m-0']
                            ) !!}
                            {{$service->name}}
                        </label>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
        @endcomponent

        @if($action == 'reEntry')
            @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.leave_information') . '</h4>'])
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('leave_date', __('partner::lang.leave_date') . ':') !!}
                        {!! Form::text('leave_date', $partner->leave?->leave_date, ['class' => 'form-control text-uppercase', 'disabled']) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('leave_type', __('partner::lang.partner_leave_type') . ':') !!}
                        {!! Form::text('leave_type', $partner->leave?->leave_type->name, ['class' => 'form-control text-uppercase', 'disabled']) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('leave_reason', __('partner::lang.partner_leave_reason') . ':') !!}
                        {!! Form::text('leave_reason', $partner->leave?->leave_reason->name, ['class' => 'form-control text-uppercase', 'disabled']) !!}
                    </div>
                </div>
            </div>

            @if(!empty($partner->leave->death_data))
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('complaintant_id', __('partner::lang.complaintant_id') . ':') !!}
                            {!! Form::text('complaintant_id', $partner->leave->death_data->complaintant_id, ['class' => 'form-control text-uppercase', 'disabled']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('complaintant_name', __('partner::lang.complaintant_name') . ':') !!}
                            {!! Form::text('complaintant_name', $partner->leave?->death_data?->complaintant_name, ['class' => 'form-control text-uppercase', 'disabled']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('complaintant_contact', __('partner::lang.complaintant_contact') . ':') !!}
                            {!! Form::text('complaintant_contact', $partner->leave?->death_data?->complaintant_contact, ['class' => 'form-control text-uppercase', 'disabled']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('death_date', __('partner::lang.death_date') . ':') !!}
                            {!! Form::text('death_date', $partner->leave?->death_data?->complaintant_contact, ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('elevate_date', __('partner::lang.elevate_date') . ':') !!}
                            {!! Form::text('elevate_date', $partner->leave?->death_data?->elevate_date, ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('death_cert_submitted', __('partner::lang.death_cert_submitted') . ':') !!}
                            {!! Form::text('death_cert_submitted', $partner->leave?->death_data?->death_cert_submitted ? 'Yes' : 'No', ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('sign_policy', __('partner::lang.sign_policy') . ':') !!}
                            {!! Form::text('sign_policy', $partner->leave?->death_data?->sign_policy ? 'Yes' : 'No', ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('beneficiarios', __('partner::lang.beneficiarios') . ':') !!}
                            {!! Form::text('beneficiarios', $partner->leave?->death_data?->beneficiarios, ['class' => 'form-control text-uppercase', 'disabled']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('cause', __('partner::lang.death_cause') . ':') !!}
                            {!! Form::text('cause', $partner->leave?->death_data?->cause, ['class' => 'form-control text-uppercase', 'disabled']) !!}
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    {!! Form::label('memo', __('partner::lang.memo') . ':') !!}
                    {!! Form::textarea('memo', $partner->leave?->memo, ['class' => 'form-control', 'rows' => 4, 'disabled']) !!}
                </div>
            </div>
            @endcomponent

            @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.reEntry') . '</h4>'])
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('return_reason_id', __('partner::lang.partner_return_reason') . ':') !!}
                        {!! Form::select('return_reason_id', $return_reasons, '', ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'required']) !!}
                    </div>
                </div>
            </div>
            @include('partner::partner.partials.debt')
            @endcomponent
        @endif

        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-primary me-4" id="button_save">{{ $submit_button_label }}</button>
        </div>

        {!! Form::close() !!}

        <div class="modal fade issue-receipt-modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

        <div class="modal fade unsettled-receipts-modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

        @include('partner::partner.partials.confirm_pin_modal')
        @include('partner::partner.partials.bulk_print')

    </section>
@endsection

@section('javascript')
    <script src="{{ asset('js/partner/partner_receipt.js?v=' . $asset_v) }}"></script>

    <script>
        let partner_id = "{{ !empty($partner) ? $partner->id : '' }}"

    @if($action == 'reEntry')
        const debt_months = {{ $debt['months'] ?? 0 }};
        const monthly_fee = {{ $debt['monthly_fee'] ?? 0 }};

        function getReEntryReason() {
            const selected_option = $("select#return_reason_id").find("option:selected");
            if (selected_option) {
                const option_text = selected_option[0].innerText;
                if (option_text == "@lang('partner::lang.extension')") {
                    return 'extension';
                } else if (option_text == "@lang('partner::lang.third_article')") {
                    return 'third_article';
                } else if (option_text == "@lang('partner::lang.common_reEntry')") {
                    return 'common_reEntry';
                } else if (option_text == "@lang('partner::lang.amnesty')") {
                    return 'amnesty';
                }
            }
        }

        function onReEntryBtnClicked() {
            const form = $("form#editPartnerForm")[0];
            const reEntryReason = getReEntryReason();
            if (reEntryReason == 'extension') {
                if(debt_months > 12) {
                    toastrSwal("@lang('partner::messages.must_reEnter_due_to_third_article')", 'error');
                    $(form).find("button[type='submit']").prop('disabled', false);
                } else {
                    showUnsettledReceipts(partner_id);
                    $(form).find("button[type='submit']").prop('disabled', false);
                }
                
            } else if (reEntryReason == "third_article") {
                if(debt_months <= 12) {
                    toastrSwal("@lang('partner::messages.must_reEnter_due_to_extension')", 'error');
                    $(form).find("button[type='submit']").prop('disabled', false);
                } else {
                    showUnsettledReceipts(partner_id);
                    $(form).find("button[type='submit']").prop('disabled', false);
                }
            } else if (reEntryReason == "common_reEntry") {
                swal({
                    text: "@lang('partner::messages.confirm_common_reEntry')",
                    icon: "warning",
                    buttons: {
                        cancel: "@lang('messages.no')",
                        confirm: "@lang('messages.yes')"
                    },
                    dangerMode: false,
                }).then((yes) => {
                    if(yes) {
                        $.ajax({
                            method: 'DELETE',
                            url: `/partner/receipts/unpaid/${partner_id}`,
                            success: function(result) {
                                if(result.success) {
                                    handleSave();
                                }
                            }
                        })
                    } else {
                        $(form).find("button[type='submit']").prop('disabled', false);
                    }
                })
            } else if (reEntryReason == "amnesty") {
                swal({
                    text: "@lang('partner::messages.confirm_amnesty_reEntry')",
                    icon: "warning",
                    buttons: {
                        cancel: "@lang('messages.no')",
                        confirm: "@lang('messages.yes')"
                    },
                    dangerMode: false,
                }).then((yes) => {
                    if(yes) {
                        const now = new Date();
                        const mY = `${String(now.getMonth() + 1).padStart(2, '0')}/${now.getFullYear()}`;
                        const issue_months = `${mY}-${mY}`;
                        $.ajax({
                            method: 'POST',
                            url: `/partner/receipts/issue`,
                            data: {
                                partner_id,
                                ignore_leave: 1,
                                issue_months,
                                paid: 0,
                            },
                            success: function(result) {
                                if(result.success) {
                                    handleSave();
                                }
                            }
                        })
                    } else {
                        $(form).find("button[type='submit']").prop('disabled', false);
                    }
                })
            }
        }
    @endif

    @if($action == 'create')
        function afterCreate(result) {
            partner_id = result.partner_id;
            $("input[name='partner_id']").val(partner_id);

            swal({
                text: "@lang('partner::messages.confirm_create_first_payment')",
                icon: "warning",
                buttons: {
                    cancel: "@lang('messages.no')",
                    confirm: "@lang('messages.yes')"
                },
                dangerMode: false,
            }).then((willPay) => {
              // debugger
                if(!willPay) {
                    $.ajax({
                        method: 'POST',
                        url: '/partner/receipts/issue',
                        data: {
                            partner_id,
                            issue_months: `${result.issue_months.start_month}-${result.issue_months.end_month}`,
                            paid: 0,
                        },
                        dataType: 'json',
                        success: function(result) {
                            if(result.newly_issued_count > 0) {
                                toastrSwal(
                                    "{{ __('partner::messages.receipt_issued_success') }}", 
                                    'success',
                                    function() {
                                        /**
                                         * this line is inactivated by viktor.cancel
                                         * on Sep. 15th 2025
                                         * because of problem 28 
                                         */
                                        // printReceipts(result.new_receipt_ref_nos, 0)

                                        setTimeout(() => {
                                            window.location.href = `/partner/partners?print_partner_id=${partner_id}`;
                                        }, 1000);
                                    }
                                );
                            } else {
                                toastrSwal(
                                    result.msg[0].message, 
                                    result.msg[0].type,
                                    function () {
                                        window.location.href = `/partner/partners?print_partner_id=${partner_id}`;
                                    }
                                );
                            }
                        }
                    })
                } else {
                    showIssueReceiptModal(partner_id);
                }
            })
        }
    @endif

        function handleSave() {
            const form = $("form#editPartnerForm")[0];
            const data = new FormData(form);
            var url = $(form).attr('action');
            $.ajax({
                method: 'POST',
                url,
                data,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function (result) {
                    if (result.success == 1) {
                        partner_id = result.partner_id;

                        toastrSwal(result.msg, 'success', function() {
                            @if($action == 'create')
                                afterCreate(result);
                            @elseif($action == 'reEntry')
                                $(".unsettled-receipts-modal").modal("hide");
                                window.location.href = `/partner/partners?print_partner_id=${partner_id}`;
                            @else
                                window.location.href = `/partner/partners?print_partner_id=${partner_id}`;
                            @endif
                        })
                    } else {
                        $('#button_save').prop('disabled', false);
                        toastrSwal(result.msg, 'error');
                    }
                }
            });
        }

        $(document).ready(function () {
            $('.date-picker').datetimepicker({
                format: moment_date_format,
                ignoreReadonly: true,
            });

            $(document).on('dp.change', 'input#date_admission', function (e) {
                if (e.date) {
                    const monthName = getMonthName(e.date.month());
                    $("#admission-month").text(monthName);
                }
            })

            $(document).on('submit', 'form#editPartnerForm', function (e) {
                e.preventDefault();
                e.stopPropagation();

                const form = e.target;

                @if($action == 'reEntry')
                    onReEntryBtnClicked();
                @else
                    handleSave();
                @endif

                return false;
            })

            $(document).on('click', '#btn_check_pin', function (e) {
                const pin_partner = $("#checkPinModal input[name='pin_partner']").val();
                if (!pin_partner) return;

                $.ajax({
                    method: 'POST',
                    url: '/users/check_pin_partner',
                    dataType: 'json',
                    data: { pin_partner },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                    },
                    success: function (result) {
                        if (result.success == 1) {
                            $(".btn-pin-verify").remove();
                            $(".input-pin-verify").removeClass('disabled');
                            $("#checkPinModal").modal("hide");
                            $("#editPartnerForm input[name='pin_partner']").val(pin_partner);
                        } else {
                            toastrSwal(result.msg, 'error');
                        }
                    }
                });
            })

            $(document).on('change', 'input[name="address"]', function (e) {
                const $collection_address = $("input[name='collection_address']");
                if($collection_address.val() == '' && e.target.value) {
                    $collection_address.val(e.target.value);
                }
            });

            $(document).on('change', 'input[name="entre"]', function (e) {
                const $collection_entre = $("input[name='collection_entre']");
                if($collection_entre.val() == '' && e.target.value) {
                    $collection_entre.val(e.target.value)
                }
            })

            $(document).on('change', 'select#partner_category_id', function(e) {
                $.ajax({
                    method: 'GET',
                    url: `/partner/partner_categories/${e.target.value}/services`,
                    success: function(result) {
                        const $additional_fee_services = $(".additional_fee_services");
                        $additional_fee_services.html('');

                        if(result.success == 1) {
                            result.services.forEach(function(service, services) {
                              if (service && service.name && !(service.name.toLowerCase().includes("cuota"))) {
                                const one_service_div = $("<div class='col-sm-3 mb-4'>");
                                let html = "<div class='checkbox'><label class='p-0'>";
                                html += `<input type='checkbox' class='input-icheck m-0' name='additional_fee_services[]' value='${service.id}' />`;
                                html += `${service.name}`;
                                html += `</div>`;
                                $(one_service_div).html(html);

                                $additional_fee_services.append(one_service_div);
                              }
                            })

                            $additional_fee_services.find('input.input-icheck').iCheck({
                                checkboxClass: 'icheckbox_square-blue',
                            });
                        }
                    }
                })
            })

        @if($action == 'create')
            $(document).on('hidden.bs.modal', '.issue-receipt-modal', function (e) {
                setTimeout(function() {
                    window.location.href = `/partner/partners?print_partner_id=${partner_id}`;
                }, 1000);
            })
        @endif

        @if($action == 'reEntry')
            $(document).on('change', '#return_reason_id', function (e) {
                if (debt_months > 0 && monthly_fee > 0) {
                    const currency = @json($debt['currency']);
                    let debt_last_month_amount = 0;

                    const reEntryReason = getReEntryReason();
                    if (reEntryReason == "extension") {
                        debt_last_month_amount = debt_months * monthly_fee;
                    } else if (reEntryReason == "third_article") {
                        debt_last_month_amount = debt_months * monthly_fee;
                    }

                    $('#debt_last_month_amount').val(__currency_trans_from(debt_last_month_amount, true, currency));
                }
            })

            $(document).on('click', '.unsettled-receipts-modal #btn_reEntry', function (e) {
                e.preventDefault();
                e.stopPropagation();

                handleSave();
            })
        @endif
        })
    </script>
@endsection