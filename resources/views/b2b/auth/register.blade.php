<x-b2b-guest-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Solicitar acesso</h1>
        <p class="text-gray-500 mt-1 text-sm">Preencha os dados da sua loja para solicitar acesso ao atacado</p>
    </x-slot>

    @if(session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl flex items-center gap-2">
            <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('b2b.register.submit') }}">
        @csrf

        <div class="space-y-4">
            <div>
                <label for="store_name" class="block text-sm font-medium text-gray-700 mb-1.5">Nome da Loja</label>
                <input id="store_name" type="text" name="store_name" value="{{ old('store_name') }}" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm"
                       placeholder="Ex: Apple Store SP" />
                @error('store_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="owner_name" class="block text-sm font-medium text-gray-700 mb-1.5">Nome do Responsável</label>
                <input id="owner_name" type="text" name="owner_name" value="{{ old('owner_name') }}" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm"
                       placeholder="João Silva" />
                @error('owner_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="document" class="block text-sm font-medium text-gray-700 mb-1.5">CNPJ ou CPF</label>
                    <input id="document" type="text" name="document" value="{{ old('document') }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm"
                           placeholder="00.000.000/0000-00" />
                    @error('document') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-1.5">WhatsApp</label>
                    <input id="whatsapp" type="text" name="whatsapp" value="{{ old('whatsapp') }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm"
                           placeholder="(17) 99999-9999" />
                    @error('whatsapp') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1.5">Cidade</label>
                    <input id="city" type="text" name="city" value="{{ old('city') }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm"
                           placeholder="São José do Rio Preto" />
                    @error('city') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="state" class="block text-sm font-medium text-gray-700 mb-1.5">UF</label>
                    <input id="state" type="text" name="state" value="{{ old('state') }}" required maxlength="2"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm uppercase"
                           placeholder="SP" />
                    @error('state') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email para contato</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm"
                       placeholder="contato@minhaloja.com" />
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-5 p-3 bg-blue-50 rounded-xl">
            <p class="text-xs text-blue-700 flex items-start gap-2">
                <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Após a solicitação, entraremos em contato pelo WhatsApp informado para liberar seu acesso e enviar os dados de login.</span>
            </p>
        </div>

        <button type="submit"
                class="w-full mt-5 py-3.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all active:scale-[0.98] text-sm flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            Enviar Solicitação
        </button>

        <p class="mt-5 text-center text-sm text-gray-500">
            Já tem conta?
            <a href="{{ route('b2b.login') }}" class="font-semibold text-blue-600 hover:text-blue-800 transition">Fazer login</a>
        </p>
    </form>
</x-b2b-guest-layout>
