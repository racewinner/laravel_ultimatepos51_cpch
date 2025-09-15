@extends('layouts.app')
@section('title', __('partner::lang.partner_category') . ' '. __('business.dashboard'))

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1>@lang('partner::lang.partner_category')</h1>
    </section>

    <!-- Main content -->
    <section class="content no-print">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('partner::lang.list_partner_categories')])
            @slot('tool')
                <div class="box-tools">
                    <a class="btn btn-block btn-primary" href="{{action([\Modules\Partner\Http\Controllers\PartnerCategoryController::class, 'create'])}}">
                        <i class="fa fa-plus"></i> @lang('messages.add')</a>
                    </a>
                </div>
            @endslot

            <table class="table table-bordered table-striped ajax_view" id="partner_category_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>@lang('partner::lang.detail')</th>
                        <th>@lang('partner::lang.impression')</th>
                        <th>@lang('partner::lang.vote')</th>
                        <th>@lang('partner::lang.assembly')</th>
                        <th>@lang('partner::lang.reserve')</th>
                        <th>@lang('partner::lang.sport')</th>
                        <th>@lang('partner::lang.other')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>

            <div class="modal fade partner_category_modal" tabindex="-1" role="dialog" 
                aria-labelledby="gridSystemModalLabel">
            </div>
        @endcomponent
    </section>
@endsection    

@section('javascript')
<script>
$(document).ready(function() {
    partner_category_table = $('#partner_category_table').DataTable({
        processing: true,
        serverSide: true,
        scrollY:    "75vh",
        scrollX:        true,
        scrollCollapse: true,
        ajax: {
            url: '/partner/partner_categories',
            // data: function(d) {
            //     d = __datatable_ajax_callback(d);
            // },
        },
        columns: [
            { data: 'detail', name: 'detail' },
            { data: 'impression', name: 'impression' },
            { data: 'vote', name: 'vote' },
            { data: 'assembly', name: 'assembly' },
            { data: 'reserve', name: 'reserve' },
            { data: 'sport', name: 'sport' },
            { data: 'other', name: 'other' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
    })

    $(document).on('click', 'a.delete-partner-category', function(e) {
        e.preventDefault();

        if(window.confirm("Are you sure to delete?") == true) {
            const url = $(e.target).closest("a").attr("href");
            $.ajax({
                method: 'DELETE',
                url,
                dataType: 'json',
                success: function(result) {
                    if(result.success == true) {
                        toastrSwal(result.msg);
                        partner_category_table.ajax.reload();    
                    } else {
                        toastrSwal(result.msg, 'error');
                    }
                }
            })
        }
    })
})
</script>
@endsection