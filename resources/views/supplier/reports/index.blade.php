@extends('layouts.supplier')

@section('title', 'Inventário')

@section('content')
<div class="mb-5">
    <h1 class="s-title">Inventário</h1>
    <p class="s-subtitle">Lista física do seu estoque disponível</p>
</div>

<div class="s-stat mb-4">
    <p class="s-stat-label">Aparelhos disponíveis</p>
    <p class="s-stat-value">{{ $available->count() }}</p>
    <p class="s-stat-meta">unidades em estoque</p>
</div>

<div class="s-card mb-4">
    <div class="s-card-pad border-b" style="border-color: var(--apple-separator);">
        <h2 class="text-base font-semibold" style="letter-spacing: -0.01em;">Estoque Disponível</h2>
    </div>

    @if($available->isEmpty())
        <div class="s-card-pad text-center py-10" style="color: var(--apple-text-secondary);">
            Nenhum aparelho disponível
        </div>
    @else
        <div class="lg:hidden">
            @foreach($available as $item)
            <a href="{{ route('supplier.stock.show', $item) }}" class="s-item-card block">
                <p class="text-sm font-semibold">{{ $item->name }}</p>
                <p class="text-xs mt-0.5" style="color: var(--apple-text-secondary);">
                    {{ collect([$item->storage, $item->color, $item->condition->label()])->filter()->join(' · ') }}
                </p>
                <p class="text-xs font-mono mt-1" style="color: var(--apple-text-tertiary);">
                    {{ $item->imei ?? $item->serial_number ?? '—' }}
                </p>
            </a>
            @endforeach
        </div>

        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr style="border-bottom: 0.5px solid var(--apple-separator);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase" style="color: var(--apple-text-secondary);">Produto</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase" style="color: var(--apple-text-secondary);">Detalhes</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase" style="color: var(--apple-text-secondary);">IMEI/Serial</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase" style="color: var(--apple-text-secondary);">Condição</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($available as $item)
                    <tr style="border-bottom: 0.5px solid var(--apple-separator);">
                        <td class="px-5 py-3 text-sm font-medium">{{ $item->name }}</td>
                        <td class="px-5 py-3 text-sm" style="color: var(--apple-text-secondary);">
                            {{ collect([$item->storage, $item->color])->filter()->join(' · ') ?: '—' }}
                        </td>
                        <td class="px-5 py-3 text-sm font-mono" style="color: var(--apple-text-secondary);">
                            {{ $item->imei ?? $item->serial_number ?? '—' }}
                        </td>
                        <td class="px-5 py-3">
                            <span class="s-badge {{ $item->condition->value === 'new' ? 's-badge-green' : 's-badge-yellow' }}">
                                {{ $item->condition->label() }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div class="s-card s-card-pad">
    <h2 class="text-base font-semibold mb-1" style="letter-spacing: -0.01em;">Compartilhar Inventário</h2>
    <p class="text-xs mb-4" style="color: var(--apple-text-secondary);">Copie a lista para enviar quando necessário</p>

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
