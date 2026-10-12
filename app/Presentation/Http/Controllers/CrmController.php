<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\CRM\Enums\DealActivityType;
use App\Domain\CRM\Enums\LeadSource;
use App\Domain\CRM\Models\Deal;
use App\Domain\CRM\Models\DealActivity;
use App\Domain\CRM\Models\PipelineStage;
use App\Domain\CRM\Models\ProductInterest;
use App\Domain\Customer\Models\CustomerTag;
use App\Domain\AI\Services\GeminiService;
use App\Domain\Schedule\Models\Appointment;
use App\Domain\WhatsApp\Models\WhatsAppMessage;
use App\Domain\WhatsApp\Services\UazapiService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrmController extends Controller
{
    // ─── Board Kanban ────────────────────────────────────────

    public function board(Request $request): View
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();

        $filterUserId = $request->get('user_id') ?: null;

        $allStages = PipelineStage::ordered()->get();
        $activeStages = PipelineStage::where('is_won', false)
            ->where('is_lost', false)
            ->where('position', '<', 90)
            ->ordered()
            ->get();

        $dealsQuery = Deal::with(['customer.tags', 'user', 'stage', 'productInterests'])
            ->open();

        if ($filterUserId) {
            $dealsQuery->forUser($filterUserId);
        }

        $deals = $dealsQuery->orderBy('position')->get();

        $dealsByStage = $activeStages->mapWithKeys(function ($stage) use ($deals) {
            $stageDeals = $deals->where('pipeline_stage_id', $stage->id)
                ->sortBy(function ($deal) {
                    $ref = $deal->last_interaction_at ?? $deal->created_at;
                    return $ref->timestamp;
                })
                ->values();

            return [$stage->id => $stageDeals];
        });

        $metricsQuery = Deal::query();
        if ($filterUserId) {
            $metricsQuery->forUser($filterUserId);
        }

        $metrics = [
            'total_open' => (clone $metricsQuery)->open()->count(),
            'total_value' => (clone $metricsQuery)->open()->sum('value'),
            'won_month' => (clone $metricsQuery)->won()
                ->whereMonth('won_at', now()->month)
                ->whereYear('won_at', now()->year)
                ->count(),
            'won_value_month' => (clone $metricsQuery)->won()
                ->whereMonth('won_at', now()->month)
                ->whereYear('won_at', now()->year)
                ->sum('value'),
            'lost_month' => (clone $metricsQuery)->lost()
                ->whereMonth('lost_at', now()->month)
                ->whereYear('lost_at', now()->year)
                ->count(),
            'stale_4h' => (clone $metricsQuery)->stale(4)->count(),
            'stale_24h' => (clone $metricsQuery)->stale(24)->count(),
            'overdue_followups' => (clone $metricsQuery)->needsFollowup()->count(),
        ];

        $sellers = \App\Domain\User\Models\User::where('active', true)->orderBy('name')->get(['id', 'name', 'role']);
        $allTags = CustomerTag::active()->ordered()->get(['id', 'name', 'slug', 'color']);

        return view('crm.board', [
            'stages' => $allStages,
            'activeStages' => $activeStages,
            'dealsByStage' => $dealsByStage,
            'metrics' => $metrics,
            'sellers' => $sellers,
            'filterUserId' => $filterUserId,
            'isAdmin' => $isAdmin,
            'attendants' => Appointment::ATTENDANTS,
            'durationOptions' => Appointment::DURATION_OPTIONS,
            'leadSources' => LeadSource::cases(),
            'allTags' => $allTags,
        ]);
    }

    // ─── Poll API (notificacao de novos leads) ─────────────────

    public function newDealsCheck(Request $request): JsonResponse
    {
        $since = $request->get('since');
        $sinceDate = $since ? \Carbon\Carbon::parse($since) : now()->subSeconds(30);

        $newDeals = Deal::where('source', 'whatsapp')
            ->where('created_at', '>', $sinceDate)
            ->with('customer:id,name')
            ->select('id', 'title', 'phone', 'customer_id', 'created_at')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return response()->json([
            'count' => $newDeals->count(),
            'deals' => $newDeals->map(fn ($d) => [
                'id' => $d->id,
                'title' => $d->title,
                'customer_name' => $d->customer?->name,
                'created_at' => $d->created_at->toISOString(),
            ]),
            'server_time' => now()->toISOString(),
        ]);
    }

    // ─── CRUD Deals ──────────────────────────────────────────

    public function create(): View
    {
        $activeStages = PipelineStage::where('is_won', false)
            ->where('is_lost', false)
            ->ordered()
            ->get();

        return view('crm.create', [
            'activeStages' => $activeStages,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $leadSourceValues = implode(',', array_column(LeadSource::cases(), 'value'));

        if ($request->filled('phone')) {
            $request->merge(['phone' => preg_replace('/\D/', '', $request->input('phone'))]);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'product_interest' => 'nullable|string|max:255',
            'value' => 'nullable|numeric|min:0',
            'phone' => 'required|string|min:10|max:20',
            'description' => 'nullable|string|max:2000',
            'expected_close_date' => 'nullable|date',
            'customer_id' => 'nullable|exists:customers,id',
            'pipeline_stage_id' => 'nullable|exists:pipeline_stages,id',
            'interest_model' => 'nullable|string|max:255',
            'interest_storage' => 'nullable|string|max:50',
            'interest_color' => 'nullable|string|max:50',
            'interest_condition' => 'nullable|string|in:novo,seminovo',
            'interest_max_budget' => 'nullable|numeric|min:0',
            'lead_source' => "nullable|string|in:{$leadSourceValues}",
            'temperature' => 'nullable|string|in:hot,warm,cold',
            'next_action' => 'nullable|string|max:255',
            'next_action_at' => 'nullable|date',
        ], [
            'phone.required' => 'Informe o telefone/WhatsApp do cliente.',
            'phone.min' => 'O telefone deve ter pelo menos 10 dígitos.',
        ]);

        $stageId = ! empty($validated['pipeline_stage_id'])
            ? $validated['pipeline_stage_id']
            : (PipelineStage::where('is_default', true)->value('id')
                ?? PipelineStage::active()->ordered()->value('id'));

        if (! $stageId) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['pipeline_stage_id' => 'Nenhuma etapa de pipeline configurada. Execute o seeder.']);
        }

        unset($validated['pipeline_stage_id']);

        if (! empty($validated['interest_model']) && empty($validated['product_interest'])) {
            $parts = array_filter([
                $validated['interest_model'],
                $validated['interest_storage'] ?? null,
                $validated['interest_color'] ?? null,
                isset($validated['interest_condition']) ? ($validated['interest_condition'] === 'novo' ? 'Novo' : 'Seminovo') : null,
            ]);
            $validated['product_interest'] = implode(' - ', $parts);
        }

        $interestFields = ['interest_model', 'interest_storage', 'interest_color', 'interest_condition', 'interest_max_budget'];
        $interestData = collect($interestFields)->mapWithKeys(fn ($k) => [$k => $validated[$k] ?? null])->toArray();
        foreach ($interestFields as $f) {
            unset($validated[$f]);
        }

        $maxPosition = Deal::where('pipeline_stage_id', $stageId)->max('position') ?? 0;

        $deal = Deal::create([
            ...$validated,
            'user_id' => auth()->id(),
            'pipeline_stage_id' => $stageId,
            'position' => $maxPosition + 1,
            'temperature' => $validated['temperature'] ?? 'warm',
            'last_interaction_at' => now(),
        ]);

        if (! empty($interestData['interest_model'])) {
            $deal->productInterests()->create([
                'customer_id' => $deal->customer_id,
                'model'       => $interestData['interest_model'],
                'storage'     => $interestData['interest_storage'],
                'color'       => $interestData['interest_color'],
                'condition'   => $interestData['interest_condition'],
                'max_budget'  => $interestData['interest_max_budget'],
            ]);
        }

        $deal->logActivity(DealActivityType::Created, "Negócio criado: {$deal->title}");

        $this->syncLeadToWhatsApp($deal);

        return redirect()->route('crm.show', $deal)
            ->with('success', 'Lead criado! Adicione interesses, agendamentos e mais detalhes.');
    }

    public function show(Deal $deal): View
    {
        $deal->load(['customer.tags', 'user', 'stage', 'activities.user', 'productInterests', 'whatsappMessages', 'appointments']);

        $allSnapshots = $deal->allNegotiationSnapshots()->get();

        if ($deal->unread_wa_count > 0) {
            $deal->update(['unread_wa_count' => 0]);
            $this->markMessagesAsRead($deal);
        }

        $allStages = PipelineStage::ordered()->get();
        $activeStages = PipelineStage::where('is_won', false)
            ->where('is_lost', false)
            ->ordered()
            ->get();

        return view('crm.show', [
            'deal' => $deal,
            'allSnapshots' => $allSnapshots,
            'stages' => $allStages,
            'activeStages' => $activeStages,
            'activityTypes' => [
                DealActivityType::Note,
                DealActivityType::WhatsApp,
                DealActivityType::Call,
            ],
        ]);
    }

    public function update(Request $request, Deal $deal): RedirectResponse
    {
        $leadSourceValues = implode(',', array_column(LeadSource::cases(), 'value'));

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'product_interest' => 'nullable|string|max:255',
            'value' => 'nullable|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'deposit_paid_at' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:2000',
            'expected_close_date' => 'nullable|date',
            'customer_id' => 'nullable|exists:customers,id',
            'lead_source' => "nullable|string|in:{$leadSourceValues}",
            'temperature' => 'nullable|string|in:hot,warm,cold',
            'next_action' => 'nullable|string|max:255',
            'next_action_at' => 'nullable|date',
        ]);

        $deal->update($validated);

        $this->syncLeadToWhatsApp($deal);

        return redirect()->route('crm.show', $deal)
            ->with('success', 'Negócio atualizado!');
    }

    public function destroy(Deal $deal): RedirectResponse
    {
        $this->authorizeDealOwnership($deal);

        $deal->delete();

        return redirect()->route('crm.board')
            ->with('success', 'Negócio removido!');
    }

    // ─── Ações do Deal ───────────────────────────────────────

    public function moveStage(Request $request, Deal $deal): JsonResponse
    {

        $validated = $request->validate([
            'pipeline_stage_id' => 'required|exists:pipeline_stages,id',
            'position' => 'required|integer|min:0',
        ]);

        $newStage = PipelineStage::findOrFail($validated['pipeline_stage_id']);
        $oldStageId = $deal->pipeline_stage_id;

        // Se mudou de stage, logar a atividade
        if ($oldStageId !== $newStage->id) {
            $deal->moveToStage($newStage);
        }

        $deal->update(['position' => $validated['position']]);

        // Reordena os deals no stage destino
        $this->reorderDealsInStage($newStage->id, $deal->id, $validated['position']);

        return response()->json(['success' => true]);
    }

    public function win(Deal $deal): RedirectResponse
    {

        $deal->markAsWon();

        return redirect()->back()->with('success', 'Negócio marcado como ganho!');
    }

    public function lose(Request $request, Deal $deal): RedirectResponse
    {
        $request->validate([
            'lost_reason' => ['required', 'string', 'min:3', 'max:500'],
        ], [
            'lost_reason.required' => 'Informe o motivo da perda.',
            'lost_reason.min' => 'O motivo deve ter pelo menos 3 caracteres.',
        ]);

        $deal->markAsLost($request->input('lost_reason'));

        return redirect()->back()->with('success', 'Negócio marcado como perdido.');
    }

    public function reopen(Request $request, Deal $deal): RedirectResponse
    {

        $stageId = $request->input('pipeline_stage_id')
            ?? PipelineStage::where('is_default', true)->first()?->id;

        $deal->reopen($stageId);

        return redirect()->route('crm.show', $deal)
            ->with('success', 'Negócio reaberto!');
    }

    // ─── Atividades ──────────────────────────────────────────

    public function storeActivity(Request $request, Deal $deal): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|in:note,whatsapp,call',
            'description' => 'required|string|max:2000',
        ]);

        $deal->logActivity(
            DealActivityType::from($validated['type']),
            $validated['description']
        );

        $deal->updateLastInteraction();

        return redirect()->route('crm.show', $deal)
            ->with('success', 'Atividade registrada!');
    }

    // ─── Interesses de Produto ─────────────────────────────

    public function storeInterest(Request $request, Deal $deal): RedirectResponse
    {
        $validated = $request->validate([
            'model' => 'required|string|max:255',
            'storage' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'condition' => 'nullable|string|in:novo,seminovo',
            'max_budget' => 'nullable|numeric|min:0',
        ]);

        $deal->productInterests()->create([
            'customer_id' => $deal->customer_id,
            ...$validated,
        ]);

        $parts = array_filter([
            $validated['model'],
            $validated['storage'] ?? null,
            $validated['color'] ?? null,
        ]);
        $deal->update(['product_interest' => implode(' - ', $parts)]);

        $deal->logActivity(DealActivityType::Note, "Interesse adicionado: {$validated['model']}");

        return redirect()->route('crm.show', $deal)
            ->with('success', 'Interesse de produto adicionado!');
    }

    public function destroyInterest(Deal $deal, ProductInterest $interest): RedirectResponse
    {
        if ($interest->deal_id !== $deal->id) {
            abort(403);
        }

        $model = $interest->model;
        $interest->delete();

        $remaining = $deal->productInterests()->first();
        $deal->update([
            'product_interest' => $remaining
                ? implode(' - ', array_filter([$remaining->model, $remaining->storage, $remaining->color]))
                : null,
        ]);

        $deal->logActivity(DealActivityType::Note, "Interesse removido: {$model}");

        return redirect()->route('crm.show', $deal)
            ->with('success', 'Interesse removido!');
    }

    // ─── Agendamentos vinculados ao Deal ────────────────────

    public function storeAppointment(Request $request, Deal $deal): RedirectResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'attendant' => 'required|string|in:' . implode(',', array_keys(Appointment::ATTENDANTS)),
            'start_time' => 'required|date_format:H:i',
            'duration_minutes' => 'required|integer|in:' . implode(',', array_keys(Appointment::DURATION_OPTIONS)),
            'service_description' => 'nullable|string|max:500',
        ]);

        $startTime = $validated['start_time'];
        $endTime = \Carbon\Carbon::createFromFormat('H:i', $startTime)
            ->addMinutes((int) $validated['duration_minutes'])
            ->format('H:i');

        if (Appointment::hasConflict($validated['attendant'], $validated['date'], $startTime, $endTime)) {
            return redirect()->route('crm.show', $deal)
                ->withErrors(['appointment' => 'Conflito de horário! Já existe um agendamento nesse período.']);
        }

        $customerName = $deal->customer?->name ?? $deal->title;
        $customerPhone = $deal->phone ?? $deal->customer?->phone;

        Appointment::create([
            'deal_id' => $deal->id,
            'customer_id' => $deal->customer_id,
            'customer_name' => $customerName,
            'customer_phone' => $customerPhone,
            'attendant' => $validated['attendant'],
            'date' => $validated['date'],
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration_minutes' => $validated['duration_minutes'],
            'service_description' => $validated['service_description'],
            'status' => 'scheduled',
        ]);

        $dateFormatted = \Carbon\Carbon::parse($validated['date'])->format('d/m/Y');
        $deal->logActivity(
            DealActivityType::Note,
            "Agendamento criado: {$dateFormatted} às {$startTime} com {$validated['attendant']}"
        );

        return redirect()->route('crm.show', $deal)
            ->with('success', 'Agendamento criado e vinculado ao lead!');
    }

    // ─── Follow-up rápido (AJAX) ────────────────────────────

    public function updateFollowup(Request $request, Deal $deal): JsonResponse
    {
        $validated = $request->validate([
            'next_action' => 'nullable|string|max:500',
            'next_action_at' => 'nullable|date',
        ]);

        $deal->update($validated);
        $deal->updateLastInteraction();

        if (! empty($validated['next_action'])) {
            $deal->logActivity(
                DealActivityType::Note,
                "Follow-up atualizado: {$validated['next_action']}"
            );
        }

        return response()->json(['success' => true]);
    }

    // ─── IA - Gemini ─────────────────────────────────────────

    public function aiSuggestMessage(Request $request, Deal $deal): JsonResponse
    {

        $gemini = app(GeminiService::class);

        if (! $gemini->isAvailable()) {
            return response()->json(['error' => 'Serviço de IA não disponível.'], 503);
        }

        $deal->load(['customer', 'stage', 'activities' => fn($q) => $q->latest()->limit(5)]);

        $context = $this->buildAiContext($deal);

        $prompt = "Com base no contexto abaixo de uma negociação de produtos Apple, gere uma mensagem curta e profissional para enviar via WhatsApp ao cliente. "
            . "A mensagem deve ser amigável, objetiva e focada em avançar a negociação. "
            . "Não use emojis em excesso. Máximo 3 frases.\n\n"
            . $context;

        $systemInstruction = "Você é um assistente de vendas especializado em produtos Apple (iPhone, MacBook, iPad, Apple Watch, AirPods). "
            . "Gere mensagens de WhatsApp naturais em português brasileiro. Apenas a mensagem, sem explicações.";

        $message = $gemini->generateContent($prompt, $systemInstruction);

        if (! $message) {
            return response()->json(['error' => 'Não foi possível gerar a sugestão.'], 500);
        }

        return response()->json(['message' => trim($message)]);
    }

    public function aiAnalyzeDeal(Request $request, Deal $deal): JsonResponse
    {

        $gemini = app(GeminiService::class);

        if (! $gemini->isAvailable()) {
            return response()->json(['error' => 'Serviço de IA não disponível.'], 503);
        }

        $deal->load(['customer', 'stage', 'activities']);

        $context = $this->buildAiContext($deal);

        $prompt = "Analise esta negociação de produto Apple e forneça:\n"
            . "1. Uma avaliação da probabilidade de fechamento (alta/média/baixa)\n"
            . "2. Um conselho prático para o vendedor avançar a negociação\n"
            . "3. Se há algum risco ou ponto de atenção\n\n"
            . "Seja objetivo, máximo 4 frases.\n\n"
            . $context;

        $systemInstruction = "Você é um consultor de vendas especializado em produtos Apple. "
            . "Analise negociações e dê conselhos práticos em português brasileiro. Apenas a análise, sem formatação markdown.";

        $analysis = $gemini->generateContent($prompt, $systemInstruction);

        if (! $analysis) {
            return response()->json(['error' => 'Não foi possível gerar a análise.'], 500);
        }

        return response()->json(['analysis' => trim($analysis)]);
    }

    // ─── Lista de negócios fechados/perdidos ─────────────────

    public function history(Request $request): View
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();
        $filterUserId = $request->get('user_id') ?: null;

        $tab = $request->get('tab', 'won');

        $query = Deal::with(['customer', 'user', 'stage']);

        if ($filterUserId) {
            $query->forUser($filterUserId);
        }

        if ($tab === 'won') {
            $query->won()->orderBy('won_at', 'desc');
        } else {
            $query->lost()->orderBy('lost_at', 'desc');
        }

        $deals = $query->paginate(20)->withQueryString();

        $sellers = \App\Domain\User\Models\User::where('active', true)->orderBy('name')->get(['id', 'name', 'role']);

        return view('crm.history', [
            'deals' => $deals,
            'tab' => $tab,
            'sellers' => $sellers,
            'filterUserId' => $filterUserId,
            'isAdmin' => $isAdmin,
        ]);
    }

    // ─── Helpers privados ────────────────────────────────────

    private function authorizeDealOwnership(Deal $deal): void
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return;
        }

        if ($deal->user_id !== $user->id) {
            abort(403, 'Apenas o responsável ou admin pode realizar esta ação.');
        }
    }

    private function reorderDealsInStage(string $stageId, string $movedDealId, int $newPosition): void
    {
        $deals = Deal::where('pipeline_stage_id', $stageId)
            ->where('id', '!=', $movedDealId)
            ->orderBy('position')
            ->get();

        $position = 0;
        foreach ($deals as $deal) {
            if ($position === $newPosition) {
                $position++;
            }
            $deal->update(['position' => $position]);
            $position++;
        }
    }

    private function buildAiContext(Deal $deal): string
    {
        $lines = [];
        $lines[] = "Negócio: {$deal->title}";
        $lines[] = "Produto de interesse: " . ($deal->product_interest ?: 'Não especificado');
        $lines[] = "Valor: " . ($deal->value ? "R$ " . number_format((float)$deal->value, 2, ',', '.') : 'Não informado');
        $lines[] = "Etapa atual: {$deal->stage->name}";
        $lines[] = "Dias desde última interação: {$deal->days_since_last_activity}";

        if ($deal->customer) {
            $lines[] = "Cliente: {$deal->customer->name}";
        }

        if ($deal->description) {
            $lines[] = "Observações: {$deal->description}";
        }

        // Últimas atividades
        $activities = $deal->activities->take(5);
        if ($activities->isNotEmpty()) {
            $lines[] = "\nÚltimas interações:";
            foreach ($activities as $activity) {
                $date = $activity->created_at->format('d/m');
                $lines[] = "- [{$date}] {$activity->type->label()}: {$activity->description}";
            }
        }

        return implode("\n", $lines);
    }

    // ─── WhatsApp Messaging ──────────────────────────────────

    public function sendWhatsApp(Request $request, Deal $deal): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:4096',
            'reply_to' => 'nullable|string',
        ]);

        $uazapi = app(UazapiService::class);
        if (!$uazapi->isConfigured()) {
            return response()->json(['success' => false, 'error' => 'UazAPI não configurada'], 500);
        }

        $phone = $deal->phone;
        if (!$phone) {
            return response()->json(['success' => false, 'error' => 'Deal sem telefone'], 422);
        }

        $replyTo = $request->input('reply_to');
        $senderName = auth()->user()->name;
        $whatsappText = "*{$senderName}:*\n{$request->message}";
        $result = $uazapi->sendText($phone, $whatsappText, $replyTo);

        if (isset($result['error'])) {
            return response()->json(['success' => false, 'error' => $result['error']], 500);
        }

        $msgData = [
            'wa_message_id' => $result['key']['id'] ?? ('sent_' . uniqid()),
            'from_phone' => $phone,
            'from_name' => auth()->user()->name . ' DG Store',
            'message_type' => 'text',
            'message_body' => $request->message,
            'raw_payload' => $result,
            'deal_id' => $deal->id,
            'direction' => 'outbound',
            'delivery_status' => 'sent',
        ];

        if ($replyTo) {
            $quoted = $deal->whatsappMessages()->where('wa_message_id', $replyTo)->first();
            $msgData['reply_to_id'] = $replyTo;
            $msgData['reply_to_body'] = $quoted?->message_body ? mb_substr($quoted->message_body, 0, 200) : null;
        }

        WhatsAppMessage::create($msgData);

        $deal->logActivity(DealActivityType::WhatsApp, "📤 Mensagem enviada: {$request->message}");
        $deal->updateLastInteraction();

        return response()->json(['success' => true]);
    }

    public function sendWhatsAppMedia(Request $request, Deal $deal): JsonResponse
    {
        $request->validate([
            'media' => 'required|file|max:16384',
            'caption' => 'nullable|string|max:1024',
        ]);

        $uazapi = app(UazapiService::class);
        if (!$uazapi->isConfigured()) {
            return response()->json(['success' => false, 'error' => 'UazAPI não configurada'], 500);
        }

        $phone = $deal->phone;
        if (!$phone) {
            return response()->json(['success' => false, 'error' => 'Deal sem telefone'], 422);
        }

        $file = $request->file('media');
        $path = $file->store("whatsapp/sent/{$deal->id}", 'public');
        $publicUrl = url("storage/{$path}");

        $isImage = str_starts_with($file->getMimeType(), 'image/');
        $senderName = auth()->user()->name;
        $caption = $request->caption
            ? "*{$senderName}:*\n{$request->caption}"
            : "*{$senderName}:*\n📎 {$file->getClientOriginalName()}";
        $result = $isImage
            ? $uazapi->sendImage($phone, $publicUrl, $caption)
            : $uazapi->sendDocument($phone, $publicUrl, $file->getClientOriginalName());

        if (isset($result['error'])) {
            return response()->json(['success' => false, 'error' => $result['error']], 500);
        }

        WhatsAppMessage::create([
            'wa_message_id' => $result['key']['id'] ?? ('sent_media_' . uniqid()),
            'from_phone' => $phone,
            'from_name' => auth()->user()->name . ' DG Store',
            'message_type' => $isImage ? 'image' : 'document',
            'message_body' => $request->caption ?? "📎 {$file->getClientOriginalName()}",
            'raw_payload' => $result,
            'deal_id' => $deal->id,
            'direction' => 'outbound',
            'delivery_status' => 'sent',
        ]);

        $deal->logActivity(DealActivityType::WhatsApp, "📤 Mídia enviada: {$file->getClientOriginalName()}");
        $deal->updateLastInteraction();

        return response()->json(['success' => true]);
    }

    public function getWhatsAppMessages(Deal $deal): JsonResponse
    {
        $messages = $deal->whatsappMessages()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'wa_message_id' => $m->wa_message_id,
                'from_name' => $m->from_name,
                'message_type' => $m->message_type,
                'message_body' => $m->message_body,
                'transcription' => $m->transcription,
                'direction' => $m->direction,
                'delivery_status' => $m->delivery_status,
                'created_at' => $m->created_at->toISOString(),
            ]);

        return response()->json(['messages' => $messages]);
    }

    public function downloadWhatsAppMedia(Deal $deal, string $messageId): JsonResponse
    {
        $message = $deal->whatsappMessages()->where('id', $messageId)->firstOrFail();
        $waId = $message->wa_message_id;

        $uazapi = app(UazapiService::class);
        $result = $uazapi->downloadMedia($waId);

        if (isset($result['error'])) {
            return response()->json(['success' => false, 'error' => $result['error']], 500);
        }

        return response()->json(['success' => true, 'data' => $result]);
    }

    public function sendWhatsAppMenu(Request $request, Deal $deal): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:60',
            'body' => 'required|string|max:1024',
            'type' => 'required|in:buttons,list',
            'buttons' => 'required_if:type,buttons|array|min:1|max:3',
            'buttons.*.text' => 'required_if:type,buttons|string|max:20',
            'sections' => 'required_if:type,list|array|min:1',
            'button_text' => 'required_if:type,list|string|max:20',
        ]);

        $uazapi = app(UazapiService::class);
        if (!$uazapi->isConfigured() || !$deal->phone) {
            return response()->json(['success' => false, 'error' => 'UazAPI não configurada ou deal sem telefone'], 500);
        }

        try {
            $result = $request->type === 'buttons'
                ? $uazapi->sendMenu($deal->phone, $request->title, $request->body, $request->buttons)
                : $uazapi->sendList($deal->phone, $request->title, $request->body, $request->button_text, $request->sections);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }

        if (isset($result['error'])) {
            return response()->json(['success' => false, 'error' => is_string($result['error']) ? $result['error'] : json_encode($result['error'])], 500);
        }

        if (isset($result['success']) && $result['success'] === false) {
            return response()->json(['success' => false, 'error' => $result['message'] ?? 'Erro da API'], 500);
        }

        $desc = $request->type === 'buttons'
            ? "📋 Menu com botões: {$request->title}"
            : "📋 Lista interativa: {$request->title}";

        WhatsAppMessage::create([
            'wa_message_id' => $result['key']['id'] ?? ('menu_' . uniqid()),
            'from_phone' => $deal->phone,
            'from_name' => auth()->user()->name . ' DG Store',
            'message_type' => 'interactive',
            'message_body' => "{$request->title}\n{$request->body}",
            'raw_payload' => $result,
            'deal_id' => $deal->id,
            'direction' => 'outbound',
            'delivery_status' => 'sent',
        ]);

        $deal->logActivity(DealActivityType::WhatsApp, "📤 {$desc}");

        return response()->json(['success' => true]);
    }

    public function checkWhatsAppNumber(Request $request): JsonResponse
    {
        $request->validate(['phone' => 'required|string']);

        $uazapi = app(UazapiService::class);
        if (!$uazapi->isConfigured()) {
            return response()->json(['success' => false, 'error' => 'UazAPI não configurada'], 500);
        }

        $result = $uazapi->checkNumber($request->phone);

        return response()->json([
            'success' => true,
            'exists' => $result['exists'] ?? false,
            'jid' => $result['jid'] ?? null,
        ]);
    }

    // ─── Real-time: Poll, Presence, Mark Read ────────────────

    public function pollWhatsAppMessages(Request $request, Deal $deal): JsonResponse
    {
        $since = $request->get('since');
        $sinceDate = $since ? \Carbon\Carbon::parse($since) : now()->subSeconds(5);

        $messages = $deal->whatsappMessages()
            ->where('created_at', '>', $sinceDate)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'wa_message_id' => $m->wa_message_id,
                'from_name' => $m->from_name,
                'message_type' => $m->message_type,
                'message_body' => $m->message_body,
                'transcription' => $m->transcription,
                'direction' => $m->direction,
                'delivery_status' => $m->delivery_status,
                'local_media_path' => $m->local_media_path,
                'reply_to_id' => $m->reply_to_id,
                'reply_to_body' => $m->reply_to_body,
                'created_at' => $m->created_at->toISOString(),
                'time' => $m->created_at->setTimezone('America/Sao_Paulo')->format('H:i'),
            ]);

        $statusUpdates = $deal->whatsappMessages()
            ->where('direction', 'outbound')
            ->where('updated_at', '>', $sinceDate)
            ->whereColumn('updated_at', '!=', 'created_at')
            ->get()
            ->map(fn ($m) => [
                'wa_message_id' => $m->wa_message_id,
                'delivery_status' => $m->delivery_status,
            ]);

        $presenceKey = "wa_presence:" . preg_replace('/\D/', '', $deal->phone ?? '');
        $presence = Cache::get($presenceKey);

        return response()->json([
            'messages' => $messages,
            'status_updates' => $statusUpdates,
            'presence' => $presence,
            'server_time' => now()->toISOString(),
        ]);
    }

    public function getWhatsAppPresence(Deal $deal): JsonResponse
    {
        $phone = preg_replace('/\D/', '', $deal->phone ?? '');
        $presence = Cache::get("wa_presence:{$phone}");

        return response()->json(['presence' => $presence]);
    }

    private function syncLeadToWhatsApp(Deal $deal): void
    {
        try {
            $uazapi = app(UazapiService::class);
            if (!$uazapi->isConfigured() || !$deal->phone) {
                return;
            }

            $name = $deal->customer?->name ?? $deal->title;
            $responsible = $deal->user?->name;

            $notes = collect([
                "Deal: {$deal->title}",
                $deal->product_interest ? "Interesse: {$deal->product_interest}" : null,
                $deal->value ? "Valor: R$ " . number_format($deal->value, 2, ',', '.') : null,
                $deal->temperature ? "Temp: {$deal->temperature}" : null,
            ])->filter()->implode(' | ');

            $uazapi->editLead($deal->phone, $name, $responsible, $notes);
        } catch (\Throwable $e) {
            Log::warning('Erro ao sincronizar lead WhatsApp', ['error' => $e->getMessage()]);
        }
    }

    private function markMessagesAsRead(Deal $deal): void
    {
        try {
            $uazapi = app(UazapiService::class);
            if (!$uazapi->isConfigured() || !$deal->phone) {
                return;
            }

            $unreadMessages = $deal->whatsappMessages()
                ->where('direction', 'inbound')
                ->whereNotIn('delivery_status', ['read'])
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();

            foreach ($unreadMessages as $msg) {
                if ($msg->wa_message_id) {
                    $uazapi->markRead($msg->wa_message_id);
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Erro ao marcar mensagens como lidas', ['error' => $e->getMessage()]);
        }
    }
}
