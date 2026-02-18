<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-semibold text-white">Novo Usuário</h2>
                <p class="text-sm text-gray-400 mt-0.5">Preencha os dados para criar o acesso</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 space-y-5">

                    <!-- Nome -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Nome completo</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-500"
                               placeholder="Ex: João Silva">
                        @error('name')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">E-mail</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-500"
                               placeholder="usuario@empresa.com.br">
                        @error('email')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Perfil de acesso</label>
                        <select name="role" required
                                class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Selecione um perfil...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->value }}" {{ old('role') === $role->value ? 'selected' : '' }}>
                                    {{ $role->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-xs text-gray-500">
                            <strong class="text-gray-400">Admin Geral:</strong> acesso total.
                            <strong class="text-gray-400">Admin Distribuidora:</strong> apenas módulo B2B.
                            <strong class="text-gray-400">Vendedor:</strong> acesso ao PDV DG Store.
                        </p>
                    </div>

                    <!-- Senha -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Senha</label>
                            <input type="password" name="password" required minlength="8"
                                   class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-500"
                                   placeholder="Mínimo 8 caracteres">
                            @error('password')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Confirmar senha</label>
                            <input type="password" name="password_confirmation" required
                                   class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-500"
                                   placeholder="Repita a senha">
                        </div>
                    </div>

                    <!-- Ativo -->
                    <div class="flex items-center gap-3">
                        <input type="hidden" name="active" value="0">
                        <input type="checkbox" name="active" value="1" id="active" checked
                               class="w-4 h-4 rounded border-gray-600 bg-gray-700 text-blue-600 focus:ring-blue-500">
                        <label for="active" class="text-sm text-gray-300">Usuário ativo</label>
                    </div>
                </div>

                <div class="mt-5 flex justify-end gap-3">
                    <a href="{{ route('admin.users.index') }}"
                       class="px-4 py-2.5 text-sm font-medium text-gray-300 hover:text-white bg-gray-700 hover:bg-gray-600 rounded-lg transition">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                        Criar Usuário
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
