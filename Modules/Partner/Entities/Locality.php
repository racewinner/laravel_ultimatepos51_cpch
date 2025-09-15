<?php

namespace Modules\Partner\Entities;

use Illuminate\Database\Eloquent\Model;

class Locality extends Model
{
    protected $table = 'localities';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public static function allLocalitiesDropdown($business_id) {
        $all_localities = Locality::where('business_id', $business_id)->pluck('name', 'id');
        return $all_localities;
    }
}
