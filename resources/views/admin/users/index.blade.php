<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-white">Gestão de Usuários</h2>
                <p class="text-sm text-gray-400 mt-0.5">Gerencie os acessos ao sistema</p>
            </div>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Novo Usuário
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-500/10 border border-green-500/30 rounded-xl text-green-400 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-700">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Usuário</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Role</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Criado em</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/50">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-700/30 transition">
                                <td class="px-5 py-4">
                                    <div class="font-medium text-white">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-400">{{ $user->email }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    @php $color = $user->role->badgeColor(); @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                        bg-{{ $color }}-500/10 text-{{ $color }}-400 border border-{{ $color }}-500/20">
                                        {{ $user->role->label() }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    @if($user->active)
                                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-green-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                                            Ativo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>
                                            Inativo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-gray-400 text-xs">
                                    {{ $user->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 hover:bg-gray-600 text-gray-300 text-xs font-medium rounded-lg transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-gray-500">
                                    Nenhum usuário encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <p class="mt-3 text-xs text-gray-500">
                Total: {{ $users->count() }} usuário(s)
            </p>
        </div>
    </div>
</x-app-layout>
