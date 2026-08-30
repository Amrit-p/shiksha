@extends('shop.layout')

@section('content')
<div class="page-wrap page-hero">
    <h1>Products</h1>
    <p><span data-product-count>{{ $products->total() }}</span> products available</p>
</div>

<div class="page-wrap catalog-layout">
    <aside class="filters panel" data-filters-panel>
        <h3>Filters</h3>
        <form data-product-filters action="{{ route('shop.products') }}" method="GET">
            <div class="filter-group">
                <label>Search</label>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Name, SKU...">
            </div>
            <div class="filter-group">
                <label>Category</label>
                <select name="category">
                    <option value="">All categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(($filters['category'] ?? '') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Product type</label>
                <select name="type">
                    <option value="">All types</option>
                    <option value="1" @selected(($filters['type'] ?? '') == '1')>Simple</option>
                    <option value="2" @selected(($filters['type'] ?? '') == '2')>Attribute</option>
                    <option value="3" @selected(($filters['type'] ?? '') == '3')>Variant</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Min price</label>
                <input type="number" name="min_price" value="{{ $filters['min_price'] ?? '' }}" min="0" step="0.01">
            </div>
            <div class="filter-group">
                <label>Max price</label>
                <input type="number" name="max_price" value="{{ $filters['max_price'] ?? '' }}" min="0" step="0.01">
            </div>
            <div class="filter-group">
                <label>Availability</label>
                <select name="availability">
                    <option value="">Any</option>
                    <option value="in_stock" @selected(($filters['availability'] ?? '') === 'in_stock')>In stock</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Sort</label>
                <select name="sort">
                    <option value="latest" @selected(($filters['sort'] ?? 'latest') === 'latest')>Latest</option>
                    <option value="name" @selected(($filters['sort'] ?? '') === 'name')>Name</option>
                    <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Price: Low to High</option>
                    <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Price: High to Low</option>
                </select>
            </div>
            <button class="btn btn-primary" type="submit" style="width:100%;">Apply</button>
            <a class="btn btn-outline" href="{{ route('shop.products') }}" style="width:100%;margin-top:.6rem;">Reset</a>
        </form>
    </aside>

    <div class="catalog-results">
        <div class="filters-toolbar">
            <button type="button" class="btn btn-outline" data-filter-toggle onclick="document.querySelector('[data-filters-panel]')?.classList.toggle('open');return false;">Filters</button>
            <a class="btn btn-primary" href="{{ route('shop.cart') }}">Enquiry Cart</a>
        </div>
        <div data-product-grid>
            @include('shop.products._grid', ['products' => $products])
        </div>
        <div data-product-pagination>
            @include('shop.products._pagination', ['products' => $products])
        </div>
    </div>
</div>
@endsection
