<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\CRM\Enums\DealActivityType;
use App\Domain\CRM\Models\Deal;
use App\Domain\CRM\Models\DealActivity;
use App\Domain\CRM\Models\PipelineStage;
use App\Domain\AI\Services\GeminiService;
use App\Http\Controllers\Controller;
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

        // Admin pode filtrar por vendedor
        $filterUserId = $isAdmin ? $request->get('user_id') : $user->id;

        $allStages = PipelineStage::ordered()->get();
        $activeStages = PipelineStage::where('is_won', false)
            ->where('is_lost', false)
            ->ordered()
            ->get();

        $dealsQuery = Deal::with(['customer', 'user', 'stage'])
            ->open();

        if ($filterUserId) {
            $dealsQuery->forUser($filterUserId);
        }

        $deals = $dealsQuery->orderBy('position')->get();

        // Agrupa deals por stage
        $dealsByStage = $activeStages->mapWithKeys(function ($stage) use ($deals) {
            return [$stage->id => $deals->where('pipeline_stage_id', $stage->id)->values()];
        });

        // Métricas
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
        ];

        // Lista de vendedores para filtro (admin)
        $sellers = $isAdmin
            ? \App\Domain\User\Models\User::where('active', true)->orderBy('name')->get(['id', 'name', 'role'])
            : collect();

        return view('crm.board', [
            'stages' => $allStages,
            'activeStages' => $activeStages,
            'dealsByStage' => $dealsByStage,
            'metrics' => $metrics,
            'sellers' => $sellers,
            'filterUserId' => $filterUserId,
            'isAdmin' => $isAdmin,
        ]);
    }

    // ─── CRUD Deals ──────────────────────────────────────────

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'product_interest' => 'nullable|string|max:255',
            'value' => 'nullable|numeric|min:0',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:2000',
            'expected_close_date' => 'nullable|date',
            'customer_id' => 'nullable|exists:customers,id',
            'pipeline_stage_id' => 'nullable|exists:pipeline_stages,id',
        ]);

        // Garante um stage válido: enviado > default > primeiro ativo
        $stageId = ! empty($validated['pipeline_stage_id'])
            ? $validated['pipeline_stage_id']
            : (PipelineStage::where('is_default', true)->value('id')
                ?? PipelineStage::active()->ordered()->value('id'));

        if (! $stageId) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['pipeline_stage_id' => 'Nenhuma etapa de pipeline configurada. Execute o seeder.']);
        }

        // Remove pipeline_stage_id do validated para não conflitar com o override
        unset($validated['pipeline_stage_id']);

        $maxPosition = Deal::where('pipeline_stage_id', $stageId)->max('position') ?? 0;

        $deal = Deal::create([
            ...$validated,
            'user_id' => auth()->id(),
            'pipeline_stage_id' => $stageId,
            'position' => $maxPosition + 1,
        ]);

        $deal->logActivity(DealActivityType::Created, "Negócio criado: {$deal->title}");

        return redirect()->route('crm.board')
            ->with('success', 'Negócio criado com sucesso!');
    }

    public function show(Deal $deal): View
    {
        $this->authorizeDeal($deal);

        $deal->load(['customer', 'user', 'stage', 'activities.user']);

        $allStages = PipelineStage::ordered()->get();
        $activeStages = PipelineStage::where('is_won', false)
            ->where('is_lost', false)
            ->ordered()
            ->get();

        return view('crm.show', [
            'deal' => $deal,
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
        $this->authorizeDeal($deal);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'product_interest' => 'nullable|string|max:255',
            'value' => 'nullable|numeric|min:0',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:2000',
            'expected_close_date' => 'nullable|date',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        $deal->update($validated);

        return redirect()->route('crm.show', $deal)
            ->with('success', 'Negócio atualizado!');
    }

    public function destroy(Deal $deal): RedirectResponse
    {
        $this->authorizeDeal($deal);

        $deal->delete();

        return redirect()->route('crm.board')
            ->with('success', 'Negócio removido!');
    }

    // ─── Ações do Deal ───────────────────────────────────────

    public function moveStage(Request $request, Deal $deal): JsonResponse
    {
        $this->authorizeDeal($deal);

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
        $this->authorizeDeal($deal);

        $deal->markAsWon();

        return redirect()->back()->with('success', 'Negócio marcado como ganho!');
    }

    public function lose(Request $request, Deal $deal): RedirectResponse
    {
        $this->authorizeDeal($deal);

        $reason = $request->input('lost_reason', '');
        $deal->markAsLost($reason);

        return redirect()->back()->with('success', 'Negócio marcado como perdido.');
    }

    public function reopen(Request $request, Deal $deal): RedirectResponse
    {
        $this->authorizeDeal($deal);

        $stageId = $request->input('pipeline_stage_id')
            ?? PipelineStage::where('is_default', true)->first()?->id;

        $deal->reopen($stageId);

        return redirect()->route('crm.show', $deal)
            ->with('success', 'Negócio reaberto!');
    }

    // ─── Atividades ──────────────────────────────────────────

    public function storeActivity(Request $request, Deal $deal): RedirectResponse
    {
        $this->authorizeDeal($deal);

        $validated = $request->validate([
            'type' => 'required|string|in:note,whatsapp,call',
            'description' => 'required|string|max:2000',
        ]);

        $deal->logActivity(
            DealActivityType::from($validated['type']),
            $validated['description']
        );

        return redirect()->route('crm.show', $deal)
            ->with('success', 'Atividade registrada!');
    }

    // ─── IA - Gemini ─────────────────────────────────────────

    public function aiSuggestMessage(Request $request, Deal $deal): JsonResponse
    {
        $this->authorizeDeal($deal);

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
        $this->authorizeDeal($deal);

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
        $filterUserId = $isAdmin ? $request->get('user_id') : $user->id;

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

        $sellers = $isAdmin
            ? \App\Domain\User\Models\User::where('active', true)->orderBy('name')->get(['id', 'name', 'role'])
            : collect();

        return view('crm.history', [
            'deals' => $deals,
            'tab' => $tab,
            'sellers' => $sellers,
            'filterUserId' => $filterUserId,
            'isAdmin' => $isAdmin,
        ]);
    }

    // ─── Helpers privados ────────────────────────────────────

    private function authorizeDeal(Deal $deal): void
    {
        $user = auth()->user();

        // Admin pode acessar qualquer deal
        if ($user->isAdmin()) {
            return;
        }

        if ($deal->user_id !== $user->id) {
            abort(403, 'Você não tem permissão para acessar este negócio.');
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
}
