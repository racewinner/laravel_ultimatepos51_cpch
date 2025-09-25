<!DOCTYPE html>
<html>

<head>
    <title>Title</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            width: 350px;
            color: #000;
            margin: 0px;
        }

        @page {
            margin: 0px;
        }
    </style>
</head>

<body>
@foreach($receiptGroups as $ref_no => $receipts)
<?php
    $first_receipt = $receipts[0];
    $total_amount = 0;
    $total_amount_additional = 0;
?>
    <section style="padding: 10px; margin-bottom: 50px;">
        <div style='text-align:right; font-size: 90%;'>
            {{$type == 'payment' ? __('lang_v1.payment') : __('invoice.receipt') }}</div>

        <div style="display:flex; flex-direction:column;align-items:center; text-align:center; margin-top:10px;">
            <div style='font-size: 110%;'>{{ $businessDetails->name }}</div>
            <div style="font-size: 90%">Soriano 1227 - Tel: 2908 1207 - 098 514097</div>
            <div style="font-size: 90%">Coronel Raíz 1002 - Tel: 2359 5074 - 099 383284 </div>
            <div style="font-size: 70%">www.proteccionchoferes.org.uy-info@proteccionchoferes.org.uy</div>
        </div>

        <table style="margin-top: 20px; width:100%;">
            <tr>
                <td style="text-align:left;">@lang('purchase.ref_no'):</td>
                <td style="text-align:right;">{{ $ref_no }}</td>
            </tr>

            <tr>
                <td style="text-align:left;">@lang('receipt.date'):</td>
                <td style="text-align:right;">{{ $type == 'payment' ? $first_receipt->paid_on : $first_receipt->created_at }}</td>
            </tr>
            <tr>
                <td style="text-align:left;">@lang('business.currency'):</td>
                <td style="text-align:right;">{{ $first_receipt->currency->symbol }}</td>
            </tr>
            <tr>
                <td style="text-align:left;">RUT:</td>
                <td style="text-align:right;">215726790010</td>
            </tr>
        </table>

        <div style="margin-top: 20px;font-weight: bold;">
            <div style="border: 1px solid #000; padding: 5px; text-align:center; text-transform:uppercase">
                @lang('partner::lang.partner_information')
            </div>
            <table style="width:100%; border: 1px solid #000; border-top-width: 0px; padding: 10px; font-size: 90%;">
                <tr>
                    <td style="text-align:left;">{{ $first_receipt->partner->display_name }}</td>
                    <td style="text-align:right;">{{$first_receipt->partner->id_card_number}}</td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;">
                        {{ $first_receipt->partner->address }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;">
                        {{ $first_receipt->partner->radio->name }} / {{ $first_receipt->partner->zone->name }} /
                        {{ $first_receipt->partner->route_id }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align:left;">@lang('partner::lang.enter'):</td>
                    <td style="text-align:right;">{{ $first_receipt->partner->date_admission }}</td>
                </tr>
                <tr>
                    <td style="text-align:left;">@lang('partner::lang.partner_category'):</td>
                    <td style="text-align:right;">{{$first_receipt->partner->category->detail}}</td>
                </tr>
            </table>
        </div>

        <div style="margin-top: 20px;">
            <table style="font-size: 90%; width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border:none; text-transform:uppercase;">
                        <th style="text-align:left;">@lang('partner::lang.service')</th>
                        <th style="">@lang('lang_v1.month')</th>
                        <th style="">@lang('invoice.price')</th>
                        <th style="">@lang('invoice.total')</th>
                    </tr>
                </thead>
                <tbody style="border-top: 1px solid #000; border-bottom: 1px solid #000;">
                    @foreach ($receipts as $receipt)
                    <?php 
                        $total_amount += $receipt->amount;
                    ?>
                        @foreach($receipt->services as $service)
                        <?php 
                            $total_amount_additional += $service->unit_cost * $receipt->months;
                        ?>
                            <tr style="border: none;">
                                <td style="text-align:left; padding-top:5px; padding-bottom: 5px;">{{$service->name}}</td>
                                <td>{{ $receipt->period }}</td>
                                <td style="text-align:center;">{{ $service->unit_cost }}</td>
                                <td style="text-align:right;">{{ $service->unit_cost * $receipt->months }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="text-align:right; margin-top: 20px;">
            <div style="padding: 5px 0px; font-weight: bold;">
                <label style="font-weight: 400 !important; text-transform:uppercase">@lang('invoice.total'):</label>
                @if ($receipt->additional_payment !== 1)
                  <span
                      style="margin-left: 10px;">{{ \App\Utils\Util::format_currency($total_amount, $first_receipt->currency) }}
                  </span>
                @else
                  <span
                      style="margin-left: 10px;">{{ \App\Utils\Util::format_currency($total_amount_additional, $first_receipt->currency) }}
                  </span>
                @endif
                <!-- <span
                    style="margin-left: 10px;">{{ \App\Utils\Util::format_currency($total_amount, $first_receipt->currency) }}
                </span> -->
            </div>
        </div>

        <div style="text-align:center; margin-top: 30px;">
            <img src="{{$base64Logo}}" style="width: 50px; height: 50px" />
            <div style='font-size:80%; margin-top: 10px;'>ESTE RECIBO NO CANCELA DEUDAS ANTERIORES</div>
            <div style='font-size:80%; margin-top: 5px;'>Afiliado a: Federación Uruguaya de Choferes</div>
            <div style='font-size:80%; margin-top: 10px;'>Exonerados de impuestos Nacionales</div>
            <div style='font-size:80%;'>(Decreto del Consejo de Gobierno, del 10/10/63)</div>
        </div>
    </section>
@endforeach
</body>

</html>