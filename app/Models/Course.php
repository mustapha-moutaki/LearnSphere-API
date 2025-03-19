<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Course extends Model
{
    use HasFactory;
    
    protected $fillable =['title', 'category_id', 'description', 'video_url'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

  
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
