<?php

namespace App\Services;

use App\Mail\TemplatedMail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

/** Account emails (invite, reset link, password changed) sent through editable email templates. */
class AccountMailService
{
    public function __construct(protected MailTemplateService $templates) {}

    /** Email a new user a link to set their own password. Returns false if the mail could not be sent. */
    public function sendInvite(User $user): bool
    {
        $token = Password::broker('invites')->createToken($user);

        return $this->send($user, 'user_welcome_set_password', [
            'role' => $user->getRoleNames()->first() ?: 'User',
            'set_password_url' => route('password.reset', ['token' => $token, 'email' => $user->email, 'invite' => 1]),
            'expires_in' => $this->expiresIn('invites'),
        ]);
    }

    public function sendResetLink(User $user, string $token): bool
    {
        return $this->send($user, 'password_reset_link', [
            'reset_url' => route('password.reset', ['token' => $token, 'email' => $user->email]),
            'expires_in' => $this->expiresIn('users'),
        ]);
    }

    public function sendPasswordChanged(User $user): bool
    {
        return $this->send($user, 'password_changed', [
            'changed_at' => now()->format('d M Y, h:i A'),
        ]);
    }

    public static function loginUrlFor(User $user): string
    {
        return $user->hasRole('Sales Executive') ? route('salesman.login') : route('login');
    }

    protected function send(User $user, string $slug, array $variables): bool
    {
        try {
            $mail = $this->templates->build($slug, $variables + [
                'name' => $user->name,
                'email' => $user->email,
                'login_url' => self::loginUrlFor($user),
            ]);

            if (! $mail) {
                Log::warning('Account email template missing or inactive', ['slug' => $slug]);

                return false;
            }

            Mail::to($user->email)->send(new TemplatedMail($mail['subject'], $mail['body']));

            return true;
        } catch (\Throwable $e) {
            Log::error('Account email failed', ['slug' => $slug, 'user_id' => $user->id, 'error' => $e->getMessage()]);

            return false;
        }
    }

    protected function expiresIn(string $broker): string
    {
        $minutes = (int) config("auth.passwords.{$broker}.expire", 60);

        return $minutes >= 120 ? intdiv($minutes, 60).' hours' : $minutes.' minutes';
    }
}
