<?php

namespace Modules\Partner\Entities;

use Illuminate\Database\Eloquent\Model;

class PartnerLeave extends Model
{
    protected $table = 'partner_leaves';

    protected $casts = [
        'death_data' => 'object', // or 'json'
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function parter() {
        return $this->belongsTo(Partner::class);
    }

    public function leave_type() {
        return $this->belongsTo(PartnerLeaveType::class, 'leave_type_id', 'id');
    }

    public function leave_reason() {
        return $this->belongsTo(PartnerLeaveReason::class, 'leave_reason_id', 'id');
    }
}
