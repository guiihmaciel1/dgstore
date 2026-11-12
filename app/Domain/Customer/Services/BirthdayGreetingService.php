<?php

declare(strict_types=1);

namespace App\Domain\Customer\Services;

use App\Domain\Customer\Models\BirthdayGreeting;
use App\Domain\Customer\Models\Customer;
use App\Domain\User\Models\User;
use App\Domain\WhatsApp\Services\UazapiService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Mensagem de aniversário para clientes, enviada pelo WhatsApp (UazAPI) quando
 * alguém clica em "Parabenizar". Cada cliente recebe no máximo uma por ano.
 */
class BirthdayGreetingService
{
    public function __construct(private readonly UazapiService $uazapi) {}

    public function message(Customer $customer): string
    {
        $firstName = $this->firstName((string) $customer->name);
        $hello = $firstName !== '' ? "Oi, {$firstName}!" : 'Oi!';

        return "{$hello} Tudo bem? 😊\n\n"
            . "Passando aqui para te desejar um feliz aniversário! 🎉🥳\n\n"
            . "Nós da DG Store desejamos que seu novo ciclo seja cheio de saúde, felicidade e muitas conquistas!\n\n"
            . "Aproveitamos também para agradecer pela confiança em nossa loja. É muito bom ter você como cliente! ❤️\n\n"
            . "Curta bastante seu dia e conte sempre com a gente!\n\n"
            . "Um grande abraço de toda a equipe DG Store! 🎂";
    }

    /**
     * Número no formato internacional (55 + DDD + número) ou null se inválido.
     */
    public function whatsappNumber(Customer $customer): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $customer->phone);

        if (strlen($digits) === 10 || strlen($digits) === 11) {
            $digits = '55' . $digits;
        }

        return preg_match('/^55\d{10,11}$/', $digits) ? $digits : null;
    }

    /**
     * Mensagens deste ano, indexadas por customer_id.
     *
     * @param  array<int, string>  $customerIds
     * @return Collection<string, BirthdayGreeting>
     */
    public function greetingsThisYear(array $customerIds): Collection
    {
        if (empty($customerIds)) {
            return collect();
        }

        return BirthdayGreeting::with('sender:id,name')
            ->whereIn('customer_id', $customerIds)
            ->where('year', now()->year)
            ->get()
            ->keyBy('customer_id');
    }

    /**
     * Envia a mensagem para o cliente, a menos que ele já tenha recebido neste ano.
     * Retorna o registro do envio (enviado ou com falha) ou null se não havia o que fazer.
     */
    public function send(Customer $customer, ?User $user = null): ?BirthdayGreeting
    {
        $number = $this->whatsappNumber($customer);
        if (! $number) {
            return null;
        }

        $year = now()->year;
        $lock = Cache::lock("birthday-greeting:{$customer->id}:{$year}", 60);

        if (! $lock->get()) {
            return null;
        }

        try {
            $existing = BirthdayGreeting::where('customer_id', $customer->id)->where('year', $year)->first();
            if ($existing?->isSent()) {
                return $existing;
            }

            $result = $this->uazapi->sendText($number, $this->message($customer));
            $failed = isset($result['error']) || empty($result);

            $greeting = BirthdayGreeting::updateOrCreate(
                ['customer_id' => $customer->id, 'year' => $year],
                [
                    'phone' => $number,
                    'status' => $failed ? BirthdayGreeting::STATUS_FAILED : BirthdayGreeting::STATUS_SENT,
                    'sent_by' => $user?->id,
                    'wa_message_id' => $failed ? null : ($result['key']['id'] ?? $result['messageid'] ?? null),
                    'error' => $failed ? mb_substr((string) ($result['error'] ?? 'Resposta vazia da UazAPI'), 0, 1000) : null,
                    'sent_at' => $failed ? null : now(),
                ]
            );

            Log::info('Aniversário: mensagem ' . ($failed ? 'falhou' : 'enviada'), [
                'customer_id' => $customer->id,
                'user_id' => $user?->id,
                'error' => $greeting->error,
            ]);

            return $greeting;
        } finally {
            $lock->release();
        }
    }

    private function firstName(string $name): string
    {
        $first = explode(' ', trim($name))[0] ?? '';

        return $first === '' ? '' : mb_convert_case(mb_strtolower($first), MB_CASE_TITLE);
    }
}
