<?php

use App\Mail\TemplatedMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

test('creating a user emails a set-password invite instead of asking for a password', function () {
    Mail::fake();
    $admin = userWithRole('Admin');

    $this->actingAs($admin)->post('/user/create', [
        'name' => 'New Salesman',
        'email' => 'new.salesman@example.com',
        'mobile' => '9000000000',
        'role' => Role::findByName('Sales Executive')->id,
    ])->assertRedirect('users')->assertSessionHas('success');

    $user = User::where('email', 'new.salesman@example.com')->firstOrFail();
    expect($user->hasRole('Sales Executive'))->toBeTrue();

    $sent = null;
    Mail::assertSent(TemplatedMail::class, function ($mail) use (&$sent) {
        $sent = $mail;

        return $mail->hasTo('new.salesman@example.com') && str_contains($mail->emailBody, 'invite=1');
    });

    // The invite link works (72h broker) and sets the password.
    preg_match('#reset-password/([^?"&]+)#', $sent->emailBody, $m);
    auth()->logout();
    $this->post('/reset-password', [
        'token' => $m[1], 'email' => $user->email, 'invite' => 1,
        'password' => 'chosen-pass', 'password_confirmation' => 'chosen-pass',
    ])->assertRedirect(route('salesman.login'));

    expect(Hash::check('chosen-pass', $user->fresh()->password))->toBeTrue();
});

test('changing password sends a confirmation email', function () {
    Mail::fake();
    $user = userWithRole('Sales Executive');

    $this->actingAs($user)->from('/salesman/change-password')->put('/password', [
        'current_password' => 'password',
        'password' => 'another-pass',
        'password_confirmation' => 'another-pass',
    ])->assertSessionHasNoErrors();

    Mail::assertSent(TemplatedMail::class, fn ($mail) => $mail->hasTo($user->email)
        && str_contains($mail->emailSubject, 'password was changed'));
});

test('deactivated users cannot log in', function () {
    $user = userWithRole('Sales Executive', ['is_active' => false]);

    $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors('email');
    $this->assertGuest();
});
