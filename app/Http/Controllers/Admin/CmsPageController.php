<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CmsPageController extends Controller
{
    public function index()
    {
        $pages = CmsPage::orderBy('sort_order')->get();

        return view('admin.cms.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.cms.form', ['page' => new CmsPage()]);
    }

    public function store(Request $request)
    {
        CmsPage::create($this->validated($request));

        return redirect()->route('admin.cms.index')->with('success', 'Page created.');
    }

    public function edit($id)
    {
        $page = CmsPage::findOrFail($id);

        return view('admin.cms.form', compact('page'));
    }

    public function update(Request $request, $id)
    {
        $page = CmsPage::findOrFail($id);
        $page->update($this->validated($request, $page->id));

        return redirect()->route('admin.cms.index')->with('success', 'Page updated.');
    }

    public function destroy($id)
    {
        CmsPage::findOrFail($id)->delete();

        return back()->with('success', 'Page deleted.');
    }

    protected function validated(Request $request, ?int $id = null): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:190',
            'slug' => 'nullable|string|max:190|unique:cms_pages,slug,'.($id ?: 'NULL').',id',
            'content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:190',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data['slug'] = ! empty($data['slug']) ? $data['slug'] : Str::slug($data['title']);
        $data['show_in_footer'] = $request->boolean('show_in_footer');
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
