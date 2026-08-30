<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantStepOption extends Model
{
    //
    protected $table = 'variant_step_options';
    protected $fillable = [
        'variant_step_id',
        'option_name',
        'option_value',
    ];
}
