<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Customer\Models\Customer;
use App\Domain\Customer\Models\CustomerTag;
use App\Domain\WhatsApp\Services\LabelSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CustomerTagController extends Controller
{
    /**
     * API: Lista todas as tags ativas (para selects/dropdowns).
     */
    public function apiIndex(): JsonResponse
    {
        $tags = CustomerTag::active()->ordered()->get(['id', 'name', 'slug', 'color']);

        return response()->json($tags);
    }

    /**
     * API: Tags de um cliente específico.
     */
    public function customerTags(Customer $customer): JsonResponse
    {
        $tags = $customer->tags()->get(['customer_tags.id', 'name', 'slug', 'color']);

        return response()->json($tags);
    }

    /**
     * API: Sync (adicionar/remover) tags de um cliente.
     */
    public function syncTags(Request $request, Customer $customer): JsonResponse
    {
        $validated = $request->validate([
            'tag_ids' => 'required|array',
            'tag_ids.*' => 'exists:customer_tags,id',
        ]);

        $pivotData = [];
        foreach ($validated['tag_ids'] as $tagId) {
            $pivotData[$tagId] = ['tagged_by' => auth()->id()];
        }

        $customer->tags()->sync($pivotData);

        return response()->json([
            'success' => true,
            'tags' => $customer->tags()->get(['customer_tags.id', 'name', 'slug', 'color']),
        ]);
    }

    /**
     * API: Adicionar uma tag a um cliente.
     */
    public function addTag(Request $request, Customer $customer): JsonResponse
    {
        $validated = $request->validate([
            'tag_id' => 'required|exists:customer_tags,id',
        ]);

        if (!$customer->tags()->where('customer_tag_id', $validated['tag_id'])->exists()) {
            $customer->tags()->attach($validated['tag_id'], [
                'tagged_by' => auth()->id(),
            ]);

            try {
                app(LabelSyncService::class)->syncToWhatsApp($customer->fresh()->load('tags'));
            } catch (\Throwable $e) {
                // Falha no sync WhatsApp não deve impedir a operação
            }
        }

        return response()->json([
            'success' => true,
            'tags' => $customer->tags()->get(['customer_tags.id', 'name', 'slug', 'color']),
        ]);
    }

    /**
     * API: Remover uma tag de um cliente.
     */
    public function removeTag(Customer $customer, CustomerTag $tag): JsonResponse
    {
        $customer->tags()->detach($tag->id);

        return response()->json([
            'success' => true,
            'tags' => $customer->tags()->get(['customer_tags.id', 'name', 'slug', 'color']),
        ]);
    }
}
