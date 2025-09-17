<!DOCTYPE html>
<html>
<head>
    <title>Title</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            width: 350px;
            color: #000;
            margin:0px;
        }
        @page {
            margin: 0px;
        }
    </style>
</head>
<body>

<section style="padding: 10px;">
    <div style='text-align:right; font-size: 90%;'>@lang('partner::lang.payment_information')</div>
    <div style="display:flex; flex-direction:column;align-items:center; text-align:center; margin-top:10px;">
        <div style='font-size: 110%;'>{{ $business_details->name }}</div>
    </div>

    <table style="margin-top: 20px; width:100%;">
        <tr>
            <td style="text-align:left;">@lang('receipt.date')</td>
            <td style="text-align:right;">{{ $transaction->created_at }}</td>
        </tr>
        <tr>
            <td style="text-align:left;">@lang('business.currency')</td>
            <td style="text-align:right;">{{ $transaction->currency->symbol }}</td>
        </tr>
    </table>

    <div style="margin-top: 20px;font-weight: bold;">
        <div style="border: 1px solid #000; padding: 5px; text-align:center; text-transform:uppercase">@lang('partner::lang.partner_information')</div>
        <div style="padding: 10px; border:1px solid #000; border-top-width: 0;">
            <div style="padding: 5px; text-align:center">{{ $partner->display_name }}</div>
            <div style="padding: 5px; text-align:center">{{ $partner->address }}</div>
            <div style="padding: 5px; text-align:center">{{ $partner->telephone }}</div>
            <div style="padding: 5px; text-align:center">{{ $partner->handphone }}</div>
        </div>
    </div>

    <div style="margin-top: 20px;">
        <table style="font-size: 90%; width: 100%;"> 
            <thead>
                <tr>
                    <th style="text-align:left; border:none; text-transform:uppercase">@lang('partner::lang.service')</th>
                    <th style="border:none; text-transform:uppercase">@lang('lang_v1.month')</th>
                    <th style="border:none; text-transform:uppercase">@lang('invoice.price')</th>
                    <th style="border:none; text-transform:uppercase">@lang('invoice.total')</th>
                </tr>
            </thead>
            <tbody style="border-top: 1px solid #000; border-bottom: 1px solid #000;">
            @foreach($transaction->payment_lines as $payment)
                <tr style="border: none;">
                    <td style="border: none; text-align:left; padding-top:5px; padding-bottom: 5px;">{{$payment->service->name}}</td>
                    <td style="border: none; text-align:center;">{{ $payment->pay_months }}</td>
                    <td style="border: none; text-align:center;">{{ $payment->unit_cost }}</td>
                    <td style="border: none; text-align:right;">{{ $payment->amount }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div style="text-align:right; margin-top: 20px;">
        <div style="padding: 5px 0px; font-weight: bold;">
            <label style="font-weight: 400 !important; text-transform:uppercase">@lang('invoice.total'):</label>
            <span style="margin-left: 10px;">{{ \App\Utils\Util::format_currency($transaction->final_total, $transaction->currency) }}</span>
        </div>
    </div>
</section>

</body>
</html>
