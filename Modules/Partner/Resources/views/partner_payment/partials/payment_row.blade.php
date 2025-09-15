<tr>
    <td>
        <div class='row_number mt-2'>{{$row_number+1}}</div>
        <input type='hidden' name='payments[{{$row_number}}][payment_id]' value="{{$payment?->id ?? 0}}" />
    </td>

    <td>
        {{ $payment->service->name ?? $service->name}}
        <input type='hidden' 
            name='payments[{{$row_number}}][service_id]' 
            value='{{$payment?->service_id ?? $service->id}}' 
            required 
        />
    </td>

    <td>
        <input type='number' 
            class='form-control input-sm payment_qty' 
            name='payments[{{$row_number}}][qty]' 
            value='{{$payment?->qty ?? 1}}' 
            required 
        />
    </td>

    <td>
        <input type='number' 
            class='form-control input-sm payment_unit_cost' 
            name='payments[{{$row_number}}][unit_cost]' 
            value='{{$payment?->unit_cost ?? $service->unit_cost}}' 
            required 
        />
    </td>
    <td>
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </span>
            <input type='text'
                class='form-control input-sm payment-pay-months month-range-picker'
                name='payments[{{$row_number}}][pay_months]'
                value='{{$payment?->pay_months}}'
                required
            />
        </div>
    </td>
    <td>
        <input type='number' 
            class='form-control input-sm payment-subtotal' 
            name='payments[{{$row_number}}][amount]' 
            value='{{$payment?->amount ?? $service->unit_cost * 1}}' 
            required 
        />
    </td>
    <td>
        <i class="fa fa-times remove_row text-danger" title="Remove" style="cursor:pointer;"></i>
    </td>
</tr>