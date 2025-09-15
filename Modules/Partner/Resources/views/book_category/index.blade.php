@extends('layouts.app')
@section('title', __('partner::lang.book_category') . ' '. __('business.dashboard'))

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1>@lang('partner::lang.book_category')
            <small></small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content no-print">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('partner::lang.list_book_categories')])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal" 
                        data-href="{{action([\Modules\Partner\Http\Controllers\BookCategoryController::class, 'create'])}}"
                        data-container=".book_category_modal"
                    >
                        <i class="fa fa-plus"></i> @lang('messages.add')</a>
                    </button>
                </div>
            @endslot

            <table class="table table-bordered table-striped ajax_view" id="book_category_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>@lang('partner::lang.name')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>

            <div class="modal fade book_category_modal" tabindex="-1" role="dialog" 
                aria-labelledby="gridSystemModalLabel">
            </div>
        @endcomponent
    </section>
@endsection    

@section('javascript')
<script>
$(document).ready(function() {
    book_category_table = $('#book_category_table').DataTable({
        processing: true,
        serverSide: true,
        scrollY:    "75vh",
        scrollX:        true,
        scrollCollapse: true,
        ajax: {
            url: '/partner/book_categories',
            // data: function(d) {
            //     d = __datatable_ajax_callback(d);
            // },
        },
        columns: [
            { data: 'name', name: 'name' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
    })

    $(document).on('click', 'a.delete-book-category', function(e) {
        e.preventDefault();

        if(window.confirm("Are you sure to delete?") == true) {
            const url = $(e.target).closest("a").attr("href");
            $.ajax({
                method: 'DELETE',
                url,
                dataType: 'json',
                success: function(result) {
                    if(result.success == true) {
                        toastrSwal(result.msg)
                        book_category_table.ajax.reload();    
                    } else {
                        toastrSwal(result.msg, 'error')
                    }
                }
            })
        }
    })

    $(document).on('click', 'button.btn-save-book-category', function(e) {
        e.preventDefault();
        $form = $("form#editBookCategoryForm");
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
                    $(".book_category_modal").modal("hide");
                    toastrSwal(result.msg)

                    book_category_table.ajax.reload();         
                } else {
                    toastrSwal(result.msg, 'error');
                }
            },
        })
    })

    $(document).on('shown.bs.modal', '.book_category_modal', function(e) {
    });
})
</script>
@endsection