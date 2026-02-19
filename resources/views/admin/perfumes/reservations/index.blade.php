<x-perfumes-admin-layout>
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Encomendas</h1>
                <p class="text-sm text-gray-600 mt-1">Gerencie as encomendas com sinal</p>
            </div>
            <a href="{{ route('admin.perfumes.reservations.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-lg hover:bg-pink-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nova Encomenda
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <!-- Estatísticas -->
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                <div class="text-sm text-blue-600 font-medium">Ativas</div>
                <div class="text-2xl font-bold text-blue-900 mt-1">{{ $stats['active'] }}</div>
            </div>
            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                <div class="text-sm text-green-600 font-medium">Concluídas</div>
                <div class="text-2xl font-bold text-green-900 mt-1">{{ $stats['completed'] }}</div>
            </div>
            <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                <div class="text-sm text-red-600 font-medium">Canceladas</div>
                <div class="text-2xl font-bold text-red-900 mt-1">{{ $stats['cancelled'] }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="text-sm text-gray-600 font-medium">Expiradas</div>
                <div class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['expired'] }}</div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="mb-6 bg-white rounded-lg shadow p-4">
            <form method="GET" class="flex gap-4">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Buscar cliente..."
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg">
                
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Todos os status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativas</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Concluídas</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Canceladas</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expiradas</option>
                </select>
                
                <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900">
                    Filtrar
                </button>
                
                @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                    <a href="{{ route('admin.perfumes.reservations.index') }}"
                       class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                        Limpar
                    </a>
                @endif
            </form>
        </div>

        <!-- Lista -->
        <div class="space-y-4">
            @forelse($reservations as $reservation)
                <div class="bg-white rounded-lg shadow p-6 hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $reservation->reservation_number }}</h3>
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @if($reservation->status->value === 'active') bg-blue-100 text-blue-800
                                    @elseif($reservation->status->value === 'completed') bg-green-100 text-green-800
                                    @elseif($reservation->status->value === 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $reservation->status->label() }}
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Cliente:</span>
                                    <span class="font-medium text-gray-900">{{ $reservation->customer->name }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Produto:</span>
                                    <span class="font-medium text-gray-900">
                                        {{ $reservation->product?->name ?? $reservation->product_description }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Valor:</span>
                                    <span class="font-medium text-gray-900">R$ {{ number_format($reservation->product_price, 2, ',', '.') }}</span>
                                </div>
                            </div>
                            
                            <div class="mt-3 flex items-center gap-4 text-sm">
                                <span class="text-gray-600">Sinal: R$ {{ number_format($reservation->deposit_paid, 2, ',', '.') }} / R$ {{ number_format($reservation->deposit_amount, 2, ',', '.') }}</span>
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $reservation->progress_percentage }}%"></div>
                                </div>
                                <span class="font-medium text-gray-900">{{ $reservation->progress_percentage }}%</span>
                            </div>
                        </div>
                        
                        <div class="ml-4">
                            <a href="{{ route('admin.perfumes.reservations.show', $reservation) }}"
                               class="px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-lg hover:bg-pink-700">
                                Ver Detalhes
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow p-12 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="mt-2">Nenhuma encomenda encontrada</p>
                </div>
            @endforelse
        </div>

        <!-- Paginação -->
        <div class="mt-6">
            {{ $reservations->links() }}
        </div>
    </div>
</x-perfumes-admin-layout>
