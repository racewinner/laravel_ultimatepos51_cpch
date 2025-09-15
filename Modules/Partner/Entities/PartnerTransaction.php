<?php

namespace Modules\Partner\Entities;

use Illuminate\Database\Eloquent\Model;

class PartnerTransaction extends Model
{
    protected $table = 'partner_transactions';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function business() {
        return $this->belongsTo(\App\Business::class, 'business_id', 'id');
    }

    public function partner() {
        return $this->belongsTo(Partner::class, 'partner_id', 'id');
    }

    public function currency() {
        return $this->belongsTo(\App\Currency::class);
    }

    public function payment_lines()
    {
        return $this->hasMany(PartnerTransactionPayment::class, 'transaction_id', 'id');
    }

    public function creator() {
        return $this->belongsTo(\App\User::class, 'created_by', 'id');
    }
}
