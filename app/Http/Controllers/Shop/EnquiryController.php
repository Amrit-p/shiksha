<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Services\CustomerCartService;
use App\Services\EnquiryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnquiryController extends Controller
{
    public function __construct(
        protected CustomerCartService $cart,
        protected EnquiryService $enquiries
    ) {}

    public function create(): View|RedirectResponse
    {
        $items = array_values($this->cart->all());

        if (empty($items)) {
            return redirect()
                ->route('shop.cart')
                ->with('error', 'Add products to your cart before submitting an enquiry.');
        }

        return view('shop.enquiry.create', [
            'items' => $items,
            'metaTitle' => 'Make Enquiry | Shiksha',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if ($this->cart->count() < 1) {
            return redirect()
                ->route('shop.cart')
                ->with('error', 'Your enquiry cart is empty.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:150',
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{7,20}$/'],
            'company' => 'nullable|string|max:150',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pin_code' => 'nullable|string|max:20',
            'message' => 'nullable|string|max:2000',
        ]);

        try {
            $enquiry = $this->enquiries->createFromCart(
                $data,
                $request->ip(),
                $request->userAgent()
            );
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', $e->getMessage() ?: 'Unable to submit enquiry. Please try again.');
        }

        return redirect()
            ->route('shop.enquiry.thankyou', $enquiry->enquiry_number)
            ->with('success', 'Enquiry submitted successfully.');
    }

    public function thankYou(string $enquiryNumber): View
    {
        return view('shop.enquiry.thankyou', [
            'enquiryNumber' => $enquiryNumber,
            'metaTitle' => 'Enquiry Submitted | Shiksha',
        ]);
    }
}
