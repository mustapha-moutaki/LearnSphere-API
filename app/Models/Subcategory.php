<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    // Assuming the table name is 'subcategories'
    protected $table = 'subcategories';

    // Define the relationship to Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
