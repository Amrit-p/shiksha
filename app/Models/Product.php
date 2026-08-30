<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
  protected $fillable = [
    'title',
    'slug',
    'wattage',
    'category_id',
    'short_description',
    'description',
    'price',
    'sale_price',
    'status',
    'image',
    'is_featured',
    'is_discounted',
    'is_tranding',
    'stock',
    'min_stock',
    'product_type',
    'mrp',
    'sale_price',
    'stock',
    'unit_id',

  ];
  protected $dates = ['deleted_at'];

  #One Product belongs to one Category
  public function category(){
    return $this->belongsTo(Category::class);
  }

  #One Product has many gallery images
  public function gallary_images()
  {
    return $this->hasMany(ProductImage::class);
  }
  public function variants()
  {
    return $this->hasMany(Variant::class)->with('bodyColor');
  }
  /**
   * RAW VARIANTS
   * (No eager loading — use for advanced cases)
   */
  public function productVariants(): HasMany
  {
      return $this->hasMany(Variant::class);
  }
    /**
     * CASE 2: Product → Attribute-only prices
     * (product_type = 2)
     */
    public function attributePrices(): HasMany
    {
        return $this->hasMany(ProductAttributePrice::class);
    }
    public function attributeImages()
{
    return $this->hasMany(ProductAttributeImage::class);
}
 
// App\Models\Product.php

public function unit()
{
    return $this->belongsTo(Unit::class);
}

/**
 * ===================================
 * NEW RELATIONSHIPS FOR 4-5 STEP VARIANTS
 * ===================================
 */

/**
 * Get the step configuration for this product (if using 4-5 steps)
 */
public function stepConfig()
{
    return $this->hasOne(VariantStepConfig::class);
}

/**
 * Check if this product uses extended variant steps (4-5 steps)
 * Returns true only if product has step config with total_steps >= 4
 */
public function hasExtendedSteps()
{
    return $this->stepConfig && $this->stepConfig->total_steps >= 4;
}

/**
 * Get total number of variant steps for this product
 * Returns 2 by default (backward compatible with existing products)
 * Returns 3, 4, or 5 if product has step configuration
 */
public function getTotalSteps()
{
    return $this->stepConfig ? $this->stepConfig->total_steps : 2;
}
}
