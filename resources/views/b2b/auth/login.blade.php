<x-b2b-guest-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Bem-vindo de volta</h1>
        <p class="text-gray-500 mt-1 text-sm">Acesse sua conta para fazer pedidos no atacado</p>
    </x-slot>

    @if(session('status'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl flex items-center gap-2">
            <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <p class="text-sm text-green-800">{{ session('status') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl flex items-center gap-2">
            <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('b2b.login.submit') }}" x-data>
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                       class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm"
                       placeholder="seu@email.com" />
            </div>
            @error('email') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="mt-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Senha</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm"
                       placeholder="Sua senha" />
            </div>
        </div>

        <div class="flex items-center justify-between mt-4">
            <label for="remember" class="inline-flex items-center cursor-pointer">
                <input id="remember" type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-600">Lembrar-me</span>
            </label>
        </div>

        <button type="submit"
                class="w-full mt-6 py-3.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all active:scale-[0.98] text-sm">
            Entrar na minha conta
        </button>

        <!-- Acesso rápido -->
        <div class="mt-5 pt-5 border-t border-gray-200">
            <button type="button"
                    onclick="document.getElementById('email').value='lojista@teste.com'; document.getElementById('password').value='password'; document.getElementById('email').focus();"
                    class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Preencher com dados demo
            </button>
        </div>

        <p class="mt-5 text-center text-sm text-gray-500">
            Não tem conta?
            <a href="{{ route('b2b.register') }}" class="font-semibold text-blue-600 hover:text-blue-800 transition">Cadastre-se</a>
        </p>
    </form>
</x-b2b-guest-layout>
