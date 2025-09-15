<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('first_month_charge', __('partner::lang.first_month_charge') . ':') !!}
            <div class="d-flex">
                {!! Form::text('debt_first_month', $debt['first_month'], ['class' => 'form-control', 'readonly']) !!}
                {!! Form::text('debt_first_month_amount', 
                    \App\Utils\Util::format_currency($debt['monthly_fee'], $debt['currency']), 
                    ['class' => 'form-control', 'readonly']) 
                !!}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('last_month_charge', __('partner::lang.last_month_charge') . ':') !!}
            <div class="d-flex">
                {!! Form::text('debt_last_month', $debt['last_month'], ['class' => 'form-control', 'readonly']) !!}
                {!! Form::text('debt_last_month_amount', '', ['class' => 'form-control','id'=>'debt_last_month_amount', 'readonly']) !!}
            </div>
        </div>
    </div>
</div>
