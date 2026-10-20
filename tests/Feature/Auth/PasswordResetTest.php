<?php

use App\Mail\TemplatedMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

/** Pull the token out of the reset link in the email body. */
function resetTokenFrom(TemplatedMail $mail): string
{
    preg_match('#reset-password/([^?"&]+)#', $mail->emailBody, $m);

    return $m[1];
}

test('reset password link screen can be rendered', function () {
    $this->get('/forgot-password')->assertStatus(200)->assertSee('Forgot your password?');
});

test('reset password link is emailed through the template', function () {
    Mail::fake();
    $user = User::factory()->create(['mobile' => '1']);

    $this->post('/forgot-password', ['email' => $user->email]);

    Mail::assertSent(TemplatedMail::class, fn ($mail) => $mail->hasTo($user->email)
        && str_contains($mail->emailBody, '/reset-password/'));
});

test('reset password screen can be rendered', function () {
    Mail::fake();
    $user = User::factory()->create(['mobile' => '1']);
    $this->post('/forgot-password', ['email' => $user->email]);

    Mail::assertSent(TemplatedMail::class, function ($mail) {
        $this->get('/reset-password/'.resetTokenFrom($mail))->assertStatus(200);

        return true;
    });
});

test('password can be reset with valid token and redirects to the right login', function () {
    Mail::fake();
    $user = userWithRole('Sales Executive');
    $this->post('/forgot-password', ['email' => $user->email]);

    Mail::assertSent(TemplatedMail::class, function ($mail) use ($user) {
        $this->post('/reset-password', [
            'token' => resetTokenFrom($mail),
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertSessionHasNoErrors()->assertRedirect(route('salesman.login'));

        return true;
    });

    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
});
