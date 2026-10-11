<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Services;

use App\Domain\Customer\Models\Customer;
use App\Domain\Customer\Models\CustomerTag;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LabelSyncService
{
    private UazapiService $uazapi;

    public function __construct(UazapiService $uazapi)
    {
        $this->uazapi = $uazapi;
    }

    /**
     * Sincroniza tags do CRM para etiquetas do WhatsApp para um cliente.
     */
    public function syncToWhatsApp(Customer $customer): void
    {
        if (!$this->uazapi->isConfigured() || !$customer->phone) {
            return;
        }

        $labels = $this->uazapi->getLabels();
        if (isset($labels['error'])) {
            Log::warning('LabelSync: erro ao buscar labels', ['error' => $labels['error']]);
            return;
        }

        $waLabels = collect($labels)->keyBy(fn ($l) => Str::slug($l['name'] ?? ''));
        $crmTags = $customer->tags;

        foreach ($crmTags as $tag) {
            $matchingLabel = $waLabels->get($tag->slug);
            if ($matchingLabel) {
                try {
                    $this->uazapi->setLabel($customer->phone, $matchingLabel['id']);
                    Log::debug('LabelSync: etiqueta aplicada', [
                        'customer_id' => $customer->id,
                        'label' => $matchingLabel['name'],
                    ]);
                } catch (\Throwable $e) {
                    Log::warning('LabelSync: falha ao aplicar etiqueta', [
                        'customer_id' => $customer->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * Importa etiquetas do WhatsApp como tags no CRM.
     */
    public function importFromWhatsApp(): array
    {
        if (!$this->uazapi->isConfigured()) {
            return ['error' => 'UazAPI não configurada'];
        }

        $labels = $this->uazapi->getLabels();
        if (isset($labels['error'])) {
            return $labels;
        }

        $imported = [];
        foreach ($labels as $label) {
            $name = $label['name'] ?? null;
            if (!$name) {
                continue;
            }

            $tag = CustomerTag::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'color' => $label['color'] ?? '#6b7280',
                    'description' => 'Importada do WhatsApp',
                    'active' => true,
                ]
            );

            $imported[] = $tag->name;
        }

        Log::info('LabelSync: etiquetas importadas do WhatsApp', ['tags' => $imported]);

        return ['imported' => $imported];
    }
}
