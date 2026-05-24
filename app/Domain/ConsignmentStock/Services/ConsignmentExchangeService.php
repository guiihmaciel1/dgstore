<?php

declare(strict_types=1);

namespace App\Domain\ConsignmentStock\Services;

use App\Domain\ConsignmentStock\Enums\ConsignmentMovementType;
use App\Domain\ConsignmentStock\Models\ConsignmentItemExchange;
use App\Domain\ConsignmentStock\Models\ConsignmentStockItem;
use App\Domain\ConsignmentStock\Models\ConsignmentStockMovement;
use App\Domain\Finance\Models\FinancialAccount;
use App\Domain\Finance\Models\FinancialCategory;
use Illuminate\Support\Facades\DB;

/**
 * Servico responsavel por trocas de aparelhos com outros lojistas.
 *
 * Cada troca:
 *  - Registra um snapshot imutavel do estado antigo + novo (ConsignmentItemExchange)
 *  - Atualiza o item consignado in-place (mantendo o ID e referencias historicas)
 *  - Cria um movimento do tipo "exchange"
 *  - Registra ajuste financeiro se houve volta/diferenca
 */
class ConsignmentExchangeService
{
    /**
     * @param  array{
     *     imei?: string|null,
     *     serial_number?: string|null,
     *     name: string,
     *     model?: string|null,
     *     storage?: string|null,
     *     color?: string|null,
     *     condition?: string|null
     * }  $newData
     */
    public function exchange(
        ConsignmentStockItem $item,
        array $newData,
        string $partnerName,
        float $costAdjustment,
        ?string $reason,
        string $userId,
    ): ConsignmentItemExchange {
        $this->validateNewImei($item, $newData);

        return DB::transaction(function () use ($item, $newData, $partnerName, $costAdjustment, $reason, $userId) {
            $exchange = ConsignmentItemExchange::create([
                'consignment_item_id' => $item->id,
                'user_id' => $userId,
                'old_imei' => $item->imei,
                'old_serial_number' => $item->serial_number,
                'old_name' => $item->name,
                'old_model' => $item->model,
                'old_storage' => $item->storage,
                'old_color' => $item->color,
                'old_condition' => $item->condition?->value ?? 'new',
                'new_imei' => $newData['imei'] ?? null,
                'new_serial_number' => $newData['serial_number'] ?? null,
                'new_name' => $newData['name'],
                'new_model' => $newData['model'] ?? null,
                'new_storage' => $newData['storage'] ?? null,
                'new_color' => $newData['color'] ?? null,
                'new_condition' => $newData['condition'] ?? 'new',
                'partner_name' => $partnerName,
                'cost_adjustment' => $costAdjustment,
                'reason' => $reason,
                'exchanged_at' => now(),
            ]);

            $item->update([
                'imei' => $newData['imei'] ?? null,
                'serial_number' => $newData['serial_number'] ?? null,
                'name' => $newData['name'],
                'model' => $newData['model'] ?? null,
                'storage' => $newData['storage'] ?? null,
                'color' => $newData['color'] ?? null,
                'condition' => $newData['condition'] ?? 'new',
            ]);

            ConsignmentStockMovement::create([
                'consignment_item_id' => $item->id,
                'user_id' => $userId,
                'type' => ConsignmentMovementType::Exchange,
                'quantity' => 1,
                'reason' => "Troca com {$partnerName}" . ($reason ? " - {$reason}" : ''),
                'reference_id' => $exchange->id,
            ]);

            $this->registerFinancialAdjustment($costAdjustment, $partnerName, $userId, $exchange->id);

            return $exchange;
        });
    }

    /**
     * Lanca o ajuste financeiro da troca, se houver.
     *
     * Convencao:
     *  - costAdjustment > 0  : recebemos volta do lojista (income)
     *  - costAdjustment < 0  : pagamos diferenca para o lojista (expense)
     *  - costAdjustment == 0 : nada a registrar
     */
    private function registerFinancialAdjustment(
        float $costAdjustment,
        string $partnerName,
        string $userId,
        string $exchangeId,
    ): void {
        if ($costAdjustment === 0.0) {
            return;
        }

        $isIncome = $costAdjustment > 0;
        $type = $isIncome ? 'income' : 'expense';
        $categoryName = $isIncome ? 'Outras Receitas' : 'Outras Despesas';
        $description = $isIncome
            ? "Troca de aparelho com {$partnerName} - volta recebida"
            : "Troca de aparelho com {$partnerName} - diferenca paga";

        $category = FinancialCategory::where('name', $categoryName)
            ->where('type', $type)
            ->first();

        if (!$category) {
            return;
        }

        $account = FinancialAccount::where('is_default', true)
            ->where('is_active', true)
            ->first()
            ?? FinancialAccount::where('is_active', true)->first();

        $amount = abs($costAdjustment);

        $transaction = \App\Domain\Finance\Models\FinancialTransaction::create([
            'account_id' => $account?->id,
            'category_id' => $category->id,
            'user_id' => $userId,
            'type' => $type,
            'status' => $account ? 'paid' : 'pending',
            'amount' => $amount,
            'description' => $description,
            'due_date' => now()->format('Y-m-d'),
            'paid_at' => $account ? now() : null,
            'reference_type' => 'ConsignmentExchange',
            'reference_id' => $exchangeId,
        ]);

        if ($account && $transaction->status === 'paid') {
            if ($isIncome) {
                $account->addBalance($amount);
            } else {
                $account->subtractBalance($amount);
            }
        }
    }

    /**
     * Garante que o IMEI/Serial novo nao colide com outros itens do estoque.
     */
    private function validateNewImei(ConsignmentStockItem $item, array $newData): void
    {
        $newImei = $newData['imei'] ?? null;
        $newSerial = $newData['serial_number'] ?? null;

        if (!$newImei && !$newSerial) {
            return;
        }

        $query = ConsignmentStockItem::query()->where('id', '!=', $item->id);

        $query->where(function ($q) use ($newImei, $newSerial) {
            if ($newImei) {
                $q->orWhere('imei', $newImei);
            }
            if ($newSerial) {
                $q->orWhere('serial_number', $newSerial);
            }
        });

        if ($query->exists()) {
            throw new \InvalidArgumentException('O IMEI/Serial informado ja existe em outro item do estoque.');
        }
    }
}
