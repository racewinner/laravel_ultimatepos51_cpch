@extends('layouts.app')
@section('title', __('partner::lang.history') . ' '. __('business.dashboard'))

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>@lang('partner::lang.history') ({{$partner->display_name}})</h1>
</section>

<section class="content no-print">
@component('components.widget', ['class' => 'box-primary', 'title' => __('partner::lang.history')])
<table class="table table-bordered table-striped ajax_view" id="partner_history_table" style="width: 100%;">
    <thead>
        <tr>
            <th>@lang('lang_v1.date')</th>
            <th>@lang('messages.action')</th>
            <th>@lang('partner::lang.data_change')</th>
            <th>@lang('messages.editor')</th>
            <th>@lang('messages.memo')</th>
        </tr>
    </thead>
</table>
@endcomponent
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    history_table = $('#partner_history_table').DataTable({
        processing: true,
        serverSide: true,
        scrollY:    "75vh",
        scrollX:        true,
        scrollCollapse: true,
        ajax: {
            url: '/partner/partner_histories/{{$partner->id}}'
        },
        columns: [
            { data: 'created_at', name: 'created_at', orderable: false, searchable: false },
            { data: 'action_type', name: 'action_type', orderable: false, searchable: false },
            { data: 'data_change', name: 'data_difference', orderable: false, searchable: false },
            { data: 'editor', name: 'editor', orderable: false, searchable: false },
            { data: 'memo', name: 'memo'},
        ]
    });
})
</script>
@endsection