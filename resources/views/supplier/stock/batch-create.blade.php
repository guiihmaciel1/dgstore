@extends('layouts.supplier')

@section('title', 'Nova Entrada')

@section('content')
<div x-data="batchEntry()" x-init="addUnit()">
    <a href="{{ route('supplier.stock.index') }}" class="s-back">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Voltar
    </a>

    <div class="mb-5">
        <h1 class="s-title">Nova Entrada</h1>
        <p class="s-subtitle">Cadastre unidades com IMEI e custo individual</p>
    </div>

    <form method="POST" action="{{ route('supplier.stock.batch-store') }}" @submit="onSubmit($event)">
        @csrf

        {{-- Lote --}}
        <div class="s-card s-card-pad mb-4">
            <h2 class="text-base font-semibold mb-4" style="letter-spacing: -0.01em;">Dados do Lote</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="s-label">Data de Recebimento</label>
                    <input type="date" name="received_at" x-model="form.received_at" required class="s-input">
                </div>
                <div>
                    <label class="s-label">Nota Fiscal</label>
                    <input type="text" name="invoice_number" x-model="form.invoice_number" class="s-input" placeholder="Opcional">
                </div>
            </div>

            <div class="mb-4" @click.outside="searchOpen = false">
                <label class="s-label">Produto <span style="color: var(--apple-red);">*</span></label>
                <div class="relative">
                    <input type="text" placeholder="Ex: iPhone 17 Pro Max"
                           x-model="searchTerm"
                           @focus="searchOpen = true; searchProducts()"
                           @input.debounce.250ms="searchProducts()"
                           class="s-input pl-10">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4" style="color: var(--apple-text-tertiary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>

                    <div x-show="searchOpen && searchResults.length > 0" x-cloak
                         class="absolute z-30 w-full mt-1 s-card shadow-lg max-h-60 overflow-y-auto">
                        <template x-for="(item, idx) in searchResults" :key="idx">
                            <button type="button" @click="selectProduct(item)"
                                    class="w-full p-3.5 text-left border-b last:border-0 active:bg-gray-50"
                                    style="border-color: var(--apple-separator);">
                                <div class="flex justify-between items-center gap-2">
                                    <span class="font-semibold text-sm" x-text="item.name"></span>
                                    <span x-show="item.in_stock_count > 0"
                                          class="s-badge s-badge-green shrink-0"
                                          x-text="item.in_stock_count + ' em estoque'"></span>
                                </div>
                                <div class="flex gap-1.5 mt-1.5 flex-wrap">
                                    <template x-for="storage in item.storages" :key="storage">
                                        <span class="text-xs px-2 py-0.5 rounded-md" style="background: var(--apple-bg); color: var(--apple-text-secondary);" x-text="storage"></span>
                                    </template>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>
                <p x-show="form.name" x-cloak class="text-xs mt-1.5 font-medium" style="color: var(--apple-green);">
                    ✓ <span x-text="form.name"></span>
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div x-show="storageOptions.length > 0" x-cloak>
                    <label class="s-label">Armazenamento</label>
                    <select x-model="form.storage" class="s-input">
                        <option value="">Selecione</option>
                        <template x-for="opt in storageOptions" :key="opt">
                            <option :value="opt" x-text="opt"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="s-label">Condição <span style="color: var(--apple-red);">*</span></label>
                    <select x-model="form.condition" class="s-input">
                        <option value="new">Novo</option>
                        <option value="used">Seminovo</option>
                        <option value="refurbished">Refurbished</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Unidades --}}
        <div class="s-card s-card-pad mb-4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold" style="letter-spacing: -0.01em;">
                    Unidades <span style="color: var(--apple-text-secondary); font-weight: 500;" x-text="'(' + units.length + ')'"></span>
                </h2>
                <button type="button" @click="addUnit()" class="s-btn s-btn-primary text-sm py-2 px-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Adicionar
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(unit, idx) in units" :key="unit._key">
                    <div class="rounded-xl p-4" style="background: var(--apple-bg); border: 0.5px solid var(--apple-separator);">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm font-semibold">Unidade <span x-text="idx + 1"></span></span>
                            <button type="button" @click="removeUnit(idx)" x-show="units.length > 1"
                                    class="text-xs font-medium" style="color: var(--apple-red);">Remover</button>
                        </div>

                        {{-- Cor --}}
                        <div class="mb-3">
                            <label class="s-label text-xs">Cor <span style="color: var(--apple-red);">*</span></label>
                            <select x-show="colorOptions.length > 0" :name="'units[' + idx + '][color]'" x-model="unit.color" required class="s-input text-sm py-2.5">
                                <option value="">Selecione a cor</option>
                                <template x-for="opt in colorOptions" :key="opt">
                                    <option :value="opt" x-text="opt"></option>
                                </template>
                            </select>
                            <input x-show="colorOptions.length === 0" type="text" :name="'units[' + idx + '][color]'" x-model="unit.color" required
                                   placeholder="Ex: Silver" class="s-input text-sm py-2.5">
                        </div>

                        {{-- IMEI / Serial --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="s-label text-xs">IMEI</label>
                                <input type="text" :name="'units[' + idx + '][imei]'" x-model="unit.imei"
                                       @blur="validateImeiSerial(idx)"
                                       inputmode="numeric"
                                       placeholder="Número IMEI"
                                       class="s-input text-sm py-2.5 font-mono"
                                       :style="unit.imei_error ? 'border-color: var(--apple-red);' : ''">
                                <input type="hidden" :name="'units[' + idx + '][product_name]'" :value="form.name">
                                <input type="hidden" :name="'units[' + idx + '][storage]'" :value="form.storage">
                                <input type="hidden" :name="'units[' + idx + '][condition]'" :value="form.condition">
                            </div>
                            <div>
                                <label class="s-label text-xs">Serial</label>
                                <input type="text" :name="'units[' + idx + '][serial_number]'" x-model="unit.serial_number"
                                       @blur="validateImeiSerial(idx)"
                                       placeholder="Serial (opcional)"
                                       class="s-input text-sm py-2.5 font-mono"
                                       :style="unit.serial_error ? 'border-color: var(--apple-red);' : ''">
                            </div>
                        </div>

                        {{-- Custo --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-3 border-t" style="border-color: var(--apple-separator);">
                            <div>
                                <label class="s-label text-xs">Custo (R$) <span style="color: var(--apple-red);">*</span></label>
                                <input type="number" :name="'units[' + idx + '][supplier_cost]'" x-model="unit.supplier_cost"
                                       required step="0.01" min="0" inputmode="decimal" placeholder="0,00"
                                       class="s-input text-sm py-2.5">
                            </div>
                            <div>
                                <label class="s-label text-xs">Preço Sugerido (R$)</label>
                                <input type="number" :name="'units[' + idx + '][suggested_price]'" x-model="unit.suggested_price"
                                       step="0.01" min="0" inputmode="decimal" placeholder="0,00"
                                       class="s-input text-sm py-2.5">
                            </div>
                        </div>

                        <p x-show="unit.imei_error || unit.serial_error" x-cloak class="text-xs mt-2" style="color: var(--apple-red);" x-text="unit.imei_error || unit.serial_error"></p>

                        {{-- Seminovo --}}
                        <div x-show="form.condition === 'used'" x-cloak class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-3 pt-3 border-t" style="border-color: var(--apple-separator);">
                            <div>
                                <label class="s-label text-xs">Bateria (%)</label>
                                <input type="number" :name="'units[' + idx + '][battery_health]'" x-model="unit.battery_health" min="0" max="100" inputmode="numeric"
                                       class="s-input text-sm py-2.5">
                            </div>
                            <label class="flex items-center gap-2 pt-6 cursor-pointer">
                                <input type="checkbox" :name="'units[' + idx + '][has_box]'" value="1" x-model="unit.has_box" style="accent-color: var(--apple-blue);">
                                <span class="text-sm">Caixa</span>
                            </label>
                            <label class="flex items-center gap-2 pt-6 cursor-pointer">
                                <input type="checkbox" :name="'units[' + idx + '][has_cable]'" value="1" x-model="unit.has_cable" style="accent-color: var(--apple-blue);">
                                <span class="text-sm">Cabo</span>
                            </label>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pb-4">
            <a href="{{ route('supplier.stock.index') }}" class="s-btn s-btn-secondary w-full sm:w-auto text-center">Cancelar</a>
            <button type="submit" :disabled="hasErrors() || submitting"
                    class="s-btn s-btn-primary w-full sm:w-auto"
                    x-text="submitting ? 'Salvando...' : 'Cadastrar Lote'">
            </button>
        </div>
    </form>
</div>

<script>
function batchEntry() {
    return {
        form: { name: '', storage: '', condition: 'new', received_at: '{{ now()->toDateString() }}', invoice_number: '' },
        units: [],
        searchTerm: '',
        searchOpen: false,
        searchResults: [],
        colorOptions: [],
        storageOptions: [],
        submitting: false,
        _nextKey: 0,

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
                imei_error: '',
                serial_error: '',
            });
        },

        removeUnit(idx) {
            if (this.units.length > 1) this.units.splice(idx, 1);
        },

        selectProduct(item) {
            this.form.name = item.name;
            this.form.storage = item.storages?.length === 1 ? item.storages[0] : (this.form.storage || item.storages?.[0] || '');
            this.colorOptions = item.colors || [];
            this.storageOptions = item.storages || [];
            this.searchTerm = item.name;
            this.searchOpen = false;
        },

        async searchProducts() {
            if (this.searchTerm.length < 2) { this.searchResults = []; return; }
            try {
                const res = await fetch(`{{ route('supplier.api.products') }}?q=${encodeURIComponent(this.searchTerm)}`);
                this.searchResults = await res.json();
            } catch(e) { this.searchResults = []; }
        },

        async validateImeiSerial(idx) {
            const unit = this.units[idx];
            unit.imei_error = '';
            unit.serial_error = '';
            if (!unit.imei && !unit.serial_number) return;

            try {
                const params = new URLSearchParams();
                if (unit.imei) params.append('imei', unit.imei);
                if (unit.serial_number) params.append('serial_number', unit.serial_number);
                const res = await fetch(`{{ route('supplier.api.validate-imei') }}?${params}`);
                const data = await res.json();
                if (!data.valid) {
                    if (unit.imei) unit.imei_error = data.message;
                    if (unit.serial_number) unit.serial_error = data.message;
                }
            } catch(e) {}
        },

        hasErrors() {
            return this.units.some(u => u.imei_error || u.serial_error) || this.units.length === 0 || !this.form.name;
        },

        onSubmit(e) {
            if (this.hasErrors()) { e.preventDefault(); return; }
            this.submitting = true;
        }
    }
}
</script>
@endsection
