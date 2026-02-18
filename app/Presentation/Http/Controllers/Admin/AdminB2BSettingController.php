<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin;

use App\Domain\B2B\Models\B2BSetting;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminB2BSettingController extends Controller
{
    public function index(): View
    {
        $minimumOrderAmount = B2BSetting::getMinimumOrderAmount();

        return view('admin.b2b.settings.index', compact('minimumOrderAmount'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'minimum_order_amount' => ['required', 'numeric', 'min:0'],
        ]);

        B2BSetting::set('minimum_order_amount', $validated['minimum_order_amount']);

        return back()->with('success', 'Configurações atualizadas com sucesso.');
    }
}
