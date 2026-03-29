<x-b2b-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-xl sm:text-2xl font-semibold text-gray-900 tracking-tight">Configurações B2B</h2>
            <p class="text-sm text-gray-500">Gerencie as configurações do B2B</p>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto w-full px-0 sm:px-1">
        <form method="POST" action="{{ route('admin.b2b.settings.update') }}" class="space-y-5 sm:space-y-6">
            @csrf
            @method('PUT')

            {{-- Dados da empresa --}}
            <section class="apple-card p-4 sm:p-6 transition-shadow duration-200 hover:shadow-md">
                <div class="flex items-start gap-3 mb-5 sm:mb-6">
                    <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </span>
                    <div class="min-w-0 flex-1 pt-0.5">
                        <h3 class="apple-section-title !mb-0">Dados da empresa</h3>
                        <p class="text-sm text-gray-500 leading-relaxed mt-2">Nome exibido aos lojistas e número para notificações de pedidos e cadastros.</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label for="company_name" class="apple-label">Nome da empresa</label>
                        <input type="text" id="company_name" name="company_name"
                               value="{{ old('company_name', $settings['company_name']) }}"
                               class="apple-input"
                               placeholder="Apple B2B" required />
                        <p class="mt-1.5 text-xs text-gray-500">Nome exibido para os lojistas no sistema B2B.</p>
                        @error('company_name') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="admin_whatsapp" class="apple-label">WhatsApp admin</label>
                        <div class="flex items-stretch gap-2">
                            <span class="inline-flex items-center px-3 sm:px-4 rounded-xl border border-gray-200 bg-gray-50/80 text-sm text-gray-500 shrink-0" aria-hidden="true">+</span>
                            <input type="text" id="admin_whatsapp" name="admin_whatsapp"
                                   value="{{ old('admin_whatsapp', $settings['admin_whatsapp']) }}"
                                   class="apple-input flex-1 min-w-0"
                                   placeholder="5517991665442" required />
                        </div>
                        <p class="mt-1.5 text-xs text-gray-500">Número usado para notificações de pedidos e cadastros (formato: DDI + DDD + Número).</p>
                        @error('admin_whatsapp') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            {{-- Pagamento --}}
            <section class="apple-card p-4 sm:p-6 transition-shadow duration-200 hover:shadow-md">
                <div class="flex items-start gap-3 mb-5 sm:mb-6">
                    <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                    <div class="min-w-0 flex-1 pt-0.5">
                        <h3 class="apple-section-title !mb-0">Pagamento</h3>
                        <p class="text-sm text-gray-500 leading-relaxed mt-2">Chave PIX e valor mínimo para finalização de pedidos.</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label for="pix_key" class="apple-label">Chave PIX</label>
                        <input type="text" id="pix_key" name="pix_key"
                               value="{{ old('pix_key', $settings['pix_key']) }}"
                               class="apple-input"
                               placeholder="email@empresa.com, CPF, CNPJ ou chave aleatória" />
                        <p class="mt-1.5 text-xs text-gray-500">Chave PIX exibida para os lojistas na tela de pagamento.</p>
                        @error('pix_key') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="minimum_order_amount" class="apple-label">Pedido mínimo (R$)</label>
                        <input type="number" id="minimum_order_amount" name="minimum_order_amount"
                               value="{{ old('minimum_order_amount', $settings['minimum_order_amount']) }}"
                               step="0.01" min="0" required
                               class="apple-input"
                               placeholder="5000.00" />
                        <p class="mt-1.5 text-xs text-gray-500">Valor mínimo que o lojista precisa atingir para finalizar um pedido.</p>
                        @error('minimum_order_amount') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            {{-- Estoque --}}
            <section class="apple-card p-4 sm:p-6 transition-shadow duration-200 hover:shadow-md">
                <div class="flex items-start gap-3 mb-5 sm:mb-6">
                    <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </span>
                    <div class="min-w-0 flex-1 pt-0.5">
                        <h3 class="apple-section-title !mb-0">Estoque</h3>
                        <p class="text-sm text-gray-500 leading-relaxed mt-2">Defina quando produtos aparecem como estoque baixo no painel.</p>
                    </div>
                </div>

                <div>
                    <label for="low_stock_threshold" class="apple-label">Limite de estoque baixo</label>
                    <input type="number" id="low_stock_threshold" name="low_stock_threshold"
                           value="{{ old('low_stock_threshold', $settings['low_stock_threshold']) }}"
                           min="0" max="100" required
                           class="apple-input"
                           placeholder="5" />
                    <p class="mt-1.5 text-xs text-gray-500">Produtos com estoque igual ou abaixo desse valor serão sinalizados com alerta no dashboard e na listagem.</p>
                    @error('low_stock_threshold') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </section>

            <div class="flex flex-col-reverse sm:flex-row sm:items-stretch gap-3 pt-1">
                <a href="{{ route('admin.b2b.dashboard') }}" class="apple-btn-secondary w-full sm:flex-1 text-center sm:max-w-none">
                    Cancelar
                </a>
                <button type="submit" class="apple-btn-primary w-full sm:flex-1">
                    Salvar configurações
                </button>
            </div>
        </form>
    </div>
</x-b2b-admin-layout>
