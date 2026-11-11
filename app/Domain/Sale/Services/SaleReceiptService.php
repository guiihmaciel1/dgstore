<?php

declare(strict_types=1);

namespace App\Domain\Sale\Services;

use App\Domain\Sale\Models\Sale;
use App\Domain\WhatsApp\Services\UazapiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SaleReceiptService
{
    public function __construct(
        private readonly UazapiService $uazapi,
    ) {}

    /**
     * PDF do recibo (o mesmo do botão NF).
     */
    public function pdf(Sale $sale): DomPdf
    {
        $sale->loadMissing(['items.product', 'customer', 'user', 'seller', 'tradeIns']);

        // Sem subsetting a fonte DejaVu inteira é embutida (~860 KB por PDF).
        return Pdf::loadView('sales.signed-receipt', ['sale' => $sale])
            ->setPaper('a4', 'portrait')
            ->setOption('isFontSubsettingEnabled', true);
    }

    public function filename(Sale $sale): string
    {
        return "recibo-{$sale->sale_number}.pdf";
    }

    /**
     * Envia o PDF do recibo ao cliente via WhatsApp e registra o envio.
     *
     * @throws RuntimeException
     */
    public function sendWhatsApp(Sale $sale, ?string $userId): Sale
    {
        $lock = Cache::lock("sale-receipt-wa:{$sale->id}", 60);
        if (!$lock->get()) {
            throw new RuntimeException('Envio do recibo já está em andamento.');
        }

        try {
            $sale->refresh();
            $this->ensureCanSend($sale);

            $result = $this->uazapi->sendDocument(
                $sale->customer->phone,
                base64_encode($this->pdf($sale)->output()),
                $this->filename($sale),
                $this->caption($sale),
            );

            if (isset($result['error'])) {
                Log::warning("Falha ao enviar recibo da venda #{$sale->sale_number}", ['result' => $result]);
                throw new RuntimeException('Não foi possível enviar o recibo pelo WhatsApp: ' . $result['error']);
            }

            $sale->update([
                'receipt_sent_at' => now(),
                'receipt_sent_by' => $userId,
            ]);

            return $sale;
        } finally {
            $lock->release();
        }
    }

    private function ensureCanSend(Sale $sale): void
    {
        if ($sale->receipt_sent_at) {
            throw new RuntimeException('Recibo já enviado em ' . $sale->receipt_sent_at->format('d/m/Y H:i') . '.');
        }
        if ($sale->isCancelled()) {
            throw new RuntimeException('Venda cancelada não pode enviar recibo.');
        }
        if (!$sale->customer?->phone) {
            throw new RuntimeException('Cliente sem telefone cadastrado.');
        }
        if (!$this->uazapi->isConfigured()) {
            throw new RuntimeException('WhatsApp não configurado.');
        }
    }

    private function caption(Sale $sale): string
    {
        $firstName = strtok(trim($sale->customer->name), ' ') ?: $sale->customer->name;

        return "Olá {$firstName}! Segue o recibo da sua compra #{$sale->sale_number} na DG Store.\n\nObrigado pela preferência!";
    }
}
