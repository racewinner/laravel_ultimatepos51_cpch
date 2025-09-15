@extends('layouts.app')
@section('title', __('partner::lang.locality') . ' ' . __('business.dashboard'))

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1>@lang('partner::lang.locality')
            <small></small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content no-print">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('partner::lang.list_localities')])
        @slot('tool')
        <div class="box-tools">
            <button type="button" class="btn btn-block btn-primary btn-modal"
                data-href="{{action([\Modules\Partner\Http\Controllers\LocalityController::class, 'create'])}}"
                data-container=".locality-modal">
                <i class="fa fa-plus"></i> @lang('messages.add')</a>
            </button>
        </div>
        @endslot

        <table class="table table-bordered table-striped ajax_view" id="locality_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang('partner::lang.name')</th>
                    <th>@lang('partner::lang.department_code')</th>
                    <th>@lang('messages.action')</th>
                </tr>
            </thead>
        </table>

        <div class="modal fade locality-modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
        @endcomponent
    </section>
@endsection

@section('javascript')
    <script>
        $(document).ready(function () {
            locality_table = $('#locality_table').DataTable({
                processing: true,
                serverSide: true,
                scrollY: "75vh",
                scrollX: true,
                scrollCollapse: true,
                ajax: {
                    url: '/partner/localities',
                    // data: function(d) {
                    //     d = __datatable_ajax_callback(d);
                    // },
                },
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'department_code', name: 'department_code' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
            })

            $(document).on('click', 'a.delete-locality', function (e) {
                e.preventDefault();

                if (window.confirm("Are you sure to delete?") == true) {
                    const url = $(e.target).closest("a").attr("href");
                    $.ajax({
                        method: 'DELETE',
                        url,
                        dataType: 'json',
                        success: function (result) {
                            if (result.success == true) {
                                toastrSwal(result.msg);
                                locality_table.ajax.reload();
                            } else {
                                toastrSwal(result.msg, 'error');
                            }
                        }
                    })
                }
            })

            $(document).on('click', 'button.btn-save-locality', function (e) {
                e.preventDefault();
                $form = $("form#editLocalityForm");
                var data = $form.serialize();
                $.ajax({
                    method: 'POST',
                    url: $form.attr('action'),
                    dataType: 'json',
                    data,
                    beforeSend: function (xhr) {
                        __disable_submit_button($form.find('button[type="submit"]'));
                    },
                    success: function (result) {
                        console.log(result);
                        if (result.success == true) {
                            $(".locality-modal").modal("hide");
                            toastrSwal(result.msg);

                            locality_table.ajax.reload();
                        } else {
                            toastrSwal(result.msg, 'error');
                        }
                    },
                })
            })
        })
    </script>
@endsection