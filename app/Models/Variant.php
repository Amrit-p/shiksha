<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class Variant extends Model
{


    protected $fillable = [
        'product_id',
        'catalog_number',
        'sku',
        'cct',
        'wattage',
        'body_color',
        'voltage',
        'dimension',
        'material',
        'color',
        'weight',
        'outer_diameter',
        'inner_diameter',
        'height',
        'mrp',
        'price',
        'stock',
        'image',
        'status',
        'sell_price',
        'name',
        'variant_name_id',
        'unit_id',
        'product_code_type_id',
        'variant_description',

    ];

    // Variant.php
    public function bodyColor()
    {
        return $this->belongsTo(Color::class, 'body_color');
    }

    public function images()
    {
        return $this->hasMany(\App\Models\VariantImage::class);
    }


    public function attributes()
    {
        // /return $this->hasMany(VariantAttribute::class);
        return $this->hasMany(VariantAttribute::class, 'variant_id');

    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function variantname()
    {
        return $this->belongsTo(VariantName::class, 'variant_name_id');
    }




    /**
     * Check if variant has a code type
     */
    public function hasCodeType()
    {
        return !is_null($this->product_code_type_id);
    }

    /**
     * Get code type name or default
     */
    public function getCodeTypeNameAttribute()
    {
        return $this->productCodeType ? $this->productCodeType->name : 'Standard';
    }

    /* ========================================
       SCOPES
       ======================================== */

    /**
     * Scope to get only active variants
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope to get variants with a specific code type
     */
    public function scopeWithCodeType($query, $codeTypeId)
    {
        return $query->where('product_code_type_id', $codeTypeId);
    }

        /**
     * Scope to filter by product
     */
    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

     /**
     * Scope to filter by sku
     */
    public function scopeBySku($query, $sku)
    {
        return $query->where('sku', $sku);
    }



    /**
     * Scope to get variants without any code type
     */
    public function scopeWithoutCodeType($query)
    {
        return $query->whereNull('product_code_type_id');
    }

    /**
     * Scope to get variants in stock
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function productCodeType()
    {
        return $this->belongsTo(ProductCodeType::class, 'product_code_type_id');
    }

    /**
     * Get the unit for this variant.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }


    /**
     * ===================================
     * NEW RELATIONSHIPS FOR 4-5 STEP VARIANTS
     * ===================================
     */

    /**
     * Get step values (step 3 and 4) for this variant
     */
    public function stepValues()
    {
        return $this->hasMany(VariantStepValue::class);
    }

    /**
     * Get the display value for a specific step number
     * 
     * @param int $stepNumber (3 or 4)
     * @return string|null
     */
    public function getStepValue($stepNumber)
    {
        $stepValue = $this->stepValues()
            ->where('step_number', $stepNumber)
            ->first();

        return $stepValue ? $stepValue->getDisplayValue() : null;
    }

// ... other relationships



    /**
     * ✅ NEW: Get custom fields for this variant
     */
    public function customFields()
    {
        return $this->hasMany(VariantCustomField::class, 'variant_id')->orderBy('field_order');
    }

    

    /**
     * ✅ NEW: Get custom field value by name
     */
     public function getCustomField($fieldTypeName)
    {
        $field = $this->customFields()
            ->whereHas('customFieldType', function($q) use ($fieldTypeName) {
                $q->where('name', $fieldTypeName);
            })
            ->first();
        
        return $field ? $field->field_value : null;
    }

    /**
     * ✅ Get custom field value by field type ID
     */
    public function getCustomFieldById($fieldTypeId)
    {
        $field = $this->customFields()
            ->where('custom_field_type_id', $fieldTypeId)
            ->first();
        
        return $field ? $field->field_value : null;
    }

     /**
     * Get variant details with all relations
     */
    public function scopeWithDetails($query)
    {
        return $query->with([
            'variantname',
            'productCodeType',
            'unit',
            'customFields.customFieldType',
            'attributes.option.attribute',
            'stepValues',
        ]);
    }
}
