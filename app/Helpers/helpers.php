<?php

use App\Models\Client;
use App\Models\Country;
use App\Models\ServiceType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use function Psy\debug;

if (!function_exists('successAlert')) {
  function successAlert()
  {
    if (session()->has('success')) {
      return '
                <div class="custom-alert success-alert alert-timeout">
                    <i class="fa fa-check-circle"></i> ' . session('success') . '
                </div>
            ';
    }
    return '';
  }
}



function getPlanExpireDate($start_date, $duration)
{
  $startDate = Carbon::parse($start_date);
  $years = (int) $duration;
  $expireDate = $startDate->copy()->addYears($years);
  return $expireDate->format('Y-m-d');
}

// function get_service_plan_id($slug)
// {
//   $cleanSlug = Str::slug(trim($slug));
//   $service = ServiceType::where('slug', $cleanSlug)->first();
//   return $service->id;
// }
#we are making dynamic above function




#Check Client Exit Or Not It Will Return True If Exit Else Return False!
// function checkClientExist($email)
// {
//   $email = strtolower($email); // convert to lowercase
//   return Client::where('email', $email)->exists();
// }

//one man army function for find slug id we use is everywhere according to the requirement!
function getIdFromSlug($tables, $slug = 'other')
{
  $cleanSlug = Str::slug(trim($slug));
  $data = DB::table($tables)->where('slug', $cleanSlug)->first();
  return $data ? $data->id : false;
}

#serach
#It Will Return Table Row Where Id Match Else Return False;
function findTableRow($table_name, $search_array, $additional = "")
{  //['name'=>'joban','age'=>18]
  $data = DB::table($table_name)->where($search_array);
  return $data ? $data : false;
}


#store
function storeHelper($table_name, $form_data, $message = "")
{
  $form_data =  $form_data->except('_token');
  $form_data['slug'] = Str::slug($form_data['name'], '-');
  $form_data['status'] = 'active';
  $form_data['created_at'] = now();
  $form_data['updated_at'] = now();
  $store = DB::table($table_name)->insert($form_data);
  if ($store) {
    Session::flash('success', $message . 'Record stored successfully!');
    return redirect()->back();
  } else {
    Session::flash('err', 'Failed to store record.');
    return redirect()->back();
  }
}

#delete 
function deleteHelper($table_name, $id, $message)
{
  $delete = DB::table($table_name)->where('id', $id)->delete();
  if ($delete) {
    Session::flash('success', $message ?: 'Record stored successfully!');
    return redirect()->back();
  } else {
    Session::flash('err', 'Failed to store record.');
    return redirect()->back();
  }
}

#getbyId
function SearchByIdHelper($table_name, $id)
{
  return DB::table($table_name)->where('id', $id)->first();
}

#update;
function updateHelper($table_name, $id, $request, $message)
{
  $err_msg = 'Something Went Wrong!!';
  $form_data = $request->except(['_method', '_token', 'update_id']);
  $update = DB::table($table_name)->where('id', $id)->update($form_data);
  if ($update) {
    Session::flash('success', $message . 'Record stored successfully!');
    return true;
  } else {
    Session::flash('err', $err_msg);
    return false;
  }
}

function getDataHelper($table_name, $where_condition_array)
{
  if (is_array($table_name)) {
    $data = [];
    foreach ($table_name as $table) {
      $data[$table] = getDataHelper($table, $where_condition_array);
    }
    return $data;
  } else {
    return DB::table($table_name)->where($where_condition_array)->get();
  }
}




if (!function_exists('dmyTimeHelper')) {
  function dmyTimeHelper($date)
  {
    if (!$date) {
      return null;
    }

    // If already Carbon (Eloquent date)
    if ($date instanceof Carbon) {
      return $date->timezone('Asia/Kolkata')
        ->format('d-m-Y h:i A');
    }

    // If string (fallback)
    return Carbon::createFromFormat('Y-m-d H:i:s', $date, 'UTC')
      ->timezone('Asia/Kolkata')
      ->format('d-m-Y h:i A');
  }
}



