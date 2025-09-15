<?php

namespace Modules\Partner\Entities;

use Illuminate\Database\Eloquent\Model;

class Salon extends Model
{
    protected $table = 'partner_salons';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public static function allSalons($business_id) {
        $rows = Salon::where('business_id', $business_id)->pluck('name', 'id');
        return $rows;
    }
}
