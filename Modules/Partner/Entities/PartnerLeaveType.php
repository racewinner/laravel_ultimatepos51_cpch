<?php

namespace Modules\Partner\Entities;

use Illuminate\Database\Eloquent\Model;

class PartnerLeaveType extends Model
{
    protected $table = 'partner_leave_types';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public static function allLeaveTypes($business_id) {
        $rows = PartnerLeaveType::where('business_id', $business_id)->pluck('name', 'id');
        return $rows;
    }
}
