<?php

declare(strict_types=1);

namespace App\Domain\Supplier\Services;

use App\Domain\Supplier\Models\Quotation;
use App\Domain\Supplier\Models\Supplier;
use App\Domain\System\Models\SystemSetting;
use Illuminate\Support\Facades\Log;

/**
 * Processa cotações de fornecedores PY (Paraguai) recebidas via WhatsApp.
 * Preços em USD são convertidos para BRL usando a taxa do sistema + frete.
 */
class WhatsAppPyQuotationService
{
    private const FREIGHT_PERCENT = 0.06;

    public function processIfQuotation(string $fromPhone, string $messageBody): bool
    {
        if (!$this->isPyQuotation($messageBody)) {
            return false;
        }

        $supplier = $this->findSupplierByPhone($fromPhone);
        if (!$supplier) {
            Log::info('WhatsAppPyQuotation: cotação PY ignorada - telefone sem fornecedor', [
                'phone' => $fromPhone,
            ]);
            return false;
        }

        return $this->parseAndStore($supplier, $messageBody);
    }

    /**
     * Processa cotação PY a partir de uma mensagem de grupo de um fornecedor já identificado.
     */
    public function processGroupQuotation(Supplier $supplier, string $messageBody): bool
    {
        if (!$this->isPyQuotation($messageBody)) {
            return false;
        }

        return $this->parseAndStore($supplier, $messageBody);
    }

    private function isPyQuotation(string $body): bool
    {
        $hasAppleHeader = (bool) preg_match('/🍎🍏\s*APPLE\s*🍎🍏/u', $body);
        $hasDollarPrices = (bool) preg_match('/\$\s*\d+[\d.,]*\d/u', $body);
        $hasAppleProducts = (bool) preg_match('/\b(?:IPHONE|AIRPODS?|APPLE\s*WATCH|IPAD|MACBOOK)\b/iu', $body);

        return ($hasAppleHeader && $hasDollarPrices) || ($hasDollarPrices && $hasAppleProducts);
    }

    private function findSupplierByPhone(string $phone): ?Supplier
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (strlen($digits) > 11) {
            $digits = substr($digits, 2);
        }

        return Supplier::active()
            ->where('origin', 'py')
            ->where(function ($q) use ($digits, $phone) {
                $q->where('phone', 'LIKE', "%{$digits}%")
                  ->orWhere('phone', 'LIKE', "%{$phone}%");
            })
            ->first();
    }

    private function parseAndStore(Supplier $supplier, string $messageBody): bool
    {
        $parser = new PyQuotationParser();
        $items = $parser->parse($messageBody);

        if (empty($items)) {
            Log::warning('WhatsAppPyQuotation: cotação PY detectada mas parser não extraiu itens', [
                'supplier' => $supplier->name,
            ]);
            return false;
        }

        $dollarRate = (float) SystemSetting::get('dollar_rate', '5.38');
        $today = today();
        $savedCount = 0;
        $adminUserId = \App\Domain\User\Models\User::where('active', true)
            ->where('role', 'admin_geral')
            ->value('id');

        foreach ($items as $item) {
            $brlPrice = $item['price_usd'] * $dollarRate * (1 + self::FREIGHT_PERCENT);

            Quotation::updateOrCreate(
                [
                    'supplier_id' => $supplier->id,
                    'product_name' => $item['product_name'],
                    'quoted_at' => $today,
                ],
                [
                    'unit_price' => round($brlPrice, 2),
                    'price_usd' => $item['price_usd'],
                    'exchange_rate' => $dollarRate,
                    'quantity' => 1,
                    'unit' => 'un',
                    'category' => $item['category'] ?: null,
                    'notes' => 'Importado via WhatsApp PY',
                    'user_id' => $adminUserId,
                ]
            );

            $savedCount++;
        }

        SystemSetting::set('supplier_quote_last_update_' . $supplier->id, now()->toISOString());
        SystemSetting::set('supplier_quote_last_supplier', $supplier->name);

        Log::info('WhatsAppPyQuotation: cotações PY importadas', [
            'supplier' => $supplier->name,
            'dollar_rate' => $dollarRate,
            'freight' => self::FREIGHT_PERCENT * 100 . '%',
            'total_parsed' => count($items),
            'saved' => $savedCount,
        ]);

        return true;
    }
}
