<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeImage extends Model
{
    protected $fillable = [
        'product_id',
        'attribute_id',
        'image',
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
}
