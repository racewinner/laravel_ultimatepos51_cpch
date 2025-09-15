<?php

namespace Modules\Partner\Entities;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public static function allServicesDropdown($business_id) {
        $all_services = Service::where('business_id', $business_id)->pluck('name', 'id');
        return $all_services;
    }

    public function currency() {
        return $this->belongsTo(\App\Currency::class);
    }
}
