<?php

declare(strict_types=1);

namespace App\Domain\CashRegister\Services;

use App\Domain\CashRegister\Enums\CashEntryType;
use App\Domain\CashRegister\Enums\CashRegisterStatus;
use App\Domain\CashRegister\Models\CashRegister;
use App\Domain\CashRegister\Models\CashRegisterEntry;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CashRegisterService
{
    /**
     * Retorna o caixa aberto atual (se existir).
     */
    public function getOpenRegister(): ?CashRegister
    {
        return CashRegister::open()
            ->with(['openedByUser', 'entries.user'])
            ->latest('opened_at')
            ->first();
    }

    /**
     * Abre um novo caixa.
     */
    public function open(string $userId, float $openingBalance): CashRegister
    {
        $existing = $this->getOpenRegister();
        if ($existing) {
            throw new \Exception('Já existe um caixa aberto. Feche-o antes de abrir outro.');
        }

        return CashRegister::create([
            'opened_by' => $userId,
            'status' => CashRegisterStatus::Open,
            'opening_balance' => $openingBalance,
            'opened_at' => now(),
        ]);
    }

    /**
     * Fecha o caixa atual.
     */
    public function close(CashRegister $register, string $userId, float $closingBalance, ?string $notes = null): CashRegister
    {
        if (!$register->isOpen()) {
            throw new \Exception('Este caixa já está fechado.');
        }

        $expected = $register->calculateExpectedBalance();

        $register->update([
            'closed_by' => $userId,
            'status' => CashRegisterStatus::Closed,
            'closing_balance' => $closingBalance,
            'expected_balance' => $expected,
            'difference' => $closingBalance - $expected,
            'closed_at' => now(),
            'closing_notes' => $notes,
        ]);

        return $register->fresh();
    }

    /**
     * Adiciona entrada ao caixa (sangria, suprimento, venda, etc.).
     */
    public function addEntry(
        CashRegister $register,
        string $userId,
        CashEntryType $type,
        float $amount,
        string $description,
        ?string $paymentMethod = null,
        ?string $referenceId = null
    ): CashRegisterEntry {
        if (!$register->isOpen()) {
            throw new \Exception('Caixa fechado. Abra o caixa para registrar movimentações.');
        }

        return CashRegisterEntry::create([
            'cash_register_id' => $register->id,
            'user_id' => $userId,
            'type' => $type,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'description' => $description,
            'reference_id' => $referenceId,
        ]);
    }

    /**
     * Retorna histórico de caixas recentes.
     */
    public function getHistory(int $limit = 15): Collection
    {
        return CashRegister::with(['openedByUser', 'closedByUser'])
            ->orderBy('opened_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Resumo do caixa aberto (por tipo de entrada).
     */
    public function getSummary(CashRegister $register): array
    {
        $entries = $register->entries;

        $byType = [];
        foreach (CashEntryType::cases() as $type) {
            $filtered = $entries->where('type', $type);
            $byType[$type->value] = [
                'label' => $type->label(),
                'color' => $type->color(),
                'is_inflow' => $type->isInflow(),
                'count' => $filtered->count(),
                'total' => (float) $filtered->sum('amount'),
            ];
        }

        $byPaymentMethod = $entries->where('type', CashEntryType::Sale)
            ->groupBy('payment_method')
            ->map(fn ($group) => [
                'count' => $group->count(),
                'total' => (float) $group->sum('amount'),
            ])
            ->toArray();

        return [
            'by_type' => $byType,
            'by_payment_method' => $byPaymentMethod,
            'total_inflow' => $register->total_inflow,
            'total_outflow' => $register->total_outflow,
            'expected_balance' => $register->calculateExpectedBalance(),
        ];
    }
}
