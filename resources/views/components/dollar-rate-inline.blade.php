@php
    $dollarRate = \App\Domain\System\Models\SystemSetting::get('dollar_rate');
    $isAdmin = auth()->user()->isAdminGeral();
    $cpRates = \App\Domain\System\Models\DollarCpRate::lastTen();
    $cpLatest = $cpRates->first();
    $cpPrevious = $cpRates->skip(1)->first();
@endphp

@if($isAdmin)
<div x-data="dollarRateInline()" class="flex items-center gap-2">
    @if(!$dollarRate)
        {{-- Alert: no rate set --}}
        <button @click="showForm = !showForm" class="flex items-center gap-1.5 px-2 py-1 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-semibold hover:bg-amber-500/20 transition" title="Cotação não informada">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <span>US$</span>
        </button>
    @else
        {{-- Compact indicator --}}
        <div class="flex items-center gap-1.5">
            <button @click="showForm = !showForm" class="flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold text-emerald-400 hover:bg-emerald-500/10 transition" title="Dólar hoje – clique para editar">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>R$ {{ number_format((float) $dollarRate, 2, ',', '.') }}</span>
            </button>
            @if($cpLatest)
                <span class="text-[10px] text-dg-500 hidden lg:inline" title="Compras Paraguai">CP R$ {{ number_format((float) $cpLatest->rate, 2, ',', '.') }}</span>
                @if($cpPrevious)
                    @php
                        $cpDiff = (float) $cpLatest->rate - (float) $cpPrevious->rate;
                        $cpPct = (float) $cpPrevious->rate > 0 ? ($cpDiff / (float) $cpPrevious->rate) * 100 : 0;
                    @endphp
                    <span class="text-[10px] font-semibold hidden lg:inline {{ $cpDiff > 0 ? 'text-red-400' : ($cpDiff < 0 ? 'text-emerald-400' : 'text-dg-500') }}">
                        {{ ($cpDiff > 0 ? '▲' : ($cpDiff < 0 ? '▼' : '–')) }}{{ number_format(abs($cpPct), 1, ',', '.') }}%
                    </span>
                @endif
            @endif
        </div>
    @endif

    {{-- Inline edit form (dropdown) --}}
    <div x-show="showForm" x-transition @click.away="showForm = false" class="absolute top-full right-0 mt-2 p-3 bg-surface-overlay border border-border rounded-xl shadow-xl z-50 min-w-[220px]">
        <form @submit.prevent="saveDollarRate()">
            <label class="text-xs text-dg-400 mb-1 block">Cotação do dólar (DG)</label>
            <div class="flex gap-2">
                <div class="relative flex-1">
                    <span class="absolute left-2 top-1/2 -translate-y-1/2 text-xs text-dg-500 font-semibold">R$</span>
                    <input type="text" x-model="rateInput" placeholder="{{ $dollarRate ? number_format((float) $dollarRate, 2, ',', '.') : ($cpLatest ? number_format((float) $cpLatest->rate, 2, ',', '.') : '5,45') }}"
                           class="w-full pl-7 pr-2 py-1.5 bg-surface-raised border border-border rounded-lg text-sm text-dg-100 font-semibold text-right focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 outline-none">
                </div>
                <button type="submit" :disabled="saving" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-bold rounded-lg hover:bg-emerald-500 transition disabled:opacity-50">
                    <span x-show="!saving">OK</span>
                    <span x-show="saving">...</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function dollarRateInline() {
    return {
        rateInput: '',
        saving: false,
        showForm: false,

        saveDollarRate() {
            if (this.saving) return;
            let value = this.rateInput.trim();
            if (!value) return;
            value = value.replace(/\./g, '').replace(',', '.');
            const numericValue = parseFloat(value);
            if (isNaN(numericValue) || numericValue <= 0 || numericValue > 99.99) {
                alert('Informe um valor válido para a cotação (ex: 5,45)');
                return;
            }
            this.saving = true;
            fetch('{{ route("admin.dollar-rate.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ dollar_rate: numericValue }),
            })
            .then(r => r.json())
            .then(data => { if (data.success) window.location.reload(); })
            .catch(() => alert('Erro ao salvar cotação.'))
            .finally(() => this.saving = false);
        }
    }
}
</script>
@endif
