<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantImage extends Model
{
    protected $fillable = [
        'variant_id',
        'image',
        'status',
    ];

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }
}
