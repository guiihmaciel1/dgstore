<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin;

use App\Domain\System\Models\SystemSetting;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DollarRateController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'dollar_rate' => 'required|numeric|min:0.01|max:99.99',
        ]);

        SystemSetting::set('dollar_rate', $request->input('dollar_rate'));
        SystemSetting::set('dollar_rate_updated_at', now()->toIso8601String());

        return response()->json([
            'success' => true,
            'dollar_rate' => (float) $request->input('dollar_rate'),
        ]);
    }
}
