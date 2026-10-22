<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AccountMailService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Invite links (new accounts) use a longer-lived broker than "forgot password" links.
        $broker = Password::broker($request->boolean('invite') ? 'invites' : 'users');
        $resetUser = null;

        $status = $broker->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request, &$resetUser) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                $resetUser = $user;
                event(new PasswordReset($user));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
        }

        return redirect(AccountMailService::loginUrlFor($resetUser))
            ->with('status', $request->boolean('invite')
                ? 'Your password has been set. You can now sign in.'
                : __($status));
    }
}
