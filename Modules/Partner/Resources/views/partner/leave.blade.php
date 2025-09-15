@extends('layouts.app')
@section('title', __('partner::lang.delete_partner') . ' ' . __('business.dashboard'))

<?php
$formUrl = action([\Modules\Partner\Http\Controllers\PartnerController::class, 'postLeave'], [$partner->id]);
$title = __('partner::lang.delete_partner');
?>

@section('content')
    <section class="content-header no-print">
        <h1>{{$title}}</h1>
    </section>

    <section class="content no-print">
        @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.partner_information') . '</h4>'])
        @include('partner::partner.partials.partner_maininfo', ['partner' => $partner])
        @endcomponent

        @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('lang_v1.payment') . '</h4>'])
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('last_month_payment', __('partner::lang.last_month_payment') . ':') !!}
                    <div class="d-flex">
                        {!! Form::text('last_pay_month', !empty($last_payment) ? $last_payment['month'] : '', ['class' => 'form-control disabled']) !!}
                        {!! Form::text('last_pay_amount', !empty($last_payment) ? \App\Utils\Util::format_currency($last_payment['amount'], $last_payment['currency']) : '', ['class' => 'form-control disabled']) !!}
                    </div>
                </div>
            </div>
        </div>
        @endcomponent

        @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.leave_information') . '</h4>'])
        {!! Form::open(['url' => $formUrl, 'method' => 'post', 'id' => 'leavePartnerForm', 'files' => true]) !!}
        <input type='hidden' name='pin_partner' id='pin_partner' />

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('leave_date', __('partner::lang.leave_date') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        {!! Form::text('leave_date', $partner->leave?->leave_date ?? date('m/d/Y'), ['class' => 'form-control date-picker', 'required']) !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('leave_type_id', __('partner::lang.partner_leave_type') . ':*') !!}
                    {!! Form::select('leave_type_id', $leave_types, $partner->leave?->leave_type_id, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'required']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('leave_reason_id', __('partner::lang.partner_leave_reason') . ':*') !!}
                    {!! Form::select('leave_reason_id', $leave_reasons, $partner->leave?->leave_reason_id, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'required']) !!}
                </div>
            </div>
        </div>
        <div class="row death-data {{ !empty($partner->leave->death_data) ? '' : 'hidden' }}">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('complaintant_id', __('partner::lang.complaintant_id') . ':*') !!}
                    {!! Form::text('death_data[complaintant_id]', $partner->leave->death_data->complaintant_id, ['class' => 'form-control text-uppercase', 'required']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('complaintant_name', __('partner::lang.complaintant_name') . ':*') !!}
                    {!! Form::text('death_data[complaintant_name]', $partner->leave?->death_data?->complaintant_name, ['class' => 'form-control text-uppercase', 'required']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('complaintant_contact', __('partner::lang.complaintant_contact') . ':') !!}
                    {!! Form::text('death_data[complaintant_contact]', $partner->leave?->death_data?->complaintant_contact, ['class' => 'form-control text-uppercase', 'placeholder' => __('partner::lang.email_or_phone')]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('death_date', __('partner::lang.death_date') . ':*') !!}
                    {!! Form::text('death_data[death_date]', $partner->leave?->death_data?->death_date, ['class' => 'form-control date-picker']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('elevate_date', __('partner::lang.elevate_date') . ':*') !!}
                    {!! Form::text('death_data[elevate_date]', $partner->leave?->death_data?->elevate_date, ['class' => 'form-control date-picker']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('death_cert_submitted', __('partner::lang.death_cert_submitted') . ':') !!}
                    {!! Form::select('death_data[death_cert_submitted]', ['1' => 'Yes', '0' => 'No'], $partner->leave?->death_data?->death_cert_submitted, ['class' => 'form-control', 'placeholder' => __('messages.please_select')]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('sign_policy', __('partner::lang.sign_policy') . ':') !!}
                    {!! Form::select('death_data[sign_policy]', ['1' => 'Yes', '0' => 'No'], $partner->leave?->death_data?->sign_policy, ['class' => 'form-control', 'placeholder' => __('messages.please_select')]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('beneficiarios', __('partner::lang.beneficiarios') . ':') !!}
                    {!! Form::text('death_data[beneficiarios]', $partner->leave?->death_data?->beneficiarios, ['class' => 'form-control text-uppercase']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('cause', __('partner::lang.death_cause') . ':') !!}
                    {!! Form::text('death_data[cause]', $partner->leave?->death_data?->cause, ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    {!! Form::label('memo', __('partner::lang.memo') . ':') !!}
                    {!! Form::textarea('memo', $partner->leave?->memo, ['class' => 'form-control', 'rows' => 4]) !!}
                </div>
            </div>
        </div>

        <div class="d-flex mt-4 justify-content-end">
            <button type="submit" class="btn btn-primary btn-leave-partner">@lang('messages.delete')</button>
        </div>

        {!! Form::close() !!}

        @endcomponent

        @include('partner::partner.partials.confirm_pin_modal')
        @include('partner::partner.partials.bulk_print')
    </section>
@endsection

@section('javascript')
    <script>
        var actionFlag = false;
        const partner_id = '{{ $partner->id }}'

        $(document).ready(function () {
            $('.date-picker').datetimepicker({
                format: moment_date_format,
                ignoreReadonly: true,
            });

            $(document).on('submit', 'form#leavePartnerForm', function (e) {
                e.preventDefault();

                $form = $("form#leavePartnerForm");

                // if leave_type is delay, should check pin.
                const pin = $("#pin_partner").val();
                const selected_option = $(e.target).find("option:selected");
                const option_text = selected_option[0].innerText.toLowerCase();
                if (option_text == "{{ strtolower(__('partner::lang.leave_type_delay')) }}" && !pin) {
                    $form.find('button[type="submit"]').attr('disabled', false);
                    $("#checkPinModal").modal("show");
                    actionFlag = true;
                    return false;
                }

                swal({
                    text: "@lang('messages.confirm_delete')",
                    icon: "warning",
                    buttons: {
                        cancel: "@lang('messages.no')",
                        confirm: "@lang('messages.yes')"
                    },
                    dangerMode: false,
                }).then((willDelete) => {
                    if (willDelete) {
                        const data = new FormData($form[0]);
                        $.ajax({
                            method: 'POST',
                            url: $form.attr('action'),
                            dataType: 'json',
                            data,
                            processData: false,
                            contentType: false,
                            beforeSend: function (xhr) {
                                __disable_submit_button($form.find('button[type="submit"]'));
                            },
                            success: function (result) {
                                console.log(result);
                                if (result.success == true) {
                                    toastrSwal(result.msg, 'success', function() {
                                        window.location.href = `/partner/partners?print_partner_id=${partner_id}`;
                                    });
                                } else {
                                    toastrSwal(result.msg, 'error');
                                }
                            },
                        })
                    } else {
                        $form.find('button[type="submit"]').attr('disabled', false);
                    }
                })

                return false;
            })

            $(document).on('change', '#leave_type_id', function (e) {
                $(".death-data").addClass("hidden");
                $(".death-data .form-control").prop('disabled', true);

                const selected_option = $(e.target).find("option:selected");
                if (selected_option) {
                    const option_text = selected_option[0].innerText.toLowerCase();
                    if (option_text == "{{ strtolower(__('partner::lang.leave_type_death')) }}") {
                        $(".death-data").removeClass("hidden");
                        $(".death-data .form-control").prop('disabled', false);
                    } else if (option_text == "{{ strtolower(__('partner::lang.leave_type_delay')) }}") {
                        $("#checkPinModal").modal("show");
                    }
                }
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
                            $("#leavePartnerForm input[name='pin_partner']").val(pin_partner);
                            $("#checkPinModal").modal("hide");
                            if (actionFlag) {
                                $("form#leavePartnerForm").find('button[type="submit"]').trigger('click');
                            }
                        } else {
                            toastrSwal(result.msg, 'error');
                        }
                    }
                });
            })
        })
    </script>
@endsection