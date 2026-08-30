@if(method_exists($products, 'hasPages') && $products->hasPages())
    <nav class="shop-pagination d-flex justify-content-center mt-5" aria-label="Product pagination">
        {{ $products->onEachSide(1)->links('pagination::bootstrap-5') }}
    </nav>
@endif
