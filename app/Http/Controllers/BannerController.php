<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/** Banners / current schemes shown on the salesman home page (slider + daily popup). */
class BannerController extends Controller
{
  public function index()
  {
    $banners = Banner::orderBy('sort_order')->orderByDesc('id')->paginate(25);

    return view('admin.banner.index', compact('banners'));
  }

  public function create()
  {
    return view('admin.banner.form', ['banner' => new Banner(['status' => 1, 'sort_order' => 0])]);
  }

  public function store(Request $request)
  {
    $data = $this->validated($request, true);
    $data['image'] = $request->file('image')->store('banners', 'public');

    Banner::create($data);

    return redirect()->route('admin.banner')->with('success', 'Banner saved.');
  }

  public function edit(Banner $banner)
  {
    return view('admin.banner.form', compact('banner'));
  }

  public function update(Request $request, Banner $banner)
  {
    $data = $this->validated($request, false);

    if ($request->hasFile('image')) {
      $old = $banner->image;
      $data['image'] = $request->file('image')->store('banners', 'public');
      if ($old) {
        Storage::disk('public')->delete($old);
      }
    }

    $banner->update($data);

    return redirect()->route('admin.banner')->with('success', 'Banner updated.');
  }

  public function toggle(Banner $banner)
  {
    $banner->update(['status' => (int) $banner->status === 1 ? 0 : 1]);

    return back()->with('success', 'Banner ' . ((int) $banner->status === 1 ? 'activated' : 'deactivated') . '.');
  }

  public function delete(Banner $banner)
  {
    if ($banner->image) {
      Storage::disk('public')->delete($banner->image);
    }
    $banner->delete();

    return back()->with('success', 'Banner deleted.');
  }

  protected function validated(Request $request, bool $imageRequired): array
  {
    $data = $request->validate([
      'title' => 'nullable|string|max:255',
      'btn_text' => 'nullable|string|max:50',
      'btn_link' => 'nullable|url|max:255',
      'image' => [$imageRequired ? 'required' : 'nullable', 'image', 'max:4096'],
      'starts_at' => 'nullable|date',
      'ends_at' => 'nullable|date|after_or_equal:starts_at',
      'sort_order' => 'nullable|integer|min:0',
    ]);
    unset($data['image']);

    $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
    $data['status'] = $request->boolean('status') ? 1 : 0;
    $data['show_in_popup'] = $request->boolean('show_in_popup');

    return $data;
  }
}
