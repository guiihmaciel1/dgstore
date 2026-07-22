{{-- Acesso rápido ao estoque: links + botões de adicionar --}}
<div x-data="{ quickStockOpen: false }" class="relative">
    <button @click="quickStockOpen = !quickStockOpen" 
            class="flex items-center gap-1.5 px-2 py-1 rounded-lg text-xs font-semibold transition"
            :class="quickStockOpen ? 'text-white bg-surface-overlay' : 'text-dg-300 hover:text-white hover:bg-surface-overlay'"
            title="Acesso rápido ao estoque">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
        <span class="hidden lg:inline">Estoque</span>
        <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': quickStockOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-show="quickStockOpen" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         @click.away="quickStockOpen = false"
         class="absolute right-0 mt-2 w-64 rounded-xl bg-surface-overlay border border-border z-50 shadow-xl overflow-hidden"
         x-cloak>
        
        {{-- Nosso Estoque --}}
        <div style="padding: 0.75rem; border-bottom: 1px solid rgba(255,255,255,0.06);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                <a href="{{ route('products.index') }}" 
                   style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; font-weight: 600; color: #e3e3e3; text-decoration: none;"
                   onmouseover="this.style.color='#10b981'" onmouseout="this.style.color='#e3e3e3'">
                    <svg style="width: 1rem; height: 1rem; color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Nosso Estoque
                </a>
                <button type="button" onclick="openProductEntryFromNav()"
                        style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; background: rgba(16,185,129,0.1); color: #10b981; border: 1px solid rgba(16,185,129,0.2); border-radius: 0.375rem; font-size: 0.6875rem; font-weight: 600; cursor: pointer;"
                        onmouseover="this.style.background='rgba(16,185,129,0.2)'" onmouseout="this.style.background='rgba(16,185,129,0.1)'">
                    <svg style="width: 0.75rem; height: 0.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Adicionar
                </button>
            </div>
            @php
                $ownStockCount = \App\Domain\Product\Models\Product::where('active', true)->where('category', 'smartphone')->sum('stock_quantity');
            @endphp
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-size: 0.6875rem; color: #818181;">{{ $ownStockCount }} smartphone(s) em estoque</span>
            </div>
        </div>

        {{-- Estoque Fornecedor Interno --}}
        <div style="padding: 0.75rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                <a href="{{ route('stock.consignment.index') }}" 
                   style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; font-weight: 600; color: #e3e3e3; text-decoration: none;"
                   onmouseover="this.style.color='#818cf8'" onmouseout="this.style.color='#e3e3e3'">
                    <svg style="width: 1rem; height: 1rem; color: #818cf8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    Estoque Externo
                </a>
                <button type="button" onclick="openConsignmentEntryFromNav()"
                        style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; background: rgba(129,140,248,0.1); color: #818cf8; border: 1px solid rgba(129,140,248,0.2); border-radius: 0.375rem; font-size: 0.6875rem; font-weight: 600; cursor: pointer;"
                        onmouseover="this.style.background='rgba(129,140,248,0.2)'" onmouseout="this.style.background='rgba(129,140,248,0.1)'">
                    <svg style="width: 0.75rem; height: 0.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Adicionar
                </button>
            </div>
            @php
                $consignmentCount = \App\Domain\ConsignmentStock\Models\ConsignmentStockItem::available()->sum('available_quantity');
            @endphp
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-size: 0.6875rem; color: #818181;">{{ $consignmentCount }} un. disponíve{{ $consignmentCount == 1 ? 'l' : 'is' }}</span>
            </div>
        </div>
    </div>
</div>

<script>
function closeQuickStockNav() {
    document.querySelectorAll('[x-data]').forEach(el => {
        if (el.__x && el.__x.$data.quickStockOpen !== undefined) {
            el.__x.$data.quickStockOpen = false;
        }
    });
}

function openProductEntryFromNav() {
    closeQuickStockNav();
    if (typeof openCreateModal === 'function') {
        openCreateModal();
    } else {
        window.location.href = '{{ route("products.index") }}?open_entry=1';
    }
}

function openConsignmentEntryFromNav() {
    closeQuickStockNav();
    if (typeof openConsignmentEntry === 'function') {
        openConsignmentEntry();
    } else {
        window.location.href = '{{ route("stock.consignment.index") }}?open_entry=1';
    }
}
</script>
