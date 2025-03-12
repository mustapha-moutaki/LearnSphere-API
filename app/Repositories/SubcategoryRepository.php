<?php
namespace App\Repositories;

use App\Models\Category;

class SubcategoryRepository 
{
    public function getSubcategories($parentId)
    {
        return Category::where('parent_id', $parentId)->get();
    }
}
