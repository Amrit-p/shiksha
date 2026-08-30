<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCodeType;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use Illuminate\Validation\Rule;



class ProductCodeTypeController extends Controller
{
   /**
     * Index page
     */
    public function index()
    {
        return view('admin.product_code_type.index');
    }

    /**
     * Store new product code type
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
                Rule::unique('product_code_types', 'name'),
            ],
        ], [
            'name.unique' => 'This product code type already exists. Please use a different name.',
        ]);

        ProductCodeType::create([
            'name'   => $request->name,
            'status' => 1,
        ]);

        return redirect()->back()->with('success', 'Product code type added successfully');
    }

    /**
     * Datatable list
     */
    public function list(Request $request)
    {
        $query = ProductCodeType::query()
            ->select('product_code_types.*')
            ->selectRaw('(
                SELECT COUNT(*) FROM variants
                WHERE variants.product_code_type_id = product_code_types.id
            ) AS variant_usage_count')
            ->selectRaw('(
                SELECT COUNT(DISTINCT product_id) FROM variants
                WHERE variants.product_code_type_id = product_code_types.id
            ) AS products_assigned_count')
            ->latest('product_code_types.id');

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

            ->addColumn('usage', function ($row) {
                $products = (int) ($row->products_assigned_count ?? 0);
                $variants = (int) ($row->variant_usage_count ?? 0);
                return '<span class="text-nowrap" title="Distinct products · Variant rows">'
                    . '<strong>' . $products . '</strong> ' . ($products === 1 ? 'product' : 'products')
                    . ' · <strong>' . $variants . '</strong> ' . ($variants === 1 ? 'variant' : 'variants')
                    . '</span>';
            })

            ->addColumn('action', function ($row) {
                $products = (int) ($row->products_assigned_count ?? 0);
                $variants = (int) ($row->variant_usage_count ?? 0);
                $inUse = $variants > 0;

                $deleteBtn = $inUse
                    ? '<button type="button" class="btn btn-sm btn-secondary" disabled title="Unassign from all products (variants) before deleting.">'
                        . 'Delete</button>'
                    : '<a href="javascript:void(0)" class="btn btn-sm btn-danger delete_btn" data-id="' . $row->id . '">'
                        . 'Delete</a>';

                return '
                    <a href="javascript:void(0)"
                       class="btn btn-sm btn-primary edit_btn"
                       data-id="' . $row->id . '">
                        Edit
                    </a>
                    ' . $deleteBtn . '
                ';
            })

            ->rawColumns(['checkbox', 'status', 'usage', 'action'])
            ->make(true);
    }

    /**
     * Edit (AJAX fetch)
     */
    public function edit($id)
    {
        $productCodeType = ProductCodeType::findOrFail($id);

        return response()->json([
            'status' => true,
            'data'   => $productCodeType,
        ]);
    }

    /**
     * Update product code type
     */
    public function update(Request $request)
    {
        $request->validate([
            'edit_id' => 'required|exists:product_code_types,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_code_types', 'name')->ignore($request->edit_id),
            ],
            'status' => 'required|in:0,1',
        ]);

        $productCodeType = ProductCodeType::findOrFail($request->edit_id);

        $productCodeType->update([
            'name'   => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Product code type updated successfully');
    }

    /**
     * Soft delete product code type (only when not in use on any variant).
     */
    public function destroy($id)
    {
        $productCodeType = ProductCodeType::findOrFail($id);

        $stats = DB::table('variants')
            ->where('product_code_type_id', $productCodeType->id)
            ->selectRaw('COUNT(*) as variants_count, COUNT(DISTINCT product_id) as products_count')
            ->first();

        $variantCount = (int) ($stats->variants_count ?? 0);
        $productCount = (int) ($stats->products_count ?? 0);

        if ($variantCount > 0) {
            return response()->json([
                'status'  => false,
                'message' => 'Cannot delete: this type is assigned to ' . $productCount . ' product(s) across '
                    . $variantCount . ' variant row(s). Remove the assignment from all variants first.',
            ], 422);
        }

        $productCodeType->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Product code type deleted successfully',
        ]);
    }

    /**
     * Route alias (web.php uses delete action).
     */
    public function delete($id)
    {
        return $this->destroy($id);
    }
}
