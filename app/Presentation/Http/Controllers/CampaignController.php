<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Customer\Models\CustomerTag;
use App\Domain\WhatsApp\Jobs\SendCampaignBatchJob;
use App\Domain\WhatsApp\Models\WhatsAppCampaign;
use App\Domain\WhatsApp\Services\CampaignService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function index(): View
    {
        $campaigns = WhatsAppCampaign::with(['creator', 'tags'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('campaigns.index', compact('campaigns'));
    }

    public function create(): View
    {
        $tags = CustomerTag::active()->ordered()->get();

        return view('campaigns.create', compact('tags'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'message' => 'required|string|max:4096',
            'media_url' => 'nullable|url|max:500',
            'tag_ids' => 'required|array|min:1',
            'tag_ids.*' => 'exists:customer_tags,id',
        ]);

        $campaign = WhatsAppCampaign::create([
            'name' => $validated['name'],
            'message' => $validated['message'],
            'media_url' => $validated['media_url'] ?? null,
            'created_by' => auth()->id(),
            'status' => 'draft',
        ]);

        $campaign->tags()->sync($validated['tag_ids']);

        $service = app(CampaignService::class);
        $count = $service->populateRecipients($campaign);

        return redirect()->route('campaigns.show', $campaign)
            ->with('success', "Campanha criada com {$count} destinatários.");
    }

    public function show(WhatsAppCampaign $campaign): View
    {
        $campaign->load(['creator', 'tags', 'recipients.customer']);

        $stats = [
            'pending' => $campaign->recipients()->where('status', 'pending')->count(),
            'sent' => $campaign->recipients()->where('status', 'sent')->count(),
            'failed' => $campaign->recipients()->where('status', 'failed')->count(),
        ];

        return view('campaigns.show', compact('campaign', 'stats'));
    }

    public function send(Request $request, WhatsAppCampaign $campaign): JsonResponse
    {
        if ($campaign->status === 'completed') {
            return response()->json(['error' => 'Campanha já finalizada'], 422);
        }

        $useQueue = $request->boolean('queue', false);

        if ($useQueue) {
            SendCampaignBatchJob::dispatch($campaign->id, 10);
            $campaign->update(['status' => 'sending', 'started_at' => $campaign->started_at ?? now()]);

            return response()->json([
                'queued' => true,
                'message' => 'Campanha adicionada à fila de envio',
            ]);
        }

        $service = app(CampaignService::class);
        $result = $service->send($campaign);

        return response()->json($result);
    }

    public function destroy(WhatsAppCampaign $campaign): RedirectResponse
    {
        if ($campaign->status === 'sending') {
            return back()->with('error', 'Não é possível excluir campanha em andamento.');
        }

        $campaign->delete();

        return redirect()->route('campaigns.index')
            ->with('success', 'Campanha excluída.');
    }
}
