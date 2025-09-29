<div class="modal-dialog" role="document" style="width:80%;min-width:1200px;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <div class="d-flex justify-content-center align-items-center">
                <h4 class="modal-title text-center">
                    @if($partner->leave == 1)
                      {{ __('partner::lang.partner_leave_print_lavel') }}
                    @else
                      {{ $partner->newly_registered ? __('partner::lang.partner_registration') : __('partner::lang.show_partner') }}
                    @endif

                      ({{$partner->display_name}})
                </h4>
                <span class="ms-4 partner-status {{ empty($partner->leave) ? 'active' : 'inactive' }}">
                    {{ empty($partner->leave) ? 'Active' : 'InActive' }}
                </span>
            </div>
        </div>

        <div class="modal-body">
            @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.partner_information' ) . '</h4>'])
                @include('partner::partner.partials.partner_maininfo_leave', ['partner' => $partner])
            @endcomponent

            @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.payment_information' ) . '</h4>'])
            <div class="row">
                <div class="col-sm-3 invoice-col">
                    <div class="form-group">
                        {!! Form::label('first_month_charge', __('partner::lang.first_month_charge') . ':') !!}
                        <div style="display: flex">
                          <div class="partner-profile" style="flex-grow: 1">{{$partner?->debt['first_month_have_charged_last']}}</div>
                          <div class="partner-profile" style="flex-grow: 1">
                              {{ \App\Utils\Util::format_currency($partner && $partner->debt['first_month_have_charged_last'] ? $partner->debt['monthly_fee'] : null, $partner?->debt['currency']) }}
                          </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3 invoice-col">
                    <div class="form-group">
                        {!! Form::label('last_month_charge', __('partner::lang.last_month_charge') . ':') !!}
                        <div style="display: flex">
                          <div class="partner-profile" style="flex-grow: 1">{{$partner?->debt['last_month_have_to_charge_after']}}</div>
                          <div class="partner-profile" style="flex-grow: 1">
                              {{ \App\Utils\Util::format_currency($partner?->debt['monthly_fee'], $partner?->debt['currency']) }}
                          </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcomponent

            @if(!empty($partner?->leave))
            @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.leave_information' ) . '</h4>'])
                <div class="row">
                    <div class="col-md-6 invoice-col">
                        <div class="form-group">
                            {!! Form::label('leave_date', __('partner::lang.leave_date') . ':') !!}
                            <div class="partner-profile">{{ $partner->leave_info?->leave_date }}</div>
                        </div>
                    </div>
                    <div class="col-md-6 invoice-col">
                        <div class="form-group">
                            {!! Form::label('leave_type', __('partner::lang.partner_leave_type') . ':') !!}
                            <div class="partner-profile">{{ $partner->leave_info?->plt_name }}</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 invoice-col">
                        <div class="form-group">
                            {!! Form::label('leave_reason', __('partner::lang.partner_leave_reason') . ':') !!}
                            <div class="partner-profile">{{ $partner->leave_info?->plr_name }}</div>
                        </div>
                    </div>
                    <div class="col-md-6 invoice-col">
                        <div class="form-group">
                            {!! Form::label('memo', __('partner::lang.memo') . ':') !!}
                            <div class="partner-profile">{{ $partner->leave_info?->memo }}</div>
                        </div>
                    </div>
                </div>
            @endcomponent
            @endif
        </div>

        <div class="modal-footer no-print">
            <button type="button" class="btn btn-primary" onclick="$(this).closest('div.modal-content').printThis();">@lang( 'messages.print' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
    </div>
</div>