<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use MessageFormatter;
use Symfony\Component\Mime\Message;
use Yajra\DataTables\DataTables;

use function Pest\Laravel\json;

// use DataTables;


class CategoryController extends Controller
{
    public function index(){
      $data['categories']=Category::where('status',1)->get();
      return view('admin.category.index',compact('data'));
    }

    public function getCategoryList(Request $request){
        $query = Category::with('parent')->select(['id','image','name','slug','description','status','category_id'])->get();
        return DataTables::of($query)
        ->addIndexColumn()
      ->editColumn('status', function ($data) {
        if ($data->status == 1) {
          return "Active";
        } 
        else {
          return 'Deactive';
        }
      })
      ->addColumn('parent',function($data){
        return ($data->parent)?$data->parent->name:"--";
      })
      ->editColumn('image', function ($data) {
        // Prepare image URL
        $imageUrl = $data->image ? asset('storage/' . $data->image) : asset('images/default.png');
        // Return HTML for clickable image
        return '
            <img src="' . $imageUrl . '" 
                 class="table-banner-thumb" 
                 style="width:40px; border-radius:10px;  object-fit:cover; cursor:pointer;" 
                 onclick="openImageUpload(' . $data->id . ')">
        ';
      })
      ->addColumn('action', function ($data) {
        return '
    <div class="dropdown action-settings">
        <a href="javascript:void(0);" 
           class="text-dark settings-toggle"
           data-toggle="dropdown"
           aria-haspopup="true"
           aria-expanded="false"
           style="font-size:18px;">
            <i class="ri-settings-3-line"></i>
        </a>

        <div class="dropdown-menu dropdown-menu-right">
             <a class="dropdown-item edit_btn text-success"
               href="javascript:void(0);"
               data-id="' . $data->id . '">
                <i class="ri-edit-line mr-2"></i> Edit
            </a>

            <a class="dropdown-item delete_btn text-danger"
               href="javascript:void(0);"
               data-id="' . $data->id . '">
                <i class="ri-delete-bin-5-line mr-2"></i> Delete
            </a>
        </div>
    </div>
    ';
      })
      ->rawColumns(['image','action'])

      ->make(true);
    }

    function edit($id){
    $data = Category::find($id);

     if($data){
      return response()->json(['data'=>$data],200);
     }else{
      return response()->json(['message'=>'something went wrong'],500);
     }


    }

private function generateSlug(Request $request)
{
    return $request->slug
        ? Str::slug($request->slug)
        : Str::slug($request->name);
}

private function uploadCategoryImage(Request $request)
{
    return $request->file('image')->store('category', 'public');
}

public function update(Request $request)
{
    $formData = $request->except(['_token', '_method', 'edit_id']);

    $category = Category::findOrFail($request->edit_id);

    // Generate slug
    $formData['slug'] = $this->generateSlug($request);

    // Upload image if exists
    if ($request->hasFile('image')) {
        $formData['image'] = $this->uploadCategoryImage($request);
    }

    $updated = $category->update($formData);

    if ($updated) {
        MessageFlashHelper('success', 'Success, Category Updated Successfully!');
    } else {
        MessageFlashHelper('error', 'Something went wrong');
    }

    return redirect()->back();
}


     public function delete($id){
      $delete = Category::find($id)->delete();
      if($delete){
        return response()->json(['message'=>'Category Deleted Successfully!!'],204);
      }else{
        return response()->json(['message'=>'Something Went Wrong!!'],500);
      }
     }

     public function store(Request $request){
      $formData = $request->except('_token');
      $slug=$request->slug? Str::slug($request->slug):Str::slug($request->name);
      $formData['slug']=$slug;


     if ($request->hasFile('image')) {
      $imagePath = $request->file('image')->store('category', 'public');
      $formData['image']=$imagePath;
     }




      $store = Category::create($formData);
      if($store){
        MessageFlashHelper('success','Category Created Successfully');
        return redirect()->back();
      }else{
        MessageFlashHelper('error','Something Went Wrong');
        return redirect()->back();
      }




     }
}
