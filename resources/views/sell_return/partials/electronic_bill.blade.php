<div class="electronic_bill_tab my-2" style="margin: 10px 0px;" data-type="card" >
	@if ($sell_return)
		@if ($sell_return->pymo_invoice)
			<i class="fa fa-download"></i>
			<a class="m-2" href="/uploads/invoices/{{$sell_return->business_id}}/{{$sell_return->pymo_invoice . '.pdf'}}" target="_blank">Download Document</a>
		@else
			<button href="#" id="generate_bill">@lang( 'lang_v1.generate_invoice' )</button>
		@endif
	@endif
</div>

@section('bill_javascript')
<script>
	$(document).ready(function() {
		$(".electronic_bill_tab").on('click', '#generate_bill', function(event) {
			// To check discount_type
			if($("#discount_type").val() == 'fixed') {
				toastr.error("Discount type not supported for e-invoice, select discount by percentage");
				return false;
			}

			event.preventDefault();
			let form_data = $("form#sell_return_form").serialize();
			let bill_data = {};
			
			form_data.split('&').forEach(function(pair) {
				let parts = pair.split('=');
				let name = decodeURIComponent(parts[0]);
				let value = decodeURIComponent(parts[1] || '');
				bill_data[name] = value;
			});
			bill_data['_method'] = 'POST';
			$.post('/pymo/sendReturnInvoice', bill_data, function(response) {
                if (response.status && response.status === 'SUCCESS') {
					toastr.success("Cfes recibidos correctamente.");
					let html = "<i class='fa fa-download'></i>";
					html += `<a class='m-2' href='/uploads/invoices/{{$sell_return->business_id}}/${response.invoice_id}.pdf' target='_blank'>Download Document</a>`;
					$(".electronic_bill_tab").html(html);
                }
				if (response.status === 'error') {
					if (response.data && response.data.message.code && response.data.message.code === 'DUPLICATED_KEY') {
						toastr.warning("Please update Invoice ID");
					} else if (response.message) {
						toastr.warning(response.message);
					} else if (response.data && response.data.message) {
						toastr.warning(response.data.message.value);
					}
				}
            }).fail(function(xhr, status, error) {
                console.error(error);
            });
		});
		// $(".electronic_bill_tab #show_bill").on('click', function(event) {
		// 	event.preventDefault();
		// 	let invoiceId = $(this).attr('invoiceId');
		// 	if (!invoiceId || invoiceId.length == 0) {
		// 		toastr.warning("There is no created invoice!");
		// 		return;
		// 	}
		// 	let bill_data = {
		// 		id: invoiceId
		// 	}
		// 	$.post('/pymo/getInvoice', bill_data, function(response) {
		// 		var link = document.createElement('a');
		// 		if (response && response.status == 'SUCCESS') {
		// 			link.href = response.url;  // Replace with the correct path to your PDF file in the public folder
		// 			link.download = 'invoice.pdf';
		// 			// Simulate a click on the link to trigger the download
		// 			link.click();
		// 		}
        //     }).fail(function(xhr, status, error) {
        //         console.error(error);
        //     });
		// });
	});
</script>
@endsection