<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::withCount(['countedOrders as used_count'])->latest()->paginate(25);

        return view('admin.coupon.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupon.form', ['coupon' => new Coupon(['type' => Coupon::TYPE_PERCENT, 'is_active' => true])]);
    }

    public function store(Request $request)
    {
        Coupon::create($this->validated($request));

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created.');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupon.form', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $coupon->update($this->validated($request, $coupon));

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated.');
    }

    public function toggle(Coupon $coupon)
    {
        $coupon->update(['is_active' => ! $coupon->is_active]);

        return back()->with('success', 'Coupon '.($coupon->is_active ? 'activated' : 'deactivated').'.');
    }

    public function destroy(Coupon $coupon)
    {
        // Orders keep coupon_code + discount; coupon_id is nulled by the FK.
        $coupon->delete();

        return back()->with('success', 'Coupon deleted.');
    }

    protected function validated(Request $request, ?Coupon $coupon = null): array
    {
        $request->merge(['code' => strtoupper(trim((string) $request->code))]);

        $data = $request->validate([
            'code' => ['required', 'string', 'max:40', 'regex:/^[A-Z0-9_-]+$/', Rule::unique('coupons', 'code')->ignore($coupon?->id)],
            'description' => 'nullable|string|max:255',
            'type' => ['required', Rule::in([Coupon::TYPE_PERCENT, Coupon::TYPE_FLAT])],
            'value' => ['required', 'numeric', 'min:0.01', $request->type === Coupon::TYPE_PERCENT ? 'max:100' : 'max:99999999'],
            'max_discount' => 'nullable|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'usage_limit_total' => 'nullable|integer|min:1',
            'usage_limit_per_shop' => 'nullable|integer|min:1',
        ], [
            'code.regex' => 'Code may only contain letters, numbers, - and _.',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        if ($data['type'] === Coupon::TYPE_FLAT) {
            $data['max_discount'] = null;
        }

        return $data;
    }
}
