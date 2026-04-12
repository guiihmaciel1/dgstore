{{-- Modal: Novo Negócio --}}
<div x-show="showNewDealForm" x-transition.opacity style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.4);" x-cloak>
    <div @click.away="showNewDealForm = false" style="background: white; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 36rem; box-shadow: 0 25px 50px rgba(0,0,0,0.25); max-height: 90vh; overflow-y: auto;">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: #111827; margin-bottom: 1rem;">Novo Negócio</h2>
        <form method="POST" action="{{ route('crm.deals.store') }}">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Título <span style="color: #dc2626;">*</span></label>
                    <input type="text" name="title" required value="{{ old('title') }}" placeholder="Ex: iPhone 16 Pro Max - João"
                           style="width: 100%; padding: 0.5rem; border: 1px solid {{ $errors->has('title') ? '#fca5a5' : '#e5e7eb' }}; border-radius: 0.375rem; font-size: 0.875rem;">
                </div>

                {{-- Interesse de Produto (estruturado) --}}
                <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 0.75rem;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: #374151; display: block; margin-bottom: 0.5rem;">Interesse de Produto</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                        <div>
                            <label style="font-size: 0.7rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Modelo</label>
                            <select name="interest_model" style="width: 100%; padding: 0.4rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem;">
                                <option value="">Selecione...</option>
                                <optgroup label="iPhone">
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
                            <label style="font-size: 0.7rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Armazenamento</label>
                            <select name="interest_storage" style="width: 100%; padding: 0.4rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem;">
                                <option value="">Qualquer</option>
                                <option {{ old('interest_storage') === '64GB' ? 'selected' : '' }}>64GB</option>
                                <option {{ old('interest_storage') === '128GB' ? 'selected' : '' }}>128GB</option>
                                <option {{ old('interest_storage') === '256GB' ? 'selected' : '' }}>256GB</option>
                                <option {{ old('interest_storage') === '512GB' ? 'selected' : '' }}>512GB</option>
                                <option {{ old('interest_storage') === '1TB' ? 'selected' : '' }}>1TB</option>
                            </select>
                        </div>
                        <div>
                            <label style="font-size: 0.7rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Cor</label>
                            <input type="text" name="interest_color" value="{{ old('interest_color') }}" placeholder="Ex: Titânio Natural"
                                   style="width: 100%; padding: 0.4rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.7rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Condição</label>
                            <select name="interest_condition" style="width: 100%; padding: 0.4rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem;">
                                <option value="">Qualquer</option>
                                <option value="novo" {{ old('interest_condition') === 'novo' ? 'selected' : '' }}>Novo</option>
                                <option value="seminovo" {{ old('interest_condition') === 'seminovo' ? 'selected' : '' }}>Seminovo</option>
                            </select>
                        </div>
                    </div>
                    <div style="margin-top: 0.5rem;">
                        <label style="font-size: 0.7rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Orçamento máximo (R$)</label>
                        <input type="number" name="interest_max_budget" step="0.01" value="{{ old('interest_max_budget') }}" placeholder="0,00"
                               style="width: 100%; padding: 0.4rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Valor (R$)</label>
                        <input type="number" name="value" step="0.01" value="{{ old('value') }}" placeholder="0,00"
                               style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Telefone / WhatsApp</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="5517996498338"
                               style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Previsão de Fechamento</label>
                        <input type="date" name="expected_close_date" value="{{ old('expected_close_date') }}"
                               style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Etapa</label>
                        <select name="pipeline_stage_id" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                            @foreach($activeStages as $stage)
                                <option value="{{ $stage->id }}" {{ $stage->is_default ? 'selected' : '' }}>{{ $stage->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Buscar Cliente</label>
                    <div style="position: relative;">
                        <input type="text" x-model="customerSearch" @input.debounce.300ms="searchCustomers()"
                               placeholder="Digite o nome do cliente..."
                               style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                        <input type="hidden" name="customer_id" x-model="selectedCustomerId">
                        <div x-show="customerResults.length > 0" style="position: absolute; z-index: 10; width: 100%; background: white; border: 1px solid #e5e7eb; border-radius: 0.375rem; margin-top: 2px; max-height: 150px; overflow-y: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <template x-for="c in customerResults" :key="c.id">
                                <div @click="selectCustomer(c)" style="padding: 0.5rem; cursor: pointer; font-size: 0.8rem; border-bottom: 1px solid #f3f4f6;"
                                     class="hover:bg-gray-50">
                                    <span x-text="c.name" style="font-weight: 600;"></span>
                                    <span x-text="c.phone ? ' - ' + c.phone : ''" style="color: #6b7280;"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div x-show="selectedCustomerName" style="margin-top: 0.25rem; font-size: 0.75rem; color: #059669;">
                        Selecionado: <strong x-text="selectedCustomerName"></strong>
                        <button type="button" @click="clearCustomer()" style="color: #dc2626; margin-left: 0.5rem; font-size: 0.7rem; border: none; background: none; cursor: pointer;">[remover]</button>
                    </div>
                </div>

                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Observações</label>
                    <textarea name="description" rows="2" placeholder="Detalhes sobre o interesse, condições, etc."
                              style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem; resize: vertical;">{{ old('description') }}</textarea>
                </div>
            </div>

            @if($errors->any())
                <div style="margin-top: 0.75rem; color: #dc2626; font-size: 0.75rem;">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 1rem;">
                <button @click.prevent="showNewDealForm = false" type="button"
                        style="padding: 0.5rem 1rem; font-size: 0.8rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; background: white; color: #6b7280; cursor: pointer;">
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
