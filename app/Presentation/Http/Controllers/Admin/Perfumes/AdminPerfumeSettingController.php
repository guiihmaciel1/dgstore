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
            'whatsapp_admin' => PerfumeSetting::get('whatsapp_admin', ''),
            'store_name'     => PerfumeSetting::get('store_name', 'DG Perfumes'),
            'pix_key'        => PerfumeSetting::get('pix_key', ''),
        ];

        return view('admin.perfumes.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'whatsapp_admin' => 'nullable|string|max:20',
            'store_name'     => 'nullable|string|max:100',
            'pix_key'        => 'nullable|string|max:255',
        ]);

        foreach (['whatsapp_admin', 'store_name', 'pix_key'] as $key) {
            PerfumeSetting::set($key, $request->input($key, ''));
        }

        return redirect()->route('admin.perfumes.settings.index')
            ->with('success', 'Configurações salvas com sucesso.');
    }
}
