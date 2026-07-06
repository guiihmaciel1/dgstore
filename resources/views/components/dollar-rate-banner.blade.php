@php
    $dollarRate = \App\Domain\System\Models\SystemSetting::get('dollar_rate');
    $isAdmin = auth()->user()->isAdminGeral();
@endphp

@if($isAdmin)
<div x-data="dollarRateBanner()" x-cloak>
    @if(!$dollarRate)
    {{-- Banner de alerta: cotação não preenchida --}}
    <div class="bg-amber-500" style="padding: 0;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div style="display: flex; align-items: center; justify-content: center; gap: 12px; padding: 10px 0; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <svg style="width: 20px; height: 20px; color: #92400e; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <span style="font-size: 14px; font-weight: 600; color: #92400e;">Cotação do dólar não informada hoje!</span>
                </div>
                <form @submit.prevent="saveDollarRate()" style="display: flex; align-items: center; gap: 8px;">
                    <div style="position: relative;">
                        <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-size: 13px; font-weight: 600; color: #92400e;">R$</span>
                        <input type="text"
                               x-model="rateInput"
                               x-ref="rateField"
                               placeholder="5,45"
                               required
                               style="width: 100px; padding: 6px 10px 6px 32px; border: 2px solid #b45309; border-radius: 8px; font-size: 14px; font-weight: 600; color: #92400e; background: rgba(255,255,255,0.8); outline: none; text-align: right;"
                               onfocus="this.style.borderColor='#92400e'; this.style.background='white'"
                               onblur="this.style.borderColor='#b45309'; this.style.background='rgba(255,255,255,0.8)'">
                    </div>
                    <button type="submit"
                            :disabled="saving"
                            style="padding: 6px 16px; background: #92400e; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background 0.2s;"
                            onmouseover="this.style.background='#78350f'"
                            onmouseout="this.style.background='#92400e'">
                        <span x-show="!saving">Salvar</span>
                        <span x-show="saving">Salvando...</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @else
    {{-- Indicador discreto: cotação preenchida --}}
    <div style="background: #065f46; padding: 0;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div style="display: flex; align-items: center; justify-content: center; gap: 10px; padding: 6px 0; flex-wrap: wrap;">
                <svg style="width: 16px; height: 16px; color: #6ee7b7; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span style="font-size: 13px; font-weight: 500; color: #a7f3d0;">Dólar hoje:</span>
                <span style="font-size: 14px; font-weight: 700; color: #ecfdf5;">R$ {{ number_format((float) $dollarRate, 2, ',', '.') }}</span>
                <button @click="showEdit = !showEdit"
                        type="button"
                        style="background: none; border: none; cursor: pointer; padding: 2px; color: #6ee7b7; display: flex; align-items: center;"
                        title="Alterar cotação">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                </button>

                {{-- Formulário inline de edição --}}
                <form x-show="showEdit" x-transition @submit.prevent="saveDollarRate()" style="display: flex; align-items: center; gap: 6px; margin-left: 4px;">
                    <div style="position: relative;">
                        <span style="position: absolute; left: 8px; top: 50%; transform: translateY(-50%); font-size: 12px; font-weight: 600; color: #6ee7b7;">R$</span>
                        <input type="text"
                               x-model="rateInput"
                               placeholder="{{ number_format((float) $dollarRate, 2, ',', '.') }}"
                               style="width: 90px; padding: 4px 8px 4px 28px; border: 1px solid #34d399; border-radius: 6px; font-size: 13px; font-weight: 600; color: white; background: rgba(255,255,255,0.15); outline: none; text-align: right;"
                               onfocus="this.style.borderColor='#6ee7b7'; this.style.background='rgba(255,255,255,0.25)'"
                               onblur="this.style.borderColor='#34d399'; this.style.background='rgba(255,255,255,0.15)'">
                    </div>
                    <button type="submit"
                            :disabled="saving"
                            style="padding: 4px 12px; background: #34d399; color: #064e3b; border: none; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer;">
                        <span x-show="!saving">OK</span>
                        <span x-show="saving">...</span>
                    </button>
                    <button @click="showEdit = false" type="button" style="background: none; border: none; cursor: pointer; color: #6ee7b7; padding: 2px;">
                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
function dollarRateBanner() {
    return {
        rateInput: '',
        saving: false,
        showEdit: false,

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
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            })
            .catch(() => alert('Erro ao salvar cotação. Tente novamente.'))
            .finally(() => this.saving = false);
        }
    }
}
</script>
@endif
