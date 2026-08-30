<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Color;
use App\Models\AttributeOption;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Variant;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use App\Models\VariantImage;
use App\Helpers\FileHelper;
use App\Models\ProductCodeType;
use App\Models\VariantAttribute;
use App\Models\ProductAttributePrice;
use App\Models\ProductAttributeImage;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\VariantName;
use Illuminate\Validation\Rules\Unique;
use App\Models\VariantStepConfig;
use App\Models\VariantStepOption;
use App\Models\VariantStepValue;
use App\Models\VariantValue;
use App\Models\VariantValuePrice;
use App\Models\VariantValueImage;
use App\Models\VariantCustomField;
use App\Models\CustomFieldType;

class ProductController extends Controller
{
  public function index()
  {
    return view('admin.product.index');
  }
  public function getProductList(Request $request)
  {
    $query = Product::query()->with('category')->orderBy('id', 'desc');

    $searchValue = $request->get('search')['value'] ?? '';
    if (strlen(trim($searchValue)) > 0) {
      $q = trim($searchValue);
      $query->where(function ($qb) use ($q) {
        $qb->where('products.title', 'like', '%' . $q . '%')
          ->orWhere('products.slug', 'like', '%' . $q . '%')
          ->orWhereHas('category', function ($c) use ($q) {
            $c->where('name', 'like', '%' . $q . '%');
          })
          ->orWhereHas('variants', function ($v) use ($q) {
            $v->where('name', 'like', '%' . $q . '%')
              ->orWhere('sku', 'like', '%' . $q . '%');
          });
      });
    }

    return DataTables::of($query)
      ->addColumn('category', function ($data) {
        return ($data->category) ? $data->category->name : "--";
      })
      // ->addColumn('action', function ($data) {
      // //  <a href="#productView" data-toggle="modal" data-target="#productView"><i class="ik ik-eye f-16 mr-15"></i></a>
      //   return '
      //    <a href="'.route('admin.product.edit',$data->id). '"><i class="ik ik-edit f-16 mr-15 text-green"></i></a>
      //   <a  href="javascript:void(0)" data-id="' . $data->id . '" class="delete_btn"><i class="ik ik-trash-2 f-16 text-red"></i></a>';
      // })


      ->addColumn('action', function ($data) {
        return '<div class="dropdown d-inline-block">
        <a class="nav-link dropdown-toggle" href="#" id="moreDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="ik ik-more-vertical"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right">


          <a class="dropdown-item" href="' . route('admin.product.edit', $data->id) . '">
              <i class="ik ik-edit"></i>
              Edit
          </a>



            <a class="dropdown-item delete_btn" data-id="' . $data->id . '" href="javascript:void(0)">
              <i class="ik ik-trash"></i> Delete </a>
        </div>
    </div>';
      })
      ->addColumn('checkbox', function ($data) {
        return '<label class="custom-control custom-checkbox">
          <input type="checkbox" class="custom-control-input select_all_child" id="" name="" value="option2">
          <span class="custom-control-label">&nbsp;</span>
        </label>';
      })
      ->rawColumns(['checkbox', 'action'])
      ->make(true);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $categories = Category::where('status', 1)->get();
    $units = Unit::where('status', 1)->get();
    $variants = VariantName::where('status', 1)->get();
    $productCodeTypes = ProductCodeType::where('status', 1)->get();
    $attributes = Attribute::where('status', 1)->get();

    // Build attribute options map
    $optionMap = [];
    foreach ($attributes as $attr) {
      $optionMap[$attr->id] = AttributeOption::where('attribute_id', $attr->id)
        ->where('status', 1)
        ->get(['id', 'name'])
        ->toArray();
    }

    // ✅ GET CUSTOM FIELD TYPES
    $customFieldTypes = CustomFieldType::where('status', 1)
      ->orderBy('display_order')
      ->orderBy('name')
      ->get(['id', 'name']);

    return view('admin.product.create', compact(
      'categories',
      'units',
      'variants',
      'productCodeTypes',
      'attributes',
      'optionMap',
      'customFieldTypes'  // ✅ PASS TO VIEW
    ));
  }

  // public function store(Request $request)
  // {
  //   // return $request->all();
  //   DB::beginTransaction();
  //   $product_image_path = null;
  //   try {
  //     if ($request->hasFile('images')) {
  //       $product_image_path = $request->file('images')
  //         ->store('products', 'public');
  //     }

