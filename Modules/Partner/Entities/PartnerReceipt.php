<?php

namespace Modules\Partner\Entities;

use Illuminate\Database\Eloquent\Model;
use \Carbon\Carbon;

class PartnerReceipt extends Model
{
    protected $table = 'partner_receipts';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id', 'id');
    }

    public function getServicesAttribute()
    {
        $services = Service::whereIn('id', explode(',', $this->service_ids))->get();
        return $services;
    }

    public function getPeriodAttribute()
    {
        $f_m = Carbon::parse($this->from_month)->format('m/Y');
        $t_m = Carbon::parse($this->to_month)->format('m/Y');

        return ($f_m == $t_m) ? $f_m : ($f_m . "-" . $t_m);
    }

    public function getMonthsAttribute()
    {
        $f_m = Carbon::parse($this->from_month);
        $t_m = Carbon::parse($this->to_month);

        $interval = $f_m->diff($t_m);
        $months = $interval->y * 12 + $interval->m + 1;

        return $months;
    }

    public function currency()
    {
        return $this->belongsTo(\App\Currency::class);
    }

    public function editor()
    {
        return $this->belongsTo(\App\User::class, 'editor_id', 'id');
    }
}
