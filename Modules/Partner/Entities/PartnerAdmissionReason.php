<?php

namespace Modules\Partner\Entities;

use Illuminate\Database\Eloquent\Model;

class PartnerAdmissionReason extends Model
{
    protected $table = 'partner_admission_reasons';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public static function allAdmissionReasons($business_id) {
        $rows = PartnerAdmissionReason::where('business_id', $business_id)->pluck('name', 'id');
        return $rows;
    }
}
