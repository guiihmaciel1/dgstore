<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Services;

use App\Domain\Customer\Models\Customer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CustomerEnrichmentService
{
    private UazapiService $uazapi;

    public function __construct(UazapiService $uazapi)
    {
        $this->uazapi = $uazapi;
    }

    /**
     * Enriquece dados do cliente via WhatsApp: avatar, business status.
     * Usa cache para evitar chamadas excessivas (1 vez a cada 24h).
     */
    public function enrich(Customer $customer): void
    {
        if (!$this->uazapi->isConfigured() || !$customer->phone) {
            return;
        }

        $shouldRefresh = !$customer->wa_avatar_cached_at
            || $customer->wa_avatar_cached_at->diffInHours(now()) > 24;

        if (!$shouldRefresh) {
            return;
        }

        $this->fetchAvatar($customer);
        $this->fetchBusinessDetails($customer);
    }

    /**
     * Busca e salva avatar do WhatsApp.
     */
    public function fetchAvatar(Customer $customer): void
    {
        try {
            $result = $this->uazapi->getChatAvatar($customer->phone);

            $avatarUrl = $result['profilePictureUrl'] ?? $result['imgUrl'] ?? $result['url'] ?? null;

            if ($avatarUrl) {
                $customer->update([
                    'wa_avatar_url' => $avatarUrl,
                    'wa_avatar_cached_at' => now(),
                ]);

                Log::debug('CustomerEnrichment: avatar atualizado', [
                    'customer_id' => $customer->id,
                ]);
            } else {
                $customer->update(['wa_avatar_cached_at' => now()]);
            }
        } catch (\Throwable $e) {
            Log::warning('CustomerEnrichment: falha ao buscar avatar', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
            ]);
            $customer->update(['wa_avatar_cached_at' => now()]);
        }
    }

    /**
     * Busca detalhes de business do WhatsApp.
     */
    public function fetchBusinessDetails(Customer $customer): void
    {
        try {
            $result = $this->uazapi->getChatDetails($customer->phone);

            $isBusiness = $result['isBusiness'] ?? $result['is_business'] ?? false;
            $businessName = $result['businessName'] ?? $result['business_name'] ?? null;

            $updateData = ['wa_is_business' => (bool) $isBusiness];
            if ($businessName) {
                $updateData['wa_business_name'] = $businessName;
            }

            $customer->update($updateData);

            Log::debug('CustomerEnrichment: business info atualizada', [
                'customer_id' => $customer->id,
                'is_business' => $isBusiness,
            ]);
        } catch (\Throwable $e) {
            Log::warning('CustomerEnrichment: falha ao buscar business details', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Processa enriquecimento de clientes que ainda não foram consultados.
     */
    public function processUnprocessed(int $limit = 30): int
    {
        $customers = Customer::whereNotNull('phone')
            ->where(function ($q) {
                $q->whereNull('wa_avatar_cached_at')
                    ->orWhere('wa_avatar_cached_at', '<', now()->subHours(24));
            })
            ->limit($limit)
            ->get();

        $processed = 0;
        foreach ($customers as $customer) {
            $this->enrich($customer);
            $processed++;
            usleep(500000);
        }

        return $processed;
    }
}
