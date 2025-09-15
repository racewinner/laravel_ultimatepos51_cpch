<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
					aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">@lang('restaurant.booking_details')</h4>
		</div>

		{!! Form::open(['url' => action([\Modules\Partner\Http\Controllers\PartnerBookingController::class, 'update'], [$booking->id]), 'method' => 'PUT', 'id' => 'edit_booking_form']) !!}
		<div class="modal-body">
			<div class="row">
				<div class="col-sm-6">
					<strong>@lang('partner::lang.partner'):</strong> {{ $booking->partner?->display_name ?? '' }}<br>
					<strong>@lang('partner::lang.salon'):</strong> {{ $booking->salon?->name ?? '--' }}<br>
					<strong>@lang('sale.status'):</strong> {{ $booking->booking_status }}<br>
					<strong>@lang('partner::lang.cost'):</strong> {{ $booking->cost }}<br>
				</div>
				<div class="col-sm-6">
					<strong>@lang('restaurant.booking_starts'):</strong> {{ $booking_start }}<br>
					<strong>@lang('restaurant.booking_ends'):</strong> {{ $booking_end }}<br>
					<strong>@lang('partner::lang.provisional_booking'):</strong>
					{{ $booking->provisional ? __('messages.yes') : __('messages.no') }}<br>
					<strong>@lang('partner::lang.confirmed_booking'):</strong>
					{{ $booking->confirmed ? __('messages.yes') : __('messages.no') }}<br>
					<strong>@lang('lang_v1.authorizor'):</strong> {{ $booking->creator->display_name }}<br>
				</div>
				<div class="col-sm-12 mt-4">
					@if(!empty($booking->booking_note))
						<strong>@lang('restaurant.customer_note'):</strong> {{ $booking->booking_note }}
					@endif
				</div>
			</div>

			<div class="row d-none">
				<div class="col-sm-12">
					<button type="button" class="btn btn-info btn-modal pull-right"
						data-href="{{action([\App\Http\Controllers\NotificationController::class, 'getTemplate'], ['transaction_id' => $booking->id, 'template_for' => 'new_partner_booking'])}}"
						data-container=".view_modal">@lang('partner::messages.send_notification_to_partner')</button>
				</div>
			</div>
			<br>

			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						{!! Form::label('booking_status', __('sale.status')) !!}
						<div class="input-group">
							{!! Form::select('booking_status', $booking_statuses, $booking->booking_status, ['class' => 'form-control', 'placeholder' => __('restaurant.change_booking_status'), 'required']) !!}
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						{!! Form::label('booking_status', __('partner::lang.open')) !!}
						<div class="input-group">
							{!! Form::checkbox('open', 1, $booking->open, ['class' => 'input-icheck m-0']) !!}
						</div>
					</div>
				</div>
			</div>

			<br>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" id="btn_delete_booking"
						data-href="{{action([\Modules\Partner\Http\Controllers\PartnerBookingController::class, 'destroy'], [$booking->id])}}">@lang('restaurant.delete_booking')</button>
				<button type="submit" class="btn btn-primary ms-4" id="btn_update_booking">@lang('messages.update')</button>
				<button type="button" class="btn btn-default ms-4" data-dismiss="modal">@lang('messages.close')</button>
			</div>

			{!! Form::close() !!}
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->