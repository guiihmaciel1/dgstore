<x-app-layout>
    <x-slot name="title">Nova Entrada em Lote</x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8" x-data="batchEntry()">

            {{-- Header --}}
            <div class="flex items-center mb-6">
                <a href="{{ route('stock.consignment.index') }}" class="mr-3 p-2 text-gray-500 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Nova Entrada em Lote</h1>
                    <p class="text-sm text-gray-500">Cadastre varias unidades de uma vez com IMEI/Serial individual</p>
                </div>
            </div>

            @if($errors->any())
                <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1rem;">
                    <div style="font-weight: 600; color: #991b1b; margin-bottom: 0.5rem;">Verifique os erros abaixo:</div>
                    <ul style="list-style: disc; padding-left: 1.25rem; color: #991b1b; font-size: 0.875rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('stock.consignment.batch-store') }}" @submit="onSubmit($event)">
                @csrf

                {{-- ─── Bloco 1: Cabecalho do Lote ─── --}}
                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 1rem; padding: 1.5rem; margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid #f3f4f6;">
                        <div style="width: 1.5rem; height: 1.5rem; background: #111827; color: white; border-radius: 9999px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700;">1</div>
                        <h2 style="font-size: 1rem; font-weight: 700; color: #111827;">Dados do Lote</h2>
                    </div>

                    {{-- Fornecedor + Data --}}
                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem;">
                                Fornecedor <span style="color: #dc2626;">*</span>
                            </label>
                            <select name="supplier_id" x-model="form.supplier_id" required
                                    style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                    onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                <option value="">Selecione o fornecedor</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem;">
                                Data de Recebimento
                            </label>
                            <input type="date" name="received_at" x-model="form.received_at"
                                   style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                   onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                        </div>
                    </div>

                    {{-- Busca do produto (autocomplete) --}}
                    <div style="position: relative;" @click.outside="searchOpen = false">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem;">
                            Produto <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="text" placeholder="Digite para buscar (ex: iPhone 17 Pro Max)"
                               x-model="searchTerm"
                               @focus="searchOpen = true; searchProducts()"
                               @input.debounce.250ms="searchProducts()"
                               style="width: 100%; padding: 0.625rem 0.625rem 0.625rem 2.5rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                               onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                        <svg style="position: absolute; left: 0.75rem; top: 2.25rem; width: 1rem; height: 1rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
                        </svg>

                        {{-- Dropdown resultados --}}
                        <div x-show="searchOpen && searchResults.length > 0" x-cloak
                             style="position: absolute; z-index: 30; left: 0; right: 0; margin-top: 0.25rem; background: white; border: 1px solid #e5e7eb; border-radius: 0.5rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); max-height: 18rem; overflow-y: auto;">
                            <template x-for="(item, idx) in searchResults" :key="idx">
                                <button type="button" @click="selectProduct(item)"
                                        style="display: block; width: 100%; padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid #f3f4f6; cursor: pointer; background: white;"
                                        onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="font-weight: 600; color: #111827;" x-text="item.name"></span>
                                        <span x-show="item.in_stock_count > 0"
                                              style="font-size: 0.6875rem; padding: 0.125rem 0.5rem; background: #dbeafe; color: #1d4ed8; border-radius: 9999px; font-weight: 600;"
                                              x-text="item.in_stock_count + ' em estoque'"></span>
                                    </div>
                                    <div style="display: flex; gap: 0.5rem; margin-top: 0.25rem; flex-wrap: wrap;">
                                        <template x-for="storage in item.storages" :key="storage">
                                            <span style="font-size: 0.6875rem; padding: 0.125rem 0.375rem; background: #f3f4f6; color: #4b5563; border-radius: 0.25rem;" x-text="storage"></span>
                                        </template>
                                    </div>
                                </button>
                            </template>
                        </div>

                        <p x-show="form.name" x-cloak style="margin-top: 0.5rem; font-size: 0.8125rem; color: #16a34a; font-weight: 500;">
                            <span>Selecionado:</span> <strong x-text="form.name"></strong>
                        </p>
                    </div>

                    {{-- Storage / Modelo / Condicao --}}
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">Storage</label>
                            <select x-show="storageOptions.length > 0" x-cloak name="storage" x-model="form.storage"
                                    style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;">
                                <option value="">Selecione</option>
                                <template x-for="opt in storageOptions" :key="opt">
                                    <option :value="opt" x-text="opt"></option>
                                </template>
                            </select>
                            <input x-show="storageOptions.length === 0" type="text" name="storage" x-model="form.storage" placeholder="Ex: 256GB"
                                   style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">Modelo (codigo)</label>
                            <input type="text" name="model" x-model="form.model" placeholder="Ex: A3106"
                                   style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                Condicao <span style="color: #dc2626;">*</span>
                            </label>
                            <select name="condition" x-model="form.condition" required
                                    style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;">
                                <option value="new">Novo</option>
                                <option value="used">Seminovo</option>
                            </select>
                        </div>
                    </div>

                    {{-- Hidden product name --}}
                    <input type="hidden" name="name" x-model="form.name">

                    {{-- Observacoes --}}
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">Observacoes do lote</label>
                        <textarea name="notes" x-model="form.notes" rows="2" placeholder="Observacoes gerais (opcional)"
                                  style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none; resize: vertical;"></textarea>
                    </div>
                </div>

                {{-- ─── Bloco 2: Unidades ─── --}}
                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 1rem; padding: 1.5rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 0.75rem; border-bottom: 1px solid #f3f4f6; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="width: 1.5rem; height: 1.5rem; background: #111827; color: white; border-radius: 9999px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700;">2</div>
                            <h2 style="font-size: 1rem; font-weight: 700; color: #111827;">Unidades <span x-text="'(' + units.length + ')'" style="color: #6b7280; font-weight: 500;"></span></h2>
                        </div>
                        <button type="button" @click="addUnit()"
                                style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; background: #111827; color: white; font-size: 0.8125rem; font-weight: 600; border: none; border-radius: 0.5rem; cursor: pointer;">
                            <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Adicionar Unidade
                        </button>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <template x-for="(unit, idx) in units" :key="unit._key">
                            <div style="border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1rem; background: #fafafa;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                                    <span style="font-size: 0.8125rem; font-weight: 700; color: #111827;" x-text="'Unidade #' + (idx + 1)"></span>
                                    <button type="button" @click="removeUnit(idx)" x-show="units.length > 1"
                                            style="padding: 0.25rem 0.5rem; background: transparent; color: #dc2626; border: none; cursor: pointer; font-size: 0.75rem; font-weight: 600;">
                                        Remover
                                    </button>
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem; margin-bottom: 0.5rem;">
                                    {{-- Cor --}}
                                    <div>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Cor</label>
                                        <select x-show="colorOptions.length > 0 && unit.color !== '__custom'" :name="'units[' + idx + '][color]'" x-model="unit.color"
                                                style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8125rem;">
                                            <option value="">-- Selecione --</option>
                                            <template x-for="opt in colorOptions" :key="opt">
                                                <option :value="opt" x-text="opt"></option>
                                            </template>
                                            <option value="__custom">+ Outra cor...</option>
                                        </select>
                                        <input x-show="colorOptions.length === 0 || unit.color === '__custom'"
                                               type="text" :name="'units[' + idx + '][color]'"
                                               :value="unit.color === '__custom' ? '' : unit.color"
                                               @input="unit.color = $event.target.value"
                                               placeholder="Ex: Silver"
                                               style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8125rem;">
                                    </div>

                                    {{-- IMEI --}}
                                    <div>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">IMEI</label>
                                        <input type="text" :name="'units[' + idx + '][imei]'" x-model="unit.imei"
                                               @blur="validateImeiSerial(idx)"
                                               placeholder="Numero IMEI"
                                               :style="unit.imei_error ? 'width: 100%; padding: 0.5rem; border: 1px solid #dc2626; border-radius: 0.375rem; font-size: 0.8125rem; font-family: monospace;' : 'width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8125rem; font-family: monospace;'">
                                    </div>

                                    {{-- Serial --}}
                                    <div>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Serial Number</label>
                                        <input type="text" :name="'units[' + idx + '][serial_number]'" x-model="unit.serial_number"
                                               @blur="validateImeiSerial(idx)"
                                               placeholder="Serial (opcional)"
                                               :style="unit.serial_error ? 'width: 100%; padding: 0.5rem; border: 1px solid #dc2626; border-radius: 0.375rem; font-size: 0.8125rem; font-family: monospace;' : 'width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8125rem; font-family: monospace;'">
                                    </div>
                                </div>

                                <div x-show="unit.imei_error || unit.serial_error" x-cloak style="font-size: 0.75rem; color: #dc2626; margin-bottom: 0.5rem;" x-text="unit.imei_error || unit.serial_error"></div>

                                {{-- Custo e Preco Sugerido --}}
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.5rem; padding-top: 0.5rem; border-top: 1px dashed #e5e7eb;">
                                    <div>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Custo (R$) <span style="color: #dc2626;">*</span></label>
                                        <input type="number" :name="'units[' + idx + '][supplier_cost]'" x-model="unit.supplier_cost"
                                               required step="0.01" min="0" placeholder="0,00"
                                               style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8125rem;">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Preço Sugerido (R$)</label>
                                        <input type="number" :name="'units[' + idx + '][suggested_price]'" x-model="unit.suggested_price"
                                               step="0.01" min="0" placeholder="0,00"
                                               style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8125rem;">
                                    </div>
                                </div>

                                {{-- Campos de Seminovo --}}
                                <div x-show="form.condition === 'used'" x-cloak
                                     style="display: grid; grid-template-columns: 1fr 1fr 1fr 2fr; gap: 0.75rem; padding-top: 0.5rem; border-top: 1px dashed #e5e7eb;">
                                    <div>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Bateria (%)</label>
                                        <input type="number" :name="'units[' + idx + '][battery_health]'" x-model="unit.battery_health" min="0" max="100" placeholder="87"
                                               style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8125rem;">
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 0.375rem; padding-top: 1.25rem;">
                                        <input type="checkbox" :name="'units[' + idx + '][has_box]'" value="1" x-model="unit.has_box" :id="'has_box_' + idx"
                                               style="width: 0.875rem; height: 0.875rem;">
                                        <label :for="'has_box_' + idx" style="font-size: 0.8125rem; color: #374151;">Caixa</label>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 0.375rem; padding-top: 1.25rem;">
                                        <input type="checkbox" :name="'units[' + idx + '][has_cable]'" value="1" x-model="unit.has_cable" :id="'has_cable_' + idx"
                                               style="width: 0.875rem; height: 0.875rem;">
                                        <label :for="'has_cable_' + idx" style="font-size: 0.8125rem; color: #374151;">Cabo</label>
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Obs.</label>
                                        <input type="text" :name="'units[' + idx + '][notes]'" x-model="unit.notes" placeholder="Observacoes desta unidade"
                                               style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8125rem;">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #f3f4f6;">
                        <a href="{{ route('stock.consignment.index') }}"
                           style="padding: 0.625rem 1.5rem; color: #6b7280; font-size: 0.875rem; text-decoration: none; border: 1px solid #e5e7eb; border-radius: 0.5rem;">
                            Cancelar
                        </a>
                        <button type="submit" :disabled="hasErrors() || submitting"
                                :style="(hasErrors() || submitting) ? 'padding: 0.625rem 1.5rem; background: #9ca3af; color: white; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: not-allowed;' : 'padding: 0.625rem 1.5rem; background: #111827; color: white; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;'">
                            <span x-text="submitting ? 'Salvando...' : 'Cadastrar Lote'"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function batchEntry() {
            return {
                form: {
                    supplier_id: @json($defaultSupplierId ?? ''),
                    name: '',
                    storage: '',
                    model: '',
                    condition: 'new',
                    received_at: '{{ now()->toDateString() }}',
                    notes: '',
                },
                units: [],
                searchTerm: '',
                searchOpen: false,
                searchResults: [],
                colorOptions: [],
                storageOptions: [],
                submitting: false,
                _nextKey: 0,

                init() {
                    this.addUnit();
                },

                async searchProducts() {
                    try {
                        const url = '{{ route('stock.consignment.catalog') }}?q=' + encodeURIComponent(this.searchTerm);
                        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                        if (!res.ok) return;
                        this.searchResults = await res.json();
                    } catch (e) {
                        this.searchResults = [];
                    }
                },

                selectProduct(item) {
                    this.form.name = item.name;
                    this.searchTerm = item.name;
                    this.colorOptions = item.colors || [];
                    this.storageOptions = item.storages || [];
                    if (this.storageOptions.length === 1) {
                        this.form.storage = this.storageOptions[0];
                    }
                    this.searchOpen = false;
                },

                addUnit() {
                    this.units.push({
                        _key: this._nextKey++,
                        color: '',
                        imei: '',
                        serial_number: '',
                        supplier_cost: '',
                        suggested_price: '',
                        battery_health: '',
                        has_box: false,
                        has_cable: false,
                        notes: '',
                        imei_error: '',
                        serial_error: '',
                    });
                },

                removeUnit(idx) {
                    if (this.units.length > 1) {
                        this.units.splice(idx, 1);
                    }
                },

                async validateImeiSerial(idx) {
                    const unit = this.units[idx];
                    unit.imei_error = '';
                    unit.serial_error = '';

                    const imei = (unit.imei || '').trim();
                    const serial = (unit.serial_number || '').trim();

                    if (!imei && !serial) return;

                    // Verifica duplicatas no proprio lote
                    for (let i = 0; i < this.units.length; i++) {
                        if (i === idx) continue;
                        const other = this.units[i];
                        if (imei && (other.imei || '').trim() === imei) {
                            unit.imei_error = 'IMEI duplicado neste lote (Unidade #' + (i + 1) + ')';
                            return;
                        }
                        if (serial && (other.serial_number || '').trim() === serial) {
                            unit.serial_error = 'Serial duplicado neste lote (Unidade #' + (i + 1) + ')';
                            return;
                        }
                    }

                    // Verifica duplicatas no banco
                    try {
                        const params = new URLSearchParams();
                        if (imei) params.set('imei', imei);
                        if (serial) params.set('serial_number', serial);

                        const url = '{{ route('stock.consignment.validate-imei') }}?' + params.toString();
                        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                        if (!res.ok) return;
                        const data = await res.json();
                        if (!data.available) {
                            if (imei) unit.imei_error = data.message;
                            if (serial && !imei) unit.serial_error = data.message;
                        }
                    } catch (e) { /* silencioso */ }
                },

                hasErrors() {
                    return this.units.some(u => u.imei_error || u.serial_error);
                },

                onSubmit(event) {
                    if (this.hasErrors()) {
                        event.preventDefault();
                        return;
                    }
                    this.submitting = true;
                },
            };
        }
    </script>
</x-app-layout>