  //     $product = Product::create([
  //       'title'             => $request->title,
  //       'slug'              => Str::slug($request->title),
  //       'category_id'       => $request->category_id,
  //       'short_description' => $request->short_description,
  //       'description'       => $request->description,
  //       'price'             => $request->price ?? 0,
  //       'sale_price'        => $request->sale_price,
  //       'status'            => 1,
  //       'image'             => $product_image_path, // null or path
  //     ]);

  //     // 2. Store Product Images (multiple)
  //     if ($request->hasFile('product_images')) {
  //       $images = $request->file('product_images'); // array of UploadedFile
  //       foreach ($images as $image) {
  //         if ($image->isValid()) {
  //           $path = $image->store('products', 'public');

  //           ProductImage::create([
  //             'product_id'    => $product->id,
  //             'product_images' => $path,
  //           ]);
  //         }
  //       }
  //     }

  //     // 3. Store Variants (one image per variant)
  //     if ($request->has('variants')) {
  //       foreach ($request->variants as $index => $variant) {
  //         $variantImage = null;
  //         // Handle variant images (array input)
  //         if (isset($variant['images']) && is_array($variant['images'])) {
  //           $firstImage = $variant['images'][0] ?? null;
  //           if ($firstImage instanceof \Illuminate\Http\UploadedFile) {
  //             $variantImage = $firstImage->store('variants', 'public');
  //           }
  //         }

  //         // return "outer image;";

  //         $variantModel =   Variant::create([
  //           'product_id'     => $product->id,
  //           'catalog_number' => $variant['catalog_no'] ?? null,
  //           'sku'            => $variant['sku'] ?? null,

  //           #attribute data
  //           'wattage'        => $variant['wattage'] ?? null,
  //           'body_color'     => $variant['body_color'] ?? null,
  //           'cct'            => $variant['cct'] ?? null,


  //           'voltage'        => $variant['voltage'] ?? null,
  //           'dimension'      => $variant['dimension'] ?? null,
  //           'material'       => $variant['material'] ?? null,
  //           'color'          => $variant['color'] ?? null,
  //           'weight'         => $variant['weight'] ?? null,

  //           'outer_diameter' => $variant['outer_diameter'] ?? null,
  //           'inner_diameter' => $variant['inner_diameter'] ?? null,
  //           'height'         => $variant['height'] ?? null,

  //           'mrp'            => $variant['mrp'] ?? null,
  //           'price'          => $variant['price'] ?? null,
  //           'stock'          => $variant['stock'] ?? 0,

  //           'image'          => $variantImage,
  //           'status'         => 1,
  //         ]);

  //         // VARIANT IMAGES
  //         if (!empty($variant['images'])) {
  //           foreach ($variant['images'] as $img) {
  //             $path = $img->store('variants', 'public');

  //             VariantImage::create([
  //               'variant_id' => $variantModel->id,
  //               'image'      => $path,
  //             ]);
  //           }
  //         }
  //       }
  //     }

  //     DB::commit();

