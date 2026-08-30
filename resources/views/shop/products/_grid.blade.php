@php
  $collection = $products instanceof \Illuminate\Pagination\AbstractPaginator ? $products : collect($products);
@endphp
@if($collection->count())
<div class="product-grid">
    @foreach($collection as $product)
        @php
            $price = $product->sale_price ?: $product->price;
            $image = $product->image ? asset('storage/'.$product->image) : asset('front_assets/img/logo.png');
        @endphp
        <article class="product-card">
            <a class="thumb" href="{{ route('shop.product', $product->slug) }}">
                <img src="{{ $image }}" alt="{{ $product->title }}" loading="lazy">
            </a>
            <div class="body">
                <div class="cat">{{ $product->category->name ?? 'Product' }}</div>
                <h3><a href="{{ route('shop.product', $product->slug) }}">{{ $product->title }}</a></h3>
                <div class="price">
                    @if($price !== null && $price !== '' && (float)$price > 0)
                        ₹{{ number_format((float)$price, 2) }}
                    @else
                        Enquire for price
                    @endif
                </div>
                <div class="actions">
                    <a class="btn btn-outline" href="{{ route('shop.product', $product->slug) }}" style="padding:.65rem 1rem;">View</a>
                    <button
                        type="button"
                        class="btn btn-primary"
                        style="padding:.65rem 1rem;"
                        data-add-to-cart="{{ route('shop.cart.add') }}"
                        data-product-id="{{ $product->id }}"
                    >Add</button>
                </div>
            </div>
        </article>
    @endforeach
</div>
@else
    <div class="empty-state panel">
        <h3>No products found</h3>
        <p>Try a different search or clear your filters.</p>
    </div>
@endif
