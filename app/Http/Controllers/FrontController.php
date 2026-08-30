<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Variant;
use App\Models\Order;
use App\Models\Color;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\VariantAttribute;
use App\Models\VariantCustomField;
use App\Models\CustomFieldType;

use function Laravel\Prompts\table;

class FrontController extends Controller
{
  public function index()
  {
    //  prx(getTopNavCat());
    $result['home_categories'] = DB::table('categories')
      ->where(['status' => 1, 'is_home' => 1])
      ->get();

    foreach ($result['home_categories'] as $list) {
      $product_data = DB::table('products')->where(['category_id' => $list->id, 'status' => 1])->get();
      if (count($product_data)) {
        $result['home_categories_product'][$list->id] = $product_data;

        foreach ($product_data as $list2) {
          $product_attr_data = DB::table('variants')->where('product_id', $list2->id)->get();

          if (count($product_attr_data)) {
            $result['home_product_attr'][$list2->id] = $product_attr_data;
          }
        }
      }
    }
    #section 2


    #get is_tranding_product;
    // $result['home_tranding_product'][$list->id] = DB::table('products')->where(['status' => 1, 'is_tranding' => 1])->get();
    // foreach ($result['home_tranding_product'] as $list1) {
    //   $tranding_product_query = DB::table('variants')->where(['product_id' => $list->id, 'status' => 1])->get();
    //   if (count($tranding_product_query)) {
    //     $result['home_tranding_product_attr'][$list1->id] = $tranding_product_query;
    //   }
    // }

    #get is_discounted product;
    // $result['home_discounted_product'][$list->id] = DB::table('products')->where(['status' => 1, 'is_discounted' => 1])->get();
    // foreach ($result['home_discounted_product'] as $list1) {
    //   $home_discounted_variant = DB::table('variants')->where(['status' => 1, 'product_id' => $list->id])->get();
    //   if (count($home_discounted_variant)) {
    //     $result['home_discounted_product_attr'][$list1->id] = $home_discounted_variant;
    //   }
    // }

    #get is_feature product;
    // $result['home_featured_product'][$list->id] = DB::table('products')->where(['status' => 1, 'is_featured' => 1])->get();
    // foreach ($result['home_featured_product'] as $list1) {
    //   $home_feature_product_attr = DB::table('variants')->where(['status' => 1, 'product_id' => $list->id])->get();
    //   if (count($home_discounted_variant)) {
    //     $result['home_featured_product_attr'][$list1->id] = $home_feature_product_attr;
    //   }
    // }

    $result['home_brand'] = DB::table('brands')
      ->where(['status' => 1])
      // ->where(['is_home' => 1])
      ->get();
    $result['home_banner'] = DB::table('banners')->where(['status' => 1])->get();
    return view('front.index', $result);
  }

  /**
   * Global product search for header autocomplete.
   * GET /search?q=...
   */
  public function globalSearch(Request $request)
  {
    $q = trim($request->get('q', ''));
    if ($q === '') {
      return response()->json([]);
    }

    $products = Product::query()
      ->where('products.status', 1)
      ->where(function ($qb) use ($q) {
        $qb->where('products.title', 'like', '%' . $q . '%')
          ->orWhere('products.slug', 'like', '%' . $q . '%')
          ->orWhereHas('category', function ($c) use ($q) {
            $c->where('name', 'like', '%' . $q . '%');
          })
          ->orWhereHas('variants', function ($v) use ($q) {
            $v->where('name', 'like', '%' . $q . '%')
              ->orWhere('sku', 'like', '%' . $q . '%')
              ->orWhere('variant_description', 'like', '%' . $q . '%');
          });
      })
      ->with('category')
      ->orderBy('products.title')
      ->limit(15)
      ->get();

    $results = $products->map(function ($p) {
      return [
        'id'    => $p->id,
        'title' => $p->title,
        'slug'  => $p->slug,
        'url'   => route('front.product', $p->slug),
        'image' => $p->image ? asset('storage/' . $p->image) : '',
        'category_name' => $p->category ? $p->category->name : '',
      ];
    });

    return response()->json($results->values()->all());
  }

