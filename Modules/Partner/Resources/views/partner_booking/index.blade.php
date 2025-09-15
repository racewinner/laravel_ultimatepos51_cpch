@extends('layouts.app')
@section('title', __('restaurant.bookings'))

@section('css')
<style type="text/css">

</style>
@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('restaurant.bookings')</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('restaurant.todays_bookings') . '</h4>'])
        <table class="table table-bordered table-condensed" id="todays_bookings_table">
            <thead>
                <tr>
                    <th>@lang('messages.action')</th>
                    <th>@lang('partner::lang.partner')</th>
                    <th>@lang('restaurant.booking_starts')</th>
                    <th>@lang('restaurant.booking_ends')</th>
                    <th>@lang('partner::lang.salon')</th>
                    <th>@lang('partner::lang.cost')</th>
                    <th>@lang('partner::lang.provisional_booking')</th>
                    <th>@lang('partner::lang.confirmed_booking')</th>
                    <th>@lang('messages.status')</th>
                    <th>@lang('lang_v1.authorizor')</th>
                </tr>
            </thead>
        </table>
        @endcomponent

        <div class="row">
            <div class="col-md-10">
                @component('components.widget', ['class' => 'box-primary'])
                @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary" data-toggle="modal"
                        data-target=".add-booking-modal">
                        <i class="fa fa-plus"></i>@lang('restaurant.add_booking')</a>
                    </button>
                </div>
                @endslot

                <div class="row">
                    <div class="col-sm-12">
                        <div id="calendar"></div>
                    </div>
                </div>
                @endcomponent
            </div>
            <div class="col-md-2">
                <div class="box box-solid">
                    <div class="box-body">
                        <!-- the events -->
                        <div class="external-event bg-yellow text-center" style="position: relative;">
                            <small>@lang('lang_v1.waiting')</small>
                        </div>
                        <div class="external-event bg-light-blue text-center" style="position: relative;">
                            <small>@lang('restaurant.booked')</small>
                        </div>
                        <div class="external-event bg-green text-center" style="position: relative;">
                            <small>@lang('restaurant.completed')</small>
                        </div>
                        <div class="external-event bg-red text-center" style="position: relative;">
                            <small>@lang('restaurant.cancelled')</small>
                        </div>
                        <small>
                            <p class="help-block">
                                <i>@lang('restaurant.click_on_any_booking_to_view_or_change_status')<br><br>
                                    @lang('restaurant.double_click_on_any_day_to_add_new_booking')
                                </i>
                            </p>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade add-booking-modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
            @include('partner::partner_booking.create_modal')
        </div>
    </section>
@endsection

