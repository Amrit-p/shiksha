<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VariantAttribute extends Model
{

 use SoftDeletes;
    protected $fillable = [
    'variant_id',
    'attribute_id',
    'attribute_option_id',
    'attribute_value',
    'mrp',
    'sell_price',
    'image',
    'product_code_type_id',
    'status',
    'is_por',  // Price on Request flag
    'stock',   // Stock per attribute option (e.g. per color: White, Black, Golden)
    'min_stock', // Alert when stock below this (0 = no alert)

];
    protected $dates = ['deleted_at'];

    
    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function option()
    {
        return $this->belongsTo(AttributeOption::class, 'attribute_option_id');
    }

     /**
     * ✅ NEW: Check if this attribute is Price on Request
     */
    public function isPriceOnRequest()
    {
        return (bool) $this->is_por;
    }

    /**
     * ✅ NEW: Get display price or POR indicator
     */
    public function getDisplayPrice()
    {
        if ($this->is_por) {
            return 'POR'; // Price on Request
        }

        return $this->sell_price ?? $this->mrp;
    }

    /**
     * Check if this attribute option has stock available.
     */
    public function inStock(): bool
    {
        return (int) ($this->stock ?? 0) > 0;
    }

    /**
     * Check if given quantity can be fulfilled.
     */
    public function hasStockFor(int $qty): bool
    {
        return (int) ($this->stock ?? 0) >= $qty;
    }
}
