<div class="modal fade" id="checkPinModal" tabindex="-1" aria-labelledby="checkPinModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('partner::lang.check_pin')</h4>
            </div>

            <div class="modal-body" style="padding: 40px 30px 50px 30px;">
                <div class="d-flex align-items-center">
                    {!! Form::password('pin_partner', ['class' => 'form-control flex-fluid']) !!}
                    <button type="button" id="btn_check_pin"
                        class="btn btn-primary ms-4">@lang('lang_v1.check')</button>
                </div>
            </div>
        </div>
    </div>
</div>