  //     return redirect()
  //       ->route('admin.product')
  //       ->with('success', 'Product created successfully');
  //   } catch (\Exception $e) {
  //     DB::rollBack();
  //     return $e->getMessage();
  //     // You can log $e->getMessage() if needed
  //     return back()->with('error', 'Something went wrong: ' . $e->getMessage());
  //   }
  // }
  /**
   * Store a newly created product in storage
   */
  /**
   * Store a newly created product in storage
   * WITH POR (Price on Request) SUPPORT
   */
  /**
   * Store a newly created product
   */
  public function store(CreateProductRequest $request)
  {
    DB::beginTransaction();

    try {
      /* PRODUCT IMAGE */
      $productImage = null;
      if ($request->hasFile('images')) {
        $productImage = FileHelper::upload(
          $request->file('images'),
          'products'
        );
      }

      /* CREATE PRODUCT */
      $product = Product::create([
        'title'             => $request->title,
        'slug'              => Str::slug($request->title),
        'category_id'       => $request->category_id,
        'product_type'      => (int) $request->product_type,
        'short_description' => $request->short_description,
        'description'       => $request->description,
        'image'             => $productImage,
        'status'            => 1,
        'unit_id'           => $request->unit_id ?? null,
      ]);

      /* PRODUCT GALLERY IMAGES */
      if ($request->hasFile('product_images')) {
        foreach ($request->file('product_images') as $image) {
          if ($image->isValid()) {
            $path = FileHelper::upload($image, 'product-gallery');
            ProductImage::create([
              'product_id'      => $product->id,
              'product_images'  => $path,
            ]);
          }
        }
      }

      /* SIMPLE PRODUCT */
      if ((int) $request->product_type === 1) {
        $product->update([
          'price'      => $request->simple['price'],
          'sale_price' => $request->simple['sale_price'] ?? null,
          'stock'      => $request->simple['stock'] ?? 0,
          'min_stock'  => (int) ($request->simple['min_stock'] ?? 0),
        ]);
      }

      /* VARIANT PRODUCT */
      if ((int) $request->product_type === 3 && $request->variants) {

        foreach ($request->variants as $variantData) {

          /* Variant Image */
          $variantImage = null;
          if (!empty($variantData['image'])) {
            $variantImage = FileHelper::upload(
              $variantData['image'],
              'variant-images'
            );
          }

          /* Get Variant Name */
          $variantName = VariantName::where('id', $variantData['variant_name_id'])
            ->where('status', 1)
            ->first();

          /* Create Variant */
          $variant = Variant::create([
            'product_id'           => $product->id,
            'variant_name_id'      => $variantData['variant_name_id'] ?? null,
            'product_code_type_id' => $variantData['product_code_type_id'] ?? null,
            'name'                 => $variantName->name ?? null,
            'sku'                  => $variantData['sku'],
            'variant_description'   => $variantData['variant_description'] ?? null,
            'stock'                => $variantData['stock'] ?? 0,
            'image'                => $variantImage,
            'status'               => 1,
            'unit_id'              => $request->unit_id ?? null,
          ]);

          // ✅ CREATE CUSTOM FIELD VALUES
          if (!empty($variantData['custom_fields'])) {
            foreach ($variantData['custom_fields'] as $fieldTypeId => $fieldValue) {
              if (empty($fieldValue)) {
                continue;
              }

              VariantCustomField::create([
                'variant_id'           => $variant->id,
                'custom_field_type_id' => (int) $fieldTypeId,
                'field_value'          => $fieldValue,
                'field_order'          => 0,
              ]);
            }
          }

          /* VARIANT ATTRIBUTES WITH POR SUPPORT */
          if (!empty($variantData['attributes'])) {
            foreach ($variantData['attributes'] as $attr) {

              $attrImage = null;
              if (isset($attr['image']) && $attr['image'] instanceof \Illuminate\Http\UploadedFile) {
                $attrImage = FileHelper::upload(
                  $attr['image'],
                  'variant-attribute-images'
                );
              }

              // ✅ CHECK IF POR
              $isPor = isset($attr['is_por']) && $attr['is_por'] == 1;

              VariantAttribute::create([
                'variant_id'          => $variant->id,
                'attribute_id'        => $attr['attribute_id'],
                'attribute_option_id' => $attr['attribute_option_id'],
                'mrp'                 => $isPor ? null : ($attr['mrp'] ?? null),
                'sell_price'          => $isPor ? null : ($attr['sell_price'] ?? null),
                'is_por'              => $isPor ? 1 : 0,
                'stock'               => (int) ($attr['stock'] ?? 0),
                'min_stock'           => (int) ($attr['min_stock'] ?? 0),
                'image'               => $attrImage,
              ]);
            }
          }
        }
      }

      DB::commit();

      return redirect()
        ->route('admin.product')
        ->with('success', 'Product created successfully');
    } catch (\Throwable $e) {
      DB::rollBack();
      return back()->withErrors($e->getMessage())->withInput();
    }
  }

