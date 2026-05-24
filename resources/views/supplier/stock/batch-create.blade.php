@extends('layouts.supplier')

@section('title', 'Nova Entrada em Lote')

@section('content')
<div class="max-w-5xl mx-auto" x-data="batchEntry()">
    <div class="mb-6">
        <a href="{{ route('supplier.stock.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Voltar
        </a>
        <h1 class="text-3xl font-semibold text-gray-900">Nova Entrada em Lote</h1>
        <p class="mt-1 text-sm text-gray-500">Cadastre várias unidades de uma vez com IMEI/Serial individual</p>
    </div>
    
    <form method="POST" action="{{ route('supplier.stock.batch-store') }}" @submit="onSubmit($event)">
        @csrf
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Dados do Lote</h2>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data de Recebimento</label>
                    <input type="date" name="received_at" x-model="form.received_at" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nota Fiscal</label>
                    <input type="text" name="invoice_number" x-model="form.invoice_number"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            
            <div class="mb-4" @click.outside="searchOpen = false">
                <label class="block text-sm font-medium text-gray-700 mb-2">Produto <span class="text-red-600">*</span></label>
                <div class="relative">
                    <input type="text" placeholder="Digite para buscar (ex: iPhone 17 Pro Max)"
                           x-model="searchTerm"
                           @focus="searchOpen = true; searchProducts()"
                           @input.debounce.250ms="searchProducts()"
                           class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    
                    <div x-show="searchOpen && searchResults.length > 0" x-cloak
                         class="absolute z-30 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-72 overflow-y-auto">
                        <template x-for="(item, idx) in searchResults" :key="idx">
                            <button type="button" @click="selectProduct(item)"
                                    class="w-full p-3 text-left hover:bg-gray-50 border-b border-gray-100">
                                <div class="flex justify-between items-center">
                                    <span class="font-semibold text-gray-900" x-text="item.name"></span>
                                    <span x-show="item.in_stock_count > 0" 
                                          class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded-full" 
                                          x-text="item.in_stock_count + ' em estoque'"></span>
                                </div>
                                <div class="flex gap-2 mt-1 flex-wrap">
                                    <template x-for="storage in item.storages" :key="storage">
                                        <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-600 rounded" x-text="storage"></span>
                                    </template>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Condição <span class="text-red-600">*</span></label>
                    <select x-model="form.condition" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="new">Novo</option>
                        <option value="used">Seminovo</option>
                        <option value="refurbished">Refurbished</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Custo <span class="text-red-600">*</span></label>
                    <input type="number" x-model="form.supplier_cost" step="0.01" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Preço Sugerido</label>
                    <input type="number" x-model="form.suggested_price" step="0.01" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Unidades</h2>
                <button type="button" @click="addUnit()" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Adicionar Unidade
                </button>
            </div>
            
            <div class="space-y-4">
                <template x-for="(unit, idx) in units" :key="unit._key">
                    <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm font-semibold text-gray-700">Unidade <span x-text="idx + 1"></span></span>
                            <button type="button" @click="removeUnit(idx)" 
                                    class="text-red-600 hover:text-red-800 text-sm">Remover</button>
                        </div>
                        
                        <div class="grid grid-cols-4 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Cor <span class="text-red-600">*</span></label>
                                <input type="text" :name="'units[' + idx + '][color]'" x-model="unit.color" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-medium text-gray-600 mb-1">IMEI</label>
                                <input type="text" :name="'units[' + idx + '][imei]'" x-model="unit.imei"
                                       @blur="validateImeiSerial(idx)"
                                       class="w-full px-3 py-2 border rounded text-sm font-mono"
                                       :class="unit.imei_error ? 'border-red-500' : 'border-gray-300'">
                                <input type="hidden" :name="'units[' + idx + '][product_name]'" :value="form.name">
                                <input type="hidden" :name="'units[' + idx + '][storage]'" :value="form.storage">
                                <input type="hidden" :name="'units[' + idx + '][condition]'" :value="form.condition">
                                <input type="hidden" :name="'units[' + idx + '][supplier_cost]'" :value="form.supplier_cost">
                                <input type="hidden" :name="'units[' + idx + '][suggested_price]'" :value="form.suggested_price">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Serial</label>
                                <input type="text" :name="'units[' + idx + '][serial_number]'" x-model="unit.serial_number"
                                       @blur="validateImeiSerial(idx)"
                                       class="w-full px-3 py-2 border rounded text-sm font-mono"
                                       :class="unit.serial_error ? 'border-red-500' : 'border-gray-300'">
                            </div>
                        </div>
                        
                        <div x-show="unit.imei_error || unit.serial_error" class="text-xs text-red-600 mt-1" x-text="unit.imei_error || unit.serial_error"></div>
                        
                        <div x-show="form.condition === 'used'" class="grid grid-cols-4 gap-3 mt-3 pt-3 border-t border-gray-200">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Bateria (%)</label>
                                <input type="number" :name="'units[' + idx + '][battery_health]'" x-model="unit.battery_health" min="0" max="100"
                                       class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                            </div>
                            <div class="flex items-center space-x-2 pt-5">
                                <input type="checkbox" :name="'units[' + idx + '][has_box]'" value="1" x-model="unit.has_box" :id="'box_' + idx">
                                <label :for="'box_' + idx" class="text-sm text-gray-700">Caixa</label>
                            </div>
                            <div class="flex items-center space-x-2 pt-5">
                                <input type="checkbox" :name="'units[' + idx + '][has_cable]'" value="1" x-model="unit.has_cable" :id="'cable_' + idx">
                                <label :for="'cable_' + idx" class="text-sm text-gray-700">Cabo</label>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        
        <div class="flex justify-end space-x-4">
            <a href="{{ route('supplier.stock.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit" :disabled="hasErrors() || submitting"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
                    x-text="submitting ? 'Salvando...' : 'Cadastrar Lote'">
            </button>
        </div>
    </form>
