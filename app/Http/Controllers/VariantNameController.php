<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VariantName;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

class VariantNameController extends Controller
{
    /**
     * Index page
     */
    public function index()
    {
        return view('admin.variant_name.index');
    }

    /**
     * Store new variant name
     */
    public function store(Request $request)
    {
        // ✅ Add unique validation
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                Rule::unique('variant_names', 'name'), // ✅ This checks uniqueness
            ],
        ], [
            'name.unique' => 'This variant name already exists. Please use a different name.',
        ]);

        VariantName::create([
            'name'   => $request->name,
            'status' => 1,
        ]);

        return redirect()->back()->with('success', 'Variant name added successfully');
    }

    /**
     * Datatable list
     */
    public function list(Request $request)
    {
        $query = VariantName::latest();

        return DataTables::of($query)
            ->addColumn('checkbox', function ($row) {
                return '
                    <label class="custom-control custom-checkbox m-0">
                        <input type="checkbox" class="custom-control-input" value="' . $row->id . '">
                        <span class="custom-control-label">&nbsp;</span>
                    </label>
                ';
            })

            ->addColumn('status', function ($row) {
                return $row->status
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-danger">Inactive</span>';
            })

            ->addColumn('action', function ($row) {
                return '
                    <a href="javascript:void(0)"
                       class="btn btn-sm btn-primary edit_btn"
                       data-id="' . $row->id . '">
                        Edit
                    </a>

                    <a href="javascript:void(0)"
                       class="btn btn-sm btn-danger delete_btn"
                       data-id="' . $row->id . '">
                        Delete
                    </a>
                ';
            })

            ->rawColumns(['checkbox', 'status', 'action'])
            ->make(true);
    }

    /**
     * Edit (AJAX fetch)
     */
    public function edit($id)
    {
        $variantName = VariantName::findOrFail($id);

        return response()->json([
            'status' => true,
            'data'   => $variantName,
        ]);
    }

    /**
     * Update variant name
     */
    public function update(Request $request)
    {
        $request->validate([
            'edit_id' => 'required|exists:variant_names,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('variant_names', 'name')->ignore($request->edit_id),
            ],
            'status' => 'required|in:0,1',
        ]);


        $variantName = VariantName::findOrFail($request->edit_id);

        $variantName->update([
            'name'   => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Variant name updated successfully');
    }

    /**
     * Soft delete variant name
     */
    public function destroy($id)
    {
        $variantName = VariantName::findOrFail($id);
        $variantName->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Variant name deleted successfully',
        ]);
    }
}
