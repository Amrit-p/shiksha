@if(method_exists($products, 'hasPages') && $products->hasPages())
    <nav class="shop-pagination" aria-label="Product pagination">
        {{ $products->onEachSide(1)->links() }}
    </nav>
@endif
