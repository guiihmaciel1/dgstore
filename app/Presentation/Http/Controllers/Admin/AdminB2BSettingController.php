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
        $settings = [
            'company_name' => B2BSetting::getCompanyName(),
            'admin_whatsapp' => B2BSetting::getAdminWhatsapp(),
            'pix_key' => B2BSetting::getPixKey(),
            'minimum_order_amount' => B2BSetting::getMinimumOrderAmount(),
            'low_stock_threshold' => B2BSetting::getLowStockThreshold(),
        ];

        return view('admin.b2b.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'admin_whatsapp' => ['required', 'string', 'max:20'],
            'pix_key' => ['nullable', 'string', 'max:255'],
            'minimum_order_amount' => ['required', 'numeric', 'min:0'],
            'low_stock_threshold' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        foreach ($validated as $key => $value) {
            B2BSetting::set($key, $value ?? '');
        }

        return back()->with('success', 'Configurações atualizadas com sucesso.');
    }
}
