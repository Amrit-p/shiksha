<?php

use App\Models\Product;
use App\Models\TrustBadge;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

function simpleProduct(): Product
{
    // The repo migrations never add these (the live DB has them), so add them for SQLite tests.
    Schema::table('products', function ($table) {
        foreach (['product_type' => 1, 'stock' => 0, 'min_stock' => 0] as $column => $default) {
            if (! Schema::hasColumn('products', $column)) {
                $table->integer($column)->default($default);
            }
        }
    });

    $categoryId = DB::table('categories')->insertGetId(['name' => 'Wires', 'slug' => 'wires']);
    $unitId = DB::table('units')->insertGetId(['name' => 'Piece', 'short_name' => 'pcs']);

    return Product::create([
        'title' => 'Copper Wire', 'slug' => 'copper-wire', 'category_id' => $categoryId, 'unit_id' => $unitId,
        'product_type' => 1, 'short_description' => 'Wire', 'price' => 100, 'stock' => 5, 'status' => 1,
    ]);
}

function productUpdatePayload(Product $product, array $extra = []): array
{
    return $extra + [
        'title' => $product->title, 'category_id' => $product->category_id, 'unit_id' => $product->unit_id,
        'product_type' => 1, 'short_description' => 'Wire', 'simple' => ['price' => 100, 'stock' => 5],
    ];
}

test('admin can upload, replace and remove a product video', function () {
    Storage::fake('public');
    $admin = userWithRole('Admin');
    $product = simpleProduct();

    $this->actingAs($admin)->patch(route('admin.product.update', $product->id), productUpdatePayload($product, [
        'video' => UploadedFile::fake()->create('demo.mp4', 2048, 'video/mp4'),
    ]))->assertSessionHasNoErrors();

    $first = $product->fresh()->video;
    expect($first)->toStartWith('product-videos/');
    Storage::disk('public')->assertExists($first);

    $this->actingAs($admin)->patch(route('admin.product.update', $product->id), productUpdatePayload($product, [
        'video' => UploadedFile::fake()->create('demo2.webm', 1024, 'video/webm'),
    ]))->assertSessionHasNoErrors();
    Storage::disk('public')->assertMissing($first);

    $second = $product->fresh()->video;
    $this->actingAs($admin)->patch(route('admin.product.update', $product->id), productUpdatePayload($product, [
        'remove_video' => 1,
    ]))->assertSessionHasNoErrors();

    expect($product->fresh()->video)->toBeNull();
    Storage::disk('public')->assertMissing($second);
});

test('product video must be mp4/webm and at most 50 MB', function () {
    Storage::fake('public');
    $admin = userWithRole('Admin');
    $product = simpleProduct();

    $this->actingAs($admin)->patch(route('admin.product.update', $product->id), productUpdatePayload($product, [
        'video' => UploadedFile::fake()->create('demo.mov', 1024, 'video/quicktime'),
    ]))->assertSessionHasErrors('video');

    $this->actingAs($admin)->patch(route('admin.product.update', $product->id), productUpdatePayload($product, [
        'video' => UploadedFile::fake()->create('big.mp4', 51201, 'video/mp4'),
    ]))->assertSessionHasErrors('video');

    expect($product->fresh()->video)->toBeNull();
});

test('admin manages trust badges; managers cannot', function () {
    Storage::fake('public');
    $admin = userWithRole('Admin');

    $this->actingAs($admin)->post(route('admin.trust-badges.store'), [
        'title' => 'ISI Certified', 'subtitle' => 'Quality tested', 'sort_order' => 2, 'is_active' => 1,
        'icon' => UploadedFile::fake()->image('isi.png', 128, 128),
    ])->assertRedirect(route('admin.trust-badges.index'));

    $badge = TrustBadge::firstOrFail();
    Storage::disk('public')->assertExists($badge->icon);

    $this->actingAs($admin)->post(route('admin.trust-badges.toggle', $badge));
    expect($badge->fresh()->is_active)->toBeFalse();

    // SVG icons are rejected (could carry script when opened directly).
    $this->actingAs($admin)->post(route('admin.trust-badges.store'), [
        'title' => 'Bad', 'icon' => UploadedFile::fake()->create('x.svg', 2, 'image/svg+xml'),
    ])->assertSessionHasErrors('icon');

    $this->actingAs(userWithRole('Manager'))->get(route('admin.trust-badges.index'))->assertForbidden();
});

test('trust badge strip shows only active badges in sort order', function () {
    TrustBadge::create(['title' => 'Second', 'icon' => 'trust-badges/b.png', 'sort_order' => 2, 'is_active' => true]);
    TrustBadge::create(['title' => 'First', 'icon' => 'trust-badges/a.png', 'sort_order' => 1, 'is_active' => true]);
    TrustBadge::create(['title' => 'Hidden one', 'icon' => 'trust-badges/c.png', 'sort_order' => 0, 'is_active' => false]);

    $html = (string) $this->view('front.partials.trust_badges', ['variant' => 'compact']);

    expect($html)->toContain('trust-badges--compact')->not->toContain('Hidden one')
        ->and(strpos($html, 'First'))->toBeLessThan(strpos($html, 'Second'));
});

test('no badges renders nothing', function () {
    expect(trim((string) $this->view('front.partials.trust_badges')))->toBe('');
});
