<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Receipt-</title>
	<style>
		body {
			font-size: 12px;
            padding: 5px;
            margin: 0;
		}
        table tr td {
            border: 1px solid;
        }
        .field-value {
            height: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .background-grey {
            background: #eee !important;
        }
        .background-white {
            background: #fff !important;
        }
	</style>
</head>

<body>
<div style="width:100%; display:flex;">
    <div style="writing-mode:vertical-lr; transform:rotate(180deg);">GRAFIC PLUS LTDA.</div>
    <div style="display:flex; justify-content:center; align-items:center; padding: 10px; width: 15%;">
        <img src="/images/partner/mark1.png" style="width:100%;" />
    </div>
    <div style="flex:1; border-radius: 5px; display:flex; padding: 15px 10px;" class="background-grey">
        <div style="width: 200px;">
            <div style="text-align:center;">
                <img src="/images/partner/mark5.png" style="width: 100px; height: 100px;" />
            </div>
            <div style="text-align:center; margin-top: 30px;">PRESIDENTE</div>
            <div style="margin-top: 30px; display:flex; justify-content:space-between;">
                <div>SECRETARIO</div>
                <div>TESORERO</div>
            </div>
            <div style="margin-top: 15px; text-align:center; font-size: 110%;">
                <span>R.U.T.</span>
                <span style="margin-left: 20px;"></span>
            </div>
        </div>
        <div style="flex:1; margin-left: 10px;">
            <div style="text-align:center; font-size: 120%; font-weight:bold;">CENTRO PROTECCION CHOFERES DE MONTEVIDEO</div>
            <div style="display:flex;">
                <div style="flex:1;">
                    <div style="padding:3px;">
                        <span>
                            <span>SORIANO</span>
                            <span style="margin-left: 10px;">{{$partner?->member_no}}</span>
                        </span>
                        <span style="margin-left: 20px;">
                            <span>-TEL.:</span>
                            <span style="margin-left: 10px;">{{$partner?->telephone}}</span>
                        </span>
                        <span style="margin-left: 20px;">
                            <span>-FAX:</span>
                            <span style="margin-left: 10px;"></span>
                        </span>
                    </div>
                    <div style="padding:3px;">
                        <span>
                            <span>Afiliado a:</span>
                            <span style="margin-left:10px;">FEDERACION URUGUAYA DE CHOFERES</span>
                        </span>
                    </div>
                </div>
                <div>
                    <div style="text-align:right; padding: 3px;">
                        <span>
                            <span>- E-mail:</span>
                            <span style="margin-left: 10px;">{{$partner?->email}}</span>
                        </span>
                    </div>
                    <div style="text-align:right; padding: 3px;">
                        
                    </div>
                </div>
            </div>
            <div style="position:relative; margin-top: 10px;">
                <table style="width:100%; border-collapse:collapse;" class="background-white"><tbody>
                    <tr>
                        <td>
                            <div class="field-value">{{$partner?->id_card_number}}</div>
                            <div style="text-align:center;" class="background-grey">SOCIO</div>
                        </td>
                        <td colspan="4">
                            <div class="field-value">{{$partner?->surname}} {{$partner?->name}}</div>
                            <div style="text-align:center;" class="background-grey">NOMBRE Y APELLIDO</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div style="text-align:center;">CATEGORIA</div>
                            <div class="field-value">{{$partner?->category->detail}}</div>
                        </td>
                        <td>
                            <div style="text-align:center;">INGRESO</div>
                            <div class="field-value">{{$partner?->income_no}}</div>
                        </td>
                        <td>
                            <div style="text-align:center;">RECIBO</div>
                            <div class="field-value">{{$partner->last_pay_receipt}}</div>
                        </td>
                        <td>
                            <div style="text-align:center;">CORRESP. A</div>
                            <div class="field-value"></div>
                        </td>
                        <td>
                            <div style="text-align:center;">IMPORTE</div>
                            <div class="field-value"></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <div class="field-value"></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <div class="field-value"></div>
                        </td>
                    </tr>
                </tbody></table>
                <div style="position:absolute; left:0; right:0; bottom: -8px; background:transparent; display:flex; justify-content:center;">
                    <span  class="background-white">ESTE RECIBO NO CANCELA DEUDAS ANTERIORES</span>
                </div>
            </div>
        </div>
    </div>
    <div style="display:flex; flex-direction:column; justify-content:center; align-items:center; padding: 0 10px;">
        <img src="/images/partner/mark3.png" style="width: 50px;" />
        <img src="/images/partner/mark4.png" style="width: 50px; margin-top: 10px;" />
    </div>
</div>
</body>
