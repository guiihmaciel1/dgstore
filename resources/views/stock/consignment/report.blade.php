<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8" x-data="consignmentReport()">
            <div class="flex items-center mb-6">
                <a href="{{ route('stock.consignment.index') }}" class="mr-3 p-2 text-gray-500 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Relatório WhatsApp - Consignado</h1>
                    <p class="text-sm text-gray-500">Gere relatórios copiáveis para enviar ao fornecedor</p>
                </div>
            </div>

            {{-- Filtros --}}
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1.5rem;">
                <form method="GET" action="{{ route('stock.consignment.report') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: end;">
                    <div style="flex: 1; min-width: 200px;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Fornecedor <span style="color: #dc2626;">*</span></label>
                        <select name="supplier_id" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                            <option value="">Selecione</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ ($selectedSupplier?->id ?? '') === $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="min-width: 140px;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">De</label>
                        <input type="date" name="date_from" value="{{ $dateFrom }}" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                    <div style="min-width: 140px;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Até</label>
                        <input type="date" name="date_to" value="{{ $dateTo }}" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                    <button type="submit" style="padding: 0.5rem 1rem; background: #111827; color: white; border: none; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; cursor: pointer;">
                        Gerar
                    </button>
                </form>
            </div>

            @if($selectedSupplier)
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    {{-- Relatório Disponível --}}
                    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden;">
                        <div style="padding: 1rem; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                            <h2 style="font-size: 1rem; font-weight: 700; color: #111827;">Estoque Disponível</h2>
                            <button type="button" @click="copyAvailable()"
                                    :style="copiedAvailable ? 'padding: 0.375rem 0.75rem; background: #059669; color: white; border: none; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600;'
                                        : 'padding: 0.375rem 0.75rem; background: #16a34a; color: white; border: none; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;'">
                                <span x-text="copiedAvailable ? 'Copiado!' : 'Copiar'"></span>
                            </button>
                        </div>
                        <div style="padding: 1rem; font-family: monospace; font-size: 0.8rem; white-space: pre-wrap; background: #f9fafb; max-height: 500px; overflow-y: auto; line-height: 1.6; color: #374151;">{{ $availableText = "📦 *ESTOQUE CONSIGNADO - DISPONÍVEL*\n━━━━━━━━━━━━━━━━━━━\nFornecedor: {$selectedSupplier->name}\nData: " . now()->format('d/m/Y') . "\n" }}@if($available->isEmpty()){{ "\nNenhum item disponível." }}@else @php $totalAvail = 0; $totalQty = 0; @endphp @foreach($available as $ai){{ "\n▸ {$ai->full_name} ({$ai->available_quantity}/{$ai->quantity})" }}@if($ai->imei){{ "\n  IMEI: {$ai->imei}" }}@endif{{ " | Custo: R$ " . number_format($ai->supplier_cost, 2, ',', '.') }}@php $totalAvail += $ai->supplier_cost * $ai->available_quantity; $totalQty += $ai->available_quantity; @endphp @endforeach{{ "\n\nTotal: {$totalQty} aparelho(s) | R$ " . number_format($totalAvail, 2, ',', '.') }}@endif</div>
                    </div>

                    {{-- Relatório Vendidos --}}
                    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden;">
                        <div style="padding: 1rem; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                            <h2 style="font-size: 1rem; font-weight: 700; color: #111827;">Vendidos</h2>
                            <button type="button" @click="copySold()"
                                    :style="copiedSold ? 'padding: 0.375rem 0.75rem; background: #059669; color: white; border: none; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600;'
                                        : 'padding: 0.375rem 0.75rem; background: #16a34a; color: white; border: none; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;'">
                                <span x-text="copiedSold ? 'Copiado!' : 'Copiar'"></span>
                            </button>
                        </div>
                        <div style="padding: 1rem; font-family: monospace; font-size: 0.8rem; white-space: pre-wrap; background: #f9fafb; max-height: 500px; overflow-y: auto; line-height: 1.6; color: #374151;">{{ $soldText = "📊 *ESTOQUE CONSIGNADO - VENDIDOS*\n━━━━━━━━━━━━━━━━━━━\nFornecedor: {$selectedSupplier->name}\nPeríodo: " . \Carbon\Carbon::parse($dateFrom)->format('d/m') . " a " . \Carbon\Carbon::parse($dateTo)->format('d/m/Y') . "\n" }}@if($sold->isEmpty()){{ "\nNenhuma venda no período." }}@else @php $totalSold = 0; $totalSoldQty = 0; @endphp @foreach($sold as $si)@php $item = $si->consignmentItem; @endphp{{ "\n▸ {$item->full_name}" }}@if($item->imei){{ "\n  IMEI: {$item->imei}" }}@endif{{ " | Custo: R$ " . number_format($item->supplier_cost, 2, ',', '.') }}{{ " | Qtd: {$si->quantity}" }}{{ "\n  Vendido em: " . $si->created_at->format('d/m/Y') }}@php $totalSold += $item->supplier_cost * $si->quantity; $totalSoldQty += $si->quantity; @endphp @endforeach{{ "\n\nTotal: {$totalSoldQty} aparelho(s) | R$ " . number_format($totalSold, 2, ',', '.') }}@endif</div>
                    </div>
                </div>
            @else
                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 3rem; text-align: center; color: #9ca3af;">
                    Selecione um fornecedor para gerar o relatório.
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
    function consignmentReport() {
        return {
            copiedAvailable: false,
            copiedSold: false,

            copyAvailable() {
                const el = document.querySelectorAll('[style*="font-family: monospace"]')[0];
                this.copyToClipboard(el?.innerText || '');
                this.copiedAvailable = true;
                setTimeout(() => { this.copiedAvailable = false; }, 2500);
            },

            copySold() {
                const el = document.querySelectorAll('[style*="font-family: monospace"]')[1];
                this.copyToClipboard(el?.innerText || '');
                this.copiedSold = true;
                setTimeout(() => { this.copiedSold = false; }, 2500);
            },

            copyToClipboard(text) {
                navigator.clipboard.writeText(text).catch(() => {
                    const ta = document.createElement('textarea');
                    ta.value = text;
                    ta.style.position = 'fixed';
                    ta.style.opacity = '0';
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                });
            }
        };
    }
    </script>
    @endpush
</x-app-layout>
