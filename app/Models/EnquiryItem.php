<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnquiryItem extends Model
{
    protected $fillable = [
        'enquiry_id',
        'product_id',
        'variant_id',
        'variant_attribute_id',
        'product_title',
        'product_sku',
        'variation_label',
        'product_image',
        'unit_price',
        'qty',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'qty' => 'integer',
            'meta' => 'array',
        ];
    }

    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class);
    }

    public function variantAttribute(): BelongsTo
    {
        return $this->belongsTo(VariantAttribute::class);
    }
}
