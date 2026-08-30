<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Attribute;

class AttributeOption extends Model
{
    protected $fillable = ['name','slug','attribute_id','status'];
    public function attribute(){
      return $this->belongsTo(Attribute::class);
    }
}
