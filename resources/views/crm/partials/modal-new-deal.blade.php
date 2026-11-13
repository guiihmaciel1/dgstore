{{-- Modal: Novo Negócio --}}
<div class="fixed inset-0 z-[50] flex items-center justify-center bg-black/40" x-show="showNewDealForm" x-transition.opacity x-cloak>
    <div class="bg-surface-raised rounded-xl p-6 w-full max-w-[36rem] shadow-[0_25px_50px_rgba(0,0,0,0.25)] max-h-[90vh] overflow-y-auto" @click.away="showNewDealForm = false">
        <h2 class="text-lg font-bold text-dg-100 mb-4">Novo Negócio</h2>
        <form method="POST" action="{{ route('crm.deals.store') }}">
            @csrf
            <div class="flex flex-col gap-3">
                <div>
                    <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Título <span class="text-red-300">*</span></label>
                    <input class="w-full p-2" type="text" name="title" required value="{{ old('title') }}" placeholder="Ex: iPhone 16 Pro Max - João"
                           style="border: 1px solid {{ $errors->has('title') ? '#fca5a5' : 'rgba(255,255,255,0.08)' }}; border-radius: 0.375rem; font-size: 0.875rem;">
                </div>

                {{-- Interesse de Produto (estruturado) --}}
                <div class="bg-surface-overlay border border-border rounded-lg p-3">
                    <label class="text-xs font-bold text-dg-300 block mb-2">Interesse de Produto</label>
                    <div class="grid grid-cols-[1fr_1fr] gap-2">
                        <div>
                            <label class="text-[0.7rem] font-semibold text-dg-400 block mb-[2px]">Modelo</label>
                            <select class="w-full p-[0.4rem] border border-border rounded-md text-[0.8rem]" name="interest_model">
                                <option value="">Selecione...</option>
                                <optgroup label="iPhone">
                                    <option {{ old('interest_model') === 'iPhone 18 Pro Max' ? 'selected' : '' }}>iPhone 18 Pro Max</option>
                                    <option {{ old('interest_model') === 'iPhone 18 Pro' ? 'selected' : '' }}>iPhone 18 Pro</option>
                                    <option {{ old('interest_model') === 'iPhone 17 Pro Max' ? 'selected' : '' }}>iPhone 17 Pro Max</option>
                                    <option {{ old('interest_model') === 'iPhone 17 Pro' ? 'selected' : '' }}>iPhone 17 Pro</option>
                                    <option {{ old('interest_model') === 'iPhone 17 Air' ? 'selected' : '' }}>iPhone 17 Air</option>
                                    <option {{ old('interest_model') === 'iPhone 17' ? 'selected' : '' }}>iPhone 17</option>
                                    <option {{ old('interest_model') === 'iPhone 16 Pro Max' ? 'selected' : '' }}>iPhone 16 Pro Max</option>
                                    <option {{ old('interest_model') === 'iPhone 16 Pro' ? 'selected' : '' }}>iPhone 16 Pro</option>
                                    <option {{ old('interest_model') === 'iPhone 16 Plus' ? 'selected' : '' }}>iPhone 16 Plus</option>
                                    <option {{ old('interest_model') === 'iPhone 16' ? 'selected' : '' }}>iPhone 16</option>
                                    <option {{ old('interest_model') === 'iPhone 15 Pro Max' ? 'selected' : '' }}>iPhone 15 Pro Max</option>
                                    <option {{ old('interest_model') === 'iPhone 15 Pro' ? 'selected' : '' }}>iPhone 15 Pro</option>
                                    <option {{ old('interest_model') === 'iPhone 15 Plus' ? 'selected' : '' }}>iPhone 15 Plus</option>
                                    <option {{ old('interest_model') === 'iPhone 15' ? 'selected' : '' }}>iPhone 15</option>
                                    <option {{ old('interest_model') === 'iPhone 14 Pro Max' ? 'selected' : '' }}>iPhone 14 Pro Max</option>
                                    <option {{ old('interest_model') === 'iPhone 14 Pro' ? 'selected' : '' }}>iPhone 14 Pro</option>
                                    <option {{ old('interest_model') === 'iPhone 14 Plus' ? 'selected' : '' }}>iPhone 14 Plus</option>
                                    <option {{ old('interest_model') === 'iPhone 14' ? 'selected' : '' }}>iPhone 14</option>
                                    <option {{ old('interest_model') === 'iPhone 13' ? 'selected' : '' }}>iPhone 13</option>
                                    <option {{ old('interest_model') === 'iPhone 12' ? 'selected' : '' }}>iPhone 12</option>
                                    <option {{ old('interest_model') === 'iPhone 11' ? 'selected' : '' }}>iPhone 11</option>
                                    <option {{ old('interest_model') === 'iPhone SE' ? 'selected' : '' }}>iPhone SE</option>
                                </optgroup>
                                <optgroup label="Outros Apple">
                                    <option {{ old('interest_model') === 'iPad' ? 'selected' : '' }}>iPad</option>
                                    <option {{ old('interest_model') === 'MacBook' ? 'selected' : '' }}>MacBook</option>
                                    <option {{ old('interest_model') === 'Apple Watch' ? 'selected' : '' }}>Apple Watch</option>
                                    <option {{ old('interest_model') === 'AirPods' ? 'selected' : '' }}>AirPods</option>
                                </optgroup>
                            </select>
                        </div>
                        <div>
                            <label class="text-[0.7rem] font-semibold text-dg-400 block mb-[2px]">Armazenamento</label>
                            <select class="w-full p-[0.4rem] border border-border rounded-md text-[0.8rem]" name="interest_storage">
                                <option value="">Qualquer</option>
                                <option {{ old('interest_storage') === '64GB' ? 'selected' : '' }}>64GB</option>
                                <option {{ old('interest_storage') === '128GB' ? 'selected' : '' }}>128GB</option>
                                <option {{ old('interest_storage') === '256GB' ? 'selected' : '' }}>256GB</option>
                                <option {{ old('interest_storage') === '512GB' ? 'selected' : '' }}>512GB</option>
                                <option {{ old('interest_storage') === '1TB' ? 'selected' : '' }}>1TB</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[0.7rem] font-semibold text-dg-400 block mb-[2px]">Cor</label>
                            <input class="w-full p-[0.4rem] border border-border rounded-md text-[0.8rem]" type="text" name="interest_color" value="{{ old('interest_color') }}" placeholder="Ex: Titânio Natural">
                        </div>
                        <div>
                            <label class="text-[0.7rem] font-semibold text-dg-400 block mb-[2px]">Condição</label>
                            <select class="w-full p-[0.4rem] border border-border rounded-md text-[0.8rem]" name="interest_condition">
                                <option value="">Qualquer</option>
                                <option value="novo" {{ old('interest_condition') === 'novo' ? 'selected' : '' }}>Novo</option>
                                <option value="seminovo" {{ old('interest_condition') === 'seminovo' ? 'selected' : '' }}>Seminovo</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-2">
                        <label class="text-[0.7rem] font-semibold text-dg-400 block mb-[2px]">Orçamento máximo (R$)</label>
                        <input class="w-full p-[0.4rem] border border-border rounded-md text-[0.8rem]" type="number" name="interest_max_budget" step="0.01" value="{{ old('interest_max_budget') }}" placeholder="0,00">
                    </div>
                </div>

                <div class="grid grid-cols-[1fr_1fr] gap-3">
                    <div>
                        <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Valor (R$)</label>
                        <input class="w-full p-2 border border-border rounded-md text-sm" type="number" name="value" step="0.01" value="{{ old('value') }}" placeholder="0,00">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Telefone / WhatsApp <span class="text-red-300">*</span></label>
                        <div class="flex gap-1.5">
                            <input class="flex-1 p-2" type="text" name="phone" id="new-deal-phone" required value="{{ old('phone') }}" placeholder="(00) 00000-0000"
                                   maxlength="15"
                                   @input="$el.value = $el.value.replace(/\D/g,'').replace(/^(\d{2})(\d)/,'($1) $2').replace(/(\d{5})(\d)/,'$1-$2').substring(0,15)"
                                   style="border: 1px solid {{ $errors->has('phone') ? '#fca5a5' : 'rgba(255,255,255,0.08)' }}; border-radius: 0.375rem; font-size: 0.875rem;">
                            <button class="py-2 px-2.5 border border-[#333] rounded-md bg-surface-overlay cursor-pointer flex items-center" type="button" onclick="checkWaNumber()" id="check-wa-btn" title="Verificar WhatsApp">
                                <svg class="w-[16px] h-[16px] text-[#25d366]" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="text-[0.7rem] mt-[0.2rem]" id="wa-check-result" style="display: none;"></div>
                        @error('phone')
                            <p class="text-[0.7rem] text-red-300 mt-1 mx-0 mb-0">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-[1fr_1fr] gap-3">
                    <div>
                        <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Previsão de Fechamento</label>
                        <input class="w-full p-2 border border-border rounded-md text-sm" type="date" name="expected_close_date" value="{{ old('expected_close_date') }}">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Etapa</label>
                        <select class="w-full p-2 border border-border rounded-md text-sm" name="pipeline_stage_id">
                            @foreach($activeStages as $stage)
                                <option value="{{ $stage->id }}" {{ $stage->is_default ? 'selected' : '' }}>{{ $stage->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Origem e Temperatura --}}
                <div class="grid grid-cols-[1fr_1fr] gap-3">
                    <div>
                        <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Origem do Lead</label>
                        <select class="w-full p-2 border border-border rounded-md text-sm" name="lead_source">
                            <option value="">Não informada</option>
                            @foreach(\App\Domain\CRM\Enums\LeadSource::cases() as $src)
                                <option value="{{ $src->value }}" {{ old('lead_source') === $src->value ? 'selected' : '' }}>{{ $src->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Temperatura</label>
                        <select class="w-full p-2 border border-border rounded-md text-sm" name="temperature">
                            <option value="hot" {{ old('temperature') === 'hot' ? 'selected' : '' }}>Quente</option>
                            <option value="warm" {{ old('temperature', 'warm') === 'warm' ? 'selected' : '' }}>Morno</option>
                            <option value="cold" {{ old('temperature') === 'cold' ? 'selected' : '' }}>Frio</option>
                        </select>
                    </div>
                </div>

                {{-- Próximo Passo --}}
                <div class="grid grid-cols-[1fr_1fr] gap-3">
                    <div>
                        <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Próximo Passo</label>
                        <input class="w-full p-2 border border-border rounded-md text-sm" type="text" name="next_action" value="{{ old('next_action') }}" placeholder="Ex: Enviar orçamento">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Data do Próximo Contato</label>
                        <input class="w-full p-2 border border-border rounded-md text-sm" type="datetime-local" name="next_action_at" value="{{ old('next_action_at') }}">
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Buscar Cliente</label>
                    <div class="relative">
                        <input class="w-full p-2 border border-border rounded-md text-sm" type="text" x-model="customerSearch" @input.debounce.300ms="searchCustomers()"
                               placeholder="Digite o nome do cliente...">
                        <input type="hidden" name="customer_id" x-model="selectedCustomerId">
                        <div class="absolute z-[10] w-full bg-surface-raised border border-border rounded-md mt-[2px] max-h-[150px] overflow-y-auto shadow-[0_4px_6px_rgba(0,0,0,0.1)]" x-show="customerResults.length > 0">
                            <template x-for="c in customerResults" :key="c.id">
                                <div @click="selectCustomer(c)"
                                     class="hover:bg-surface p-2 cursor-pointer text-[0.8rem] border-b border-border-subtle">
                                    <span class="font-semibold" x-text="c.name"></span>
                                    <span class="text-dg-400" x-text="c.phone ? ' - ' + c.phone : ''"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="mt-1 text-xs text-emerald-600" x-show="selectedCustomerName">
                        Selecionado: <strong x-text="selectedCustomerName"></strong>
                        <button class="text-red-300 ml-2 text-[0.7rem] border-0 bg-transparent cursor-pointer" type="button" @click="clearCustomer()">[remover]</button>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Observações</label>
                    <textarea class="w-full p-2 border border-border rounded-md text-sm resize-y" name="description" rows="2" placeholder="Detalhes sobre o interesse, condições, etc.">{{ old('description') }}</textarea>
                </div>
            </div>

            @if($errors->any())
                <div class="mt-3 text-red-300 text-xs">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="flex gap-2 justify-end mt-4">
                <button class="py-2 px-4 text-[0.8rem] border border-border rounded-md bg-surface-raised text-dg-400 cursor-pointer" @click.prevent="showNewDealForm = false" type="button">
                    Cancelar
                </button>
                <button class="py-2 px-4 text-[0.8rem] bg-[#111827] text-white rounded-md border-0 cursor-pointer font-semibold" type="submit">
                    Criar Negócio
                </button>
            </div>
        </form>
    </div>
</div>
<script>
async function checkWaNumber() {
    const input = document.getElementById('new-deal-phone');
    const btn = document.getElementById('check-wa-btn');
    const result = document.getElementById('wa-check-result');
    const phone = input.value.replace(/\D/g, '');

    if (phone.length < 10) {
        result.style.display = 'block';
        result.style.color = '#fca5a5';
        result.textContent = 'Digite um número válido';
        return;
    }

    btn.style.opacity = '0.5';
    btn.disabled = true;
    result.style.display = 'block';
    result.style.color = '#999';
    result.textContent = 'Verificando...';

    try {
        const res = await fetch(`/api/crm/check-number?phone=${phone}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();

        if (data.exists) {
            result.style.color = '#25D366';
            result.textContent = '✓ Número tem WhatsApp';
        } else {
            result.style.color = '#fca5a5';
            result.textContent = '✗ Número não encontrado no WhatsApp';
        }
    } catch(e) {
        result.style.color = '#fca5a5';
        result.textContent = 'Erro ao verificar';
    } finally {
        btn.style.opacity = '1';
        btn.disabled = false;
    }
}
</script>