  /**
   * Show the form for editing the specified product
   */
  /**
   * ✅ COMPLETE WORKING edit() METHOD - CORRECTED
   */
  public function edit($id)
  {
    // ✅ FETCH PRODUCT WITH ALL RELATIONSHIPS - FIXED!
    $product = Product::with([
      'variants.attributes',
      'variants.customFields.customFieldType',  // ✅ COMPLETE relationship chain!
      'gallary_images'
    ])->findOrFail($id);  // ✅ Use findOrFail() instead of whereId()->first()
    // ✅ FETCH ALL REQUIRED DATA
    $categories = Category::where('status', 1)->get();
    $units = Unit::where('status', 1)->get();
    $variants = VariantName::where('status', 1)->get();
    $productCodeTypes = ProductCodeType::where('status', 1)->get();
    $attributes = Attribute::where('status', 1)->get();

    // ✅ BUILD ATTRIBUTE OPTIONS MAP
    $optionMap = [];
    foreach ($attributes as $attr) {
      $optionMap[$attr->id] = AttributeOption::where('attribute_id', $attr->id)
        ->where('status', 1)
        ->get(['id', 'name'])
        ->toArray();
    }

    // ✅ FETCH CUSTOM FIELD TYPES (CRITICAL!)
    $customFieldTypes = CustomFieldType::where('status', 1)
      ->orderBy('display_order')
      ->orderBy('name')
      ->get(['id', 'name']);

    // ✅ RETURN VIEW WITH ALL DATA
    return view('admin.product.edit', compact(
      'product',
      'categories',
      'units',
      'variants',
      'productCodeTypes',
      'attributes',
      'optionMap',
      'customFieldTypes'  // ✅ CRITICAL: Pass to view!
    ));
  }


  // public function update(Request $request, $id)
  // {
  //   // return $request->all();
  //   DB::beginTransaction();
  //   try {
  //     $product = Product::findOrFail($id);
  //     /* ---------- MAIN PRODUCT IMAGE ---------- */
  //     $product_image_path = $product->image;

  //     if ($request->hasFile('images')) {
  //       if ($product->image && Storage::disk('public')->exists($product->image)) {
  //         Storage::disk('public')->delete($product->image);
  //       }

  //       $product_image_path = $request->file('images')
  //         ->store('products', 'public');
  //     }

  //     /* ---------- UPDATE PRODUCT ---------- */
  //     $product->update([
  //       'title'             => $request->title,
  //       'slug'              => Str::slug($request->title),
  //       'category_id'       => $request->category_id,
  //       'short_description' => $request->short_description,
  //       'description'       => $request->description,
  //       'price'             => $request->price ?? 0,
  //       'sale_price'        => $request->sale_price,
  //       'is_featured'       => $request->is_featured,
  //       'is_discounted'     => $request->is_discounted,
  //       'is_tranding'       => $request->is_tranding,
  //       'image'             => $product_image_path,
  //     ]);

  //     /* ---------- PRODUCT GALLERY IMAGES ---------- */
  //     if ($request->hasFile('product_images')) {
  //       foreach ($request->file('product_images') as $image) {
  //         if ($image->isValid()) {
  //           ProductImage::create([
  //             'product_id'     => $product->id,
  //             'product_images' => $image->store('products', 'public'),
  //           ]);
  //         }
  //       }
  //     }

  //     /* ---------- VARIANTS ---------- */
  //     // SIMPLE & SAFE: remove old variants and re-insert
  //     $product->variants()->delete();

  //     if ($request->has('variants')) {
  //       foreach ($request->variants as $index => $variant) {

  //         $variantImage = null;

  //         // Handle variant images (first image only — same as store)
  //         if (isset($variant['images']) && is_array($variant['images'])) {
  //           $firstImage = $variant['images'][0] ?? null;
  //           if ($firstImage instanceof \Illuminate\Http\UploadedFile) {
  //             $variantImage = $firstImage->store('variants', 'public');
  //           }
  //         }

  //         Variant::create([
  //           'product_id'     => $product->id,
  //           'catalog_number' => $variant['catalog_no'] ?? null,
  //           'sku'            => $variant['sku'] ?? null,

  //           'wattage'        => $variant['wattage'] ?? null,
  //           'cct'            => $variant['cct'] ?? null,
  //           'body_color'     => $variant['body_color'] ?? null,

  //           'voltage'        => $variant['voltage'] ?? null,
  //           'dimension'      => $variant['dimension'] ?? null,
  //           'material'       => $variant['material'] ?? null,
  //           'color'          => $variant['color'] ?? null,
  //           'weight'         => $variant['weight'] ?? null,

  //           'outer_diameter' => $variant['outer_diameter'] ?? null,
  //           'inner_diameter' => $variant['inner_diameter'] ?? null,
  //           'height'         => $variant['height'] ?? null,

