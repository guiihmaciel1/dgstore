<?php

namespace App\Presentation\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SupplierAuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('supplier.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $this->ensureIsNotRateLimited($request);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('supplier')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            RateLimiter::clear($this->throttleKey($request));

            return redirect()->intended(route('supplier.dashboard'));
        }

        RateLimiter::hit($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('supplier')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('supplier.login');
    }

    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(Request $request): string
    {
        return strtolower($request->input('email')).'|'.$request->ip();
    }
}
