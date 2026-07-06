<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\CRM\Models\Deal;
use App\Domain\CRM\Models\PipelineStage;
use App\Domain\Customer\Models\Customer;
use App\Domain\Marketing\Models\MarketingPrice;
use App\Domain\Marketing\Models\MarketingUsedListing;
use App\Domain\Negotiation\Models\NegotiationSnapshot;
use App\Domain\Product\Models\Product;
use App\Domain\Schedule\Models\Appointment;
use Illuminate\Support\Collection;

class IdleModeSuggestionService
{
    private const MAX_PER_SOURCE = 5;

    public function getSuggestions(): array
    {
        $suggestions = collect()
            ->merge($this->birthdaysToday())
            ->merge($this->simulationsExpiring())
            ->merge($this->birthdaysTomorrow())
            ->merge($this->newLeadsWaiting())
            ->merge($this->staleStock())
            ->merge($this->marketingWithoutPhotos())
            ->merge($this->todayAppointments());

        return $this->sortByPriority($suggestions)->values()->all();
    }

    public function getCount(): int
    {
        return count($this->getSuggestions());
    }

    private function newLeadsWaiting(): Collection
    {
        $defaultStage = PipelineStage::where('is_default', true)->first();

        if (!$defaultStage) {
            return collect();
        }

        return Deal::open()
            ->where('pipeline_stage_id', $defaultStage->id)
            ->whereDoesntHave('activities', fn ($q) => $q->whereIn('type', ['note', 'whatsapp', 'call']))
            ->with('customer')
            ->orderBy('created_at', 'asc')
            ->limit(self::MAX_PER_SOURCE)
            ->get()
            ->map(fn (Deal $deal) => [
                'category' => 'crm',
                'priority' => 'medium',
                'icon' => 'user-plus',
                'title' => 'Lead novo sem interação',
                'message' => sprintf(
                    '%s entrou há %s — ninguém fez contato ainda',
                    $deal->customer?->name ?? $deal->phone ?? 'Lead',
                    $deal->created_at->diffForHumans(short: true)
                ),
                'action_label' => 'Iniciar atendimento',
                'action_url' => route('crm.show', $deal),
                'whatsapp_url' => $this->whatsappUrl($deal->customer?->phone ?? $deal->phone),
            ]);
    }

    private function birthdaysToday(): Collection
    {
        return Customer::whereNotNull('birth_date')
            ->whereNotNull('phone')
            ->whereMonth('birth_date', now()->month)
            ->whereDay('birth_date', now()->day)
            ->limit(self::MAX_PER_SOURCE)
            ->get()
            ->map(fn (Customer $c) => [
                'category' => 'customers',
                'priority' => 'high',
                'icon' => 'cake',
                'title' => 'Aniversariante HOJE',
                'message' => sprintf('%s faz aniversário hoje! Mande uma mensagem', $c->name),
                'action_label' => 'Ver perfil',
                'action_url' => route('customers.show', $c),
                'whatsapp_url' => $this->birthdayWhatsappUrl($c),
            ]);
    }

    private function birthdaysTomorrow(): Collection
    {
        $tomorrow = now()->addDay();

        return Customer::whereNotNull('birth_date')
            ->whereNotNull('phone')
            ->whereMonth('birth_date', $tomorrow->month)
            ->whereDay('birth_date', $tomorrow->day)
            ->limit(self::MAX_PER_SOURCE)
            ->get()
            ->map(fn (Customer $c) => [
                'category' => 'customers',
                'priority' => 'medium',
                'icon' => 'cake',
                'title' => 'Aniversariante amanhã',
                'message' => sprintf('%s faz aniversário amanhã — prepare-se!', $c->name),
                'action_label' => 'Ver perfil',
                'action_url' => route('customers.show', $c),
                'whatsapp_url' => null,
            ]);
    }

    private function simulationsExpiring(): Collection
    {
        return NegotiationSnapshot::active()
            ->where('expires_at', '>=', today())
            ->where('expires_at', '<=', today()->addDays(2))
            ->with('customer')
            ->orderBy('expires_at')
            ->limit(self::MAX_PER_SOURCE)
            ->get()
            ->map(fn (NegotiationSnapshot $snap) => [
                'category' => 'sales',
                'priority' => 'medium',
                'icon' => 'hourglass',
                'title' => 'Simulação expirando',
                'message' => sprintf(
                    '%s — %s expira %s',
                    $snap->customer?->name ?? 'Cliente',
                    $snap->product_description,
                    $snap->expires_at->isToday() ? 'HOJE' : 'amanhã'
                ),
                'action_label' => 'Reabrir simulador',
                'action_url' => route('tools.negotiation-simulator', [
                    'snap_product' => $snap->product_description,
                    'snap_price' => $snap->product_price,
                ]),
                'whatsapp_url' => $this->whatsappUrl($snap->customer?->phone),
            ]);
    }

