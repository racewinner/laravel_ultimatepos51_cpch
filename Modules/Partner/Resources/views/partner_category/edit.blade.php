@extends('layouts.app')
@section('title', __('partner::lang.partner_category') . ' '. __('business.dashboard'))

<?php
$formUrl = empty($partner_category) ? 
    action([\Modules\Partner\Http\Controllers\PartnerCategoryController::class, 'store']) :
    action([\Modules\Partner\Http\Controllers\PartnerCategoryController::class, 'update'], [$partner_category?->id]);

$title = empty($partner_category) ? __('partner::lang.add_partner_category') : __('partner::lang.edit_partner_category');
?>

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1>{{$title}}</h1>
    </section>

    <section class="content no-print">
    {!! Form::open(['url' => $formUrl, 'method' => empty($partner_category) ? 'post' : 'put', 'id' => 'editPartnerCategoryForm', 'files' => true ]) !!}
        <input type='hidden' name='service_ids' value='{{$partner_category?->service_ids}}' />
        @component('components.widget', ['class' => 'box-primary'])
        <div class="row">
            <div class="col-sm-12">
                {!! Form::label('detail', __('partner::lang.detail') . ':*') !!}
                {!! Form::text('detail', $partner_category?->detail, ['class' => 'form-control', 'placeholder' => __('partner::lang.detail'), 'required']) !!}
            </div>
        </div>

        <div class="row">
            <div class="col-sm-2 mt-4">
                <div class="checkbox"><label class='p-0'>
                    {!! Form::checkbox('impression', 1, $partner_category?->impression, ['class' => 'input-icheck m-0']) !!}
                    @lang('partner::lang.impression')
                </label></div>
            </div>
            <div class="col-sm-2 mt-4">
                <div class="checkbox"><label class='p-0'>
                    {!! Form::checkbox('vote', 1, $partner_category?->vote, ['class' => 'input-icheck m-0']) !!}
                    @lang('partner::lang.vote')
                </label></div>
            </div>
            <div class="col-sm-2 mt-4">
                <div class="checkbox"><label class='p-0'>
                    {!! Form::checkbox('assembly', 1, $partner_category?->assembly, ['class' => 'input-icheck m-0']) !!}
                    @lang('partner::lang.assembly')
                </div>
            </div>
            <div class="col-sm-2 mt-4">
                <div class="checkbox"><label class='p-0'>
                    {!! Form::checkbox('reserve', 1, $partner_category?->reserve, ['class' => 'input-icheck m-0']) !!}
                    @lang('partner::lang.reserve')
                </div>
            </div>
            <div class="col-sm-2 mt-4">
                <div class="checkbox"><label class='p-0'>
                    {!! Form::checkbox('sport', 1, $partner_category?->sport, ['class' => 'input-icheck m-0']) !!}
                    @lang('partner::lang.sport')
                </div>
            </div>
            <div class="col-sm-2 mt-4">
                <div class="checkbox"><label class='p-0'>
                    {!! Form::checkbox('other', 1, $partner_category?->other, ['class' => 'input-icheck m-0']) !!}
                    @lang('partner::lang.other')
                </div>
            </div>
        </div>
        @endcomponent

        @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.services' ) . '</h4>'])
        <div class="row">
        @foreach($services as $service)
            <div class="col-sm-3 mb-4">
                <div class="checkbox"><label class='p-0'>
                    {!! Form::checkbox('service', $service->id, in_array($service->id, $partner_category?->services ?? []), ['class' => 'input-icheck m-0']) !!}
                    {{$service->name}}
                </div>
            </div>
        @endforeach
        </div>
        @endcomponent        

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary" id="btn-save-partner-category">@lang( 'messages.save' )</button>
        </div>
    {!! Form::close() !!}
    </section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    $(document).on('submit', 'form#editPartnerCategoryForm', function(e) {
        e.preventDefault();

        // services
        let service_ids = '';
        const chkboxes = $("input[type='checkbox'][name='service']:checked");
        for(let i=0; i<chkboxes.length; i++) {
            if(i > 0) service_ids += ",";
            service_ids += chkboxes[i].value;
        }
        if(!service_ids) {
            toastrSwal("@lang('partner::messages.services_cannot_be_empty')", 'error');
            $("#btn-save-partner-category").prop('disabled', false);
            return false;
        }

        $("input[name='service_ids']").val(service_ids);

        $form = $("form#editPartnerCategoryForm");
        var data = $form.serialize();

        $.ajax({
            method: 'POST',
            url: $form.attr('action'),
            dataType: 'json',
            data,
            beforeSend: function(xhr) {
                __disable_submit_button($form.find('button[type="submit"]'));
            },
            success: function(result) {
                console.log(result);
                if(result.success == true) {
                    toastrSwal(result.msg, 'success', function() {
                        window.location.href = '/partner/partner_categories';
                    });
                } else {
                    toastrSwal(result.msg, 'error');
                }
            },
        })

        return false;
    })
})
</script>
@endsection