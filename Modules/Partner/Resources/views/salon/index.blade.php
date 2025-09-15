@extends('layouts.app')
@section('title', __('partner::lang.salon') . ' '. __('business.dashboard'))

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1>@lang('partner::lang.salon')
            <small></small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content no-print">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('partner::lang.list_salons')])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal" 
                        data-href="{{action([\Modules\Partner\Http\Controllers\SalonController::class, 'create'])}}"
                        data-container=".salon-modal"
                    >
                        <i class="fa fa-plus"></i> @lang('messages.add')</a>
                    </button>
                </div>
            @endslot

            <table class="table table-bordered table-striped ajax_view" id="salon_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>@lang('partner::lang.name')</th>
                        <th>@lang('partner::lang.open')</th>
                        <th>@lang('partner::lang.people_number')</th>
                        <th>@lang('partner::lang.daytime')</th>
                        <th>@lang('partner::lang.nighttime')</th>
                        <th>@lang('partner::lang.price_for_partner')</th>
                        <th>@lang('partner::lang.price_for_no_partner')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>

            <div class="modal fade salon-modal" tabindex="-1" role="dialog" 
                aria-labelledby="gridSystemModalLabel">
            </div>
        @endcomponent
    </section>
@endsection    

@section('javascript')
<script>
$(document).ready(function() {
    salon_table = $('#salon_table').DataTable({
        processing: true,
        serverSide: true,
        scrollY:    "75vh",
        scrollX:        true,
        scrollCollapse: true,
        ajax: {
            url: '/partner/salons',
            // data: function(d) {
            //     d = __datatable_ajax_callback(d);
            // },
        },
        columns: [
            { data: 'name', name: 'name' },
            { data: 'open', name: 'open', orderable: false, searchable: false },
            { data: 'people_number', name: 'people_number', orderable: false, searchable: false },
            { data: 'daytime', name: 'daytime', orderable: false, searchable: false },
            { data: 'nighttime', name: 'nighttime', orderable: false, searchable: false },
            { data: 'price_for_partner', name: 'price_for_partner', searchable: false },
            { data: 'price_for_no_partner', name: 'price_for_no_partner', searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
    })

    $(document).on('shown.bs.modal', '.salon-modal', function(e) {
        $('input.input-icheck').iCheck({
            checkboxClass: 'icheckbox_square-blue',
        });

        $('.timepicker').timepicker({
            hourStart: 0,
            hourEnd: 11,
            minuteStep: 15,
            onChange: function(info){
                console.log('Time changed:', info.value);
            }
        });
    })

    $(document).on('click', 'a.delete-salon', function(e) {
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
                        salon_table.ajax.reload();    
                    } else {
                        toastrSwal(result.msg, 'error');
                    }
                }
            })
        }
    })

    $(document).on('submit', 'form#editSalonForm', function(e) {
        e.preventDefault();
        $form = $("form#editSalonForm");
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
                    $(".salon-modal").modal("hide");
                    toastrSwal(result.msg);

                    salon_table.ajax.reload();         
                } else {
                    toastrSwal(result.msg, 'error');
                }
            },
        })
    })
})
</script>
@endsection