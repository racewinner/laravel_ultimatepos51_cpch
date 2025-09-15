<?php

namespace Modules\Partner\Entities;

use Illuminate\Database\Eloquent\Model;

class PartnerBooking extends Model
{
    protected $table = 'partner_bookings';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function salon()
    {
        return $this->belongsTo(Salon::class);
    }

    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }
}
