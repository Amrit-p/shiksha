<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CustomFieldType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomFieldTypeController extends Controller
{
    /**
     * Display a listing of custom field types
     */
    public function index()
    {
        $customFieldType = CustomFieldType::orderBy('display_order')
            ->orderBy('name')
            ->get();

        return view('admin.custom_field_type.index', compact('customFieldType'));
    }

    /**
     * Show the form for creating a new custom field type
     */
    public function create()
    {
        return view('admin.custom_field_type.create');
    }

    /**
     * Store a newly created custom field type
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:custom_field_types,name',
            'description' => 'nullable|string|max:500',
            'display_order' => 'nullable|integer|min:0',
            'status' => 'nullable|boolean',
        ]);

        CustomFieldType::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'display_order' => $request->display_order ?? 0,
            'status' => $request->status ?? 1,
        ]);

        return redirect()
            ->route('admin.custom-field-type.index')
            ->with('success', 'Custom field type created successfully');
    }

    /**
     * Show the form for editing the specified custom field type
     */
    public function edit($id)
    {
        $customFieldType = CustomFieldType::findOrFail($id);
        return view('admin.custom_field_type.edit', compact('customFieldType'));
    }

    /**
     * Update the specified custom field type
     */
    public function update(Request $request, $id)
    {
        $customFieldType = CustomFieldType::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:custom_field_types,name,' . $id,
            'description' => 'nullable|string|max:500',
            'display_order' => 'nullable|integer|min:0',
            'status' => 'nullable|boolean',
        ]);

        $customFieldType->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'display_order' => $request->display_order ?? 0,
            'status' => $request->status ?? 1,
        ]);

        return redirect()
            ->route('admin.custom-field-type.index')
            ->with('success', 'Custom field type updated successfully');
    }

    /**
     * Remove the specified custom field type
     */
    public function destroy($id)
    {
        $customFieldType = CustomFieldType::findOrFail($id);
        
        // Check if this field type is being used
        $usageCount = $customFieldType->variantCustomFields()->count();
        
        if ($usageCount > 0) {
            return back()->withErrors("Cannot delete. This field type is being used in {$usageCount} variant(s).");
        }

        $customFieldType->delete();

        return redirect()
            ->route('admin.custom_field_type.index')
            ->with('success', 'Custom field type deleted successfully');
    }

    /**
     * Toggle status (active/inactive)
     */
    public function toggleStatus($id)
    {
        $customFieldType = CustomFieldType::findOrFail($id);
        $customFieldType->status = !$customFieldType->status;
        $customFieldType->save();

        return back()->with('success', 'Status updated successfully');
    }
}