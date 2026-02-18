<x-b2b-guest-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Solicitação Enviada!</h1>
    </x-slot>

    <div class="text-center py-2">
        <div class="mx-auto w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mb-5">
            <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h2 class="text-lg font-bold text-gray-900 mb-2">Cadastro recebido</h2>
        <p class="text-gray-600 text-sm leading-relaxed max-w-xs mx-auto">
            Para agilizar a aprovação, envie sua solicitação diretamente pelo WhatsApp clicando no botão abaixo.
        </p>

        @php
            $adminPhone = '5517991665442';
            $message = "Olá! Gostaria de solicitar acesso à *Distribuidora Apple B2B*.\n\n"
                . "*Dados da Loja:*\n"
                . "Loja: {$store}\n"
                . "Responsável: {$owner}\n"
                . "CNPJ/CPF: {$document}\n"
                . "Cidade: {$city}\n"
                . "WhatsApp: {$whatsapp}\n"
                . "Email: {$email}\n\n"
                . "Aguardo a liberação do meu acesso. Obrigado!";
            $waLink = "https://wa.me/{$adminPhone}?text=" . urlencode($message);
        @endphp

        <a href="{{ $waLink }}" target="_blank"
           class="mt-6 w-full py-3.5 px-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition-all active:scale-[0.98] text-sm flex items-center justify-center gap-2.5 shadow-lg shadow-green-200">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
            </svg>
            Enviar solicitação pelo WhatsApp
        </a>

        <!-- Resumo dos dados -->
        <div class="mt-6 bg-gray-50 rounded-xl p-4 text-left">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Dados enviados</p>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Loja</dt>
                    <dd class="font-medium text-gray-900">{{ $store }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Responsável</dt>
                    <dd class="text-gray-900">{{ $owner }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">CNPJ/CPF</dt>
                    <dd class="text-gray-900">{{ $document }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Cidade</dt>
                    <dd class="text-gray-900">{{ $city }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">WhatsApp</dt>
                    <dd class="text-gray-900">{{ $whatsapp }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Email</dt>
                    <dd class="text-gray-900">{{ $email }}</dd>
                </div>
            </dl>
        </div>

        <div class="mt-6 p-3 bg-amber-50 rounded-xl">
            <p class="text-xs text-amber-700 flex items-start gap-2">
                <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                <span>Seus dados de login serão enviados pelo WhatsApp após a aprovação do cadastro.</span>
            </p>
        </div>

        <a href="{{ route('b2b.login') }}" class="mt-5 inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-900 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Voltar ao login
        </a>
    </div>
</x-b2b-guest-layout>
