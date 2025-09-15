<div class="row">
    <div class="col-sm-3 invoice-col">
        <div class="form-group">
            {!! Form::label('surname', __('partner::lang.surname') . ':*') !!}
            <div class="partner-profile">{{$partner?->surname}}</div>
        </div>
    </div>
    <div class="col-sm-3 invoice-col">
        <div class="form-group">
            {!! Form::label('name', __('partner::lang.name') . ':*') !!}
            <div class="partner-profile">{{$partner?->name}}</div>
        </div>
    </div>
    <div class="col-sm-3 invoice-col">
        <div class="form-group">
            {!! Form::label('idcard', __('partner::lang.idcard') . ':*') !!}
            <div class="partner-profile">{{$partner?->id_card_number}}</div>
        </div>
    </div>
    <div class="col-sm-3 invoice-col">
        <div class="form-group">
            {!! Form::label('address', __('partner::lang.address') . ':*') !!}
            <div class="partner-profile">{{$partner?->address}}</div>
        </div>
    </div>
    <div class="col-sm-3 invoice-col">
        <div class="form-group">
            {!! Form::label('telephone', __('partner::lang.telephone') . ':') !!}
            <div class="partner-profile">{{$partner?->telephone}}</div>
        </div>
    </div>
    <div class="col-sm-3 invoice-col">
        <div class="form-group">
            {!! Form::label('handphone', __('partner::lang.handphone') . ':*') !!}
            <div class="partner-profile">{{$partner?->handphone}}</div>
        </div>
    </div>
    <div class="col-sm-3 invoice-col">
        <div class="form-group">
            {!! Form::label('date_admission', __('partner::lang.date_admission') . ':') !!}
            <div class="partner-profile">{{$partner?->date_admission}}</div>
        </div>
    </div>
    <div class="col-sm-3 invoice-col">
        <div class="form-group">
            {!! Form::label('email', __('partner::lang.email') . ':') !!}
            <div class="partner-profile">{{$partner?->email}}</div>
        </div>
    </div>
    <div class="col-sm-3 invoice-col">
        <div class="form-group">
            {!! Form::label('category', __('partner::lang.partner_category') . ':') !!}
            <div class="partner-profile">{{$partner?->category?->detail}}</div>
        </div>
    </div>
    <div class="col-sm-3 invoice-col">
        <div class="form-group">
            {!! Form::label('radio', __('partner::lang.radio') . ':') !!}
            <div class="partner-profile">{{$partner?->radio?->name}}</div>
        </div>
    </div>
    <div class="col-sm-3 invoice-col">
        <div class="form-group">
            {!! Form::label('zone', __('partner::lang.zone') . ':') !!}
            <div class="partner-profile">{{$partner?->zone?->name}}</div>
        </div>
    </div>
    <div class="col-sm-3 invoice-col">
        <div class="form-group">
            {!! Form::label('route', __('partner::lang.route') . ':') !!}
            <div class="partner-profile">{{$partner?->route_id}}</div>
        </div>
    </div>
</div>