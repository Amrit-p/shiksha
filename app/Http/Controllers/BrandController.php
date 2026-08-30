<?php
namespace App\Http\Controllers;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
  public function index()
  {
    return view('admin.brand.index');
  }
  public function getBrandList()
  {

    $query = Brand::get();
    return DataTables::of($query)
      ->addColumn('action', function ($data) {
        return '
        <a href="#" title="Edit">
            <i class="ik ik-edit f-16 mr-15 text-green"></i>
        </a>
        <a href="javascript:void(0);" 
           class="delete_btn" 
           data-id="' . $data->id . '" 
           title="Delete">
            <i class="ik ik-trash-2 f-16 text-red"></i>
        </a>
    ';
      })
      ->editColumn('image', function ($data) {
        // Prepare image URL
        $imageUrl = $data->image ? asset('storage/' . $data->image) : asset('images/default.png');

        // Return HTML for clickable image
        return '
            <img src="' . $imageUrl . '" 
                 class="table-brand-thumb" 
                 style="width:40px;  object-fit:cover; cursor:pointer;" 
                 onclick="openImageUpload(' . $data->id . ')">
        ';
      })




      ->editColumn('status', function ($data) {
        if ($data->status == 1) {
          return "Active";
        } else {
          return "Deactive";
        }
      })
      ->addColumn('checkbox', function ($data) {
        return '<label class="custom-control custom-checkbox">
          <input type="checkbox" class="custom-control-input select_all_child" id="" name="" value="option2">
          <span class="custom-control-label">&nbsp;</span>
        </label>';
      })
      ->rawColumns(['image', 'checkbox', 'action'])
      ->make(true);
  }
  public function create()
  {
    return view('admin.brand.create', compact('data'));
  }
  public function store(Request $request)
  {

    $imagePath = "";

    if ($request->hasFile('image')) {
      $imagePath = $request->file('image')->store('brand', 'public');
    }

    $create = Brand::create([
      'name' => $request->name,
      'image' => $imagePath,
      'status' => 1
    ]);
       
    if ($create) {
      MessageFlashHelper('success', 'Brand Store Successfully');
    } else {
      MessageFlashHelper('error', 'Something went wrong!');
    }
    return redirect()->back();
  }
  public function delete($id)
  {
    $delete = Brand::find($id)->delete();
    if ($delete) {
      return response()->json(['message' => 'Brand Deleted Successfully!', 'success' => true], 200);
    } else {
      return response()->json(['message' => 'something went wrong!', 'success' => false], 500);
    }
  }
}