  // ============================================================
  // This is the updated `product` method in your FrontController
  // (or whichever controller handles the frontend product page)
  // ============================================================

public function product(Request $request, $slug)
{
    $product = Product::where('slug', $slug)
        ->where('status', 1)
        ->with([
            'variants.variantname',
            'variants.productCodeType',
            'variants.attributes.option.attribute',
            'variants.customFields.customFieldType.variantCustomFields',
            'unit',
        ])
        ->firstOrFail();

    $variantsJson = $product->variants->map(function ($v) use ($product) {
        return [
            'id'                      => $v->id,
            'name'                    => $v->name,
            'variantname_id'          => $v->variantname_id ?? null,
            'variant_name'            => $v->variantname ? $v->variantname->name : '',
            'product_code_type_id'    => $v->product_code_type_id,
            'product_code_type_name'  => $v->productCodeType ? $v->productCodeType->name : null,
            'image'                   => $v->image ? asset('storage/' . $v->image) : asset('storage/' . $product->image),
            'mrp'                     => (float)($v->mrp ?? 0),
            'sell_price'              => ($v->sell_price > 0) ? (float)$v->sell_price : null,
            'stock'                   => (int)($v->stock ?? 0),
            // ✅ ADD THIS LINE
            'variant_description'             => $v->variant_description ?? '',

            'custom_fields'           => $v->customFields->map(function($cf) {
                return [
                    'id'                 => $cf->id,
                    'type_id'            => $cf->custom_field_type_id,
                    'type_name'          => $cf->customFieldType->name ?? 'Custom Field',
                    'value'              => $cf->field_value,
                    'field_type'         => $cf->customFieldType->field_type ?? 'text',
                    'is_dependent_on'    => $cf->customFieldType->is_dependent_on ?? null,
                    'options'            => $cf->customFieldType->variantCustomFields ? $cf->customFieldType->variantCustomFields->map(function($opt) use ($cf) {
                        return [
                            'id'                => $opt->id,
                            'label'             => $cf->customFieldType->name,
                            'value'             => $opt->field_value,
                            'depends_on_value'  => $opt->depends_on_value ?? null,
                            'variant_type_id'   => $opt->variant_type_id ?? null,
                        ];
                    })->values() : [],
                ];
            })->values(),

            'attributes'              => $v->attributes->map(function ($a) {
                return [
                    'id'                           => $a->id,
                    'label_name'                   => $a->option->attribute ? $a->option->attribute->name : '',
                    'label'                        => optional($a->option)->name,
                    'mrp'                          => (float)($a->mrp ?? 0),
                    'sell_price'                   => ($a->sell_price > 0) ? (float)$a->sell_price : null,
                    'is_por'                       => (bool)($a->is_por ?? false),
                    'stock'                        => (int)($a->stock ?? 0),
                    'image'                        => $a->image ? asset('storage/' . $a->image) : '',
                    'required_custom_field_id'     => $a->custom_field_required_id ?? null,
                ];
            })->values(),
        ];
    })->values();

    $hasCodeTypes = $product->variants->whereNotNull('product_code_type_id')->isNotEmpty();

    $codeTypes = [];
    if ($hasCodeTypes) {
        $codeTypeGroups = $product->variants
            ->whereNotNull('product_code_type_id')
            ->groupBy('product_code_type_id');

        foreach ($codeTypeGroups as $codeTypeId => $variants) {
            $firstVariant = $variants->first();
            if ($firstVariant && $firstVariant->productCodeType) {
                $codeTypes[] = [
                    'id'          => $codeTypeId,
                    'name'        => $firstVariant->productCodeType->name,
                    'variant_ids' => $variants->pluck('id')->unique()->toArray(),
                ];
            }
        }
    }

    $disabledAttributeIds = [];
    foreach ($product->variants as $variant) {
        foreach ($variant->attributes as $attribute) {
            if (($attribute->stock ?? 0) <= 0 || ($attribute->is_disabled ?? false)) {
                $disabledAttributeIds[] = $attribute->id;
            }
        }
    }

    return view('front.product', compact(
        'product',
        'variantsJson',
        'disabledAttributeIds',
        'hasCodeTypes',
        'codeTypes'
    ));
}


public function product_old(Request $request, $slug)
  {

    // $product = Product::where(['slug'=>$slug,'status'=>1])
    // ->with(['category', 'gallary_images', 'variants'])
    // ->get();


    // return $slug;
    $result['product'] =
      DB::table('products')
      ->where(['status' => 1])
      ->where(['slug' => $slug])
      ->get();

    // prx($result['product']);


    #get product variant
    foreach ($result['product'] as $list1) {
      $product_attr_query = $result['product_attr'][$list1->id] =
        DB::table('variants')
        ->leftJoin('attribute_options', 'attribute_options.id', '=', 'variants.wattage')

        // ->leftJoin('sizes', 'sizes.id', '=', 'products_attr.size_id')
        // ->leftJoin('colors', 'colors.id', '=', 'products_attr.color_id')
        ->where(['variants.product_id' => $list1->id])
        ->get();
      if (count($product_attr_query)) {
        $result['product_attr'][$list1->id] = $product_attr_query;
      }
    }

    #get product gallary image
    foreach ($result['product'] as $list1) {
      $gallary_images = DB::table('product_images')->where('product_id', $list1->id)->get();
      if (count($gallary_images)) {
        $result['product_images'][$list1->id] = $gallary_images;
      }
    }

    // return $result['product'];






    $result['related_product'] =
      DB::table('products')
      ->where(['status' => 1])
      ->where('slug', '!=', $slug)
      ->where(['category_id' => $result['product'][0]->category_id])
      ->get();
    foreach ($result['related_product'] as $list1) {

      $related_product_query = DB::table('variants')
        ->leftJoin('attribute_options', 'attribute_options.id', '=', 'variants.wattage')
        // ->leftJoin('colors', 'colors.id', '=', 'products_attr.color_id')
        ->where(['variants.product_id' => $list1->id])
        ->get();
      if (count($related_product_query)) {
        $result['related_product_attr'][$list1->id] = $related_product_query;
      }
    }


    // prx($result);
    return view('front.product', $result);
    // return view('front.product',compact('product'));
  }


  // public function AddToCart(Request $request)
  // {
  //   $user_id = Auth::user()->id;
  //   $search_varaint_arr = [];
  //   $search_cart_arr = ['user_id' => $user_id];
  //   // $search_arr = [];

  //   // #variant variable start;
  //   // $color = "";
  //   // $cct="";
  //   // $wattage="";
  //   // #variant variable end

  //   $qty = "";
  //   // $product_id="";



  //   if ($request->has('wattage')) {
  //     $search_varaint_arr['wattage'] = $request->wattage;
  //   }

  //   if ($request->has('cct')) {
  //     $search_varaint_arr['cct'] = $request->cct;
  //   }

  //   #get color body id from slug;
  //   if ($request->has('color')) {
  //     $color = Color::where('slug', $request->color)->first();
  //     $body_color = $color?->id;
  //     if ($body_color) {
  //       $search_varaint_arr['body_color'] = $body_color;
  //     }
  //   }

  //   if ($request->has('pqty')) {
  //     $qty = $request->pqty;
  //   }
  //   if ($request->has('product_id')) {
  //     $search_varaint_arr['product_id'] = $request->product_id;
  //     $search_cart_arr['product_id'] = $request->product_id;
  //   }
  //   $variant = Variant::where($search_varaint_arr)->first();
  //   $variant_id = $variant->id;

  //   $search_cart_arr['product_attr_id'] = $variant_id;

  //   //check record exit in table or not if exit just update Qty;
  //   $check = Cart::where($search_cart_arr)->first();

