{!! Form::open([
    'url' => '/partner/receipts/bulk_print',
    'method' => 'post',
    'id' => 'form_bulk_print',
    'target' => '_blank',
]) !!}
    <input type='hidden' name='type' value='' />
    <input type='hidden' name='ref_nos' />
{!! Form::close() !!}