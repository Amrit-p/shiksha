<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantCustomField extends Model
{  
   

    protected $table = 'variant_custom_fields';
    protected $fillable = [
        'variant_id',
        'custom_field_type_id',    // ✅ CORRECT!
        'field_value',
        'field_order',
    ];
    /**
     * Get the variant that owns this custom field
     */
   public function variant()
    {
        return $this->belongsTo(
            Variant::class,
            'variant_id',
            'id'
        );
    }

    public function customFieldType()
    {
        return $this->belongsTo(CustomFieldType::class, 'custom_field_type_id', 'id');
    }

    /**
     * Scope to get fields ordered by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('field_order', 'asc');
    }

    /**
     * Scope to filter by custom field type
     */
    public function scopeByFieldType($query, $fieldTypeId)
    {
        return $query->where('custom_field_type_id', $fieldTypeId);
    }

    /**
     * Get the display name for the field
     */
    public function getDisplayNameAttribute()
    {
        return $this->customFieldType?->name ?? 'Unknown Field';
    }
}