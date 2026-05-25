@extends('layouts.supplier')

@section('title', 'Relatórios')

@section('content')
<div class="mb-5">
    <h1 class="s-title">Relatórios</h1>
    <p class="s-subtitle">Acompanhe estoque e vendas</p>
</div>

{{-- Filters --}}
<div class="s-card s-card-pad mb-4">
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div>
            <label class="s-label">Data Inicial</label>
            <input type="date" name="from" value="{{ $from }}" class="s-input py-2.5">
        </div>
        <div>
            <label class="s-label">Data Final</label>
            <input type="date" name="to" value="{{ $to }}" class="s-input py-2.5">
        </div>
        <div class="flex items-end">
            <button type="submit" class="s-btn s-btn-primary w-full">Filtrar</button>
        </div>
    </form>
</div>

{{-- Summary stats --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
    <div class="s-stat">
        <div class="flex items-center justify-between">
            <p class="s-stat-label">Estoque Disponível</p>
            <span class="text-lg font-bold" style="color: var(--apple-green);">{{ $available->count() }}</span>
        </div>
        <p class="s-stat-value text-xl sm:text-2xl mt-2">R$ {{ number_format($availableTotal, 0, ',', '.') }}</p>
        <p class="s-stat-meta">valor total</p>
    </div>
    <div class="s-stat">
        <div class="flex items-center justify-between">
            <p class="s-stat-label">Vendidos no Período</p>
            <span class="text-lg font-bold" style="color: var(--apple-blue);">{{ $sold->count() }}</span>
        </div>
        <p class="s-stat-value text-xl sm:text-2xl mt-2">R$ {{ number_format($soldTotal, 0, ',', '.') }}</p>
        <p class="s-stat-meta">repasse total</p>
    </div>
</div>

{{-- Available stock --}}
<div class="s-card mb-4">
    <div class="s-card-pad border-b" style="border-color: var(--apple-separator);">
        <h2 class="text-base font-semibold" style="letter-spacing: -0.01em;">Estoque Disponível</h2>
    </div>

    @if($available->isEmpty())
        <div class="s-card-pad text-center py-8" style="color: var(--apple-text-secondary);">Nenhum item disponível</div>
    @else
        <div class="lg:hidden">
            @foreach($available as $item)
            <div class="s-item-card">
                <div class="flex justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold truncate">{{ $item->name }}</p>
                        <p class="text-xs mt-0.5" style="color: var(--apple-text-secondary);">{{ collect([$item->storage, $item->color])->filter()->join(' · ') }}</p>
                        <p class="text-xs font-mono mt-1" style="color: var(--apple-text-tertiary);">{{ $item->imei ?? $item->serial_number ?? '—' }}</p>
                    </div>
                    <p class="text-sm font-bold shrink-0">R$ {{ number_format($item->supplier_cost, 0, ',', '.') }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr style="border-bottom: 0.5px solid var(--apple-separator);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase" style="color: var(--apple-text-secondary);">Produto</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase" style="color: var(--apple-text-secondary);">IMEI/Serial</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase" style="color: var(--apple-text-secondary);">Custo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($available as $item)
                    <tr style="border-bottom: 0.5px solid var(--apple-separator);">
                        <td class="px-5 py-3">
                            <p class="text-sm font-medium">{{ $item->name }}</p>
                            <p class="text-xs" style="color: var(--apple-text-secondary);">{{ collect([$item->storage, $item->color])->filter()->join(' · ') }}</p>
                        </td>
                        <td class="px-5 py-3 text-sm font-mono" style="color: var(--apple-text-secondary);">{{ $item->imei ?? $item->serial_number ?? '—' }}</td>
                        <td class="px-5 py-3 text-right text-sm font-semibold">R$ {{ number_format($item->supplier_cost, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- Sold --}}
<div class="s-card mb-4">
    <div class="s-card-pad border-b" style="border-color: var(--apple-separator);">
        <h2 class="text-base font-semibold" style="letter-spacing: -0.01em;">Vendidos no Período</h2>
    </div>

    @if($sold->isEmpty())
        <div class="s-card-pad text-center py-8" style="color: var(--apple-text-secondary);">Nenhuma venda no período</div>
    @else
        <div class="lg:hidden">
            @foreach($sold as $item)
            <div class="s-item-card">
                <div class="flex justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold truncate">{{ $item->name }}</p>
                        <p class="text-xs mt-0.5" style="color: var(--apple-text-secondary);">{{ collect([$item->storage, $item->color])->filter()->join(' · ') }}</p>
                        <p class="text-xs mt-1" style="color: var(--apple-text-tertiary);">{{ $item->sold_at->format('d/m/Y') }}</p>
                    </div>
                    <p class="text-sm font-bold shrink-0" style="color: var(--apple-green);">R$ {{ number_format($item->supplier_cost, 0, ',', '.') }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr style="border-bottom: 0.5px solid var(--apple-separator);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase" style="color: var(--apple-text-secondary);">Produto</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase" style="color: var(--apple-text-secondary);">IMEI/Serial</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase" style="color: var(--apple-text-secondary);">Data</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase" style="color: var(--apple-text-secondary);">Repasse</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sold as $item)
                    <tr style="border-bottom: 0.5px solid var(--apple-separator);">
                        <td class="px-5 py-3">
                            <p class="text-sm font-medium">{{ $item->name }}</p>
                            <p class="text-xs" style="color: var(--apple-text-secondary);">{{ collect([$item->storage, $item->color])->filter()->join(' · ') }}</p>
                        </td>
                        <td class="px-5 py-3 text-sm font-mono" style="color: var(--apple-text-secondary);">{{ $item->imei ?? $item->serial_number ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm" style="color: var(--apple-text-secondary);">{{ $item->sold_at->format('d/m/Y') }}</td>
                        <td class="px-5 py-3 text-right text-sm font-semibold" style="color: var(--apple-green);">R$ {{ number_format($item->supplier_cost, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- WhatsApp --}}
<div class="s-card s-card-pad">
    <h2 class="text-base font-semibold mb-1" style="letter-spacing: -0.01em;">Relatório WhatsApp</h2>
    <p class="text-xs mb-4" style="color: var(--apple-text-secondary);">Copie e envie para a DG Store</p>

    <div class="relative">
        <textarea id="whatsappReport" readonly rows="10"
                  class="w-full s-input text-xs font-mono resize-none pr-24">{{ $whatsappReport }}</textarea>
        <button onclick="copyReport()"
                class="absolute top-2 right-2 s-btn s-btn-primary text-xs py-2 px-3">
            Copiar
        </button>
    </div>
    <p id="copyFeedback" class="hidden text-xs font-medium mt-2" style="color: var(--apple-green);">✓ Copiado!</p>
</div>

<script>
function copyReport() {
    const textarea = document.getElementById('whatsappReport');
    const feedback = document.getElementById('copyFeedback');
    navigator.clipboard.writeText(textarea.value).then(() => {
        feedback.classList.remove('hidden');
        setTimeout(() => feedback.classList.add('hidden'), 2500);
    });
}
</script>
@endsection
