<x-perfumes-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900">Configurações</h2>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <form action="{{ route('admin.perfumes.settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-5">
                    <div>
                        <label for="store_name" class="block text-sm font-medium text-gray-700">Nome da Loja</label>
                        <input type="text" name="store_name" id="store_name"
                               value="{{ old('store_name', $settings['store_name']) }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-pink-500 focus:border-pink-500 text-sm">
                        @error('store_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="whatsapp_admin" class="block text-sm font-medium text-gray-700">WhatsApp Admin</label>
                        <input type="text" name="whatsapp_admin" id="whatsapp_admin"
                               value="{{ old('whatsapp_admin', $settings['whatsapp_admin']) }}"
                               placeholder="17991665442"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-pink-500 focus:border-pink-500 text-sm">
                        <p class="mt-1 text-xs text-gray-400">Número sem formatação (DDD + número)</p>
                        @error('whatsapp_admin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="pix_key" class="block text-sm font-medium text-gray-700">Chave PIX</label>
                        <input type="text" name="pix_key" id="pix_key"
                               value="{{ old('pix_key', $settings['pix_key']) }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-pink-500 focus:border-pink-500 text-sm">
                        @error('pix_key')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-pink-600 to-rose-500 text-white text-sm font-semibold rounded-lg hover:from-pink-500 hover:to-rose-400 shadow-md shadow-pink-500/20 transition">
                        Salvar Configurações
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-perfumes-admin-layout>
