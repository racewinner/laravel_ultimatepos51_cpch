@extends('layouts.app')
@section('title', __('partner::lang.service') . ' '. __('business.dashboard'))

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1>@lang('partner::lang.service')
            <small></small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content no-print">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('partner::lang.list_services')])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal" 
                        data-href="{{action([\Modules\Partner\Http\Controllers\ServiceController::class, 'create'])}}"
                        data-container=".service_modal"
                    >
                        <i class="fa fa-plus"></i> @lang('messages.add')</a>
                    </button>
                </div>
            @endslot

            <table class="table table-bordered table-striped ajax_view" id="service_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>@lang('partner::lang.name')</th>
                        <th>@lang('purchase.unit_cost')</th>
                        <th>@lang('business.currency')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>

            <div class="modal fade service_modal" tabindex="-1" role="dialog" 
                aria-labelledby="gridSystemModalLabel">
            </div>
        @endcomponent
    </section>
@endsection    

@section('javascript')
<script>
$(document).ready(function() {
    service_table = $('#service_table').DataTable({
        processing: true,
        serverSide: true,
        scrollY:    "75vh",
        scrollX:        true,
        scrollCollapse: true,
        ajax: {
            url: '/partner/services',
        },
        columns: [
            { data: 'name', name: 'name' },
            { data: 'unit_cost', name: 'unit_cost', searchable: false},
            { data: 'currency', name: 'currency_id', searchable: false},
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
    })

    $(document).on('click', 'a.delete-service', function(e) {
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
                        service_table.ajax.reload();    
                    } else {
                        toastrSwal(result.msg, 'error');
                    }
                }
            })
        }
    })

    $(document).on('submit', 'form#editServiceForm', function(e) {
        e.preventDefault();
        $form = $("form#editServiceForm");
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
                    $(".service_modal").modal("hide");
                    toastrSwal(result.msg);

                    service_table.ajax.reload();         
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