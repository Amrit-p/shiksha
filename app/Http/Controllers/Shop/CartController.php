<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Services\CustomerCartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(protected CustomerCartService $cart) {}

    public function index(): View
    {
        $items = array_values($this->cart->all());

        return view('shop.cart.index', [
            'items' => $items,
            'count' => $this->cart->count(),
            'metaTitle' => 'Enquiry Cart | Shiksha',
        ]);
    }

    public function add(Request $request): JsonResponse
    {
        $data = $request->all();
        if ($request->isJson()) {
            $data = $request->json()->all();
        }

        $validator = validator($data, [
            'product_id' => 'required|integer|exists:products,id',
            'variant_id' => 'nullable|integer',
            'variant_attribute_id' => 'nullable|integer',
            'qty' => 'nullable|integer|min:1|max:9999',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->cart->add($validator->validated());

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->isJson() ? $request->json()->all() : $request->all();
        $validator = validator($data, [
            'key' => 'required|string',
            'qty' => 'required|integer|min:0|max:9999',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $result = $this->cart->updateQty($validator->validated()['key'], (int) $validator->validated()['qty']);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function remove(Request $request): JsonResponse
    {
        $data = $request->isJson() ? $request->json()->all() : $request->all();
        $validator = validator($data, ['key' => 'required|string']);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $result = $this->cart->remove($validator->validated()['key']);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function clear(): JsonResponse
    {
        $this->cart->clear();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared.',
            'count' => 0,
            'items' => [],
        ]);
    }

    public function count(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'count' => $this->cart->count(),
        ]);
    }
}
