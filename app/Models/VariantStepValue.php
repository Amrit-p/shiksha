<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantStepValue extends Model
{
    //
    protected $table = 'variant_step_values';
    protected $fillable = [
        'variant_id',
        'variant_step_id',
        'option_id',
    ];
        
}
