<?php

declare(strict_types=1);

namespace App\Domain\Supplier\Services;

use App\Domain\Supplier\Models\Quotation;
use App\Domain\Supplier\Models\Supplier;
use App\Domain\System\Models\SystemSetting;
use Illuminate\Support\Facades\Log;

/**
 * Processa mensagens de cotação recebidas via WhatsApp de fornecedores cadastrados.
 * Detecta automaticamente mensagens com "REPASSE" e alimenta o sistema de cotações.
 */
class WhatsAppQuotationService
{
    /**
     * Verifica se a mensagem é uma cotação de fornecedor e processa.
     * Retorna true se processou, false se não era cotação.
     */
    public function processIfQuotation(string $fromPhone, string $messageBody): bool
    {
        if (!$this->isQuotationMessage($messageBody)) {
            return false;
        }

        $supplier = $this->findSupplierByPhone($fromPhone);
        if (!$supplier) {
            Log::info('WhatsAppQuotation: mensagem REPASSE ignorada - telefone sem fornecedor', [
                'phone' => $fromPhone,
            ]);
            return false;
        }

        return $this->parseAndStore($supplier, $messageBody);
    }

    /**
     * Verifica se a mensagem contém palavra-chave de cotação.
     */
    private function isQuotationMessage(string $body): bool
    {
        return (bool) preg_match('/\bREPASSE\b/iu', $body);
    }

    /**
     * Busca fornecedor pelo telefone (normalizado).
     */
    private function findSupplierByPhone(string $phone): ?Supplier
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (strlen($digits) > 11) {
            $digits = substr($digits, 2);
        }

        return Supplier::active()
            ->where(function ($q) use ($digits, $phone) {
                $q->where('phone', 'LIKE', "%{$digits}%")
                  ->orWhere('phone', 'LIKE', "%{$phone}%");
            })
            ->first();
    }

    /**
     * Parseia a mensagem e salva as cotações.
     */
    private function parseAndStore(Supplier $supplier, string $messageBody): bool
    {
        $parser = new BrQuotationParser();
        $items = $parser->parse($messageBody);

        if (empty($items)) {
            Log::warning('WhatsAppQuotation: REPASSE detectado mas parser não extraiu itens', [
                'supplier' => $supplier->name,
            ]);
            return false;
        }

        $today = today();
        $savedCount = 0;

        foreach ($items as $item) {
            if (!$item['available'] || $item['unit_price'] <= 0) {
                continue;
            }

            Quotation::updateOrCreate(
                [
                    'supplier_id' => $supplier->id,
                    'product_name' => $item['product_name'],
                    'quoted_at' => $today,
                ],
                [
                    'unit_price' => $item['unit_price'],
                    'quantity' => 1,
                    'unit' => 'un',
                    'category' => $item['category'] ?: null,
                    'notes' => 'Importado via WhatsApp REPASSE',
                ]
            );

            $savedCount++;
        }

        SystemSetting::set('supplier_quote_last_update_' . $supplier->id, now()->toISOString());
        SystemSetting::set('supplier_quote_last_supplier', $supplier->name);

        Log::info('WhatsAppQuotation: cotações importadas', [
            'supplier' => $supplier->name,
            'total_parsed' => count($items),
            'saved' => $savedCount,
        ]);

        return true;
    }
}