if (!function_exists('dmyHelper')) {
  function dmyHelper($date)
  {
    if (!$date) {
      return null; // or return '-' if you want a placeholder
    }

    return Carbon::parse($date)->format('d-m-Y');
  }
}

function MessageFlashHelper($key, $message)
{
  Session::flash($key, $message);
}


function prx($arr)
{
  echo "<pre>";
  print_r($arr);
  die();
}

function getTopNavCat()
{
  $result = DB::table('categories')
    ->where(['status' => 1])
    ->get();
  $arr = [];
  foreach ($result as $row) {
    $arr[$row->id]['name'] = $row->name;
    $arr[$row->id]['parent_id'] = $row->category_id;
    $arr[$row->id]['category_slug'] = $row->slug;
  }
  // return $arr;
  $str = buildTreeView($arr, 0);
  return $str;
}

$html = '';
function buildTreeView($arr, $parent, $level = 0, $prelevel = -1)
{
  global $html;
  foreach ($arr as $id => $data) {
    if ($parent == $data['parent_id']) {
      
      if ($level > $prelevel) {
        if ($html == '') {
          $html .= '<ul class="nav navbar-nav">';
        } else {
          $html .= '<ul class="dropdown-menu">';
        }
      }

      if ($level == $prelevel) {
        $html .= '</li>';
      }
      $url = route('front.category', $data['category_slug']);
      $html .= '<li><a href="' . $url . '">' . $data['name'] . '<span class="caret"></span></a>';
      if ($level > $prelevel) {
        $prelevel = $level;
      }
      $level++;
      buildTreeView($arr, $id, $level, $prelevel);
      $level--;
    }
  }
  if ($level == $prelevel) {
    $html .= '</li></ul>';
  }
  return $html;
}


function getUserTempId(){
if(session()->has('USER_TEMP_ID') === null){
  $rand = rand(111111111,999999999);
  session()->put('USER_TEMP_ID',$rand);
  return $rand;
}else{
  return session()->get('USER_TEMP_ID');
}
}



function getAddToCartTotalItem()
{
    $user_id = Auth::id();

    return DB::table('carts')
        ->leftJoin('products', 'products.id', '=', 'carts.product_id')
        ->leftJoin('variants', 'variants.id', '=', 'carts.variant_id')
        ->leftJoin('variant_attributes', 'variant_attributes.id', '=', 'carts.variant_attribute_id')
        ->leftJoin('attributes', 'attributes.id', '=', 'variant_attributes.attribute_id')
        ->leftJoin('product_code_types', 'product_code_types.id', '=', 'variants.product_code_type_id')
        ->leftJoin('attribute_options', 'attribute_options.id', '=', 'variant_attributes.attribute_option_id')
        ->where('carts.user_id', $user_id)
        ->select( 
            'carts.product_id',
            'carts.variant_id',
            'carts.variant_attribute_id',
            'carts.id as cart_id',
            'carts.qty',
            'carts.price',

            // product
            'products.title as product_title',
            'products.image',
            'products.slug',
            'products.product_type as product_type',
            'products.price as product_price',
            'products.sale_price as product_sale_price',

            // variant
            'variant_attributes.id as variant_attribute_variant_id',
            'variant_attributes.is_por as is_por',

            'variant_attributes.mrp as variant_attribute_mrp',
            'variant_attributes.sell_price as variant_attribute_sell_price',
            'variants.name as variant_name',

            // attribute
            'attributes.name as attribute_name',
            'attribute_options.name as attribute_option_name',
            // product code type
            'product_code_types.name as product_code_type_name',
            
        )
        ->get();
}


// function getAddToCartTotalItem()
// {
//   $user_id = Auth::user()->id;
//   $result = DB::table('carts')
//     ->leftJoin('products', 'products.id', '=', 'carts.product_id')
//     ->leftJoin('variants', 'variants.id', '=', 'carts.product_attr_id')
//     ->where(['user_id' => $user_id])
//      ->select('carts.qty', 'products.title', 'products.image', 'variants.price', 'products.slug', 'products.id as pid', 'variants.id as attr_id')
//     ->get();
//   return $result;
// }
// prx(getAddToCartTotalItem());
