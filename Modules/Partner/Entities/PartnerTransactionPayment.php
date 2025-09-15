<?php

namespace Modules\Partner\Entities;

use Illuminate\Database\Eloquent\Model;

class PartnerTransactionPayment extends Model
{
    protected $table = 'partner_transaction_payments';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function service() {
        return $this->belongsTo(Service::class);
    }

    public function transaction() {
        return $this->belongsTo(PartnerTransaction::class);
    }

    public function getPayMonthsAttribute() {
        $f_month = \Carbon\Carbon::parse($this->from_month)->format('m/Y'); 
        $t_month = \Carbon\Carbon::parse($this->to_month)->format('m/Y'); 
        return $f_month != $t_month ? "$f_month - $t_month" : $f_month;
    }
}
