<?php

namespace Modules\Partner\Entities;

use Illuminate\Database\Eloquent\Model;

class BookCategory extends Model
{
    protected $table = 'book_categories';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public static function allBookCategoriesDropdown($business_id) {
        $rows = BookCategory::where('business_id', $business_id)->pluck('name', 'id');
        return $rows;
    }
}
