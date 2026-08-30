@php
  $collection = $products instanceof \Illuminate\Pagination\AbstractPaginator ? $products : collect($products);
  $cardCols = $cols ?? 'col-6 col-lg-4';
@endphp
@forelse($collection as $product)
    <x-shop.product-card :product="$product" :index="$loop->index" :cols="$cardCols" :reveal="false" />
@empty
    <div class="col-12">
        <div class="no-results" style="display:block">
            <i class="bi bi-search"></i>
            <h4>No products match those filters</h4>
            <p class="mb-3">Try clearing a category or searching a different name or model code.</p>
            <a href="{{ route('shop.products') }}" class="btn btn-brand">Reset filters</a>
        </div>
    </div>
@endforelse
