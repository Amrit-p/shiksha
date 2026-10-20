<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

/*
|--------------------------------------------------------------------------
| App helpers
|--------------------------------------------------------------------------
*/

/** Roles as they exist in production (Manager comes from its migration). */
function seedAppRoles(): void
{
    $admin = \Spatie\Permission\Models\Permission::findOrCreate('admin', 'web');
    $shop = \Spatie\Permission\Models\Permission::findOrCreate('frontend_shop', 'web');
    \Spatie\Permission\Models\Role::findOrCreate('Admin', 'web')->givePermissionTo($admin);
    \Spatie\Permission\Models\Role::findOrCreate('Sales Executive', 'web')->givePermissionTo($shop);
    app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
}

function userWithRole(string $role, array $attributes = []): \App\Models\User
{
    seedAppRoles();

    return tap(\App\Models\User::factory()->create($attributes + ['mobile' => '9999999999']))->assignRole($role);
}

/** Order with one simple product (qty 10) and one variant product with two options (qty 5 + 3). */
function orderWithSnapshot(array $attributes = []): \App\Models\Order
{
    return \App\Models\Order::create($attributes + [
        'user_id' => $attributes['user_id'] ?? \App\Models\User::factory()->create(['mobile' => '1'])->id,
        'owner_name' => 'Owner',
        'owner_phone' => '9876543210',
        'shop_name' => 'Shop',
        'order_status' => 'pending',
        'payment_status' => 'pending',
        'total_qty' => 18,
        'total_amount' => 1000,
        'product_json' => json_encode([
            ['product' => ['id' => 1, 'name' => 'Bulb'], 'variant' => null, 'attributes' => [], 'qty' => 10, 'unit_price' => 50, 'total' => 500],
            ['product' => ['id' => 2, 'name' => 'Wire'], 'variant' => ['id' => 7, 'name' => '1.5mm'], 'attributes' => [
                ['attribute_name' => 'Colour', 'option_name' => 'Red', 'is_por' => 0, 'qty' => 5, 'unit_price' => 50, 'total' => 250],
                ['attribute_name' => 'Colour', 'option_name' => 'Black', 'is_por' => 0, 'qty' => 3, 'unit_price' => 50, 'total' => 150],
            ]],
        ]),
    ]);
}
