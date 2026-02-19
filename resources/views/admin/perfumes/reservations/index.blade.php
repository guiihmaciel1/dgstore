<x-perfumes-admin-layout>
    <div class="p-4">
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center gap-6">
                <h1 class="text-xl font-bold text-gray-900">Encomendas</h1>
                <div class="flex items-center gap-3 text-sm">
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full font-medium">{{ $stats['active'] }} Ativas</span>
                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full font-medium">{{ $stats['completed'] }} Concluídas</span>
                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full font-medium">{{ $stats['cancelled'] }} Canceladas</span>
                    <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full font-medium">{{ $stats['expired'] }} Expiradas</span>
                </div>
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
            <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filtros Compactos -->
        <div class="mb-4 bg-white rounded-lg shadow-sm p-3">
            <form method="GET" class="flex gap-3">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Buscar cliente..."
                       class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                
                <select name="status" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                    <option value="">Todos os status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativas</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Concluídas</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Canceladas</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expiradas</option>
                </select>
                
                <button type="submit" class="px-4 py-2 text-sm bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition">
                    Filtrar
                </button>
                
                @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                    <a href="{{ route('admin.perfumes.reservations.index') }}"
                       class="px-4 py-2 text-sm bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                        Limpar
                    </a>
                @endif
            </form>
        </div>

        <!-- Lista Compacta -->
        <div class="space-y-3">
            @forelse($reservations as $reservation)
                <div class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-1.5">
                                <h3 class="text-base font-semibold text-gray-900">{{ $reservation->reservation_number }}</h3>
                                <span class="px-2 py-0.5 text-[10px] font-medium rounded-full
                                    @if($reservation->status->value === 'active') bg-blue-100 text-blue-800
                                    @elseif($reservation->status->value === 'completed') bg-green-100 text-green-800
                                    @elseif($reservation->status->value === 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $reservation->status->label() }}
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-4 text-xs">
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
                            
                            <div class="mt-2 flex items-center gap-3 text-xs">
                                <span class="text-gray-600">Sinal: R$ {{ number_format($reservation->deposit_paid, 2, ',', '.') }} / R$ {{ number_format($reservation->deposit_amount, 2, ',', '.') }}</span>
                                <div class="flex-1 bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $reservation->progress_percentage }}%"></div>
                                </div>
                                <span class="font-medium text-gray-900">{{ $reservation->progress_percentage }}%</span>
                            </div>
                        </div>
                        
                        <div class="ml-4">
                            <a href="{{ route('admin.perfumes.reservations.show', $reservation) }}"
                               class="px-3 py-1.5 bg-pink-600 text-white text-xs font-medium rounded-lg hover:bg-pink-700 transition">
                                Ver
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
        <div class="mt-4">
            {{ $reservations->links() }}
        </div>
    </div>
</x-perfumes-admin-layout>