</div>

<script>
function batchEntry() {
    return {
        form: { name: '', storage: '', condition: 'new', supplier_cost: '', suggested_price: '', received_at: '{{ now()->toDateString() }}' },
        units: [],
        searchTerm: '',
        searchOpen: false,
        searchResults: [],
        submitting: false,
        _nextKey: 0,
        
        addUnit() {
            this.units.push({ _key: this._nextKey++, color: '', imei: '', serial_number: '', battery_health: '', has_box: false, has_cable: false });
        },
        
        removeUnit(idx) {
            this.units.splice(idx, 1);
        },
        
        selectProduct(item) {
            this.form.name = item.name;
            this.form.storage = item.storages[0] || '';
            this.searchTerm = item.name;
            this.searchOpen = false;
        },
        
        async searchProducts() {
            if (this.searchTerm.length < 2) return;
            try {
                const response = await fetch(`{{ route('supplier.api.products') }}?q=${encodeURIComponent(this.searchTerm)}`);
                this.searchResults = await response.json();
            } catch(e) {
                console.error(e);
            }
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
                
                const response = await fetch(`{{ route('supplier.api.validate-imei') }}?${params}`);
                const data = await response.json();
                
                if (!data.valid) {
                    if (unit.imei) unit.imei_error = data.message;
                    if (unit.serial_number) unit.serial_error = data.message;
                }
            } catch(e) {
                console.error(e);
            }
        },
        
        hasErrors() {
            return this.units.some(u => u.imei_error || u.serial_error) || this.units.length === 0 || !this.form.name;
        },
        
        onSubmit(e) {
            if (this.hasErrors()) {
                e.preventDefault();
                alert('Corrija os erros antes de continuar');
                return false;
            }
            this.submitting = true;
        }
    }
}
</script>
@endsection
