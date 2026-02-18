<x-b2b-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800">Configurações B2B</h2>
        <p class="text-sm text-gray-500 mt-0.5">Gerencie as configurações da distribuidora</p>
    </x-slot>

    <form method="POST" action="{{ route('admin.b2b.settings.update') }}" class="max-w-3xl space-y-6">
        @csrf
        @method('PUT')

        {{-- Dados da empresa --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Dados da Empresa
                </h3>
            </div>
            <div class="p-6 space-y-5">
                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">Nome da Empresa</label>
                    <input type="text" id="company_name" name="company_name"
                           value="{{ old('company_name', $settings['company_name']) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Distribuidora Apple B2B" required />
                    <p class="mt-1 text-xs text-gray-500">Nome exibido para os lojistas no sistema B2B.</p>
                    @error('company_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="admin_whatsapp" class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Admin</label>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500 bg-gray-100 px-3 py-2.5 rounded-lg border border-gray-300">+</span>
                        <input type="text" id="admin_whatsapp" name="admin_whatsapp"
                               value="{{ old('admin_whatsapp', $settings['admin_whatsapp']) }}"
                               class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="5517991665442" required />
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Número usado para notificações de pedidos e cadastros (formato: DDI + DDD + Número).</p>
                    @error('admin_whatsapp') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Pagamento --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Pagamento
                </h3>
            </div>
            <div class="p-6 space-y-5">
                <div>
                    <label for="pix_key" class="block text-sm font-medium text-gray-700 mb-1">Chave PIX</label>
                    <input type="text" id="pix_key" name="pix_key"
                           value="{{ old('pix_key', $settings['pix_key']) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="email@empresa.com, CPF, CNPJ ou chave aleatória" />
                    <p class="mt-1 text-xs text-gray-500">Chave PIX exibida para os lojistas na tela de pagamento.</p>
                    @error('pix_key') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="minimum_order_amount" class="block text-sm font-medium text-gray-700 mb-1">Pedido Mínimo (R$)</label>
                    <input type="number" id="minimum_order_amount" name="minimum_order_amount"
                           value="{{ old('minimum_order_amount', $settings['minimum_order_amount']) }}"
                           step="0.01" min="0" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="5000.00" />
                    <p class="mt-1 text-xs text-gray-500">Valor mínimo que o lojista precisa atingir para finalizar um pedido.</p>
                    @error('minimum_order_amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Estoque --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Estoque
                </h3>
            </div>
            <div class="p-6 space-y-5">
                <div>
                    <label for="low_stock_threshold" class="block text-sm font-medium text-gray-700 mb-1">Limite de Estoque Baixo</label>
                    <input type="number" id="low_stock_threshold" name="low_stock_threshold"
                           value="{{ old('low_stock_threshold', $settings['low_stock_threshold']) }}"
                           min="0" max="100" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="5" />
                    <p class="mt-1 text-xs text-gray-500">Produtos com estoque igual ou abaixo desse valor serão sinalizados com alerta no dashboard e na listagem.</p>
                    @error('low_stock_threshold') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Botão salvar --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.b2b.dashboard') }}" class="px-5 py-2.5 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-100 transition">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
                Salvar Configurações
            </button>
        </div>
    </form>
</x-b2b-admin-layout>
