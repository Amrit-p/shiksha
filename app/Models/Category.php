<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'description', 'slug','category_id','status','image','is_home'];

  public function parent()
  {
    return $this->belongsTo(Category::class, 'category_id');
  }
}
