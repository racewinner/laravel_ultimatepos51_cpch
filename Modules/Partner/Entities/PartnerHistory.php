<?php

namespace Modules\Partner\Entities;
use \App\User;

use Illuminate\Database\Eloquent\Model;

class PartnerHistory extends Model
{
    protected $table = 'partner_histories';
    protected $guarded = ['id'];

    public function partner() {
        return $this->belongsTo(Partner::class, 'partner_id', 'id');
    }

    public function editor() {
        return $this->belongsTo(User::class, 'editor_id', 'id');
    }
}