  //           'mrp'            => $variant['mrp'] ?? null,
  //           'price'          => $variant['price'] ?? null,
  //           'stock'          => $variant['stock'] ?? 0,

  //           'image'          => $variantImage,
  //           'status'         => 1,
  //         ]);
  //       }
  //     }

  //     DB::commit();

  //     return redirect()
  //       ->route('admin.product')
  //       ->with('success', 'Product updated successfully');
  //   } catch (\Exception $e) {
  //     return $e;
  //     DB::rollBack();
  //     return back()->with('error', 'Something went wrong: ' . $e->getMessage());
  //   }
  // }

  public function update(UpdateProductRequest $request, $id)
  {
    DB::beginTransaction();

    try {

      /* =============================
                   NORMALIZE PRODUCT TYPE
                ============================== */
      $productType = (int) $request->product_type;

      /* =============================
                   FETCH PRODUCT
                ============================== */
      $product = Product::with([
        'variants.attributes',
        'variants.customFields',
        'gallary_images'
      ])->findOrFail($id);

      /* =============================
                   MAIN IMAGE
                ============================== */
      if ($request->hasFile('images')) {
        if ($product->image && Storage::disk('public')->exists($product->image)) {
          Storage::disk('public')->delete($product->image);
        }
        $product->image = $request->file('images')->store('products', 'public');
      }

      /* =============================
                   UPDATE PRODUCT
                ============================== */
      $product->update([
        'title'             => $request->title,
        'slug'              => \Str::slug($request->title),
        'category_id'       => $request->category_id,
        'product_type'      => $productType,
        'short_description' => $request->short_description,
        'description'       => $request->description,
        'unit_id'           => $request->unit_id,
      ]);

      /* =============================
                   SIMPLE PRODUCT
                ============================== */
      if ($productType === 1) {
        // Delete all variants + their attributes + their custom fields + their images
        foreach ($product->variants as $variant) {
          // ✅ DELETE CUSTOM FIELDS
          $variant->customFields()->delete();

          foreach ($variant->attributes as $attr) {
            if ($attr->image && Storage::disk('public')->exists($attr->image)) {
              Storage::disk('public')->delete($attr->image);
            }
            $attr->delete();
          }

          if ($variant->image && Storage::disk('public')->exists($variant->image)) {
            Storage::disk('public')->delete($variant->image);
          }

          $variant->delete();
        }

        $product->update([
          'price'      => $request->simple['price'],
          'sale_price' => $request->simple['sale_price'] ?? null,
          'stock'      => $request->simple['stock'] ?? 0,
          'min_stock'  => (int) ($request->simple['min_stock'] ?? 0),
        ]);
      }

      /* =============================
                   VARIANT PRODUCT
                ============================== */
      if ($productType === 3 && $request->variants) {
        $incomingVariantIds = [];

        \Log::info('ProductController@update: variants payload', [
          'variant_count' => count($request->variants),
          'each_attr_count' => array_map(function ($v) {
            return count($v['attributes'] ?? []);
          }, $request->variants),
        ]);

        foreach ($request->variants as $vIdx => $variantData) {
          /* ---------- VARIANT IMAGE ---------- */
          $variantImage = null;
          if (!empty($variantData['image'])) {
            $variantImage = $variantData['image']->store('variant-images', 'public');
          }

          $variantName = VariantName::where('id', $variantData['variant_name_id'])
            ->where('status', 1)
            ->first();

          /* ---------- UPDATE / CREATE VARIANT ---------- */
          $variant = Variant::updateOrCreate(
            [
              'id'         => $variantData['id'] ?? null,
              'product_id' => $product->id,
            ],
            [
              'name'                 => $variantName->name ?? null,
              'variant_name_id'      => $variantData['variant_name_id'],
              'sku'                  => $variantData['sku'],
              'stock'                => $variantData['stock'] ?? 0,
              'product_code_type_id' => $variantData['product_code_type_id'] ?? null,
              'variant_description'   => $variantData['variant_description'] ?? null,
              'image'                => $variantImage ?? optional(
                Variant::find($variantData['id'] ?? 0)
              )->image,
              'status'               => 1,
            ]
          );

          $incomingVariantIds[] = $variant->id;

          /* ---------- ✅ CUSTOM FIELDS ---------- */
          if (!empty($variantData['custom_fields'])) {
            // Delete custom fields not in incoming data
            $incomingFieldTypeIds = array_keys($variantData['custom_fields']);

            $variant->customFields()
              ->whereNotIn('custom_field_type_id', $incomingFieldTypeIds)
              ->delete();

            // Create or update custom fields
            foreach ($variantData['custom_fields'] as $fieldTypeId => $fieldValue) {
              // Skip empty values
              if (empty($fieldValue)) {
                continue;
              }

              // Type cast field type ID to integer
              $fieldTypeId = (int) $fieldTypeId;

              // Update or create the custom field
              VariantCustomField::updateOrCreate(
                [
                  'variant_id' => $variant->id,
                  'custom_field_type_id' => $fieldTypeId,
                ],
                [
                  'field_value' => $fieldValue,
                  'field_order' => 0,
                ]
              );
            }
          } else {
            // If no custom fields provided, delete all existing ones
            $variant->customFields()->delete();
          }

          /* ---------- ATTRIBUTES ---------- */
          $incomingAttrIds = [];
          $attrs = $variantData['attributes'] ?? [];

          \Log::info('ProductController@update: processing attributes for variant ' . $variant->id, [
            'attr_count' => count($attrs),
            'attrs'      => array_map(function ($a, $i) {
              return ['i' => $i, 'id' => $a['id'] ?? null, 'attribute_id' => $a['attribute_id'] ?? null, 'attribute_option_id' => $a['attribute_option_id'] ?? null];
            }, $attrs, array_keys($attrs)),
          ]);

          foreach ($attrs as $attr) {
            $attrImage = null;
            if (!empty($attr['image'])) {
              $attrImage = $attr['image']->store('variant-attribute-images', 'public');
            }
            $isPor = isset($attr['is_por']) && $attr['is_por'] == 1;

            // Fix: new attribute row sometimes doesn't submit attribute_option_id (e.g. JS clone/reindex).
            // Use first option for this attribute when option is missing but attribute_id is present.
            $attributeOptionId = $attr['attribute_option_id'] ?? null;
            if ((empty($attributeOptionId) || $attributeOptionId === '') && !empty($attr['attribute_id'])) {
              $firstOption = AttributeOption::where('attribute_id', $attr['attribute_id'])
                ->where('status', 1)
                ->orderBy('id')
                ->first();
              if ($firstOption) {
                $attributeOptionId = $firstOption->id;
                \Log::warning('ProductController@update: attribute_option_id was missing for attribute_id ' . $attr['attribute_id'] . ', used first option ' . $attributeOptionId);
              }
            }

            try {
              $va = VariantAttribute::updateOrCreate(
                [
                  'id'         => $attr['id'] ?? null,
                  'variant_id' => $variant->id,
                ],
                [
                  'attribute_id'        => $attr['attribute_id'],
                  'attribute_option_id' => $attributeOptionId,
                  'mrp'                 => $isPor ? null : ($attr['mrp'] ?? null),
                  'sell_price'          => $isPor ? null : ($attr['sell_price'] ?? null),
                  'is_por'              => $isPor,
                  'stock'               => (int) ($attr['stock'] ?? 0),
                  'min_stock'           => (int) ($attr['min_stock'] ?? 0),
                  'image'               => $attrImage ?? optional(
                    VariantAttribute::find($attr['id'] ?? 0)
                  )->image,
                ]
              );
              $incomingAttrIds[] = $va->id;
              \Log::info('ProductController@update: attribute saved', ['id' => $va->id, 'attribute_id' => $va->attribute_id, 'attribute_option_id' => $va->attribute_option_id]);
            } catch (\Throwable $attrEx) {
              \Log::error('ProductController@update: attribute save failed', [
                'attr' => $attr,
                'error' => $attrEx->getMessage(),
                'trace' => $attrEx->getTraceAsString(),
              ]);
              throw $attrEx;
            }
          }

          /* ---------- DELETE REMOVED ATTRIBUTES ---------- */
          $variant->attributes()
            ->whereNotIn('id', $incomingAttrIds)
            ->each(function ($a) {
              if ($a->image && Storage::disk('public')->exists($a->image)) {
                Storage::disk('public')->delete($a->image);
              }
              $a->delete();
            });
        }

        /* ---------- DELETE REMOVED VARIANTS ---------- */
        $product->variants()
          ->whereNotIn('id', $incomingVariantIds)
          ->each(function ($v) {
            // ✅ DELETE CUSTOM FIELDS
            $v->customFields()->delete();

            foreach ($v->attributes as $a) {
              if ($a->image && Storage::disk('public')->exists($a->image)) {
                Storage::disk('public')->delete($a->image);
              }
              $a->delete();
            }

            if ($v->image && Storage::disk('public')->exists($v->image)) {
              Storage::disk('public')->delete($v->image);
            }

            $v->delete();
          });
      }

      /* =============================
                   GALLERY IMAGES
                ============================== */
      if ($request->hasFile('product_images')) {
        foreach ($request->file('product_images') as $img) {
          $path = $img->store('product-gallery', 'public');

          ProductImage::create([
            'product_id'     => $product->id,
            'product_images' => $path,
          ]);
        }
      }

      DB::commit();

      return redirect()
        ->route('admin.product')
        ->with('success', 'Product updated successfully');
    } catch (\Throwable $e) {
      DB::rollBack();
      \Log::error('ProductController@update: exception', [
        'message' => $e->getMessage(),
        'file'    => $e->getFile(),
        'line'    => $e->getLine(),
        'trace'   => $e->getTraceAsString(),
      ]);
      return redirect()
        ->back()
        ->with('error', $e->getMessage())
        ->with('error_file', $e->getFile())
        ->with('error_line', $e->getLine())
        ->with('error_trace', $e->getTraceAsString())
        ->withInput();
    }
  }

