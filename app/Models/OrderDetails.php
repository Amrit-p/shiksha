<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'variant_attribute_id', // ✅ REQUIRED
        'price',
        'qty'
    ];

    // 🔹 Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // 🔹 Order
    public function order()
    {
        return $this->belongsTo(Order::class)->with('user');
    }

    // 🔹 Variant
    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    // 🔹 Variant Attribute ✅ THIS FIXES THE ERROR
    public function variantAttribute()
    {
        return $this->belongsTo(
            VariantAttribute::class,
            'variant_attribute_id'
        );
    }
}
