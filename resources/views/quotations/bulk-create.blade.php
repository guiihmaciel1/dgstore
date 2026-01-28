<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('quotations.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Cadastro Rápido de Cotações
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form method="POST" action="{{ route('quotations.bulk-store') }}" x-data="bulkQuotationForm()">
                    @csrf
                    
                    <div class="space-y-6">
                        <!-- Fornecedor e Data -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                            <div>
                                <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Fornecedor <span class="text-red-500">*</span>
                                </label>
                                <select name="supplier_id" id="supplier_id" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Selecione um fornecedor</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="quoted_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Data da Cotação <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="quoted_at" id="quoted_at" required
                                       value="{{ date('Y-m-d') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('quoted_at')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Lista de Cotações -->
                        <div>
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Produtos</h3>
                                <button type="button" @click="addRow()"
                                        class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Adicionar Linha
                                </button>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-gray-200 dark:border-gray-700">
                                            <th class="text-left py-2 px-1 font-medium text-gray-700 dark:text-gray-300" style="width: 35%;">Produto *</th>
                                            <th class="text-left py-2 px-1 font-medium text-gray-700 dark:text-gray-300" style="width: 18%;">Preço Unit. *</th>
                                            <th class="text-left py-2 px-1 font-medium text-gray-700 dark:text-gray-300" style="width: 12%;">Qtd</th>
                                            <th class="text-left py-2 px-1 font-medium text-gray-700 dark:text-gray-300" style="width: 10%;">Un</th>
                                            <th class="text-left py-2 px-1 font-medium text-gray-700 dark:text-gray-300" style="width: 20%;">Obs</th>
                                            <th class="py-2 px-1" style="width: 5%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(row, index) in rows" :key="index">
                                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                                <td class="py-2 px-1">
                                                    <input type="text" 
                                                           :name="`quotations[${index}][product_name]`"
                                                           x-model="row.product_name"
                                                           required
                                                           placeholder="Nome do produto"
                                                           class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                </td>
                                                <td class="py-2 px-1">
                                                    <div class="relative">
                                                        <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-400 text-xs">R$</span>
                                                        <input type="number" 
                                                               :name="`quotations[${index}][unit_price]`"
                                                               x-model="row.unit_price"
                                                               required
                                                               step="0.01" min="0.01"
                                                               placeholder="0,00"
                                                               class="w-full pl-8 text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                    </div>
                                                </td>
                                                <td class="py-2 px-1">
                                                    <input type="number" 
                                                           :name="`quotations[${index}][quantity]`"
                                                           x-model="row.quantity"
                                                           step="0.01" min="0.01"
                                                           placeholder="1"
                                                           class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                </td>
                                                <td class="py-2 px-1">
                                                    <select :name="`quotations[${index}][unit]`"
                                                            x-model="row.unit"
                                                            class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                        <option value="un">un</option>
                                                        <option value="cx">cx</option>
                                                        <option value="kg">kg</option>
                                                        <option value="pç">pç</option>
                                                        <option value="par">par</option>
                                                    </select>
                                                </td>
                                                <td class="py-2 px-1">
                                                    <input type="text" 
                                                           :name="`quotations[${index}][notes]`"
                                                           x-model="row.notes"
                                                           placeholder="Observação"
                                                           class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                </td>
                                                <td class="py-2 px-1 text-center">
                                                    <button type="button" @click="removeRow(index)" 
                                                            x-show="rows.length > 1"
                                                            class="text-red-500 hover:text-red-700">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            @error('quotations')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Resumo -->
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    Total de itens: <strong x-text="rows.length" class="text-gray-900 dark:text-gray-100"></strong>
                                </span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    Valor total estimado: 
                                    <strong class="text-green-600 dark:text-green-400" x-text="formatCurrency(totalValue())"></strong>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end gap-4">
                        <a href="{{ route('quotations.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition font-medium">
                            Salvar Todas as Cotações
                        </button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>

    <script>
        function bulkQuotationForm() {
            return {
                rows: [
                    { product_name: '', unit_price: '', quantity: 1, unit: 'un', notes: '' }
                ],

                addRow() {
                    this.rows.push({ product_name: '', unit_price: '', quantity: 1, unit: 'un', notes: '' });
                },

                removeRow(index) {
                    if (this.rows.length > 1) {
                        this.rows.splice(index, 1);
                    }
                },

                totalValue() {
                    return this.rows.reduce((sum, row) => {
                        const price = parseFloat(row.unit_price) || 0;
                        const qty = parseFloat(row.quantity) || 1;
                        return sum + (price * qty);
                    }, 0);
                },

                formatCurrency(value) {
                    return new Intl.NumberFormat('pt-BR', {
                        style: 'currency',
                        currency: 'BRL'
                    }).format(value);
                }
            }
        }
    </script>
</x-app-layout>
