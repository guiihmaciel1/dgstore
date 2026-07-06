<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\Services\IdleModeSuggestionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IdleModeController extends Controller
{
    public function __construct(
        private readonly IdleModeSuggestionService $suggestionService,
    ) {}

    public function suggestions(Request $request): JsonResponse
    {
        $suggestions = $this->suggestionService->getSuggestions();

        return response()->json([
            'suggestions' => $suggestions,
            'count' => count($suggestions),
        ]);
    }
}
