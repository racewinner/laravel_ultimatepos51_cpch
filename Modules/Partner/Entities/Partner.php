<?php

namespace Modules\Partner\Entities;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $table = 'partners';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function category() {
        return $this->belongsTo(PartnerCategory::class, 'partner_category_id', 'id');
    }

    public function submitPatnerCategory() {
        return $this->belongsTo(PartnerCategory::class, 'submit_partner_category_id', 'id');
    }

    public function bookCategory() {
        return $this->belongsTo(BookCategory::class, 'cat_book_id', 'id');
    }

    public function locality() {
        return $this->belongsTo(Locality::class, 'locality_id', 'id');
    }

    public function maritalStatus() {
        return $this->belongsTo(MaritalStatus::class, 'marital_status_id', 'id');
    }

    public function admissionReason() {
        return $this->belongsTo(PartnerAdmissionReason::class, 'admission_reason_id', 'id');
    }

    public function zone() {
        return $this->belongsTo(Zone::class, 'zone_id', 'id');
    }

    public function radio() {
        return $this->belongsTo(Radio::class, 'radio_id', 'id');
    }

    public function leave() {
        return $this->hasOne(PartnerLeave::class);
    }

    public function getDisplayNameAttribute() {
        return ($this->surname ?? '') . " " . ($this->name ?? '');
    }

    public function getServicesAttribute() {
        $services = Service::whereIn('id', explode(',', $this->category->service_ids))->get();
        return $services;
    }

    public function getFeeServicesAttribute() {
        $services = Service::whereIn('id', explode(',', $this->category->service_ids))->where('name', 'LIKE', '%cuota%')->get();
        return $services;
    }

    public function getNotFeeServicesAttribute() {
      $services = Service::whereIn('id', explode(',', $this->category->service_ids))->where('name', 'NOT LIKE', '%cuota%')->get();
      return $services;
    }

    public function getAdditionalFeeServicesAttribute() {
        $services = Service::whereIn('id', explode(',', $this->additional_fee_service_ids))->get();
        return $services;
    }

    public function getAdditionalFeeServiceIdArrayAttribute() {
        $service_ids = explode(',', $this->additional_fee_service_ids);
        return $service_ids;
    }

    public function getMonthlyFeeAttribute() {
        $services = Service::whereIn('id', explode(',', $this->category->service_ids))->where('name', 'LIKE', '%cuota%')->get();
        $monthlyFee = $services->sum('unit_cost');
        return $monthlyFee;
    }

    public function getCurrencyAttribute() {
        return $this->services->first()->currency;
    }
}
