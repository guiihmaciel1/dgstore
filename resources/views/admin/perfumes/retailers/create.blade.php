<x-perfumes-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900">Novo Lojista</h2>
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.perfumes.retailers.index') }}"
           class="text-sm text-pink-600 hover:text-pink-700 font-medium inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar aos lojistas
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <form method="POST" action="{{ route('admin.perfumes.retailers.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full rounded-lg border border-gray-200 focus:border-pink-500 focus:ring-pink-500 @error('name') border-red-300 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Owner name --}}
                <div>
                    <label for="owner_name" class="block text-sm font-medium text-gray-700 mb-1">Proprietário</label>
                    <input type="text" name="owner_name" id="owner_name" value="{{ old('owner_name') }}"
                           class="w-full rounded-lg border border-gray-200 focus:border-pink-500 focus:ring-pink-500 @error('owner_name') border-red-300 @enderror">
                    @error('owner_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Document --}}
                <div>
                    <label for="document" class="block text-sm font-medium text-gray-700 mb-1">Documento (CPF/CNPJ)</label>
                    <input type="text" name="document" id="document" value="{{ old('document') }}"
                           class="w-full rounded-lg border border-gray-200 focus:border-pink-500 focus:ring-pink-500 @error('document') border-red-300 @enderror">
                    @error('document')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- WhatsApp --}}
                <div>
                    <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-1">WhatsApp *</label>
                    <input type="text" name="whatsapp" id="whatsapp" value="{{ old('whatsapp') }}" required
                           class="w-full rounded-lg border border-gray-200 focus:border-pink-500 focus:ring-pink-500 @error('whatsapp') border-red-300 @enderror">
                    @error('whatsapp')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- City --}}
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                    <input type="text" name="city" id="city" value="{{ old('city') }}"
                           class="w-full rounded-lg border border-gray-200 focus:border-pink-500 focus:ring-pink-500 @error('city') border-red-300 @enderror">
                    @error('city')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- State --}}
                <div>
                    <label for="state" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select name="state" id="state"
                            class="w-full rounded-lg border border-gray-200 focus:border-pink-500 focus:ring-pink-500 @error('state') border-red-300 @enderror">
                        <option value="">Selecione</option>
                        @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                            <option value="{{ $uf }}" {{ old('state') === $uf ? 'selected' : '' }}>{{ $uf }}</option>
                        @endforeach
                    </select>
                    @error('state')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           class="w-full rounded-lg border border-gray-200 focus:border-pink-500 focus:ring-pink-500 @error('email') border-red-300 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status"
                            class="w-full rounded-lg border border-gray-200 focus:border-pink-500 focus:ring-pink-500 @error('status') border-red-300 @enderror">
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Ativo</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inativo</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Notes --}}
                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                    <textarea name="notes" id="notes" rows="3"
                              class="w-full rounded-lg border border-gray-200 focus:border-pink-500 focus:ring-pink-500 @error('notes') border-red-300 @enderror">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-8 flex items-center gap-4">
                <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-pink-600 to-rose-500 text-white text-sm font-semibold rounded-lg hover:from-pink-500 hover:to-rose-400 shadow-md shadow-pink-500/20 transition">
                    Salvar Lojista
                </button>
                <a href="{{ route('admin.perfumes.retailers.index') }}"
                   class="text-sm text-pink-600 hover:text-pink-700 font-medium">Voltar</a>
            </div>
        </form>
    </div>
</x-perfumes-admin-layout>
