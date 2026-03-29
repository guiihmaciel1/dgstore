<x-b2b-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.b2b.retailers.index') }}" class="-ml-2 rounded-xl p-2 text-gray-400 transition-all duration-200 hover:bg-gray-100 hover:text-gray-600" aria-label="Voltar">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-semibold tracking-tight text-gray-900 sm:text-2xl">Cadastrar Lojista</h2>
                <p class="mt-0.5 text-sm text-gray-500">Preencha os dados do novo lojista</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-3xl">
        <form method="POST" action="{{ route('admin.b2b.retailers.store') }}" class="space-y-5 sm:space-y-6">
            @csrf

            <div class="apple-card p-5 transition-all duration-200 sm:p-6">
                <div class="mb-4 flex items-center gap-3 sm:mb-5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50">
                        <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </span>
                    <h3 class="apple-section-title !mb-0">Dados da loja</h3>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5">
                    <div class="sm:col-span-2">
                        <label for="store_name" class="apple-label">Nome da Loja *</label>
                        <input id="store_name" type="text" name="store_name" value="{{ old('store_name') }}" required
                               class="apple-input rounded-xl shadow-sm transition-all duration-200"
                               placeholder="Ex: Apple Store SP"/>
                        @error('store_name') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="owner_name" class="apple-label">Responsável *</label>
                        <input id="owner_name" type="text" name="owner_name" value="{{ old('owner_name') }}" required
                               class="apple-input rounded-xl shadow-sm transition-all duration-200"
                               placeholder="João Silva"/>
                        @error('owner_name') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="document" class="apple-label">CNPJ ou CPF *</label>
                        <input id="document" type="text" name="document" value="{{ old('document') }}" required
                               class="apple-input rounded-xl shadow-sm transition-all duration-200"
                               placeholder="00.000.000/0000-00"/>
                        @error('document') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="city" class="apple-label">Cidade *</label>
                        <input id="city" type="text" name="city" value="{{ old('city') }}" required
                               class="apple-input rounded-xl shadow-sm transition-all duration-200"
                               placeholder="São José do Rio Preto"/>
                        @error('city') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="state" class="apple-label">UF *</label>
                        <input id="state" type="text" name="state" value="{{ old('state') }}" required maxlength="2"
                               class="apple-input rounded-xl uppercase shadow-sm transition-all duration-200"
                               placeholder="SP"/>
                        @error('state') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="apple-card p-5 transition-all duration-200 sm:p-6">
                <div class="mb-4 flex items-center gap-3 sm:mb-5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50">
                        <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    <h3 class="apple-section-title !mb-0">Contato</h3>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5">
                    <div>
                        <label for="email" class="apple-label">Email *</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                               class="apple-input rounded-xl shadow-sm transition-all duration-200"
                               placeholder="contato@loja.com"/>
                        @error('email') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="whatsapp" class="apple-label">WhatsApp *</label>
                        <input id="whatsapp" type="text" name="whatsapp" value="{{ old('whatsapp') }}" required
                               class="apple-input rounded-xl shadow-sm transition-all duration-200"
                               placeholder="(17) 99999-9999"/>
                        @error('whatsapp') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="apple-card p-5 transition-all duration-200 sm:p-6">
                <div class="mb-4 flex items-center gap-3 sm:mb-5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50">
                        <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </span>
                    <h3 class="apple-section-title !mb-0">Acesso e status</h3>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5">
                    <div>
                        <label for="password" class="apple-label">Senha</label>
                        <input id="password" type="text" name="password" value=""
                               class="apple-input rounded-xl shadow-sm transition-all duration-200"
                               placeholder="Deixe vazio para gerar automaticamente"/>
                        <p class="mt-1.5 text-xs text-gray-500">Se vazio, uma senha aleatória será gerada.</p>
                        @error('password') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="status" class="apple-label">Status inicial *</label>
                        <select id="status" name="status" required class="apple-select rounded-xl shadow-sm transition-all duration-200">
                            <option value="approved" {{ old('status', 'approved') == 'approved' ? 'selected' : '' }}>Aprovado</option>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pendente</option>
                            <option value="blocked" {{ old('status') == 'blocked' ? 'selected' : '' }}>Bloqueado</option>
                        </select>
                        @error('status') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 pt-1 sm:flex-row sm:items-center">
                <a href="{{ route('admin.b2b.retailers.index') }}" class="apple-btn-secondary justify-center rounded-xl shadow-sm transition-all duration-200 sm:justify-start">
                    Cancelar
                </a>
                <button type="submit" class="apple-btn-primary justify-center rounded-xl shadow-sm transition-all duration-200">
                    Cadastrar Lojista
                </button>
            </div>
        </form>
    </div>
</x-b2b-admin-layout>
