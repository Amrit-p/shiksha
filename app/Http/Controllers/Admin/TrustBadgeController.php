<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Models\TrustBadge;
use Illuminate\Http\Request;

/** Trust badges shown as a small carousel on the salesman home and product pages. */
class TrustBadgeController extends Controller
{
    public function index()
    {
        $badges = TrustBadge::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.trust_badge.index', compact('badges'));
    }

    public function create()
    {
        return view('admin.trust_badge.form', ['badge' => new TrustBadge(['is_active' => true, 'sort_order' => 0])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, true);
        $data['icon'] = FileHelper::upload($request->file('icon'), 'trust-badges');

        TrustBadge::create($data);

        return redirect()->route('admin.trust-badges.index')->with('success', 'Trust badge added.');
    }

    public function edit(TrustBadge $trustBadge)
    {
        return view('admin.trust_badge.form', ['badge' => $trustBadge]);
    }

    public function update(Request $request, TrustBadge $trustBadge)
    {
        $data = $this->validated($request, false);

        if ($request->hasFile('icon')) {
            FileHelper::delete($trustBadge->icon);
            $data['icon'] = FileHelper::upload($request->file('icon'), 'trust-badges');
        }

        $trustBadge->update($data);

        return redirect()->route('admin.trust-badges.index')->with('success', 'Trust badge updated.');
    }

    public function toggle(TrustBadge $trustBadge)
    {
        $trustBadge->update(['is_active' => ! $trustBadge->is_active]);

        return back()->with('success', 'Trust badge '.($trustBadge->is_active ? 'shown' : 'hidden').'.');
    }

    public function destroy(TrustBadge $trustBadge)
    {
        FileHelper::delete($trustBadge->icon);
        $trustBadge->delete();

        return back()->with('success', 'Trust badge deleted.');
    }

    protected function validated(Request $request, bool $iconRequired): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:60',
            'subtitle' => 'nullable|string|max:120',
            // Raster images only: an uploaded SVG could carry script when opened directly.
            'icon' => [$iconRequired ? 'required' : 'nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:1024'],
            'sort_order' => 'nullable|integer|min:0',
        ]);
        unset($data['icon']);

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
