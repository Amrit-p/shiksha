<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttributePrice extends Model
{
    protected $table = 'product_attribute_prices';

    protected $fillable = [
        'product_id',           // ✅ MUST BE HERE
        'attribute_id',
        'attribute_option_id',
        'price',
        'stock',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function option()
    {
        return $this->belongsTo(AttributeOption::class, 'attribute_option_id');
    }
}