  //   if ($check) {
  //     #update quantity
  //     if ($qty == 0) {
  //       #if count 0 delete into cart
  //       $check->delete();
  //       $msg = "removed";
  //     } else {
  //       #otherwise update quanity
  //       $check->update(['qty' => $qty]);
  //       $msg = "updated";
  //     }
  //   } else {
  //     #add to card all value
  //     $search_cart_arr['qty'] = $qty;
  //     $store = Cart::create($search_cart_arr);
  //     $msg = "added";
  //   }

  //   // return  $search_cart_arr;

  //   $result = DB::table('carts')
  //     ->leftJoin('products', 'products.id', '=', 'carts.product_id')
  //     ->leftJoin('variants', 'variants.id', '=', 'carts.product_attr_id')
  //     ->where(['user_id' => $user_id])
  //     ->select('carts.qty', 'products.title', 'products.image', 'variants.price', 'products.slug', 'products.id as pid', 'variants.id as attr_id')
  //     ->get();
  //   return response()->json(['msg' => $msg, 'data' => $result, 'totalItem' => count($result)]);


  //   #----------------------------------------------------------------------------------------------------------------------
  //   $pqty = $request->pqty;
  //   $wattage = $request->wattage;
  //   $product_id = $request->product_id;


  //   $user_id = Auth::user()->id;
  //   #done
  //   $result = DB::table('variants')->where(['product_id' => $product_id, 'wattage' => $wattage])->get();
  //   $product_attr_id = $result[0]->id;

  //   #update if get this value just update quantity;
  //   #done
  //   $check = DB::table('carts')
  //     ->where(['user_id' => $user_id, 'product_id' => $product_id, 'product_attr_id' => $product_attr_id])
  //     ->get();


  //   if (isset($check[0])) {
  //     $update_id = $check[0]->id;

  //     if ($pqty == 0) {
  //       DB::table('carts')
  //         ->where(['id' => $update_id])
  //         ->delete();
  //       $msg = "removed";
  //     } else {
  //       DB::table('carts')
  //         ->where(['id' => $update_id])
  //         ->update(['qty' => $pqty]);
  //       $msg = "updated";
  //     }
  //   } else {
  //     $id = DB::table('carts')->insertGetId([
  //       'user_id' => $user_id,
  //       'product_id' => $product_id,
  //       'product_attr_id' => $product_attr_id,
  //       'qty' => $pqty
  //     ]);
  //     $msg = "added";
  //   }


