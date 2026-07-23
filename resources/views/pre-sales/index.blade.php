<x-app-layout>
    <x-slot name="title">Pré-Vendas</x-slot>
    <div class="py-6">
        <div class="px-6 lg:px-8">
            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); border-radius: 0.5rem; color: #6ee7b7;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-radius: 0.5rem; color: #fca5a5;">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Cabeçalho -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-dg-100">Pré-Vendas</h1>
                    <p class="text-sm text-dg-500">Propostas registradas pelas vendedoras</p>
                </div>
                <a href="{{ route('pre-sales.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-3 bg-surface text-white font-semibold rounded-lg hover:bg-surface-elevated transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Nova Pré-Venda</span>
                </a>
            </div>

            <!-- Card Principal -->
            <div style="background: #141414; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06); overflow: hidden;">

                <!-- Filtros -->
                <div class="p-4 border-b border-border bg-surface">
                    <form method="GET" action="{{ route('pre-sales.index') }}">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
                            <div>
                                <label class="block text-xs font-medium text-dg-500 mb-1">Buscar</label>
                                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nº, IMEI, cliente..."
                                       class="w-full px-3 py-2 border border-border-strong rounded-lg text-sm focus:border-gray-900 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-dg-500 mb-1">Status</label>
                                <select name="status" class="w-full px-3 py-2 border border-border-strong rounded-lg text-sm bg-surface-raised focus:border-gray-900 focus:outline-none">
                                    <option value="">Todos</option>
                                    @foreach(\App\Domain\PreSale\Enums\PreSaleStatus::cases() as $status)
                                        <option value="{{ $status->value }}" {{ ($filters['status'] ?? '') === $status->value ? 'selected' : '' }}>
                                            {{ $status->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-dg-500 mb-1">Vendedora</label>
                                <select name="seller_id" class="w-full px-3 py-2 border border-border-strong rounded-lg text-sm bg-surface-raised focus:border-gray-900 focus:outline-none">
                                    <option value="">Todas</option>
                                    @foreach($sellers as $seller)
                                        <option value="{{ $seller->id }}" {{ ($filters['seller_id'] ?? '') == $seller->id ? 'selected' : '' }}>
                                            {{ $seller->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit"
                                        class="flex-1 px-4 py-2 bg-surface-overlay text-white text-sm font-medium rounded-lg hover:bg-surface-elevated transition-colors">
                                    Filtrar
                                </button>
                                <a href="{{ route('pre-sales.index') }}"
                                   class="px-4 py-2 text-dg-400 text-sm font-medium rounded-lg border border-border hover:bg-surface-overlay transition-colors">
                                    Limpar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Contadores -->
                <div class="p-4 bg-surface border-b border-border">
                    <div class="flex flex-wrap gap-3">
                        @php
                            $counts = [
                                'pending' => $preSales->where('status', \App\Domain\PreSale\Enums\PreSaleStatus::Pending)->count(),
                                'ready' => $preSales->where('status', \App\Domain\PreSale\Enums\PreSaleStatus::Ready)->count(),
                                'converted' => $preSales->where('status', \App\Domain\PreSale\Enums\PreSaleStatus::Converted)->count(),
                                'cancelled' => $preSales->where('status', \App\Domain\PreSale\Enums\PreSaleStatus::Cancelled)->count(),
                            ];
                        @endphp
                        <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: rgba(202,138,4,0.12); color: #fbbf24;">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #fbbf24;"></span>
                            {{ $counts['pending'] }} pendente{{ $counts['pending'] !== 1 ? 's' : '' }}
                        </span>
                        @if($counts['ready'] > 0)
                        <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: rgba(59,130,246,0.12); color: #60a5fa;">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #60a5fa;"></span>
                            {{ $counts['ready'] }} pronta{{ $counts['ready'] !== 1 ? 's' : '' }}
                        </span>
                        @endif
                        <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: rgba(22,163,106,0.12); color: #4ade80;">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #4ade80;"></span>
                            {{ $counts['converted'] }} efetivada{{ $counts['converted'] !== 1 ? 's' : '' }}
                        </span>
                        <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: rgba(220,38,38,0.12); color: #f87171;">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #f87171;"></span>
                            {{ $counts['cancelled'] }} cancelada{{ $counts['cancelled'] !== 1 ? 's' : '' }}
                        </span>
                    </div>
                </div>

                <!-- Tabela Desktop -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr style="background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.06);">
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181;">Pré-Venda</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181;">Cliente</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181;">Produto</th>
                                <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181;">Valor</th>
                                <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181;">Sinal</th>
                                <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181;">Saldo</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181;">Status</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181;">Data</th>
                                <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($preSales as $preSale)
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);" class="hover:bg-surface-overlay transition-colors">
                                    <td style="padding: 0.75rem 1rem;">
                                        <div style="font-size: 0.75rem; font-weight: 600; color: #818181;">{{ $preSale->pre_sale_number }}</div>
                                        <div style="font-size: 0.6875rem; color: #515151;">{{ $preSale->seller_name }}</div>
                                    </td>
                                    <td style="padding: 0.75rem 1rem;">
                                        <div style="font-size: 0.875rem; color: #a4a4a4;">{{ $preSale->customer?->name ?? 'N/A' }}</div>
                                        <div style="font-size: 0.6875rem; color: #666;">{{ $preSale->customer?->formatted_phone ?? '' }}</div>
                                    </td>
                                    <td style="padding: 0.75rem 1rem;">
                                        <div style="font-size: 0.8125rem; color: #a4a4a4; max-width: 14rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $preSale->product_name }}">
                                            {{ $preSale->product_name }}
                                        </div>
                                        <div style="font-size: 0.6875rem; color: #515151;">IMEI: {{ $preSale->product_imei }}</div>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: right; font-size: 0.8125rem; color: #a4a4a4;">
                                        {{ $preSale->formatted_unit_price }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: right; font-size: 0.8125rem; color: #60a5fa;">
                                        {{ $preSale->formatted_down_payment }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: right; font-size: 0.8125rem; font-weight: 600; color: #a4a4a4;">
                                        {{ $preSale->formatted_final_balance }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center;">
                                        <span style="display: inline-block; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 600; {{ $preSale->status->color() }}">
                                            {{ $preSale->status->label() }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center; font-size: 0.8125rem; color: #818181;">
                                        {{ $preSale->created_at->format('d/m/Y') }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: center;">
                                        @php $isOwner = auth()->id() === $preSale->seller_id; @endphp
                                        <div style="display: flex; align-items: center; justify-content: center; gap: 0.375rem;">
                                            @if(!auth()->user()->isAdmin() && !$isOwner && $preSale->isPending())
                                                <form method="POST" action="{{ route('pre-sales.mark-ready', $preSale) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" title="Marcar como pronta"
                                                            style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 0.375rem; color: #60a5fa; background: rgba(59,130,246,0.1); border: none; cursor: pointer;"
                                                            class="hover:bg-surface-elevated transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('pre-sales.show', $preSale) }}"
                                               style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 0.375rem; color: #818181; background: rgba(255,255,255,0.04);"
                                               class="hover:bg-surface-elevated transition-colors" title="Ver detalhes">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" style="padding: 3rem 1rem; text-align: center; color: #515151; font-size: 0.875rem;">
                                        Nenhuma pré-venda encontrada.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Cards Mobile -->
                <div class="md:hidden">
                    @forelse($preSales as $preSale)
                        <div class="border-b border-border hover:bg-surface-overlay transition-colors">
                            <a href="{{ route('pre-sales.show', $preSale) }}" class="block" style="padding: 1rem; {{ !auth()->user()->isAdmin() && $preSale->isPending() ? 'padding-bottom: 0.5rem;' : '' }}">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <div style="font-size: 0.75rem; font-weight: 600; color: #818181;">{{ $preSale->pre_sale_number }}</div>
                                        <div style="font-size: 0.875rem; color: #a4a4a4; margin-top: 0.125rem;">{{ $preSale->customer?->name ?? 'N/A' }}</div>
                                    </div>
                                    <span style="display: inline-block; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.65rem; font-weight: 600; {{ $preSale->status->color() }}">
                                        {{ $preSale->status->label() }}
                                    </span>
                                </div>
                                <div style="font-size: 0.8125rem; color: #a4a4a4; margin-bottom: 0.5rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $preSale->product_name }}
                                </div>
                                <div class="flex justify-between items-center">
                                    <div style="font-size: 0.75rem; color: #515151;">
                                        Sinal: <span style="color: #60a5fa;">{{ $preSale->formatted_down_payment }}</span>
                                    </div>
                                    <div style="font-size: 0.875rem; font-weight: 600; color: #a4a4a4;">
                                        {{ $preSale->formatted_unit_price }}
                                    </div>
                                </div>
                                <div style="font-size: 0.6875rem; color: #515151; margin-top: 0.375rem;">
                                    {{ $preSale->seller_name }} · {{ $preSale->created_at->format('d/m/Y H:i') }}
                                </div>
                            </a>
                            @php $isOwnerMobile = auth()->id() === $preSale->seller_id; @endphp
                            @if(!auth()->user()->isAdmin() && !$isOwnerMobile && $preSale->isPending())
                                <div style="padding: 0 1rem 0.75rem;">
                                    <form method="POST" action="{{ route('pre-sales.mark-ready', $preSale) }}">
                                        @csrf
                                        <button type="submit"
                                                style="width: 100%; padding: 0.5rem; font-size: 0.75rem; font-weight: 600; border-radius: 0.375rem; background: rgba(59,130,246,0.1); color: #60a5fa; border: 1px solid rgba(59,130,246,0.2); cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.375rem;">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Concluída — Pronta p/ Lançar
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div style="padding: 3rem 1rem; text-align: center; color: #515151; font-size: 0.875rem;">
                            Nenhuma pré-venda encontrada.
                        </div>
                    @endforelse
                </div>

                <!-- Paginação -->
                @if($preSales->hasPages())
                    <div class="p-4 border-t border-border">
                        {{ $preSales->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
