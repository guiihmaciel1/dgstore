{{-- Modal: Novo Negócio --}}
<div x-show="showNewDealForm" x-transition.opacity style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.4);" x-cloak>
    <div @click.away="showNewDealForm = false" style="background: #141414; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 36rem; box-shadow: 0 25px 50px rgba(0,0,0,0.25); max-height: 90vh; overflow-y: auto;">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 1rem;">Novo Negócio</h2>
        <form method="POST" action="{{ route('crm.deals.store') }}">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; color: #818181; display: block; margin-bottom: 2px;">Título <span style="color: #fca5a5;">*</span></label>
                    <input type="text" name="title" required value="{{ old('title') }}" placeholder="Ex: iPhone 16 Pro Max - João"
                           style="width: 100%; padding: 0.5rem; border: 1px solid {{ $errors->has('title') ? '#fca5a5' : '#e5e7eb' }}; border-radius: 0.375rem; font-size: 0.875rem;">
                </div>

                {{-- Interesse de Produto (estruturado) --}}
                <div style="background: #1a1a1a; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; padding: 0.75rem;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: #a4a4a4; display: block; margin-bottom: 0.5rem;">Interesse de Produto</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                        <div>
                            <label style="font-size: 0.7rem; font-weight: 600; color: #818181; display: block; margin-bottom: 2px;">Modelo</label>
                            <select name="interest_model" style="width: 100%; padding: 0.4rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem;">
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
                            <label style="font-size: 0.7rem; font-weight: 600; color: #818181; display: block; margin-bottom: 2px;">Armazenamento</label>
                            <select name="interest_storage" style="width: 100%; padding: 0.4rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem;">
                                <option value="">Qualquer</option>
                                <option {{ old('interest_storage') === '64GB' ? 'selected' : '' }}>64GB</option>
                                <option {{ old('interest_storage') === '128GB' ? 'selected' : '' }}>128GB</option>
                                <option {{ old('interest_storage') === '256GB' ? 'selected' : '' }}>256GB</option>
                                <option {{ old('interest_storage') === '512GB' ? 'selected' : '' }}>512GB</option>
                                <option {{ old('interest_storage') === '1TB' ? 'selected' : '' }}>1TB</option>
                            </select>
                        </div>
                        <div>
                            <label style="font-size: 0.7rem; font-weight: 600; color: #818181; display: block; margin-bottom: 2px;">Cor</label>
                            <input type="text" name="interest_color" value="{{ old('interest_color') }}" placeholder="Ex: Titânio Natural"
                                   style="width: 100%; padding: 0.4rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.7rem; font-weight: 600; color: #818181; display: block; margin-bottom: 2px;">Condição</label>
                            <select name="interest_condition" style="width: 100%; padding: 0.4rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem;">
                                <option value="">Qualquer</option>
                                <option value="novo" {{ old('interest_condition') === 'novo' ? 'selected' : '' }}>Novo</option>
                                <option value="seminovo" {{ old('interest_condition') === 'seminovo' ? 'selected' : '' }}>Seminovo</option>
                            </select>
                        </div>
                    </div>
                    <div style="margin-top: 0.5rem;">
                        <label style="font-size: 0.7rem; font-weight: 600; color: #818181; display: block; margin-bottom: 2px;">Orçamento máximo (R$)</label>
                        <input type="number" name="interest_max_budget" step="0.01" value="{{ old('interest_max_budget') }}" placeholder="0,00"
                               style="width: 100%; padding: 0.4rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: #818181; display: block; margin-bottom: 2px;">Valor (R$)</label>
                        <input type="number" name="value" step="0.01" value="{{ old('value') }}" placeholder="0,00"
                               style="width: 100%; padding: 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: #818181; display: block; margin-bottom: 2px;">Telefone / WhatsApp <span style="color: #fca5a5;">*</span></label>
                        <div style="display: flex; gap: 0.375rem;">
                            <input type="text" name="phone" id="new-deal-phone" required value="{{ old('phone') }}" placeholder="(00) 00000-0000"
                                   maxlength="15"
                                   @input="$el.value = $el.value.replace(/\D/g,'').replace(/^(\d{2})(\d)/,'($1) $2').replace(/(\d{5})(\d)/,'$1-$2').substring(0,15)"
                                   style="flex: 1; padding: 0.5rem; border: 1px solid {{ $errors->has('phone') ? '#fca5a5' : '#e5e7eb' }}; border-radius: 0.375rem; font-size: 0.875rem;">
                            <button type="button" onclick="checkWaNumber()" id="check-wa-btn" title="Verificar WhatsApp"
                                    style="padding: 0.5rem 0.625rem; border: 1px solid #333; border-radius: 0.375rem; background: #1a1a1a; cursor: pointer; display: flex; align-items: center;">
                                <svg style="width: 16px; height: 16px; color: #25D366;" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                            </button>
                        </div>
                        <div id="wa-check-result" style="font-size: 0.7rem; margin-top: 0.2rem; display: none;"></div>
                        @error('phone')
                            <p style="font-size: 0.7rem; color: #fca5a5; margin: 0.25rem 0 0 0;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: #818181; display: block; margin-bottom: 2px;">Previsão de Fechamento</label>
                        <input type="date" name="expected_close_date" value="{{ old('expected_close_date') }}"
                               style="width: 100%; padding: 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: #818181; display: block; margin-bottom: 2px;">Etapa</label>
                        <select name="pipeline_stage_id" style="width: 100%; padding: 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.875rem;">
                            @foreach($activeStages as $stage)
                                <option value="{{ $stage->id }}" {{ $stage->is_default ? 'selected' : '' }}>{{ $stage->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Origem e Temperatura --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: #818181; display: block; margin-bottom: 2px;">Origem do Lead</label>
                        <select name="lead_source" style="width: 100%; padding: 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.875rem;">
                            <option value="">Não informada</option>
                            @foreach(\App\Domain\CRM\Enums\LeadSource::cases() as $src)
                                <option value="{{ $src->value }}" {{ old('lead_source') === $src->value ? 'selected' : '' }}>{{ $src->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: #818181; display: block; margin-bottom: 2px;">Temperatura</label>
                        <select name="temperature" style="width: 100%; padding: 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.875rem;">
                            <option value="hot" {{ old('temperature') === 'hot' ? 'selected' : '' }}>Quente</option>
                            <option value="warm" {{ old('temperature', 'warm') === 'warm' ? 'selected' : '' }}>Morno</option>
                            <option value="cold" {{ old('temperature') === 'cold' ? 'selected' : '' }}>Frio</option>
                        </select>
                    </div>
                </div>

                {{-- Próximo Passo --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: #818181; display: block; margin-bottom: 2px;">Próximo Passo</label>
                        <input type="text" name="next_action" value="{{ old('next_action') }}" placeholder="Ex: Enviar orçamento"
                               style="width: 100%; padding: 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: #818181; display: block; margin-bottom: 2px;">Data do Próximo Contato</label>
                        <input type="datetime-local" name="next_action_at" value="{{ old('next_action_at') }}"
                               style="width: 100%; padding: 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                </div>

                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; color: #818181; display: block; margin-bottom: 2px;">Buscar Cliente</label>
                    <div style="position: relative;">
                        <input type="text" x-model="customerSearch" @input.debounce.300ms="searchCustomers()"
                               placeholder="Digite o nome do cliente..."
                               style="width: 100%; padding: 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.875rem;">
                        <input type="hidden" name="customer_id" x-model="selectedCustomerId">
                        <div x-show="customerResults.length > 0" style="position: absolute; z-index: 10; width: 100%; background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; margin-top: 2px; max-height: 150px; overflow-y: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <template x-for="c in customerResults" :key="c.id">
                                <div @click="selectCustomer(c)" style="padding: 0.5rem; cursor: pointer; font-size: 0.8rem; border-bottom: 1px solid rgba(255,255,255,0.04);"
                                     class="hover:bg-surface">
                                    <span x-text="c.name" style="font-weight: 600;"></span>
                                    <span x-text="c.phone ? ' - ' + c.phone : ''" style="color: #818181;"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div x-show="selectedCustomerName" style="margin-top: 0.25rem; font-size: 0.75rem; color: #059669;">
                        Selecionado: <strong x-text="selectedCustomerName"></strong>
                        <button type="button" @click="clearCustomer()" style="color: #fca5a5; margin-left: 0.5rem; font-size: 0.7rem; border: none; background: none; cursor: pointer;">[remover]</button>
                    </div>
                </div>

                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; color: #818181; display: block; margin-bottom: 2px;">Observações</label>
                    <textarea name="description" rows="2" placeholder="Detalhes sobre o interesse, condições, etc."
                              style="width: 100%; padding: 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.875rem; resize: vertical;">{{ old('description') }}</textarea>
                </div>
            </div>

            @if($errors->any())
                <div style="margin-top: 0.75rem; color: #fca5a5; font-size: 0.75rem;">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 1rem;">
                <button @click.prevent="showNewDealForm = false" type="button"
                        style="padding: 0.5rem 1rem; font-size: 0.8rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; background: #141414; color: #818181; cursor: pointer;">
                    Cancelar
                </button>
                <button type="submit"
                        style="padding: 0.5rem 1rem; font-size: 0.8rem; background: #111827; color: white; border-radius: 0.375rem; border: none; cursor: pointer; font-weight: 600;">
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
