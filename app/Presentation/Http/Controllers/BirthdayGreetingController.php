<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Customer\Models\BirthdayGreeting;
use App\Domain\Customer\Models\Customer;
use App\Domain\Customer\Services\BirthdayGreetingService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class BirthdayGreetingController extends Controller
{
    /**
     * "Parabenizar": manda a mensagem de aniversário pela UazAPI e marca como enviada.
     * Se o cliente já recebeu neste ano, não envia de novo.
     */
    public function store(Customer $customer, BirthdayGreetingService $service): RedirectResponse
    {
        if (! $service->whatsappNumber($customer)) {
            return back()->with('error', "{$customer->name} não tem um celular válido cadastrado.");
        }

        $alreadySent = BirthdayGreeting::where('customer_id', $customer->id)
            ->where('year', now()->year)
            ->where('status', BirthdayGreeting::STATUS_SENT)
            ->exists();

        if ($alreadySent) {
            return back()->with('success', "{$customer->name} já recebeu a mensagem de aniversário este ano.");
        }

        $greeting = $service->send($customer, auth()->user());

        if (! $greeting) {
            return back()->with('error', 'Outro envio para este cliente está em andamento. Tente de novo em instantes.');
        }

        return $greeting->isSent()
            ? back()->with('success', "Mensagem de aniversário enviada para {$customer->name}.")
            : back()->with('error', "Não foi possível enviar para {$customer->name}: {$greeting->error}");
    }
}
