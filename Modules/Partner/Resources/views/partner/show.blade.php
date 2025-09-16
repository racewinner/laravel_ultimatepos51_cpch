<div class="modal-dialog" role="document" style="width:80%;min-width:1200px;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <div class="d-flex justify-content-center align-items-center">
                <h4 class="modal-title text-center">
                    {{ $partner->newly_registered ? __('partner::lang.partner_registration') : __('partner::lang.show_partner') }}
                    ({{$partner->display_name}})
                </h4>
                <span class="ms-4 partner-status {{ empty($partner->leave) ? 'active' : 'inactive' }}">
                    {{ empty($partner->leave) ? 'Active' : 'InActive' }}
                </span>
            </div>
        </div>

        <div class="modal-body">
            @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.partner_information' ) . '</h4>'])
                @include('partner::partner.partials.partner_maininfo', ['partner' => $partner])
            @endcomponent

            @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.register_information' ) . '</h4>'])
            <div class="row">
                <div class="col-sm-3 invoice-col">
                    <div class="form-group">
                        {!! Form::label('admission_reason', __('partner::lang.admission_reason') . ':') !!}
                        <div class="partner-profile">{{$partner?->admissionReason->name}}</div>
                    </div>
                </div>
                <div class="col-sm-3 invoice-col">
                    <div class="form-group">
                        {!! Form::label('collection_address', __('partner::lang.collection_address') . ':') !!}
                        <div class="partner-profile">{{$partner?->collection_address}}</div>
                    </div>
                </div>
                <div class="col-sm-3 invoice-col">
                    <div class="form-group">
                        {!! Form::label('collection_entre', __('partner::lang.collection_entre') . ':') !!}
                        <div class="partner-profile">{{$partner?->collection_entre}}</div>
                    </div>
                </div>
                <div class="col-sm-3 invoice-col">
                    <div class="form-group">
                        {!! Form::label('collection_telephone', __('partner::lang.collection_telephone') . ':') !!}
                        <div class="partner-profile">{{$partner?->collection_telephone}}</div>
                    </div>
                </div>
                <div class="col-sm-3 invoice-col">
                    <div class="form-group">
                        {!! Form::label('collection_handphone', __('partner::lang.collection_handphone') . ':') !!}
                        <div class="partner-profile">{{$partner?->collection_handphone}}</div>
                    </div>
                </div>
                <div class="col-sm-3 invoice-col">
                    <div class="form-group">
                        {!! Form::label('permanent_observation', __('partner::lang.permanent_observation') . ':') !!}
                        <div class="partner-profile">{{$partner?->permanent_observation}}</div>
                    </div>
                </div>
                <div class="col-sm-3 invoice-col">
                    <div class="form-group">
                        {!! Form::label('accepted_at', __('partner::lang.accepted_at') . ':') !!}
                        <div class="partner-profile">{{$partner?->accepted_at}}</div>
                    </div>
                </div>
                <div class="col-sm-3 invoice-col">
                    <div class="form-group">
                        {!! Form::label('application_submission_date', __('partner::lang.application_submission_date') . ':') !!}
                        <div class="partner-profile">{{$partner?->application_submission_date}}</div>
                    </div>
                </div>
                <div class="col-sm-3 invoice-col">
                    <div class="form-group">
                        {!! Form::label('submit_partner', __('partner::lang.submit_partner') . ':') !!}
                        <div class="partner-profile">{{$partner?->submit_partner}}</div>
                    </div>
                </div>
                <div class="col-sm-3 invoice-col">
                    <div class="form-group">
                        {!! Form::label('submit_partner_category', __('partner::lang.submit_partner_category') . ':') !!}
                        <div class="partner-profile">{{$partner?->submit_partner_category}}</div>
                    </div>
                </div>
                <div class="col-sm-3 invoice-col">
                    <div class="form-group">
                        {!! Form::label('radio_id', __('partner::lang.radio') . ':') !!}
                        <div class="partner-profile">{{$partner?->radio?->name}}</div>
                    </div>
                </div>
                <div class="col-sm-3 invoice-col">
                    <div class="form-group">
                        {!! Form::label('zone_id', __('partner::lang.zone') . ':') !!}
                        <div class="partner-profile">{{$partner?->zone?->name}}</div>
                    </div>
                </div>
                <div class="col-sm-3 invoice-col">
                    <div class="form-group">
                        {!! Form::label('route_id', __('partner::lang.route') . ':') !!}
                        <div class="partner-profile">{{$partner?->route_id}}</div>
                    </div>
                </div>
                <div class="col-sm-3 invoice-col">
                    <div class="form-group">
                        {!! Form::label('referrer_name', __('partner::lang.referrer_name') . ':') !!}
                        <div class="partner-profile">{{$partner?->referrer_name}}</div>
                    </div>
                </div>
                <div class="col-sm-3 invoice-col">
                    <div class="form-group">
                        {!! Form::label('referrer_no', __('partner::lang.referrer_no') . ':') !!}
                        <div class="partner-profile">{{$partner?->referrer_no}}</div>
                    </div>
                </div>
            </div>
            @endcomponent

            @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.payment_information' ) . '</h4>'])
            <div class="row">
                <div class="col-sm-3 invoice-col">
                    <div class="form-group">
                        {!! Form::label('first_month_charge', __('partner::lang.first_month_charge') . ':') !!}
                        <div style="display: flex">
                          <div class="partner-profile" style="flex-grow: 1">{{$partner?->debt['first_month']}}</div>
                          <div class="partner-profile" style="flex-grow: 1">
                              {{ \App\Utils\Util::format_currency($partner?->debt['monthly_fee'], $partner?->debt['currency']) }}
                          </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3 invoice-col">
                    <div class="form-group">
                        {!! Form::label('last_month_charge', __('partner::lang.last_month_charge') . ':') !!}
                        <div style="display: flex">
                          <div class="partner-profile" style="flex-grow: 1">{{$partner?->debt['last_month']}}</div>
                          <div class="partner-profile" style="flex-grow: 1">
                              {{ \App\Utils\Util::format_currency($partner?->debt['months'] * $partner?->debt['monthly_fee'], $partner?->debt['currency']) }}
                          </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-sm-3 invoice-col">
                    <div class="form-group">
                        {!! Form::label('debt_amount', __('partner::lang.debt_amount') . ':') !!}
                        <div class="partner-profile">
                            {{ \App\Utils\Util::format_currency($partner?->debt['months'] * $partner?->debt['monthly_fee'], $partner?->debt['currency']) }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-3 invoice-col">
                    <div class="form-group">
                        {!! Form::label('first_month_paid_on_debt', __(key: 'partner::lang.first_month_charge') . ':') !!}
                        <div class="partner-profile">{{$partner?->first_month_charge}}</div>
                    </div>
                </div>
                <div class="col-sm-3 invoice-col">
                    <div class="form-group">
                        {!! Form::label('last_pay_receipt', __('partner::lang.last_pay_receipt') . ':') !!}
                        <div class="partner-profile">{{$partner?->last_pay_receipt}}</div>
                    </div>
                </div>

                <div class="col-sm-3 invoice-col">
                    <div class="form-group">
                        {!! Form::label('remain_debt', __('partner::lang.remain_debt') . ':') !!}
                        <div class="partner-profile">{{$partner?->remain_debt}}</div>
                    </div>
                </div> -->
            </div>
            @endcomponent

            @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.additional_fee_services') . '</h4>'])
            <div class="row">
            @foreach ($partner->additional_fee_services as $service)
                <div class="col-md-3 invoice-col">{{ $service->name }}</div>
            @endforeach
            </div>
            @endcomponent

            @if(!empty($partner?->leave))
            @component('components.widget', ['class' => 'box-primary', 'header' => '<h4>' . __('partner::lang.leave_information' ) . '</h4>'])
                <div class="row">
                    <div class="col-md-3 invoice-col">
                        <div class="form-group">
                            {!! Form::label('leave_date', __('partner::lang.leave_date') . ':') !!}
                            <div class="partner-profile">{{ $partner->leave?->leave_date }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 invoice-col">
                        <div class="form-group">
                            {!! Form::label('leave_type', __('partner::lang.partner_leave_type') . ':') !!}
                            <div class="partner-profile">{{ $partner->leave?->leave_type->name }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 invoice-col">
                        <div class="form-group">
                            {!! Form::label('leave_reason', __('partner::lang.partner_leave_reason') . ':') !!}
                            <div class="partner-profile">{{ $partner->leave?->leave_reason->name }}</div>
                        </div>
                    </div>
                </div>

                @if(!empty($partner->leave->death_data))
                    <div class="row">
                        <div class="col-md-3 invoice-col">
                            <div class="form-group">
                                {!! Form::label('complaintant_id', __('partner::lang.complaintant_id') . ':') !!}
                                <div class="partner-profile">{{ $partner->leave?->death_data->complaintant_id }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 invoice-col">
                            <div class="form-group">
                                {!! Form::label('complaintant_name', __('partner::lang.complaintant_name') . ':') !!}
                                <div class="partner-profile">{{ $partner->leave?->death_data?->complaintant_name }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 invoice-col">
                            <div class="form-group">
                                {!! Form::label('complaintant_contact', __('partner::lang.complaintant_contact') . ':') !!}
                                <div class="partner-profile">{{ $partner->leave?->death_data?->complaintant_contact }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 invoice-col">
                            <div class="form-group">
                                {!! Form::label('death_date', __('partner::lang.death_date') . ':') !!}
                                <div class="partner-profile">{{ $partner->leave?->death_data?->death_date }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 invoice-col">
                            <div class="form-group">
                                {!! Form::label('elevate_date', __('partner::lang.elevate_date') . ':') !!}
                                <div class="partner-profile">{{ $partner->leave?->death_data?->elevate_date }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 invoice-col">
                            <div class="form-group">
                                {!! Form::label('death_cert_submitted', __('partner::lang.death_cert_submitted') . ':') !!}
                                <div class="partner-profile">{{ $partner->leave?->death_data?->death_cert_submitted }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 invoice-col">
                            <div class="form-group">
                                {!! Form::label('sign_policy', __('partner::lang.sign_policy') . ':') !!}
                                <div class="partner-profile">{{ $partner->leave?->death_data?->sign_policy ? 'Yes' : 'No' }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 invoice-col">
                            <div class="form-group">
                                {!! Form::label('beneficiarios', __('partner::lang.beneficiarios') . ':') !!}
                                <div class="partner-profile">{{ $partner->leave?->death_data?->beneficiarios }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 invoice-col">
                            <div class="form-group">
                                {!! Form::label('cause', __('partner::lang.death_cause') . ':') !!}
                                <div class="partner-profile">{{ $partner->leave?->death_data?->cause }}</div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-12">
                        {!! Form::label('memo', __('partner::lang.memo') . ':') !!}
                        <div class="partner-profile">{{ $partner->leave?->memo }}</div>
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