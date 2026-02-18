<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin\Perfumes;

use App\Domain\Perfumes\Models\PerfumeSetting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminPerfumeSettingController extends Controller
{
    public function index()
    {
        $settings = [
            'whatsapp_admin'  => PerfumeSetting::get('whatsapp_admin', ''),
            'store_name'      => PerfumeSetting::get('store_name', 'DG Perfumes'),
            'pix_key'         => PerfumeSetting::get('pix_key', ''),
            'dollar_rate'     => PerfumeSetting::get('dollar_rate', '5.30'),
        ];

        return view('admin.perfumes.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'whatsapp_admin' => 'nullable|string|max:20',
            'store_name'     => 'nullable|string|max:100',
            'pix_key'        => 'nullable|string|max:255',
            'dollar_rate'    => 'nullable|numeric|min:0.01|max:99.99',
        ]);

        foreach (['whatsapp_admin', 'store_name', 'pix_key', 'dollar_rate'] as $key) {
            PerfumeSetting::set($key, $request->input($key, ''));
        }

        return redirect()->route('admin.perfumes.settings.index')
            ->with('success', 'Configurações salvas com sucesso.');
    }

    public function updateDollarRate(Request $request)
    {
        $request->validate([
            'dollar_rate' => 'required|numeric|min:0.01|max:99.99',
        ]);

        PerfumeSetting::set('dollar_rate', $request->input('dollar_rate'));

        return response()->json(['success' => true]);
    }
}
