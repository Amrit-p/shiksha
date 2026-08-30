<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the admin login view.
     */
    public function create(): View
    {
        return view('admin.pages.login', [
            'loginRouteName' => 'login',
        ]);
    }

    /**
     * Display the salesman login view.
     */
    public function createSalesman(): View
    {
        return view('admin.pages.login', [
            'loginRouteName' => 'salesman.login.store',
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();
        $user = auth()->user();

        if ($user->hasRole('Sales Executive')) {
            return redirect()->intended(route('front.index'));
        }

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        $isSales = $user && $user->hasRole('Sales Executive');

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect($isSales ? route('salesman.login') : route('shop.home'));
    }
}