  //   $result = DB::table('carts')
  //     ->leftJoin('products', 'products.id', '=', 'carts.product_id')
  //     ->leftJoin('variants', 'variants.id', '=', 'carts.product_attr_id')
  //     ->where(['user_id' => $user_id])
  //     ->select('carts.qty', 'products.title', 'products.image', 'variants.price', 'products.slug', 'products.id as pid', 'variants.id as attr_id')
  //     ->get();
  //   return response()->json(['msg' => $msg, 'data' => $result, 'totalItem' => count($result)]);
  //   // Cart::create();
  //   // return $request->all();
  // }
  public function addToCart(Request $request)
{

// dd($request->all());
    $userId = auth()->id();

    $productId = (int) $request->product_id;
    $variantId = $request->variant_id ?: null;
    $attrId    = $request->variant_attribute_id ?: null;
    $qty       = (int) $request->pqty;

    if (!$productId || $qty <= 0) {
        return response()->json([
            'msg' => 'Invalid data'
        ], 422);
    }

    /*
    |--------------------------------------------------------------------------
    | GET PRODUCT TYPE
    |--------------------------------------------------------------------------
    */
    $product = DB::table('products')
        ->where('id', $productId)
        ->select('id', 'product_type', 'price', 'sale_price', 'stock')
        ->first();

    if (!$product) {
        return response()->json(['msg' => 'Product not found'], 404);
    }

    /*
    |--------------------------------------------------------------------------
    | PRICE CALCULATION + STOCK (TYPE BASED)
    |--------------------------------------------------------------------------
    */
    $price = 0;
    $availableStock = null; // used for stock check

    // ✅ SIMPLE PRODUCT (type = 1)
    if ((int) $product->product_type === 1) {
        $price = $product->sale_price ?: $product->price;
        $availableStock = (int) ($product->stock ?? 0);
    }

    // ✅ VARIANT PRODUCT (type = 3)
    if ((int) $product->product_type === 3) {

        if (!$attrId) {
            return response()->json([
                'msg' => 'Variant attribute required'
            ], 422);
        }

        $attribute = DB::table('variant_attributes')
            ->where('id', $attrId)
            ->select('sell_price', 'mrp', 'is_por', 'stock')
            ->first();

        if (!$attribute) {
            return response()->json([
                'msg' => 'Invalid variant attribute'
            ], 422);
        }

        $availableStock = (int) ($attribute->stock ?? 0);

        if ($attribute->is_por && $request->has('por_price')) {
            $price = (float) $request->por_price;
        } else {
            $price = $attribute->sell_price ?: $attribute->mrp;
        }
    }

    if (!$price || $price <= 0) {
        return response()->json(['msg' => 'Price not found'], 422);
    }

    /*
    |--------------------------------------------------------------------------
    | BUILD UNIQUE CART KEY (DUPLICATE PREVENT)
    |--------------------------------------------------------------------------
    */
    if (!is_null($variantId)) {
        $where['variant_id'] = $variantId;
    } else {
        $where['variant_id'] = null;
    }

    if (!is_null($attrId)) {
        $where['variant_attribute_id'] = $attrId;
    } else {
        $where['variant_attribute_id'] = null;
    }

    if (!is_null($userId)) {
        $where['user_id'] = $userId;
    } else {
        $where['user_id'] = null;
    }

    if (!is_null($productId)) {
        $where['product_id'] = $productId;
    } else {
        $where['product_id'] = null;
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK EXISTING CART ROW (for stock calculation)
    |--------------------------------------------------------------------------
    */
    $cart = Cart::where($where)->first();
    $qtyAfterAdd = $cart ? ($cart->qty + $qty) : $qty;

    /*
    |--------------------------------------------------------------------------
    | STOCK CHECK: available must be >= qty after add
    |--------------------------------------------------------------------------
    */
    if ($availableStock !== null && $qtyAfterAdd > $availableStock) {
        return response()->json([
            'msg' => 'Insufficient stock. Available: ' . $availableStock . ', requested: ' . $qtyAfterAdd
        ], 422);
    }

    if ($cart) {
        // ✅ DUPLICATE FOUND → UPDATE QTY
        $cart->update([
            'qty' => $cart->qty + $qty,
            'price' => $price, // ✅ POR price or normal price

        ]);
        $msg = 'updated';
    } else {
        // ✅ NEW ENTRY
        Cart::create([
            'user_id'              => $userId,
            'product_id'           => $productId,
            'variant_id'           => $variantId,
            'variant_attribute_id' => $attrId,
            'qty'                  => $qty,
            'price'                => $price, // ✅ POR price or normal price

        ]);
        $msg = 'added';
    }

    /*
    |--------------------------------------------------------------------------
    | FETCH UPDATED CART (RESPONSE)
    |--------------------------------------------------------------------------
    */

    $result = DB::table('carts')
        ->leftJoin('products', 'products.id', '=', 'carts.product_id')
        ->leftJoin('variants', 'variants.id', '=', 'carts.variant_id')

        // 🔥 attribute joins
        ->leftJoin('variant_attributes', 'variant_attributes.id', '=', 'carts.variant_attribute_id')
        ->leftJoin('attributes', 'attributes.id', '=', 'variant_attributes.attribute_id')
        ->leftJoin('attribute_options', 'attribute_options.id', '=', 'variant_attributes.attribute_option_id')

        ->where('carts.user_id', $userId)
        ->select(
            'carts.qty',
            'carts.price as  cart_price',
            // product
            'products.title as product_title',
            'products.image',
            'products.slug',

            // variant
            'variants.name as variant_name',
            'variant_attributes.is_por as is_por',

            // attribute
            'attributes.name as attribute_name',
            'attribute_options.name as attribute_option_name',

            // price logic (unchanged as you asked)
            DB::raw('
               CASE
                WHEN variant_attributes.is_por = 1
                    THEN carts.price
                ELSE COALESCE(
                    variant_attributes.sell_price,
                    variant_attributes.mrp,
                    variants.sell_price,
                    variants.mrp,
                    products.sale_price,
                    products.price
                )
            END as price
            ')
        )
        ->get();

    return response()->json([
        'msg'        => $msg,
        'data'       => $result,
        'totalItem'  => count($result),
    ]);
}
  // public function cart(Request $request)
  // {
  //   $user_id = Auth::user()->id;
  //   $result['list'] = DB::table('carts')
  //     ->leftJoin('products', 'products.id', '=', 'carts.product_id')
  //     // ->leftJoin('variants', 'variants.id', '=', 'carts.product_attr_id')
  //     ->leftJoin('variants', 'variants.id', '=', 'carts.variant_id')


  //     // ->leftJoin('sizes', 'sizes.id', '=', 'products_attr.size_id')//no need
  //     // ->leftJoin('colors', 'colors.id', '=', 'products_attr.color_id')  //no need
  //     ->where(['user_id' => $user_id])
  //     // ->where(['user_type' => "registerd"])
  //     // ->select('carts.qty', 'products.name', 'products.image', '', 'colors.color', 'products_attr.price', 'products.slug', 'products.id as pid', 'products_attr.id as attr_id')
  //     ->select('carts.qty', 'products.title', 'products.image', 'variants.price', 'products.slug', 'variants.id as attr_id', 'products.id as pid', 'variants.wattage')
  //     ->get();
  //   // return $result;
  //   return view('front.cart', $result);
  // }

  public function cart(Request $request)
  {
    $user_id = Auth::id();
    $list = DB::table('carts')
    ->leftJoin('products', 'products.id', '=', 'carts.product_id')
    ->leftJoin('variants', 'variants.id', '=', 'carts.variant_id')
    ->leftJoin('variant_attributes', 'variant_attributes.id', '=', 'carts.variant_attribute_id')
    ->leftJoin('attributes', 'attributes.id', '=', 'variant_attributes.attribute_id')
    ->leftJoin('product_code_types', 'product_code_types.id', '=', 'variants.product_code_type_id')
    ->leftJoin('attribute_options', 'attribute_options.id', '=', 'variant_attributes.attribute_option_id')
    ->where('carts.user_id', $user_id)
    ->select(
        'carts.id as cart_id',
        'carts.qty',
        'carts.price as cart_price',

        'products.id as product_id',
        'products.title as product_title',
        'products.slug',
        'products.image',
        'products.product_type as product_type',
        'products.price as product_price',
        'products.sale_price as product_sale_price',
        'products.image as product_image',

        'variants.id as variant_id',
        'variants.name as variant_name',

        'variant_attributes.is_por as is_por',
        'variant_attributes.mrp as variant_attribute_mrp',
        'variant_attributes.sell_price as variant_attribute_sell_price',
        'variant_attributes.image as variant_attribute_image',

        'attributes.name as attribute_name',
        'attribute_options.name as attribute_option',

        'product_code_types.name as product_code_type_name',

        // ✅ FIXED PRICE LOGIC
        DB::raw('
            CASE
                WHEN variant_attributes.is_por = 1
                    THEN carts.price
                ELSE COALESCE(
                    variant_attributes.sell_price,
                    variant_attributes.mrp,
                    variants.sell_price,
                    variants.mrp,
                    products.sale_price,
                    products.price
                )
            END as price
        ')
    )
    ->get();


    // $list = DB::table('carts')
    //   ->leftJoin('products', 'products.id', '=', 'carts.product_id')
    //   ->leftJoin('variants', 'variants.id', '=', 'carts.variant_id')
    //   ->leftJoin('variant_attributes', 'variant_attributes.id', '=', 'carts.variant_attribute_id')
    //   ->leftJoin('attributes', 'attributes.id', '=', 'variant_attributes.attribute_id')
    //   ->leftJoin('product_code_types', 'product_code_types.id', '=', 'variants.product_code_type_id')
    //   ->leftJoin('attribute_options', 'attribute_options.id', '=', 'variant_attributes.attribute_option_id')
    //   ->where('carts.user_id', $user_id)
    //   ->select(
    //     'carts.id as cart_id',
    //     'carts.qty',

    //     'products.id as product_id',
    //     'products.title as product_title',
    //     'products.slug',
    //     'products.image',
    //     'products.product_type as product_type',
    //     'products.price as product_price',
    //     'products.sale_price as product_sale_price',
    //     'products.image as product_image',

    //     'variants.id as variant_id',
    //     'variants.name as variant_name',
    //     'variant_attributes.is_por as is_por',
    //     'variant_attributes.mrp as variant_attribute_mrp',
    //     'variant_attributes.sell_price as variant_attribute_sell_price',
    //     'variant_attributes.image as variant_attribute_image',

    //     'attributes.name as attribute_name',
    //     'attribute_options.name as attribute_option',
    //     // product code type
    //     'product_code_types.name as product_code_type_name',

    //     DB::raw('
    //             COALESCE(
    //                 variant_attributes.sell_price,
    //                 variant_attributes.mrp,
    //                 variants.sell_price,
    //                 variants.mrp,
    //                 products.sale_price,
    //                 products.price
    //             ) as price
    //         ')
    //   )
    //   ->get();

    return view('front.cart', compact('list'));
  }



  // public function category(Request $request, $slug)
  // {
  //   $sort = "";
  //   $sort_txt = "";
  //   $filter_price_start = "";
  //   $filter_price_end = "";
  //   $color_filter = "";
  //   $colorFilterArr = [];
  //   if ($request->get('sort') !== null) {
  //     $sort = $request->get('sort');
  //   }
  //   $query = DB::table('products');
  //   $query = $query->distinct()->select('products.*');
  //   $query = $query->leftJoin('categories', 'categories.id', '=', 'products.category_id');
  //   $query = $query->leftJoin('variants', 'products.id', '=', 'variants.product_id');
  //   $query = $query->where(['products.status' => 1]);
  //   $query = $query->where(['categories.slug' => $slug]);


  //   if ($sort == 'name') {
  //     $query = $query->orderBy('products.title', 'asc');
  //     $sort_txt = "Product Name";
  //   }
  //   if ($sort == 'date') {
  //     $query = $query->orderBy('products.id', 'desc');
  //     $sort_txt = "Date";
  //   }
  //   if ($sort == 'price_desc') {
  //     $query = $query->orderBy('variants.price', 'desc');
  //     $sort_txt = "Price - DESC";
  //   }
  //   if ($sort == 'price_asc') {
  //     $query = $query->orderBy('products_attr.price', 'asc');
  //     $sort_txt = "Price - ASC";
  //   }


  //   if ($request->get('filter_price_start') !== null && $request->get('filter_price_end') !== null) {
  //     $filter_price_start = $request->get('filter_price_start');
  //     $filter_price_end = $request->get('filter_price_end');

  //     if ($filter_price_start > 0 && $filter_price_end > 0) {
  //       $query = $query->whereBetween('variants.price', [$filter_price_start, $filter_price_end]);
  //     }
  //   }

  //   // if ($request->get('color_filter') !== null) {
  //   //   $color_filter = $request->get('color_filter');
  //   //   $colorFilterArr = explode(":", $color_filter);
  //   //   $colorFilterArr = array_filter($colorFilterArr);

  //   //   $query = $query->where(['products_attr.color_id' => $request->get('color_filter')]);
  //   // }

  //   // $query = $query->distinct()->select('products.*');
  //   $query = $query->get();
  //   $result['product'] = $query;
  //   foreach ($result['product'] as $list1) {

  //     $query1 = DB::table('variants');
  //     // $query1 = $query1->leftJoin('sizes', 'sizes.id', '=', 'products_attr.size_id');
  //     // $query1 = $query1->leftJoin('colors', 'colors.id', '=', 'products_attr.color_id');
  //     $query1 = $query1->where(['variants.product_id' => $list1->id]);
  //     $query1 = $query1->get();
  //     $result['product_attr'][$list1->id] = $query1;
  //   }

  //   // $result['product_attr'];

  //   // $result['colors'] = DB::table('colors')
  //   //   ->where(['status' => 1])
  //   //   ->get();


  //   // $result['categories_left'] = DB::table('categories')
  //   //   ->where(['status' => 1])
  //   //   ->get();

  //   // $result['slug'] = $slug;
  //   $result['sort'] = $sort;
  //   $result['sort_txt'] = $sort_txt;
  //   $result['filter_price_start'] = $filter_price_start;
  //   $result['filter_price_end'] = $filter_price_end;
  //   // $result['color_filter'] = $color_filter;
  //   // $result['colorFilterArr'] = $colorFilterArr;
  //   return view('front.category', $result);
  // }

 public function category(Request $request, $slug)
{
    $sort = $request->get('sort', '');
    $sort_txt = '';
    $filter_price_start = $request->get('filter_price_start');
    $filter_price_end   = $request->get('filter_price_end');

    $query = Product::query()
        ->where('status', 1)
        ->whereHas('category', function ($q) use ($slug) {
            $q->where('slug', $slug);
        })
        ->with(['variants' => function ($q) {
            $q->select('id', 'product_id', 'price', 'mrp');
        }]);

    /* ================= SORT ================= */
    if ($sort === 'name') {
        $query->orderBy('title', 'asc');
        $sort_txt = 'Product Name';
    } elseif ($sort === 'date') {
        $query->orderBy('id', 'desc');
        $sort_txt = 'Date';
    } elseif ($sort === 'price_asc') {
        $query->orderByRaw('(SELECT MIN(price) FROM variants WHERE variants.product_id = products.id) ASC');
        $sort_txt = 'Price - ASC';
    } elseif ($sort === 'price_desc') {
        $query->orderByRaw('(SELECT MAX(price) FROM variants WHERE variants.product_id = products.id) DESC');
        $sort_txt = 'Price - DESC';
    }

    /* ================= PRICE FILTER ================= */
    if ($filter_price_start && $filter_price_end) {
        $query->whereHas('variants', function ($q) use ($filter_price_start, $filter_price_end) {
            $q->whereBetween('price', [$filter_price_start, $filter_price_end]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | PAGINATION (12 per page - change as needed)
    |--------------------------------------------------------------------------
    */
    $products = $query->paginate(10)->withQueryString();

    return view('front.category', [
        'product'             => $products,
        'sort'                => $sort,
        'sort_txt'            => $sort_txt,
        'filter_price_start'  => $filter_price_start,
        'filter_price_end'    => $filter_price_end,
    ]);
}

  public function checkout(Request $request)
  {
    // return "i am her";
    $result['cart_data'] = getAddToCartTotalItem();
    if (isset($result['cart_data'][0])) { #if cart have value than enter inside if condition;
      $user = Auth::user()->name;
      // return $user;
      if (Auth::check()) {
        $customer_info = Auth::user();
        $result['customers']['name'] = $customer_info->name;
        $result['customers']['email'] = $customer_info->email;
        $result['customers']['mobile'] = $customer_info->mobile;
        $result['customers']['address'] = $customer_info->address;
        $result['customers']['city'] = $customer_info->city;
        $result['customers']['state'] = $customer_info->state;
        $result['customers']['zip'] = $customer_info->pin_code;
      } else {
        $result['customers']['name'] = '';
        $result['customers']['email'] = '';
        $result['customers']['mobile'] = '';
        $result['customers']['address'] = '';
        $result['customers']['city'] = '';
        $result['customers']['state'] = '';
        $result['customers']['zip'] = '';
      }

      return view('front.checkout', $result);
    } else {
      return redirect('/');
    }
  }

  #old code
  // public function place_order(Request $request)
  // {
  //   DB::beginTransaction(); // 🔹 start transaction

  //   try {
  //     $uid = Auth::id();
  //     $totalPrice = 0;
  //     $getAddToCartTotalItem = getAddToCartTotalItem(); #get detail from cart table and get total;

  //     foreach ($getAddToCartTotalItem as $list) {
  //       $totalPrice = $totalPrice + ($list->qty * $list->price);
  //     }

  //     $orderArray = [
  //       "user_id" => $uid,
  //       "name" => $request->name,
  //       "email" => $request->email,
  //       "mobile" => $request->mobile,
  //       "address" => $request->address,
  //       "city" => $request->city,
  //       "state" => $request->state,
  //       "pin_code" => $request->zip,
  //       "payment_type" => $request->payment_type,
  //       "payment_status" => "pending",
  //       "total_amount" => $totalPrice,
  //       "order_status" => "processing",
  //       "payment_id"   => 0,
  //     ];

  //     $order = Order::create($orderArray);
  //     $order_id = $order->id;
  //     return $order_id;
  //     if ($order_id > 0) {
  //       foreach ($getAddToCartTotalItem as $list) {
  //         $prductDetailArr['product_id'] = $list->pid;
  //         $prductDetailArr['variant_id'] = $list->attr_id;
  //         $prductDetailArr['price'] = $list->price;
  //         $prductDetailArr['qty'] = $list->qty;
  //         $prductDetailArr['order_id'] = $order_id;
  //         OrderDetails::create($prductDetailArr);
  //       }

  //       //empty cart if order placed
  //       Cart::where('user_id', $uid)->delete();
  //       $request->session()->put('ORDER_ID', $order_id);

  //       DB::commit(); // 🔹 success → save everything

  //       $status = "success";
  //       $msg = "Order placed";
  //     } else {
  //       DB::rollBack(); // 🔹 failure → undo everything

  //       $status = "false";
  //       $msg = "Please try after sometime";
  //     }
  //   } catch (\Exception $e) {
  //     DB::rollBack(); // 🔹 error → undo everything

  //     $status = "false";
  //     $msg = "Please try after sometime";
  //     // logger($e->getMessage()); // optional
  //   }

  //   return response()->json(['status' => $status, 'msg' => $msg,'order_id'=> $order_id]);
  // }
public function place_order(Request $request)
{
    DB::beginTransaction();

    try {

        $salesmanId = Auth::id();

        $cartItems = Cart::where('user_id', $salesmanId)->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'msg' => 'Cart is empty'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | PREFETCH + STOCK VALIDATION (before creating order)
        |--------------------------------------------------------------------------
        */
        $products = Product::whereIn('id', $cartItems->pluck('product_id')->unique())
            ->get()->keyBy('id');

        $variants = Variant::with(['productCodeType'])
            ->whereIn('id', $cartItems->pluck('variant_id')->filter()->unique())
            ->get()->keyBy('id');

        $variantAttrs = VariantAttribute::with(['attribute', 'option'])
            ->whereIn('id', $cartItems->pluck('variant_attribute_id')->filter()->unique())
            ->get()->keyBy('id');

        // Validate stock: simple products
        $simpleQtyByProduct = $cartItems->whereNull('variant_id')->groupBy('product_id')->map->sum('qty');
        foreach ($simpleQtyByProduct as $pid => $totalQty) {
            $p = $products->get($pid);
            if ($p && (int) ($p->stock ?? 0) < $totalQty) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'msg' => 'Insufficient stock for product: ' . $p->title . ' (available: ' . (int) $p->stock . ', required: ' . $totalQty . ')'
                ], 422);
            }
        }

        // Validate stock: variant attributes (per color/option)
        $variantQtyByAttr = $cartItems->whereNotNull('variant_attribute_id')->groupBy('variant_attribute_id')->map->sum('qty');
        foreach ($variantQtyByAttr as $vaId => $totalQty) {
            $va = $variantAttrs->get($vaId);
            if ($va && (int) ($va->stock ?? 0) < $totalQty) {
                DB::rollBack();
                $optName = $va->option->name ?? 'Option';
                return response()->json([
                    'status' => 'error',
                    'msg' => 'Insufficient stock for ' . $optName . ' (available: ' . (int) $va->stock . ', required: ' . $totalQty . ')'
                ], 422);
            }
        }

        $order = Order::create([
            'user_id'        => $salesmanId,
            'shop_name'      => $request->shop_name,
            'owner_name'     => $request->owner_name,
            'owner_phone'    => $request->owner_phone,
            'owner_email'    => $request->owner_email,
            'owner_address'  => $request->owner_address,
            'city'           => $request->city,
            'pin_code'       => $request->pin_code,
            'state'          => $request->state,
            'gst_number'     => $request->gst_number,
            'total_qty'      => 0,
            'total_amount'   => 0,
            'payment_type'   => $request->payment_type,
            'payment_status' => 'pending',
            'order_status'   => $request->order_status ?? 'pending',
            'punch_in_time'  => $request->punch_in_time ?? now(),
             'is_gst'         => $request->is_gst ?? 1,
            'is_transport'    => $request->is_transport ?? 1,
            'transport_details' => $request->transport_details ?? null,
            'landmark'        => $request->landmark ?? null,
            'transport_name'     => $request->transport_name ?? null,
            'credit_days'       => $request->credit_days ?? null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | PROCESS (products, variants, variantAttrs already loaded above)
        |--------------------------------------------------------------------------
        */

        $productSnapshots = [];
        $grandTotal = 0;
        $totalQty = 0;

        $grouped = $cartItems->groupBy(function ($item) {
            return $item->product_id . '_' . ($item->variant_id ?? 0);
        });

        foreach ($grouped as $groupItems) {

            $firstItem = $groupItems->first();
            $product = $products->get($firstItem->product_id);

            if (!$product) continue;

            /*
            |--------------------------------------------------------------------------
            | SIMPLE PRODUCT
            |--------------------------------------------------------------------------
            */

            if ((int)$product->product_type === 1) {

                $unitPrice = (
                    !is_null($product->sale_price) &&
                    $product->sale_price > 0 &&
                    $product->sale_price <= $product->price
                ) ? (float)$product->sale_price : (float)$product->price;

                $qty = $groupItems->sum('qty');
                $lineTotal = $unitPrice * $qty;

                OrderDetails::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'variant_id' => null,
                    'variant_attribute_id' => null,
                    'qty' => $qty,
                    'price' => $unitPrice,
                    'total' => $lineTotal,
                ]);

                // Deduct simple product stock
                $product->decrement('stock', $qty);

                $productSnapshots[] = [
                    'product' => [
                        'id' => $product->id,
                        'name' => $product->title,
                        'image' => $product->image,
                    ],
                    'variant' => null,
                    'custom_fields' => [],
                    'attributes' => [],
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                    'total' => $lineTotal,
                ];

                $grandTotal += $lineTotal;
                $totalQty += $qty;

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | VARIANT PRODUCT
            |--------------------------------------------------------------------------
            */

            $variant = $variants->get($firstItem->variant_id);
            $attributes = [];

            foreach ($groupItems as $item) {

                $variantAttr = $variantAttrs->get($item->variant_attribute_id);
                if (!$variantAttr) continue;

                if (!empty($variantAttr->is_por) && $variantAttr->is_por == 1) {
                    $unitPrice = (float) $item->price;
                } else {
                    $unitPrice = (
                        !is_null($variantAttr->sell_price) &&
                        $variantAttr->sell_price > 0 &&
                        $variantAttr->sell_price <= $variantAttr->mrp
                    ) ? (float)$variantAttr->sell_price : (float)$variantAttr->mrp;
                }

                $lineTotal = $unitPrice * $item->qty;

                OrderDetails::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                    'variant_attribute_id' => $variantAttr->id,
                    'qty' => $item->qty,
                    'price' => $unitPrice,
                    'total' => $lineTotal,
                ]);

                // Deduct variant attribute stock (e.g. per color)
                $variantAttr->decrement('stock', (int) $item->qty);

                $attributes[] = [
                    'attribute_name' => $variantAttr->attribute->name ?? null,
                    'option_name'    => $variantAttr->option->name ?? null,
                    'qty'            => (int)$item->qty,
                    'unit_price'     => $unitPrice,
                    'is_por'         => $variantAttr->is_por,
                    'total'          => $lineTotal,
                    'image'          => $variantAttr->image,
                ];

                $grandTotal += $lineTotal;
                $totalQty += (int)$item->qty;
            }

            /*
            |--------------------------------------------------------------------------
            | CUSTOM FIELDS FROM DB (CORRECT WAY)
            |--------------------------------------------------------------------------
            */

            $customFieldsSnapshot = [];

            if ($variant) {

                $customFields = VariantCustomField::with('customFieldType')
                    ->where('variant_id', $variant->id)
                    ->orderBy('field_order', 'asc')
                    ->get();

                foreach ($customFields as $cf) {
                    $customFieldsSnapshot[] = [
                        'type_id'   => $cf->custom_field_type_id,
                        'type_name' => $cf->customFieldType->name ?? null,
                        'value'     => $cf->field_value,
                        'order'     => $cf->field_order,
                    ];
                }
            }

            $productSnapshots[] = [
                'product' => [
                    'id' => $product->id,
                    'name' => $product->title,
                    'image' => $product->image,
                ],
                'variant' => $variant ? [
                    'id' => $variant->id,
                    'name' => $variant->name,
                    'product_code_type_name' => $variant->productCodeType->name ?? null,
                ] : null,
                'custom_fields' => $customFieldsSnapshot,
                'attributes' => $attributes,
            ];
        }

        Cart::where('user_id', $salesmanId)->delete();

        $order->update([
            'total_qty' => $totalQty,
            'total_amount' => $grandTotal,
            'product_json' => json_encode($productSnapshots),
        ]);

        DB::commit();

        return response()->json([
            'status'   => 'success',
            'msg'      => 'Order punched successfully',
            'order_id' => $order->id,
            'redirect' => route('front.order.success', $order->id)
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'status' => 'error',
            'msg' => $e->getMessage(),
        ]);
    }
}

  public function order_placed(Request $request)
  {
    if ($request->session()->has('ORDER_ID')) {
      return view('front.order_placed');
    } else {
      return redirect('/');
    }
  }

  public function order(Request $request)
  {
    $logged_user_id = Auth::id();
    $result['orders'] = Order::where('user_id', $logged_user_id)->get();
    return view('front.order', $result);
  }
  // public function order_detail(Request $request, $id)
  // {
  //   $logged_user_id = Auth::id();

  //   $orderDetails = OrderDetails::with([
  //     'order',
  //     'product',
  //     'variant',
  //     'variantAttribute'
  //   ])
  //     ->where('order_id', $id)
  //     ->whereHas('order', function ($q) use ($logged_user_id) {
  //       $q->where('user_id', $logged_user_id);
  //     })
  //     ->get();

  //   if ($orderDetails->isEmpty()) {
  //     return redirect('/');
  //   }

  //   $order = $orderDetails->first()->order;

  //   return view('front.order_detail', compact('orderDetails', 'order'));
  // }


  public function order_detail(Request $request, $id)
  {
    $logged_user_id = Auth::id();

    $order = Order::where('id', $id)
      ->where('user_id', $logged_user_id)
      ->first();

    if (!$order) {
      return redirect('/')->with('error', 'Order not found');
    }

    $items = [];
    if (!empty($order->product_json)) {
      $items = json_decode($order->product_json, true);
      if (!is_array($items)) {
        $items = [];
      }
    }

    if (empty($items)) {
      return redirect('/')->with('error', 'No order items found');
    }

    return view('front.order_detail', compact('order', 'items'));
  }

  public function removeCartItem(Request $request)
  {
    $request->validate([
      'cart_id' => 'required|integer'
    ]);
    $userId = Auth::id();

    Cart::where('id', $request->cart_id)
      ->where('user_id', $userId)
      ->delete();

    return response()->json([
      'status' => true,
      'message' => 'Item removed from cart'
    ]);
  }

  public function updateCartQty(Request $request)
  {
    $request->validate([
      'cart_id' => 'required|integer',
      'qty'     => 'required|integer|min:1',
    ]);

    $userId = Auth::id();

    $cart = Cart::where('id', $request->cart_id)
      ->where('user_id', $userId)
      ->firstOrFail();

    // ✅ Update qty
    $cart->qty = $request->qty;
    $cart->save();

    // ✅ Get price (attribute → variant → product fallback)
    $item = DB::table('carts')
      ->leftJoin('products', 'products.id', '=', 'carts.product_id')
      ->leftJoin('variants', 'variants.id', '=', 'carts.variant_id')
      ->leftJoin('variant_attributes', 'variant_attributes.id', '=', 'carts.variant_attribute_id')
      ->where('carts.id', $cart->id)
      ->select(
        'carts.qty',
        DB::raw('
                COALESCE(
                    variant_attributes.sell_price,
                    variants.sell_price,
                    variants.mrp,
                    products.sale_price,
                    products.price
                ) as price
            ')
      )
      ->first();

    // ✅ Cart total
    $cartTotal = DB::table('carts')
      ->leftJoin('products', 'products.id', '=', 'carts.product_id')
      ->leftJoin('variants', 'variants.id', '=', 'carts.variant_id')
      ->leftJoin('variant_attributes', 'variant_attributes.id', '=', 'carts.variant_attribute_id')
      ->where('carts.user_id', $userId)
      ->select(DB::raw('SUM(
            carts.qty *
            COALESCE(
                variant_attributes.sell_price,
                variants.sell_price,
                variants.mrp,
                products.sale_price,
                products.price
            )
        ) as total'))
      ->value('total');

    return response()->json([
      'status'      => true,
      'qty'         => $item->qty,
      'price'       => (float)$item->price,
      'row_total'   => $item->qty * $item->price,
      'cart_total'  => $cartTotal ?? 0,
    ]);
  }

  // app/Http/Controllers/FrontController.php

  // public function orderSuccess($orderId)
  // {
  //   $userId = Auth::id();

  //   $order = DB::table('orders')
  //     ->where('id', $orderId)
  //     ->where('user_id', $userId)
  //     ->first();

  //   if (!$order) {
  //     return redirect('/')->with('error', 'Order not found');
  //   }

  //   $items = DB::table('order_details')
  //     ->leftJoin('products', 'products.id', '=', 'order_details.product_id')
  //     ->leftJoin('variants', 'variants.id', '=', 'order_details.variant_id')
  //     ->leftJoin('attributes', 'attributes.id', '=', 'order_details.variant_attribute_id')

  //     // ->leftJoin(
  //     //     'variant_attributes',
  //     //     'variant_attributes.id',
  //     //     '=',
  //     //     'order_details.variant_attribute_id'
  //     // )
  //     ->where('order_details.order_id', $orderId)
  //     ->select(
  //       'products.title',
  //       'products.image',
  //       'products.product_type',
  //       'order_details.qty',
  //       'order_details.price',
  //       'variants.name as variant_name',

  //       // Attribute
  //       'attributes.name as attribute_name',

  //       // 'variant_attributes.attribute_name as attribute_name',
  //       // 'variant_attributes.attribute_value as attribute_value',
  //     )
  //     ->get();

  //   return view('front.order_placed', compact('order', 'items'));
  // }

  public function orderSuccess($orderId)
  {
    $userId = Auth::id();

    $order = DB::table('orders')
      ->where('id', $orderId)
      ->where('user_id', $userId)
      ->first();

    if (!$order) {
      return redirect('/')->with('error', 'Order not found');
    }

    // 🔥 Decode snapshot (single source of truth)
    $items = [];

    if (!empty($order->product_json)) {
      $items = json_decode($order->product_json, true);
    }

    return view('front.order_placed', compact('order', 'items'));
  }
}
