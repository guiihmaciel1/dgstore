<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}"
               class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-700 hover:bg-gray-600 text-gray-400 hover:text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex items-center gap-3">
                @php $color = $user->role->badgeColor(); @endphp
                <div class="w-10 h-10 rounded-full bg-{{ $color }}-500/15 border border-{{ $color }}-500/30 flex items-center justify-center">
                    <span class="text-base font-bold text-{{ $color }}-400">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-white">{{ $user->name }}</h2>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-500/10 text-{{ $color }}-400 border border-{{ $color }}-500/20">
                            {{ $user->role->label() }}
                        </span>
                        @if($user->active)
                            <span class="inline-flex items-center gap-1 text-xs text-emerald-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                Ativo
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>
                                Inativo
                            </span>
                        @endif
                        <span class="text-xs text-gray-600">•</span>
                        <span class="text-xs text-gray-500">Membro desde {{ $user->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition
                     class="mb-4 flex items-center gap-3 p-4 bg-green-500/10 border border-green-500/30 rounded-xl text-green-400 text-sm">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="space-y-4">

                    {{-- Card: Dados pessoais --}}
                    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-gray-700/60 bg-gray-800/80">
                            <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Dados Pessoais
                            </h3>
                        </div>
                        <div class="p-5 space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Nome completo</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                       class="w-full bg-gray-900/50 border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-600' }} text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                @error('name')
                                    <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">E-mail</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                       class="w-full bg-gray-900/50 border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-600' }} text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                @error('email')
                                    <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Card: Perfil de acesso --}}
                    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-gray-700/60 bg-gray-800/80">
                            <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                Perfil de Acesso
                            </h3>
                        </div>
                        <div class="p-5 space-y-3">
                            @foreach($roles as $role)
                                @php
                                    $colors = ['admin_geral' => 'purple', 'admin_b2b' => 'blue', 'seller' => 'green', 'seller_b2b' => 'indigo'];
                                    $descriptions = [
                                        'admin_geral' => 'Acesso total: DG Store e Distribuidora B2B.',
                                        'admin_b2b'   => 'Acesso exclusivo ao painel da Distribuidora B2B.',
                                        'seller'      => 'Acesso ao PDV e operações da DG Store.',
                                        'seller_b2b'  => 'Reservado para vendedores B2B (futuro).',
                                    ];
                                    $c = $colors[$role->value] ?? 'gray';
                                    $desc = $descriptions[$role->value] ?? '';
                                    $isSelected = old('role', $user->role->value) === $role->value;
                                @endphp
                                <label class="relative flex items-start gap-3 p-3.5 rounded-xl border cursor-pointer transition-all
                                    {{ $isSelected ? 'border-'.$c.'-500/60 bg-'.$c.'-500/10' : 'border-gray-700/60 hover:border-gray-600 bg-gray-900/30 hover:bg-gray-700/20' }}">
                                    <input type="radio" name="role" value="{{ $role->value }}"
                                           {{ $isSelected ? 'checked' : '' }} required
                                           class="mt-0.5 text-blue-600 bg-gray-700 border-gray-600 focus:ring-blue-500">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-semibold text-white">{{ $role->label() }}</span>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-{{ $c }}-500/10 text-{{ $c }}-400">
                                                {{ $role->value }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $desc }}</p>
                                    </div>
                                </label>
                            @endforeach
                            @error('role')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Card: Nova senha --}}
                    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-gray-700/60 bg-gray-800/80">
                            <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Senha
                                <span class="text-xs font-normal text-gray-500 ml-1">— deixe em branco para manter a atual</span>
                            </h3>
                        </div>
                        <div class="p-5 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Nova senha</label>
                                <input type="password" name="password" minlength="8"
                                       class="w-full bg-gray-900/50 border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-600' }} text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-600"
                                       placeholder="Mínimo 8 caracteres">
                                @error('password')
                                    <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Confirmar senha</label>
                                <input type="password" name="password_confirmation"
                                       class="w-full bg-gray-900/50 border border-gray-600 text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-600"
                                       placeholder="Repita a senha">
                            </div>
                        </div>
                    </div>

                    {{-- Card: Status --}}
                    <div class="bg-gray-800 rounded-xl border border-gray-700 px-5 py-4 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-white">Conta ativa</p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                @if(auth()->id() === $user->id)
                                    Você não pode desativar sua própria conta.
                                @else
                                    Usuários inativos não conseguem fazer login.
                                @endif
                            </p>
                        </div>
                        <div class="flex items-center">
                            <input type="hidden" name="active" value="0">
                            <label class="relative inline-flex items-center cursor-pointer {{ auth()->id() === $user->id ? 'opacity-60 pointer-events-none' : '' }}">
                                <input type="checkbox" name="active" value="1" id="active"
                                       {{ $user->active ? 'checked' : '' }}
                                       {{ auth()->id() === $user->id ? 'disabled' : '' }}
                                       class="sr-only peer">
                                <div class="w-10 h-5 bg-gray-700 peer-checked:bg-blue-600 rounded-full peer transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
                            </label>
                        </div>
                    </div>

                </div>

                <div class="mt-5 flex justify-end gap-3">
                    <a href="{{ route('admin.users.index') }}"
                       class="px-4 py-2.5 text-sm font-medium text-gray-400 hover:text-white bg-gray-700 hover:bg-gray-600 rounded-xl transition border border-gray-600/50">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-500 rounded-xl transition shadow-lg shadow-blue-500/20">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
