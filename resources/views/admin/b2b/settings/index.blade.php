<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900">Configurações B2B</h2>
    </x-slot>

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('admin.b2b.settings.update') }}">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pedido Mínimo (R$)</label>
                    <input type="number" name="minimum_order_amount" value="{{ old('minimum_order_amount', $minimumOrderAmount) }}"
                           step="0.01" min="0" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="5000.00" />
                    <p class="mt-1 text-xs text-gray-500">Valor mínimo que o lojista precisa atingir para finalizar um pedido.</p>
                    @error('minimum_order_amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-200">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Salvar Configurações
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
