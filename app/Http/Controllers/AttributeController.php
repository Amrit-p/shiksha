<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

class AttributeController extends Controller
{
  public function index()
  {
    return view('admin.attribute.index');
  }

  public function getList()
  {
    $query = Attribute::get();
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate with custom messages
        $messages = [
            'name.required' => 'The attribute name field is required.',
            'name.unique' => 'This attribute name has already been taken.',
            'name.max' => 'The attribute name cannot exceed 255 characters.',
            'name.min' => 'The attribute name must be at least 2 characters.',
        ];

        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255|unique:attributes,name',
        ], $messages);

        // Generate slug
        $validated['slug'] = Str::slug($validated['name']);
        $validated['status'] = 1; // Default to active

        // Create attribute
        $create = Attribute::create($validated);

        if ($request->ajax()) {
            if ($create) {
                return response()->json([
                    'success' => true,
                    'message' => 'Attribute created successfully!',
                    'data' => $create
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong!'
                ], 500);
            }
        }

        // For non-AJAX requests
        if ($create) {
            MessageFlashHelper('success', 'Attribute Store Successfully');
        } else {
            MessageFlashHelper('error', 'Something went wrong!');
        }

        return redirect()->back();
    }

  public function delete($id)
  {
    $delete = Attribute::find($id)->delete();
    if ($delete) {
      return response()->json(['message' => 'Attribute Deleted Successfully!', 'success' => true], 200);
    } else {
      return response()->json(['message' => 'something went wrong!', 'success' => false], 500);
    }
  }

  public function edit($id)
  {

    $data = Attribute::find($id);
    if ($data) {
      return response()->json(['data' => $data, 'success' => true], 200);
    } else {
      return response()->json(['success' => false], 500);
    }
  }

   /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // Check if edit_id exists
        if (!$request->has('edit_id') || !$request->edit_id) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attribute ID is missing',
                    'errors' => ['edit_id' => ['Attribute ID is missing']]
                ], 422);
            }
            return redirect()->back()->withErrors(['edit_id' => 'Attribute ID is missing']);
        }

        // Check if attribute exists
        $attribute = Attribute::find($request->edit_id);
        if (!$attribute) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attribute not found',
                    'errors' => ['edit_id' => ['Attribute not found']]
                ], 422);
            }
            return redirect()->back()->withErrors(['edit_id' => 'Attribute not found']);
        }

        // Custom validation messages
        $messages = [
            'name.required' => 'The attribute name field is required.',
            'name.unique' => 'This attribute name has already been taken.',
            'name.max' => 'The attribute name cannot exceed 255 characters.',
            'name.min' => 'The attribute name must be at least 2 characters.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The selected status is invalid.',
        ];

        // Validate the input
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                Rule::unique('attributes', 'name')->ignore($request->edit_id),
            ],
            'status' => 'required|in:0,1',
        ], $messages);

        // Generate slug
        $validated['slug'] = Str::slug($validated['name']);

        // Update the attribute
        $update = $attribute->update($validated);

        if ($request->ajax()) {
            if ($update) {
                return response()->json([
                    'success' => true,
                    'message' => 'Attribute updated successfully!',
                    'data' => $attribute
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong!',
                    'errors' => []
                ], 500);
            }
        }

        // For non-AJAX requests
        if ($update) {
            MessageFlashHelper('success', 'Attribute Updated Successfully!');
        } else {
            MessageFlashHelper('error', 'Something Went Wrong!');
        }

        return redirect()->back();
    }

}
