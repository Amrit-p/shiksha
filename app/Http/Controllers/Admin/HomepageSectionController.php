<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use Illuminate\Http\Request;

class HomepageSectionController extends Controller
{
    public function index()
    {
        $sections = HomepageSection::orderBy('sort_order')->get();

        return view('admin.homepage.index', compact('sections'));
    }

    public function edit($id)
    {
        $section = HomepageSection::findOrFail($id);

        return view('admin.homepage.edit', compact('section'));
    }

    public function update(Request $request, $id)
    {
        $section = HomepageSection::findOrFail($id);

        $data = $request->validate([
            'title' => 'nullable|string|max:190',
            'subtitle' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('homepage', 'public');
        }

        $data['is_active'] = $request->boolean('is_active');
        $section->update($data);

        return redirect()->route('admin.homepage.index')->with('success', 'Homepage section updated.');
    }
}
