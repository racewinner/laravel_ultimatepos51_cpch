<?php

namespace Modules\Partner\Entities;

use Illuminate\Database\Eloquent\Model;

class PartnerCategory extends Model
{
    protected $table = 'partner_categories';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public static function allPartnerCategoriesDropdown($business_id) {
        $all_categories = PartnerCategory::where('business_id', $business_id)->orderBy('id')->pluck('detail', 'id');
        return $all_categories;
    }

    public function getServicesAttribute() {
        return explode(',', $this->service_ids);
    }
}
