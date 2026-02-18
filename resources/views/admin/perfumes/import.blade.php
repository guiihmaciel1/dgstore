<x-perfumes-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">Importar Produtos via PDF</h2>
            <a href="{{ route('admin.perfumes.products.index') }}"
               class="text-sm text-pink-600 hover:text-pink-700 font-medium">
                Voltar aos Produtos
            </a>
        </div>
    </x-slot>

    <div x-data="importPerfumes()">
        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800">
                <ul class="list-disc pl-5 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Etapa 1: Upload PDF --}}
        <div class="mb-6 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 bg-pink-50/40">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    1. Selecionar PDF
                </h3>
            </div>
            <div class="p-5">
                <div class="border-2 border-dashed border-gray-200 rounded-lg p-8 text-center hover:border-pink-400 transition cursor-pointer"
                     @click="$refs.pdfInput.click()">
                    <input type="file" accept=".pdf" class="hidden" x-ref="pdfInput"
                           @change="onFileSelected($event)">
                    <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">
                        <span class="text-pink-600 font-medium">Clique para selecionar</span> ou arraste o arquivo
                    </p>
                    <p class="mt-1 text-xs text-gray-400">PDF até 10MB</p>
                    <p x-show="fileName" x-text="fileName" class="mt-3 text-sm font-semibold text-pink-600"></p>
                </div>

                <div class="flex justify-between items-center mt-4 flex-wrap gap-3">
                    <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" x-model="forceRegex"
                               class="w-3.5 h-3.5 rounded border-gray-300 text-pink-600 focus:ring-pink-500 cursor-pointer">
                        <span class="text-xs text-gray-500">Usar apenas regex (sem IA)</span>
                    </label>

                    <button type="button" @click="analyzePdf()"
                            :disabled="loading || !file"
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-lg transition"
                            :class="loading || !file
                                ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                : 'bg-gradient-to-r from-pink-600 to-rose-500 text-white hover:from-pink-500 hover:to-rose-400 shadow-md shadow-pink-500/20 cursor-pointer'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             :class="loading ? 'animate-spin' : ''">
                            <template x-if="!loading">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </template>
                            <template x-if="loading">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </template>
                        </svg>
                        <span x-text="loading ? 'Analisando...' : 'Analisar PDF'"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mensagem --}}
        <template x-if="message">
            <div class="mb-6 p-4 rounded-xl text-sm border"
                 :class="messageType === 'error'
                    ? 'bg-red-50 border-red-200 text-red-800'
                    : 'bg-emerald-50 border-emerald-200 text-emerald-800'">
                <div class="flex items-center gap-2 flex-wrap">
                    <span x-text="message"></span>
                    <template x-if="parserUsed && messageType !== 'error'">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                              :class="parserUsed === 'ai' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700'"
                              x-text="parserUsed === 'ai' ? 'via IA' : 'via Regex'"></span>
                    </template>
                </div>
            </div>
        </template>

        {{-- Etapa 2: Preview e Confirmação --}}
        <template x-if="items.length > 0">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 bg-pink-50/40 flex justify-between items-center">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        2. Revisar e Importar
                        <span class="font-normal normal-case tracking-normal text-gray-400" x-text="'(' + selectedCount + ' de ' + items.length + ' selecionados)'"></span>
                    </h3>
                    <div class="flex gap-2">
                        <button type="button" @click="selectAll()"
                                class="px-3 py-1.5 text-xs border border-gray-200 rounded-lg bg-white cursor-pointer text-gray-600 hover:bg-gray-50 transition">
                            Selecionar Todos
                        </button>
                        <button type="button" @click="deselectAll()"
                                class="px-3 py-1.5 text-xs border border-gray-200 rounded-lg bg-white cursor-pointer text-gray-600 hover:bg-gray-50 transition">
                            Desmarcar Todos
                        </button>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.perfumes.import.store') }}">
                    @csrf

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-pink-50/40">
                                <tr>
                                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-10"></th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nome</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Marca</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cód. Barras</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">ML</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Preço US$</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Categoria</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="hover:bg-gray-50 transition"
                                        :class="item.selected ? '' : 'opacity-40'">
                                        <td class="px-5 py-3 text-center">
                                            <input type="checkbox" :checked="item.selected" @change="item.selected = $event.target.checked"
                                                   class="w-4 h-4 rounded border-gray-300 text-pink-600 focus:ring-pink-500 cursor-pointer">
                                            <input type="hidden" :name="'items[' + index + '][selected]'" :value="item.selected ? 1 : 0">
                                        </td>
                                        <td class="px-5 py-3">
                                            <input type="text" x-model="item.name"
                                                   :name="'items[' + index + '][name]'"
                                                   class="w-full rounded-lg border border-gray-200 px-2.5 py-1.5 text-sm focus:border-pink-500 focus:ring-pink-500 min-w-[200px]">
                                        </td>
                                        <td class="px-5 py-3">
                                            <input type="text" x-model="item.brand"
                                                   :name="'items[' + index + '][brand]'"
                                                   class="w-full rounded-lg border border-gray-200 px-2.5 py-1.5 text-sm focus:border-pink-500 focus:ring-pink-500 min-w-[100px]">
                                        </td>
                                        <td class="px-5 py-3">
                                            <input type="text" x-model="item.barcode"
                                                   :name="'items[' + index + '][barcode]'"
                                                   class="w-full rounded-lg border border-gray-200 px-2.5 py-1.5 text-sm font-mono focus:border-pink-500 focus:ring-pink-500 min-w-[130px]">
                                        </td>
                                        <td class="px-5 py-3 text-center">
                                            <input type="text" x-model="item.size_ml"
                                                   :name="'items[' + index + '][size_ml]'"
                                                   class="w-16 rounded-lg border border-gray-200 px-2.5 py-1.5 text-sm text-center focus:border-pink-500 focus:ring-pink-500">
                                        </td>
                                        <td class="px-5 py-3 text-right">
                                            <input type="number" x-model.number="item.sale_price" step="0.01" min="0"
                                                   :name="'items[' + index + '][sale_price]'"
                                                   class="w-24 rounded-lg border border-gray-200 px-2.5 py-1.5 text-sm text-right focus:border-pink-500 focus:ring-pink-500">
                                        </td>
                                        <td class="px-5 py-3 text-center">
                                            <select x-model="item.category"
                                                    :name="'items[' + index + '][category]'"
                                                    class="rounded-lg border border-gray-200 px-2.5 py-1.5 text-sm focus:border-pink-500 focus:ring-pink-500">
                                                <option value="masculino">Masculino</option>
                                                <option value="feminino">Feminino</option>
                                                <option value="unissex">Unissex</option>
                                            </select>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="px-5 py-4 border-t border-gray-100 flex justify-between items-center">
                        <p class="text-sm text-gray-500">
                            <span x-text="selectedCount"></span> produtos selecionados
                        </p>
                        <button type="submit" :disabled="selectedCount === 0"
                                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-lg transition"
                                :class="selectedCount === 0
                                    ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                    : 'bg-gradient-to-r from-pink-600 to-rose-500 text-white hover:from-pink-500 hover:to-rose-400 shadow-md shadow-pink-500/20 cursor-pointer'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Importar Produtos
                        </button>
                    </div>
                </form>
            </div>
        </template>

        {{-- Info --}}
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
            <h4 class="text-xs font-semibold text-blue-800">Como funciona</h4>
            <p class="text-xs text-blue-600 mt-1">O sistema extrai o texto do PDF e usa IA (Gemini) para identificar os produtos. Se a IA estiver indisponível, usa regex como fallback. Revise os dados antes de confirmar a importação.</p>
        </div>
    </div>

    @push('scripts')
    <script>
        function importPerfumes() {
            return {
                file: null,
                fileName: '',
                items: [],
                loading: false,
                message: '',
                messageType: '',
                forceRegex: false,
                parserUsed: '',

                onFileSelected(event) {
                    this.file = event.target.files[0] || null;
                    this.fileName = this.file ? this.file.name : '';
                    this.items = [];
                    this.message = '';
                },

                async analyzePdf() {
                    if (!this.file) return;

                    this.loading = true;
                    this.message = '';
                    this.items = [];
                    this.parserUsed = '';

                    try {
                        const formData = new FormData();
                        formData.append('pdf_file', this.file);
                        if (this.forceRegex) {
                            formData.append('force_regex', '1');
                        }

                        const response = await fetch('{{ route("admin.perfumes.import.preview") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: formData,
                        });

                        const data = await response.json();
                        this.parserUsed = data.parser_used || '';

                        if (data.success) {
                            this.items = data.items.map(item => ({
                                ...item,
                                selected: true,
                            }));
                            this.message = data.message;
                            this.messageType = 'success';
                        } else {
                            this.message = data.message || 'Nenhum produto encontrado no PDF.';
                            this.messageType = 'error';
                        }
                    } catch (error) {
                        this.message = 'Erro ao analisar o PDF. Tente novamente.';
                        this.messageType = 'error';
                        console.error(error);
                    } finally {
                        this.loading = false;
                    }
                },

                get selectedCount() {
                    return this.items.filter(i => i.selected).length;
                },

                selectAll() {
                    this.items.forEach(i => i.selected = true);
                },

                deselectAll() {
                    this.items.forEach(i => i.selected = false);
                },
            };
        }
    </script>
    @endpush
</x-perfumes-admin-layout>
