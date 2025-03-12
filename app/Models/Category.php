<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Category extends Model
{
    use HasFactory;
    protected $fillable =['name', 'parent_id'];


    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('children');;
    }


    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }
}
