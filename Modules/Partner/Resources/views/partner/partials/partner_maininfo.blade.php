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
            {!! Form::label('entre', __('partner::lang.entre') . ':*') !!}
            <div class="partner-profile">{{$partner?->entre}}</div>
        </div>
    </div>
    <div class="col-sm-3 invoice-col">
        <div class="form-group">
            {!! Form::label('locality', __('partner::lang.locality') . ':') !!}
            <div class="partner-profile">{{$partner?->locality->name}}</div>
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
            {!! Form::label('sign_policy', __('partner::lang.sign_policy') . ':') !!}
            <div class="partner-profile">{{$partner?->sign_policy}}</div>
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
            {!! Form::label('marital_status', __('partner::lang.marital_status') . ':') !!}
            <div class="partner-profile">{{$partner?->maritalStatus->status}}</div>
        </div>
    </div>
    <div class="col-sm-3 invoice-col">
        <div class="form-group">
            {!! Form::label('dob', __('partner::lang.dob') . ':') !!}
            <div class="partner-profile">{{$partner?->dob}}</div>
        </div>
    </div>
    <div class="col-sm-3 invoice-col">
        <div class="form-group">
            {!! Form::label('age', __('partner::lang.age') . ':') !!}
            <div class="partner-profile">{{$partner?->age}}</div>
        </div>
    </div>
    <div class="col-sm-3 invoice-col">
        <div class="form-group">
            {!! Form::label('book_no', __('partner::lang.book_no') . ':') !!}
            <div class="partner-profile">{{$partner?->book_no}}</div>
        </div>
    </div>
    <div class="col-sm-3 invoice-col">
        <div class="form-group">
            {!! Form::label('cat_book', __('partner::lang.cat_book') . ':*') !!}
            <div class="partner-profile">{{$partner?->bookCategory->name}}</div>
        </div>
    </div>
    <div class="col-sm-3 invoice-col">
        <div class="form-group">
            {!! Form::label('date_expire_book', __('partner::lang.date_expire_book') . ':') !!}
            <div class="partner-profile">{{$partner?->date_expire_book}}</div>
        </div>
    </div>
    <div class="col-sm-3 invoice-col">
        <div class="form-group">
            {!! Form::label('issuance_place', __('partner::lang.issuance_place') . ':*') !!}
            <div class="partner-profile">{{$partner?->issuance_place}}</div>
        </div>
    </div>
    <div class="col-sm-3 invoice-col">
        <div class="form-group">
            {!! Form::label('issuance_place', __('partner::lang.partner_category') . ':') !!}
            <div class="partner-profile">{{$partner?->category->detail}}</div>
        </div>
    </div>
</div>