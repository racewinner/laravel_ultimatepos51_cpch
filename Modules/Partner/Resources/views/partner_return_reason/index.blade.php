@extends('layouts.app')
@section('title', __('partner::lang.partner_return_reason') . ' '. __('business.dashboard'))

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1>@lang('partner::lang.partner_return_reason')
            <small></small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content no-print">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('partner::lang.list_partner_return_reasons')])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal" 
                        data-href="{{action([\Modules\Partner\Http\Controllers\PartnerReturnReasonController::class, 'create'])}}"
                        data-container=".partner-return-reason-modal"
                    >
                        <i class="fa fa-plus"></i> @lang('messages.add')</a>
                    </button>
                </div>
            @endslot

            <table class="table table-bordered table-striped ajax_view" id="partner_return_reason_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>@lang('partner::lang.name')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>

            <div class="modal fade partner-return-reason-modal" tabindex="-1" role="dialog" 
                aria-labelledby="gridSystemModalLabel">
            </div>
        @endcomponent
    </section>
@endsection    

@section('javascript')
<script>
$(document).ready(function() {
    partner_return_reason_table = $('#partner_return_reason_table').DataTable({
        processing: true,
        serverSide: true,
        scrollY:    "75vh",
        scrollX:        true,
        scrollCollapse: true,
        ajax: {
            url: '/partner/partner_return_reasons',
            // data: function(d) {
            //     d = __datatable_ajax_callback(d);
            // },
        },
        columns: [
            { data: 'name', name: 'name' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
    })

    $(document).on('click', 'a.delete-partner-return-reason', function(e) {
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
                        partner_return_reason_table.ajax.reload();    
                    } else {
                        toastrSwal(result.msg, 'error');
                    }
                }
            })
        }
    })

    $(document).on('click', 'button.btn-save-partner-return-reason', function(e) {
        e.preventDefault();
        $form = $("form#editPartnerReturnReasonForm");
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
                    $(".partner-return-reason-modal").modal("hide");
                    toastrSwal(result.msg);

                    partner_return_reason_table.ajax.reload();         
                } else {
                    toastrSwal(result.msg, 'error');
                }
            },
        })
    })
})
</script>
@endsection