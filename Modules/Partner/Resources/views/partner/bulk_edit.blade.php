@extends('layouts.app')
@section('title', __('partner::lang.partner') . ' '. __('business.dashboard'))

@section('content')
<section class="content-header no-print">
    <h1>@lang('partner::lang.partner_bulk_edit')</h1>
</section>

<section class="content no-print">
    {!! Form::open([
        'url' => action([\Modules\Partner\Http\Controllers\PartnerController::class, 'bulkUpdate']), 
        'method' => 'post', 
        'id' => 'editPartnerForm', 
    ]) !!}

    <div class="row">
        <div class="col-md-12">
            <table class="table text-center table-bordered" id="partner_bulk_edit_form">
                <thead>
                    <tr class="bg-gray">
                        <th>@lang('partner::lang.partner')</th>
                        <th>@lang('partner::lang.collection_address')</th>
                        <th>@lang('partner::lang.entre')</th>
                        <th>@lang('partner::lang.telephone')</th>
                        <th>@lang('partner::lang.handphone')</th>
                        <th>@lang('partner::lang.radio')</th>
                        <th>@lang('partner::lang.zone')</th>
                        <th>@lang('partner::lang.route')</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($partners as $partner)
                    <tr class="bg-green">
                        <td>{{ $partner->display_name }}</td>
                        <td>
                            {!! Form::text('partners['.$partner->id.'][collection_address]', $partner->collection_address, ['class' => 'form-control text-uppercase']) !!}
                        </td>
                        <td>
                            {!! Form::text('partners['.$partner->id.'][entre]', $partner->entre, ['class' => 'form-control text-uppercase']) !!}
                        </td>
                        <td>
                            {!! Form::text('partners['.$partner->id.'][telephone]', $partner->telephone, ['class' => 'form-control']) !!}
                        </td>
                        <td>
                            {!! Form::text('partners['.$partner->id.'][handphone]', $partner->handphone, ['class' => 'form-control']) !!}
                        </td>
                        <td>
                            {!! Form::select('partners['.$partner->id.'][radio_id]', $radios, $partner->radio_id, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2 input-sm', 'style' => 'width: 100%;']) !!}
                        </td>
                        <td>
                            {!! Form::select('partners['.$partner->id.'][zone_id]', $zones, $partner->zone_id, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2 input-sm', 'style' => 'width: 100%;']) !!}
                        </td>
                        <td>
                            {!! Form::number('partners['.$partner->id.'][route_id]', $partner->route_id, ['class' => 'form-control text-center']) !!}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12 d-flex justify-content-end">
            <button class="btn btn-primary" type='submit'>@lang('messages.save')</button>
        </div>
    </div>

    {!! Form::close() !!}
</section>
@endsection

