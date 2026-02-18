<x-b2b-guest-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Criar conta de lojista</h1>
        <p class="text-gray-500 mt-1 text-sm">Preencha os dados para solicitar acesso ao atacado</p>
    </x-slot>

    <form method="POST" action="{{ route('b2b.register.submit') }}">
        @csrf

        <div class="space-y-4">
            <!-- Dados da Loja -->
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Dados da Loja</p>

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
                           placeholder="(11) 99999-9999" />
                    @error('whatsapp') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1.5">Cidade</label>
                    <input id="city" type="text" name="city" value="{{ old('city') }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm"
                           placeholder="São Paulo" />
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

            <!-- Dados de Acesso -->
            <div class="pt-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Dados de Acesso</p>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm"
                       placeholder="contato@minhaloja.com" />
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Senha</label>
                    <input id="password" type="password" name="password" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm"
                           placeholder="Mín. 6 caracteres" />
                    @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirmar</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm"
                           placeholder="Repita a senha" />
                </div>
            </div>
        </div>

        <button type="submit"
                class="w-full mt-6 py-3.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all active:scale-[0.98] text-sm">
            Solicitar Cadastro
        </button>

        <p class="mt-5 text-center text-sm text-gray-500">
            Já tem conta?
            <a href="{{ route('b2b.login') }}" class="font-semibold text-blue-600 hover:text-blue-800 transition">Fazer login</a>
        </p>
    </form>
</x-b2b-guest-layout>
