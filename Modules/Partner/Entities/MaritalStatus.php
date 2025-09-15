<?php

namespace Modules\Partner\Entities;

use Illuminate\Database\Eloquent\Model;

class MaritalStatus extends Model
{
    protected $table = 'marital_statuses';

    protected $guarded = ['id'];

    public static function allMaritalStatuses($business_id) {
        $statuses = MaritalStatus::where('business_id', $business_id)->pluck('status', 'id');
        return $statuses;
    }
}