  public function delete($id)
  {
    DB::beginTransaction();

    try {

      $product = Product::with([
        'variants.attributes',
        'variants.customFields',
        'gallary_images'
      ])->find($id);
// dd($product);
      if (!$product) {
        return response()->json([
          'success' => false,
          'message' => 'Product not found!'
        ], 404);
      }

      /*
        |--------------------------------------------------------------------------
        | SOFT DELETE VARIANT RELATED DATA
        |--------------------------------------------------------------------------
        */
      foreach ($product->variants as $variant) {

        // Soft delete attributes
        foreach ($variant->attributes as $attr) {
          // $attr->delete();
        }

        // Soft delete custom fields
        foreach ($variant->customFields as $cf) {
          // $cf->delete();
        }

        // Soft delete variant
        // $variant->delete();
      }

      /*
        |--------------------------------------------------------------------------
        | SOFT DELETE PRODUCT GALLERY
        |--------------------------------------------------------------------------
        */
      if ($product->gallary_images) {
        foreach ($product->gallary_images as $gallery) {
          // $gallery->delete();
        }
      }

      /*
        |--------------------------------------------------------------------------
        | SOFT DELETE PRODUCT
        |--------------------------------------------------------------------------
        */
      $product->delete();

      DB::commit();

      return response()->json([
        'success' => true,
        'message' => 'Product Deleted Successfully!'
      ], 200);
    } catch (\Throwable $e) {
      DB::rollBack();

      return response()->json([
        'success' => false,
        'message' => $e->getMessage()
      ], 500);
    }
  }