    private function staleStock(): Collection
    {
        return Product::active()
            ->inStock()
            ->whereIn('condition', ['used', 'refurbished'])
            ->where('created_at', '<=', now()->subDays(30))
            ->orderBy('created_at')
            ->limit(self::MAX_PER_SOURCE)
            ->get()
            ->map(fn (Product $p) => [
                'category' => 'stock',
                'priority' => 'low',
                'icon' => 'package',
                'title' => 'Seminovo parado',
                'message' => sprintf(
                    '%s está em estoque há %d dias',
                    $p->name,
                    (int) $p->created_at->diffInDays(now())
                ),
                'action_label' => 'Ver produto',
                'action_url' => route('products.show', $p),
                'whatsapp_url' => null,
            ]);
    }

    private function marketingWithoutPhotos(): Collection
    {
        $listings = MarketingUsedListing::withCount('images')
            ->having('images_count', '=', 0)
            ->with('listable')
            ->limit(self::MAX_PER_SOURCE)
            ->get()
            ->map(fn (MarketingUsedListing $l) => [
                'category' => 'marketing',
                'priority' => 'low',
                'icon' => 'camera',
                'title' => 'Seminovo sem fotos',
                'message' => sprintf(
                    '%s não tem fotos no marketing',
                    $l->listable?->name ?? 'Produto'
                ),
                'action_label' => 'Ir ao marketing',
                'action_url' => route('marketing.index'),
                'whatsapp_url' => null,
            ]);

        $prices = MarketingPrice::active()
            ->withCount('images')
            ->having('images_count', '=', 0)
            ->limit(self::MAX_PER_SOURCE)
            ->get()
            ->map(fn (MarketingPrice $mp) => [
                'category' => 'marketing',
                'priority' => 'low',
                'icon' => 'camera',
                'title' => 'Modelo sem fotos',
                'message' => sprintf('%s %s sem fotos na tabela', $mp->name, $mp->storage ?? ''),
                'action_label' => 'Ir ao marketing',
                'action_url' => route('marketing.index'),
                'whatsapp_url' => null,
            ]);

        return $listings->merge($prices)->take(self::MAX_PER_SOURCE);
    }

    private function todayAppointments(): Collection
    {
        return Appointment::forDate(today()->format('Y-m-d'))
            ->active()
            ->where('start_time', '>=', now()->format('H:i:s'))
            ->orderBy('start_time')
            ->limit(self::MAX_PER_SOURCE)
            ->get()
            ->map(fn (Appointment $a) => [
                'category' => 'schedule',
                'priority' => 'low',
                'icon' => 'calendar',
                'title' => 'Agendamento hoje',
                'message' => sprintf(
                    '%s às %s — %s',
                    $a->customer_name,
                    substr($a->start_time, 0, 5),
                    $a->service_description ?: 'sem descrição'
                ),
                'action_label' => 'Ver agenda',
                'action_url' => route('schedule.index'),
                'whatsapp_url' => $this->whatsappUrl($a->customer_phone),
            ]);
    }

    private function sortByPriority(Collection $suggestions): Collection
    {
        $order = ['high' => 0, 'medium' => 1, 'low' => 2];

        return $suggestions->sortBy(fn ($s) => $order[$s['priority']] ?? 3);
    }

    private function whatsappUrl(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $clean = preg_replace('/\D/', '', $phone);

        if (strlen($clean) < 8) {
            return null;
        }

        if (!str_starts_with($clean, '55')) {
            $clean = '55' . $clean;
        }

        return 'https://wa.me/' . $clean;
    }

    private function birthdayWhatsappUrl(Customer $customer): ?string
    {
        if (!$customer->phone) {
            return null;
        }

        $firstName = explode(' ', trim($customer->name))[0];
        $message = "Olá {$firstName}! 🎂🎉\n\nA equipe DG Store deseja um Feliz Aniversário! Que seu dia seja repleto de alegrias. Venha nos visitar, temos uma surpresa especial para você! 🎁";

        $clean = preg_replace('/\D/', '', $customer->phone);
        if (!str_starts_with($clean, '55')) {
            $clean = '55' . $clean;
        }

        return 'https://wa.me/' . $clean . '?text=' . urlencode($message);
    }
}