@section('javascript')
    <script type="text/javascript">
        function reset_booking_form() {
            $("form#add_booking_form #booking_note").val('');
            $("form#add_booking_form #partner_id").val('').trigger('change');
            $("form#add_booking_form #salon_id").val('');
            $("form#add_booking_form #start_time").val('');
            $("form#add_booking_form #end_time").val('');
        }

        function reload_calendar() {
            var events_source = {
                url: '/partner/bookings',
                type: 'get',
            }
            $('#calendar').fullCalendar('removeEventSource', events_source);
            $('#calendar').fullCalendar('addEventSource', events_source);
            $('#calendar').fullCalendar('refetchEvents');
        }

        $(document).ready(function () {
            clickCount = 0;
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay,listWeek'
                },
                eventLimit: 2,
                events: '/partner/bookings',
                eventRender: function (event, element) {
                    var title_html = event.partner_name;
                    if (event.salon_name) {
                        title_html += '<br>' + event.salon_name;
                    }
                    // title_html += '<br>' + event.start_time + ' - ' + event.end_time;

                    element.find('.fc-title').html(title_html);
                    element.attr('data-href', event.url);
                    element.attr('data-container', '.view_modal');
                    element.addClass('btn-modal');
                },
                dayClick: function (date, jsEvent, view) {
                    clickCount++;
                    if (clickCount == 2) {
                        $('.add-booking-modal').modal('show');
                        $('form#add_booking_form #start_time').data("DateTimePicker").date(date).ignoreReadonly(true);
                        $('form#add_booking_form #end_time').data("DateTimePicker").date(date).ignoreReadonly(true);
                    }
                    var clickTimer = setInterval(function () {
                        clickCount = 0;
                        clearInterval(clickTimer);
                    }, 500);
                }
            });

            $('form#add_booking_form').find('#start_time, #end_time').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                minDate: moment(),
                ignoreReadonly: true,
            });

            $(document).on('shown.bs.modal', '.view_modal', function(e) {
                $('input.input-icheck').iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                });
            })

            $(document).on('hidden.bs.modal', '.add-booking-modal', function (e) {
                $('#partner-section i.expand-tag').addClass('hidden');
                $('#partner-section .partner-detail').addClass('hidden');

                $('#salon-section i.expand-tag').addClass('hidden');
                $('#salon-section .salon-detail').addClass('hidden');
            });

            $(document).on('change', '#salon-section select#salon_id', function(e) {
                const salon_id = e.target.value;
                $.ajax({
                    method: 'GET',
                    url: `/partner/salons/${salon_id}`,
                    success: function(result) {
                        if(result.success == 1) {
                            $('#salon-section i.expand-tag').removeClass('hidden');
                            $('#salon-section .salon-detail').removeClass('hidden');

                            $('#salon-section .salon-detail input[name="daytime_from"]').val(result.salon.daytime_from);
                            $('#salon-section .salon-detail input[name="daytime_to"]').val(result.salon.daytime_to);
                            $('#salon-section .salon-detail input[name="nighttime_from"]').val(result.salon.nighttime_from);
                            $('#salon-section .salon-detail input[name="nighttime_to"]').val(result.salon.nighttime_to);
                            $('#salon-section .salon-detail input[name="price_for_partner"]').val(result.salon.price_for_partner);
                            $('#salon-section .salon-detail input[name="price_for_no_partner"]').val(result.salon.price_for_no_partner);
                        } else {
                            toastrSwal(result.msg, 'error');
                        }
                    }
                })
            })

            $(document).on('click', '#partner-section i.expand-tag', function(e) {
                if($(e.target).hasClass('fa-angle-down')) {
                    $(e.target).removeClass('fa-angle-down');
                    $(e.target).addClass('fa-angle-up');
                    $('#partner-section .partner-detail').removeClass('hidden');
                } else {
                    $(e.target).removeClass('fa-angle-up');
                    $(e.target).addClass('fa-angle-down');
                    $('#partner-section .partner-detail').addClass('hidden');
                }
            })

            $(document).on('click', '#salon-section i.expand-tag', function(e) {
                if($(e.target).hasClass('fa-angle-down')) {
                    $(e.target).removeClass('fa-angle-down');
                    $(e.target).addClass('fa-angle-up');
                    $('#salon-section .salon-detail').removeClass('hidden');
                } else {
                    $(e.target).removeClass('fa-angle-up');
                    $(e.target).addClass('fa-angle-down');
                    $('#salon-section .salon-detail').addClass('hidden');
                }
            })

            $(document).on('shown.bs.modal', '.add-booking-modal', function (e) {
                $('form#add_booking_form #partner_id').select2({
                    dropdownParent: $(".add-booking-modal"),
                    ajax: {
                        url: '/partner/partners/get_partners',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term, // search term
                                page: params.page,
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data.map(function (item) {
                                    return {
                                        ...item,
                                        text: `${item.surname} ${item.name} (${item.id_card_number})`
                                    }
                                }),
                            };
                        },
                    },
                    minimumInputLength: 1,
                    escapeMarkup: function (m) {
                        return m;
                    },
                    templateResult: function (data) {
                        if (!data.id) {
                            return data.text;
                        }
                        return `${data.surname} ${data.name} (${data.id_card_number})`;
                    },
                    language: {
                        noResults: function () {
                            var name = $('#partner_id')
                                .data('select2')
                                .dropdown.$search.val();
                            return (
                                '<a href="/partner/partners/create" class="btn btn-link add_new_partner"><i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i>&nbsp; ' +
                                __translate('add_name_as_new_partner', { name: name }) +
                                '</a>'
                            );
                        },
                    },
                }).on('select2:select', function (e) {
                    var data = e.params.data;
                    $("#partner-section .partner-detail").removeClass("hidden");
                    $("#partner-section i.expand-tag").removeClass('hidden');

                    $(".partner-detail #id_card_number").val(data.id_card_number);
                    $(".partner-detail #address").val(data.address);
                    $(".partner-detail #entre").val(data.entre);
                    $(".partner-detail #locality").val(data.locality?.name);
                    $(".partner-detail #telephone").val(data.telephone);
                    $(".partner-detail #handphone").val(data.handphone);
                    $(".partner-detail #date_admission").val(data.date_admission);
                    $(".partner-detail #email").val(data.email);
                })

                reset_booking_form();
            })

            $(document).on('submit', "form#add_booking_form", function (e) {
                e.preventDefault();

                const form = e.target;
                const data = new FormData(form);
                var url = $(form).attr('url');
                $.ajax({
                    method: 'POST',
                    url,
                    data,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    beforeSend: function (xhr) {
                        __disable_submit_button($(form).find('button[type="submit"]'));
                    },
                    success: function (result) {
                        if (result.success == 1) {
                            if (result.send_notification) {
                            }

                            $('.add-booking-modal').modal('hide');

                            reload_calendar();
                            todays_bookings_table.ajax.reload();

                            toastrSwal(result.msg);
                            $(form).find('button[type="submit"]').attr('disabled', false);
                        } else {
                            toastrSwal(result.msg, 'error');
                        }

                        $(form).find('button[type="submit"]').attr('disabled', false);
                    }
                });

                return false;
            })

            $(document).on('submit', 'form#edit_booking_form', function (e) {
                e.preventDefault();

                const form = e.target;
                var data = $(form).serialize();
                const url = $(form).attr('action');
                $.ajax({
                    method: 'PUT',
                    url,
                    data,
                    dataType: 'json',
                    beforeSend: function (xhr) {
                        __disable_submit_button($(form).find('button[type="submit"]'));
                    },
                    success: function (result) {
                        if (result.success == 1) {
                            $('.view_modal').modal('hide');
                            toastrSwal(result.msg);
                            reload_calendar();
                            todays_bookings_table.ajax.reload();
                        } else {
                            toastrSwal(result.msg, 'error');
                        }
                        $(form).find('button[type="submit"]').attr('disabled', false);
                    }
                });
            })

            $(document).on('click', 'button#btn_delete_booking', function () {
                swal({
                    text: LANG.sure,
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var href = $(this).data('href');
                        $.ajax({
                            method: "DELETE",
                            url: href,
                            dataType: "json",
                            success: function (result) {
                                if (result.success == true) {
                                    $('div.view_modal').modal('hide');
                                    toastrSwal(result.msg);
                                    reload_calendar();
                                    todays_bookings_table.ajax.reload();
                                } else {
                                    toastrSwal(result.msg, error);
                                }
                            }
                        }); 
                    }
                });
            });

            $(document).on('change', 'select#salon_id', function(e) {
                $('.salon-detail').removeClass('d-none');
            })

            todays_bookings_table = $('#todays_bookings_table').DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                searching: false,
                pageLength: 10,
                dom: 'frtip',
                ajax: {
                    url: "/partner/bookings/get-todays-bookings",
                    data: function (d) {

                    }
                },
                columns: [
                    { data: 'action' },
                    { data: 'partner' },
                    { data: 'booking_start', name: 'booking_start' },
                    { data: 'booking_end', name: 'booking_end' },
                    { data: 'salon' },
                    { data: 'cost' },
                    { data: 'provisional' },
                    { data: 'confirmed' },
                    { data: 'booking_status' },
                    { data: 'creator' },
                ]
            });
        })    
    </script>
@endsection