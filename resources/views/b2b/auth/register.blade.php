<x-b2b-guest-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Solicitar acesso</h1>
        <p class="text-gray-400 mt-1.5 text-sm">Preencha os dados da sua loja para solicitar acesso ao atacado</p>
    </x-slot>

    @if(session('error'))
        <div class="mb-5 p-3.5 bg-red-50 border border-red-200/60 rounded-xl flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            </div>
            <p class="text-sm text-red-700">{{ session('error') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('b2b.register.submit') }}">
        @csrf

        <div class="space-y-4">
            <div>
                <label for="store_name" class="apple-label">Nome da Loja</label>
                <input id="store_name" type="text" name="store_name" value="{{ old('store_name') }}" required
                       class="apple-input" placeholder="Ex: Apple Store SP" />
                @error('store_name') <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="owner_name" class="apple-label">Nome do Responsavel</label>
                <input id="owner_name" type="text" name="owner_name" value="{{ old('owner_name') }}" required
                       class="apple-input" placeholder="Joao Silva" />
                @error('owner_name') <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="document" class="apple-label">CNPJ ou CPF</label>
                    <input id="document" type="text" name="document" value="{{ old('document') }}" required
                           class="apple-input" placeholder="00.000.000/0000-00" />
                    @error('document') <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="whatsapp" class="apple-label">WhatsApp</label>
                    <input id="whatsapp" type="text" name="whatsapp" value="{{ old('whatsapp') }}" required
                           class="apple-input" placeholder="(17) 99999-9999" />
                    @error('whatsapp') <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-2">
                    <label for="city" class="apple-label">Cidade</label>
                    <input id="city" type="text" name="city" value="{{ old('city') }}" required
                           class="apple-input" placeholder="Sao Jose do Rio Preto" />
                    @error('city') <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="state" class="apple-label">UF</label>
                    <input id="state" type="text" name="state" value="{{ old('state') }}" required maxlength="2"
                           class="apple-input uppercase" placeholder="SP" />
                    @error('state') <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="email" class="apple-label">Email para contato</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                       class="apple-input" placeholder="contato@minhaloja.com" />
                @error('email') <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-5 p-3.5 bg-blue-50/80 border border-blue-100 rounded-xl">
            <p class="text-xs text-blue-600 flex items-start gap-2.5 leading-relaxed">
                <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                <span>Apos a solicitacao, entraremos em contato pelo WhatsApp informado para liberar seu acesso e enviar os dados de login.</span>
            </p>
        </div>

        <button type="submit" class="w-full mt-5 apple-btn-primary py-3.5 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
            Enviar Solicitacao
        </button>

        <p class="mt-6 text-center text-sm text-gray-400">
            Ja tem conta?
            <a href="{{ route('b2b.login') }}" class="font-semibold text-blue-500 hover:text-blue-600 transition-colors">Fazer login</a>
        </p>
    </form>
</x-b2b-guest-layout>
