<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Followup\Enums\FollowupStatus;
use App\Domain\Followup\Enums\FollowupType;
use App\Domain\Followup\Models\Followup;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FollowupController extends Controller
{
    public function index(Request $request): View
    {
        $query = Followup::with(['customer', 'user'])
            ->where('user_id', auth()->id());

        // Filtro por status
        $status = $request->get('status', 'pending');
        if ($status === 'pending') {
            $query->where('status', FollowupStatus::Pending);
        } elseif ($status === 'done') {
            $query->where('status', FollowupStatus::Done);
        } elseif ($status === 'cancelled') {
            $query->where('status', FollowupStatus::Cancelled);
        }

        // Filtro por tipo
        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }

        // Ordenacao: atrasados primeiro, depois por data
        if ($status === 'pending') {
            $query->orderByRaw("CASE WHEN due_date < CURDATE() THEN 0 WHEN due_date = CURDATE() THEN 1 ELSE 2 END")
                  ->orderBy('due_date', 'asc');
        } else {
            $query->orderBy('updated_at', 'desc');
        }

        $followups = $query->paginate(20)->withQueryString();

        // Contagens para badges (uma única query)
        $rawCounts = Followup::where('user_id', auth()->id())
            ->where('status', FollowupStatus::Pending)
            ->selectRaw("COUNT(*) as pending")
            ->selectRaw("SUM(CASE WHEN DATE(due_date) = CURDATE() THEN 1 ELSE 0 END) as today")
            ->selectRaw("SUM(CASE WHEN DATE(due_date) < CURDATE() THEN 1 ELSE 0 END) as overdue")
            ->first();

        $counts = [
            'pending' => (int) ($rawCounts->pending ?? 0),
            'today' => (int) ($rawCounts->today ?? 0),
            'overdue' => (int) ($rawCounts->overdue ?? 0),
        ];

        return view('followups.index', [
            'followups' => $followups,
            'counts' => $counts,
            'types' => FollowupType::cases(),
            'currentStatus' => $status,
            'currentType' => $request->get('type'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(array_column(FollowupType::cases(), 'value'))],
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'due_date' => 'required|date',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        Followup::create([
            ...$validated,
            'user_id' => auth()->id(),
            'status' => FollowupStatus::Pending->value,
        ]);

        return redirect()->route('followups.index')
            ->with('success', 'Follow-up criado com sucesso!');
    }

    public function update(Request $request, Followup $followup): RedirectResponse
    {
        $this->authorizeFollowup($followup);

        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(array_column(FollowupType::cases(), 'value'))],
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'due_date' => 'required|date',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        $followup->update($validated);

        return redirect()->route('followups.index')
            ->with('success', 'Follow-up atualizado!');
    }

    public function destroy(Followup $followup): RedirectResponse
    {
        $this->authorizeFollowup($followup);

        $followup->delete();

        return redirect()->route('followups.index')
            ->with('success', 'Follow-up removido!');
    }

    public function complete(Followup $followup): RedirectResponse
    {
        $this->authorizeFollowup($followup);

        $followup->markAsDone();

        return redirect()->back()
            ->with('success', 'Follow-up concluido!');
    }

    public function cancel(Followup $followup): RedirectResponse
    {
        $this->authorizeFollowup($followup);

        $followup->markAsCancelled();

        return redirect()->back()
            ->with('success', 'Follow-up cancelado!');
    }

    private function authorizeFollowup(Followup $followup): void
    {
        if ($followup->user_id !== auth()->id()) {
            abort(403, 'Voce nao tem permissao para alterar este follow-up.');
        }
    }
}
