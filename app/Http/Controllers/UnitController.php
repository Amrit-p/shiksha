<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;
use Yajra\DataTables\Facades\DataTables;

class UnitController extends Controller
{
    /**
     * Index page
     */
    public function index()
    {
        return view('admin.unit.index');
    }

    /**
     * Store new unit
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'short_name' => 'required|string|max:50',
            // 'multiplier' => 'nullable|numeric|min:0',
        ]);

        Unit::create([
            'name'       => $request->name,
            'short_name' => $request->short_name,
            'multiplier' =>  1,
            'status'     => 1,
        ]);

        return redirect()->back()->with('success', 'Unit added successfully');
    }

    /**
     * Datatable list
     */
    public function list(Request $request)
    {
        $query = Unit::query()->latest();

        return DataTables::of($query)
            ->addColumn('checkbox', function ($row) {
                return '
                    <label class="custom-control custom-checkbox m-0">
                        <input type="checkbox" class="custom-control-input" value="'.$row->id.'">
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
                       data-id="'.$row->id.'">
                        Edit
                    </a>

                    <a href="javascript:void(0)"
                       class="btn btn-sm btn-danger delete_btn"
                       data-id="'.$row->id.'">
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
        $unit = Unit::findOrFail($id);

        return response()->json([
            'status' => true,
            'data'   => $unit,
        ]);
    }

    /**
     * Update unit
     */
    public function update(Request $request)
    {
        $request->validate([
            'edit_id'    => 'required|exists:units,id',
            'name'       => 'required|string|max:255',
            'short_name' => 'required|string|max:50',
            'multiplier' => 'nullable|numeric|min:0',
            'status'     => 'required|in:0,1',
        ]);

        $unit = Unit::findOrFail($request->edit_id);

        $unit->update([
            'name'       => $request->name,
            'short_name' => $request->short_name,
            'multiplier' => $request->multiplier ?? 1,
            'status'     => $request->status,
        ]);

        return redirect()->back()->with('success', 'Unit updated successfully');
    }

    /**
     * Delete unit
     */
    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);
        $unit->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Unit deleted successfully',
        ]);
    }
}
