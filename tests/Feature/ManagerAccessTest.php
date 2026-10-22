<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

test('manager can open orders, enquiries and users but not catalog or settings', function () {
    $manager = userWithRole('Manager');

    $this->actingAs($manager);
    $this->get('/admin/dashboard')->assertOk();
    $this->get('/admin/order')->assertOk();
    $this->get('/users')->assertOk();

    $this->get('/admin/product')->assertForbidden();
    $this->get('/admin/website-settings')->assertForbidden();
    $this->get('/admin/coupons')->assertForbidden();
    $this->get('/admin/banner')->assertForbidden();
});

test('manager can create a sales executive but not an admin or manager', function () {
    Mail::fake();
    $manager = userWithRole('Manager');

    $this->actingAs($manager)->post('/user/create', [
        'name' => 'Sales Guy', 'email' => 'sales@example.com', 'mobile' => '9',
        'role' => Role::findByName('Sales Executive')->id,
    ])->assertSessionHasNoErrors();
    expect(User::where('email', 'sales@example.com')->exists())->toBeTrue();

    $this->actingAs($manager)->post('/user/create', [
        'name' => 'Sneaky', 'email' => 'sneaky@example.com', 'mobile' => '9',
        'role' => Role::findByName('Admin')->id,
    ])->assertSessionHasErrors('role');
    expect(User::where('email', 'sneaky@example.com')->exists())->toBeFalse();
});

test('manager cannot edit, deactivate or delete an admin', function () {
    $manager = userWithRole('Manager');
    $admin = userWithRole('Admin');

    $this->actingAs($manager)->get('/user/'.$admin->id)->assertRedirect('users');
    $this->actingAs($manager)->post('/user/'.$admin->id.'/toggle-active');
    $this->actingAs($manager)->get('/user/delete/'.$admin->id);

    expect($admin->fresh())->not->toBeNull()
        ->and($admin->fresh()->is_active)->toBeTrue();
});

test('recently deleted product routes now require admin login', function () {
    $this->post('/admin/product/restore/1')->assertRedirect('/login');
    $this->post('/admin/product/permanently-delete/1')->assertRedirect('/login');
});
