<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomFieldType extends Model
{
    protected $table = 'custom_field_types';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'field_type',  // text, dropdown, number, etc.
        'is_dependent_on',  // ID of parent custom field type
        'display_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'display_order' => 'integer',
        'is_dependent_on' => 'integer',
    ];

    /**
     * Boot method to auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });

        static::updating(function ($model) {
            $model->slug = Str::slug($model->name);
        });
    }

    /**
     * Get variant custom fields using this type
     */
    public function variantCustomFields()
    {
        return $this->hasMany(VariantCustomField::class, 'custom_field_type_id', 'id')->orderBy('field_order');
    }

    /**
     * ✅ Get options for this custom field type (for dropdowns)
     */
    // public function options()
    // {
    //     return $this->hasMany(CustomFieldOption::class, 'custom_field_type_id', 'id')
    //                 ->orderBy('display_order')
    //                 ->orderBy('label');
    // }

    /**
     * ✅ Get the parent custom field type this depends on
     */
    public function dependsOn()
    {
        return $this->belongsTo(CustomFieldType::class, 'is_dependent_on', 'id');
    }

    /**
     * ✅ Get child custom field types that depend on this one
     */
    public function dependentFields()
    {
        return $this->hasMany(CustomFieldType::class, 'is_dependent_on', 'id');
    }

    /**
     * Scope: Get only active field types
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope: Get ordered by display_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

    /**
     * Scope: Get only independent fields (not dependent on others)
     */
    public function scopeIndependent($query)
    {
        return $query->whereNull('is_dependent_on');
    }

    /**
     * Scope: Get only dependent fields
     */
    public function scopeDependent($query)
    {
        return $query->whereNotNull('is_dependent_on');
    }
}