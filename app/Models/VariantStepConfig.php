<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantStepConfig extends Model
{
    //
    protected $table = 'variant_step_configs';
    protected $fillable = [
        'product_id',
        'step_name',            
        'step_description',
        'step_order',
    ];
}
