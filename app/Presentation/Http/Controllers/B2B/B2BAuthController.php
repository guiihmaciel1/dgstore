<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\B2B;

use App\Domain\B2B\DTOs\CreateRetailerDTO;
use App\Domain\B2B\Services\B2BRetailerService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class B2BAuthController extends Controller
{
    public function showLogin(): View
    {
        return view('b2b.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('b2b')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $retailer = Auth::guard('b2b')->user();

            if ($retailer->isBlocked()) {
                Auth::guard('b2b')->logout();
                return back()->with('error', 'Sua conta foi bloqueada. Entre em contato com o administrador.');
            }

            if ($retailer->isPending()) {
                return redirect()->route('b2b.pending');
            }

            return redirect()->intended(route('b2b.catalog'));
        }

        return back()->withErrors([
            'email' => 'As credenciais informadas não conferem.',
        ])->onlyInput('email');
    }

    public function showRegister(): View
    {
        return view('b2b.auth.register');
    }

    public function register(Request $request, B2BRetailerService $service): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'owner_name' => ['required', 'string', 'max:255'],
            'document' => ['required', 'string', 'max:18', 'unique:b2b_retailers,document'],
            'whatsapp' => ['required', 'string', 'max:20'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'size:2'],
            'email' => ['required', 'email', 'unique:b2b_retailers,email'],
        ]);

        $validated['password'] = Str::random(12);

        $retailer = $service->create(CreateRetailerDTO::fromArray($validated));

        return redirect()->route('b2b.register.success', [
            'store' => $retailer->store_name,
            'owner' => $retailer->owner_name,
            'city' => $retailer->city . '/' . $retailer->state,
            'whatsapp' => $retailer->whatsapp,
            'document' => $retailer->document,
            'email' => $retailer->email,
        ]);
    }

    public function registerSuccess(Request $request): View
    {
        return view('b2b.auth.register-success', [
            'store' => $request->query('store', ''),
            'owner' => $request->query('owner', ''),
            'city' => $request->query('city', ''),
            'whatsapp' => $request->query('whatsapp', ''),
            'document' => $request->query('document', ''),
            'email' => $request->query('email', ''),
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('b2b')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('b2b.login');
    }

    public function pending(): View|RedirectResponse
    {
        $retailer = Auth::guard('b2b')->user();

        if (!$retailer) {
            return redirect()->route('b2b.login');
        }

        if ($retailer->isApproved()) {
            return redirect()->route('b2b.catalog');
        }

        return view('b2b.auth.pending-approval');
    }
}
