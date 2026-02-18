<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-white">Gestão de Usuários</h2>
                <p class="text-sm text-gray-400 mt-0.5">Controle de acessos e perfis do sistema</p>
            </div>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-blue-500/20 hover:shadow-blue-500/30 transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Novo Usuário
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition
                     class="flex items-center gap-3 p-4 bg-green-500/10 border border-green-500/30 rounded-xl text-green-400 text-sm">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') }}
                    <button @click="show = false" class="ml-auto text-green-600 hover:text-green-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif

            {{-- Resumo de roles --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @php
                    $counts = $users->groupBy(fn($u) => $u->role->value);
                    $roleInfo = [
                        'admin_geral'  => ['label' => 'Admin Geral',       'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color' => 'purple'],
                        'admin_b2b'    => ['label' => 'Admin B2B',          'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'color' => 'blue'],
                        'seller'       => ['label' => 'Vendedor',           'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'color' => 'green'],
                        'seller_b2b'   => ['label' => 'Vendedor B2B',       'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'indigo'],
                    ];
                @endphp
                @foreach($roleInfo as $roleValue => $info)
                    @php $count = isset($counts[$roleValue]) ? $counts[$roleValue]->count() : 0; @endphp
                    <div class="bg-gray-800/60 border border-gray-700/60 rounded-xl p-4 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-{{ $info['color'] }}-500/10 flex items-center justify-center shrink-0">
                            <svg class="w-4.5 h-4.5 text-{{ $info['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $info['icon'] }}"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-white leading-tight">{{ $count }}</div>
                            <div class="text-xs text-gray-400">{{ $info['label'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Tabela de usuários --}}
            <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-700 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-white">Todos os Usuários</h3>
                    <span class="text-xs text-gray-500">{{ $users->count() }} no total</span>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-700/60 bg-gray-800/80">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Usuário</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Perfil</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Acesso</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Cadastro</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/40">
                        @forelse($users as $user)
                            @php $color = $user->role->badgeColor(); @endphp
                            <tr class="hover:bg-gray-700/25 transition group">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-{{ $color }}-500/15 border border-{{ $color }}-500/20 flex items-center justify-center shrink-0">
                                            <span class="text-sm font-bold text-{{ $color }}-400">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-white text-sm">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                                        bg-{{ $color }}-500/10 text-{{ $color }}-400 border border-{{ $color }}-500/20">
                                        {{ $user->role->label() }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        @if($user->canAccessDGStore())
                                            <span class="inline-flex items-center gap-1 text-xs text-gray-400 bg-gray-700/60 border border-gray-600/40 rounded px-1.5 py-0.5">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                                DG Store
                                            </span>
                                        @endif
                                        @if($user->canAccessB2BAdmin())
                                            <span class="inline-flex items-center gap-1 text-xs text-blue-400 bg-blue-500/10 border border-blue-500/20 rounded px-1.5 py-0.5">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                                                B2B
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    @if($user->active)
                                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-400">
                                            <span class="relative flex h-2 w-2">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-60"></span>
                                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                                            </span>
                                            Ativo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500">
                                            <span class="w-2 h-2 rounded-full bg-gray-600"></span>
                                            Inativo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <div class="text-xs text-gray-400">{{ $user->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-600">{{ $user->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 hover:bg-blue-600 text-gray-300 hover:text-white text-xs font-medium rounded-lg transition-all duration-150 border border-gray-600/50 hover:border-blue-500">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-14 text-center">
                                    <svg class="w-10 h-10 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <p class="text-gray-500 text-sm">Nenhum usuário encontrado.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Legenda de roles --}}
            <div class="bg-gray-800/40 border border-gray-700/50 rounded-xl p-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Guia de Perfis</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs text-gray-400">
                    <div class="flex items-start gap-2">
                        <span class="w-2 h-2 rounded-full bg-purple-400 mt-0.5 shrink-0"></span>
                        <span><strong class="text-gray-300">Admin Geral:</strong> acesso total ao sistema, incluindo DG Store e Distribuidora B2B.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-400 mt-0.5 shrink-0"></span>
                        <span><strong class="text-gray-300">Admin Distribuidora:</strong> acesso exclusivo ao painel B2B.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-400 mt-0.5 shrink-0"></span>
                        <span><strong class="text-gray-300">Vendedor:</strong> acesso ao PDV e operações da DG Store.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-400 mt-0.5 shrink-0"></span>
                        <span><strong class="text-gray-300">Vendedor B2B:</strong> reservado para vendedores da distribuidora (futuro).</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
