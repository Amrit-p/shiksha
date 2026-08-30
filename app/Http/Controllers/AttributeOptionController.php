<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\AttributeOption;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use MessageFormatter;
use Yajra\DataTables\Facades\DataTables;

class AttributeOptionController extends Controller
{
  public function index()
  {
    $result['attributes'] = Attribute::where('status',1)->get();
    return view('admin.attribute_option.index',$result);
  }


  public function getList() 
  {

    $query = AttributeOption::with('attribute')->get();
    return DataTables::of($query)
      ->addColumn('action', function ($data) {      
        return '
        <a data-id="' . $data->id . '" href="javascript:void(0)" 
        class="edit_btn
        title="Edit">
            <i class="ik ik-edit f-16 mr-15 text-green"></i>
        </a>
        <a href="javascript:void(0);" 
           class="delete_btn" 
           data-id="' . $data->id . '" 
           title="Delete">
            <i class="ik ik-trash-2 f-16 text-red"></i>
        </a>';
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
      ->rawColumns(['checkbox', 'action'])
      ->make(true);
  }


  /**
   * ✅ NEW - Check if attribute option name is unique (AJAX endpoint)
   * Used for real-time validation in create/edit forms
   */
  public function checkUnique(Request $request)
  {
    $name = $request->name;
    $attributeId = $request->attribute_id;
    $id = $request->id; // null for create, set for edit
    
    // Check if name exists for this attribute (excluding current record if editing)
    $exists = AttributeOption::where('name', $name)
        ->where('attribute_id', $attributeId)
        ->when($id, function($query) use ($id) {
            return $query->where('id', '!=', $id);
        })
        ->exists();
    
    return response()->json([
        'exists' => $exists
    ]);
  }


  public function store(Request $request)
  {
    // ✅ Server-side validation with uniqueness check
    $request->validate([
      'name' => [
        'required',
        'string',
        'max:255',
        Rule::unique('attribute_options')
          ->where('attribute_id', $request->attribute_id)
      ],
      'attribute_id' => 'required|exists:attributes,id',
      'status' => 'required|in:0,1',
    ], [
      'name.unique' => 'This attribute option name already exists for the selected attribute.',
    ]);

    $formData = $request->except(['_token']);
    $formData['slug'] = Str::slug($request->name);
    
    $create = AttributeOption::create($formData);
    
    if ($create) {
      MessageFlashHelper('success', 'Attribute Option Created Successfully');
    } else {
      MessageFlashHelper('error', 'Something went wrong!');
    }
    
    return redirect()->back();
  }

  
  public function delete($id)
  {
    $delete = AttributeOption::find($id)->delete();
    if ($delete) {
      return response()->json(['message' => 'Attribute Option Deleted Successfully!', 'success' => true], 200);
    } else {
      return response()->json(['message' => 'something went wrong!', 'success' => false], 500);
    }
  }


  public function edit($id)
  {
    $data = AttributeOption::with('attribute')->find($id);
    if ($data) {
      return response()->json(['data' => $data, 'success' => true], 200);
    } else {
      return response()->json(['success' => false], 500);
    }
  }                 


  public function update(Request $request)
  {
    // ✅ Server-side validation with uniqueness check (excluding current record)
    $request->validate([
      'name' => [
        'required',
        'string',
        'max:255',
        Rule::unique('attribute_options')
          ->where('attribute_id', $request->attribute_id)
          ->ignore($request->edit_id)
      ],
      'attribute_id' => 'required|exists:attributes,id',
      'status' => 'required|in:0,1',
    ], [
      'name.unique' => 'This attribute option name already exists for the selected attribute.',
    ]);

    $formData = $request->except(['_token', '_method', 'edit_id']);
    $formData['slug'] = Str::slug($request->name);
    
    $update = AttributeOption::find($request->edit_id)->update($formData);
    
    if ($update) {
      MessageFlashHelper('success', 'Attribute Option Updated Successfully!');
      return redirect()->back();
    } else {
      MessageFlashHelper('error', 'Something Went Wrong!');
      return redirect()->back();
    }
  }
}