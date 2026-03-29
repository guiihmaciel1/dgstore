<x-b2b-guest-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Bem-vindo de volta</h1>
        <p class="text-gray-400 mt-1.5 text-sm">Acesse sua conta para fazer pedidos no atacado</p>
    </x-slot>

    @if(session('status'))
        <div class="mb-5 p-3.5 bg-green-50 border border-green-200/60 rounded-xl flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <p class="text-sm text-green-700">{{ session('status') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-5 p-3.5 bg-red-50 border border-red-200/60 rounded-xl flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            </div>
            <p class="text-sm text-red-700">{{ session('error') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('b2b.login.submit') }}">
        @csrf

        <div class="space-y-4">
            <div>
                <label for="email" class="apple-label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                       class="apple-input" placeholder="seu@email.com" />
                @error('email') <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="apple-label">Senha</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       class="apple-input" placeholder="Sua senha" />
            </div>

            <div class="flex items-center">
                <label for="remember" class="inline-flex items-center cursor-pointer">
                    <input id="remember" type="checkbox" name="remember" class="w-4 h-4 rounded-md border-gray-300 text-blue-500 focus:ring-blue-500/20 transition">
                    <span class="ml-2.5 text-sm text-gray-500">Lembrar-me</span>
                </label>
            </div>
        </div>

        <button type="submit" class="w-full mt-6 apple-btn-primary py-3.5 text-sm">
            Entrar na minha conta
        </button>

        <p class="mt-6 text-center text-sm text-gray-400">
            Nao tem conta?
            <a href="{{ route('b2b.register') }}" class="font-semibold text-blue-500 hover:text-blue-600 transition-colors">Cadastre-se</a>
        </p>
    </form>
</x-b2b-guest-layout>
