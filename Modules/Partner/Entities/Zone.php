<?php

namespace Modules\Partner\Entities;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $table = 'partner_zones';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public static function allZones($business_id) {
        $rows = Zone::where('business_id', $business_id)->pluck('name', 'id');
        return $rows;
    }
}