  public function recentlyDeletedPage()
  {
    $products = Product::onlyTrashed()->get();
    return view('admin.product.recently_deleted', compact('products'));
  }

  //   public function recentlydeleted()
  //   {
  //     $products = Product::onlyTrashed();

  //     return dataTables()->of($products)
  //     ->addColumn('action', function ($row) {
  //         $restorebtn = '<button class="btn btn-sm btn-success restore-btn" data-id="' . $row->id . '">Restore</button>';
  //         return $restorebtn;

  //     })
  //     ->make(true);
  //   }

  // ProductController.php

  //  public function permanentlyDelete($id)
  // {
  //     // Get product including soft deleted
  //     $product = Product::withTrashed()->find($id);

  //     if ($product) {
  //         // // Optional: delete image files if needed
  //         // if ($product->product_type == 1 && $product->image && Storage::disk('public')->exists($product->image)) {
  //         //     Storage::disk('public')->delete($product->image);
  //         // }

  //         // Permanently delete the product
  //         $product->forceDelete();

  //         return response()->json([
  //             'success' => true,
  //             'message' => 'Product has been deleted permanently.'
  //         ]);
  //     }

  //     return response()->json([
  //         'success' => false,
  //         'message' => 'Product not found!'
  //     ]);
  // }

  public function permanentlyDelete($id)
  {
    DB::beginTransaction();

    try {

      $product = Product::withTrashed()
        ->with([
          'variants.attributes',
          'variants.customFields',
          'gallary_images'
        ])
        ->find($id);

      if (!$product) {
        return response()->json([
          'success' => false,
          'message' => 'Product not found!'
        ]);
      }

      /*
        |--------------------------------------------------------------------------
        | DELETE PRODUCT MAIN IMAGE
        |--------------------------------------------------------------------------
        */
      if ($product->image && Storage::disk('public')->exists($product->image)) {
        Storage::disk('public')->delete($product->image);
      }

      /*
        |--------------------------------------------------------------------------
        | DELETE PRODUCT GALLERY IMAGES
        |--------------------------------------------------------------------------
        */
      if ($product->gallary_images) {
        foreach ($product->gallary_images as $gallery) {

          if (
            $gallery->product_images &&
            Storage::disk('public')->exists($gallery->product_images)
          ) {

            Storage::disk('public')->delete($gallery->product_images);
          }

          $gallery->forceDelete();
        }
      }

      /*
        |--------------------------------------------------------------------------
        | DELETE VARIANTS + ATTRIBUTES + CUSTOM FIELDS
        |--------------------------------------------------------------------------
        */
      foreach ($product->variants as $variant) {

        /*
            |------------------------
            | Variant Attributes
            |------------------------
            */
        foreach ($variant->attributes as $attr) {

          if (
            $attr->image &&
            Storage::disk('public')->exists($attr->image)
          ) {

            Storage::disk('public')->delete($attr->image);
          }

          // $attr->forceDelete();
        }

        /*
            |------------------------
            | Variant Custom Fields
            |------------------------
            */
        foreach ($variant->customFields as $cf) {
          $cf->forceDelete();
        }

        /*
            |------------------------
            | Variant Image
            |------------------------
            */
        if (
          $variant->image &&
          Storage::disk('public')->exists($variant->image)
        ) {

          Storage::disk('public')->delete($variant->image);
        }

        $variant->forceDelete();
      }

      /*
        |--------------------------------------------------------------------------
        | FINAL PRODUCT DELETE
        |--------------------------------------------------------------------------
        */
      $product->forceDelete();

      DB::commit();

      return response()->json([
        'success' => true,
        'message' => 'Product has been permanently deleted.'
      ]);
    } catch (\Throwable $e) {

      DB::rollBack();

      return response()->json([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }

  public function restore($id)
  {
    DB::beginTransaction();

    try {

      $product = Product::withTrashed()
        // ->with([
        //   'variants' => function ($q) {
        //     $q->withTrashed()
        //       ->with([
        //         'attributes' => function ($a) {
        //           $a->withTrashed();
        //         },
        //         'customFields' => function ($c) {
        //           $c->withTrashed();
        //         }
        //       ]);
        //   }
        // ])
        ->find($id);

      if (!$product) {
        return response()->json([
          'success' => false,
          'message' => 'Product not found!'
        ]);
      }

      /*
        |--------------------------------------------------------------------------
        | RESTORE PRODUCT
        |--------------------------------------------------------------------------
        */
      $product->restore();

      /*
        |--------------------------------------------------------------------------
        | RESTORE VARIANTS
        |--------------------------------------------------------------------------
        */
      foreach ($product->variants as $variant) {

        // if ($variant->trashed()) {
        // $variant->restore();
        // }

        /*
            |------------------------
            | Restore Attributes
            |------------------------
            */
        foreach ($variant->attributes as $attr) {
          // if ($attr->trashed()) {
          // $attr->restore();
          // }
        }

        /*
            |------------------------
            | Restore Custom Fields
            |------------------------
            */
        foreach ($variant->customFields as $cf) {
          // if ($cf->trashed()) {
          // $cf->restore();
          // }
        }
      }

      DB::commit();

      return response()->json([
        'success' => true,
        'message' => 'Product has been restored successfully.'
      ]);
    } catch (\Throwable $e) {

      DB::rollBack();

      return response()->json([
        'success' => false,
        'message' => $e->getMessage()
      ]);
    }
  }
}
