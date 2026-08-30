<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
     protected $fillable = [
        'product_id',
        'variant_id',
        'product_attr_id',
        'variant_attribute_id',
        'user_id',
        'user_type',
        'qty',
        'price',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }   
    public function variant()
    {
        return $this->belongsTo(Variant::class, 'variant_id');
    }
    public function variantAttribute()
    {
        return $this->belongsTo(VariantAttribute::class, 'variant_attribute_id');
    }
    
}


