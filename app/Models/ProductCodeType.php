<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCodeType extends Model
{
    use SoftDeletes;


    protected $fillable = [
        'name',
        'status',
    ];

    /**
     * Variants that use this product code type (assignment is on variants table).
     */
    public function variants()
    {
        return $this->hasMany(Variant::class, 'product_code_type_id');
    }
}
