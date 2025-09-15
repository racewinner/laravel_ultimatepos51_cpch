<?php

namespace Modules\Partner\Entities;

use Illuminate\Database\Eloquent\Model;

class PartnerReturnReason extends Model
{
    protected $table = 'partner_return_reasons';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public static function allReturnReasons($business_id) {
        $rows = PartnerReturnReason::where('business_id', $business_id)->pluck('name', 'id');
        return $rows;
    }
}
