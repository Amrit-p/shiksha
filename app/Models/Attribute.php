<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = ['name','slug','status'];

    public function product_option(){
      return $this->hasMany(AttributeOption::class);
    }

    public function options()
    {
        return $this->hasMany(AttributeOption::class, 'attribute_id');
    }

}
