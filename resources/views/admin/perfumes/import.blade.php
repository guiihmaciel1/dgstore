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
        {{-- Upload --}}
        <div class="mb-6 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 bg-pink-50/40">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Selecionar PDF
                </h3>
            </div>
            <div class="p-5">
                <div class="border-2 border-dashed rounded-lg p-8 text-center transition cursor-pointer"
                     :class="importing ? 'border-gray-200 opacity-50 pointer-events-none' : 'border-gray-200 hover:border-pink-400'"
                     @click="$refs.pdfInput.click()">
                    <input type="file" accept=".pdf" class="hidden" x-ref="pdfInput"
                           @change="onFileSelected($event)" :disabled="importing">
                    <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">
                        <span class="text-pink-600 font-medium">Clique para selecionar</span> ou arraste o arquivo
                    </p>
                    <p class="mt-1 text-xs text-gray-400">PDF até 10MB</p>
                    <p x-show="fileName" x-text="fileName" class="mt-3 text-sm font-semibold text-pink-600"></p>
                </div>

                <div class="flex justify-end mt-4">
                    <button type="button" @click="startImport()"
                            :disabled="importing || !file"
                            class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold rounded-lg border transition"
                            :class="importing || !file
                                ? 'bg-gray-200 text-gray-500 border-gray-300 cursor-not-allowed'
                                : 'bg-pink-600 text-white border-pink-600 hover:bg-pink-700 hover:border-pink-700 cursor-pointer'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Importar Produtos
                    </button>
                </div>
            </div>
        </div>

        {{-- Progress --}}
        <div x-show="importing || done" x-transition class="mb-6 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 bg-pink-50/40">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4" :class="importing ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <template x-if="importing">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </template>
                        <template x-if="!importing">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </template>
                    </svg>
                    <span x-text="importing ? 'Importando...' : 'Resultado'"></span>
                </h3>
            </div>
            <div class="p-5">
                {{-- Progress bar --}}
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-sm font-medium text-gray-700" x-text="progressMessage"></span>
                        <span class="text-sm font-bold" :class="hasError ? 'text-red-600' : 'text-pink-600'" x-text="Math.round(progress) + '%'"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="h-3 rounded-full transition-all duration-300 ease-out"
                             :class="hasError ? 'bg-red-500' : 'bg-pink-600'"
                             :style="'width: ' + progress + '%'"></div>
                    </div>
                </div>

                {{-- Counters --}}
                <div x-show="totalProducts > 0" class="flex items-center gap-6 text-sm text-gray-600">
                    <div class="flex items-center gap-1.5">
                        <div class="w-2 h-2 rounded-full bg-pink-600"></div>
                        <span><strong x-text="processedProducts"></strong> de <strong x-text="totalProducts"></strong> produtos</span>
                    </div>
                    <div x-show="processedProducts > 0 && importing" class="flex items-center gap-1.5">
                        <div class="w-2 h-2 rounded-full bg-gray-400 animate-pulse"></div>
                        <span>Lote de 100</span>
                    </div>
                </div>

                {{-- Final result --}}
                <div x-show="done && !hasError" class="mt-4 p-4 bg-emerald-50 border border-emerald-200 rounded-lg">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-sm font-medium text-emerald-800" x-text="resultMessage"></span>
                    </div>
                </div>

                <div x-show="done && hasError" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-medium text-red-800" x-text="resultMessage"></span>
                    </div>
                </div>

                {{-- Import again button --}}
                <div x-show="done" class="mt-4 flex justify-end">
                    <button type="button" @click="reset()"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-pink-600 border border-pink-200 rounded-lg hover:bg-pink-50 transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Nova Importação
                    </button>
                </div>
            </div>
        </div>

        {{-- Info --}}
        <div x-show="!importing && !done" class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <h4 class="text-xs font-semibold text-blue-800">Como funciona</h4>
            <p class="text-xs text-blue-600 mt-1">O sistema extrai o texto do PDF e identifica os produtos automaticamente. Todos os produtos existentes serão substituídos pelos novos. O processamento é feito em lotes de 100 para maior velocidade.</p>
        </div>

        {{-- Zerar produtos --}}
        <div x-data="clearProducts()" x-show="!importing" class="mt-6 bg-white rounded-xl border border-red-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-semibold text-gray-700">Zerar Base de Produtos</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Remove todos os produtos importados. Esta ação não pode ser desfeita.</p>
                </div>
                <button type="button" @click="confirmClear()"
                        :disabled="clearing"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg border transition"
                        :class="clearing
                            ? 'bg-gray-200 text-gray-500 border-gray-300 cursor-not-allowed'
                            : 'bg-red-600 text-white border-red-600 hover:bg-red-700 cursor-pointer'">
                    <svg x-show="!clearing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    <svg x-show="clearing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <span x-text="clearing ? 'Limpando...' : 'Zerar Produtos'"></span>
                </button>
            </div>
            <div x-show="clearMessage" x-transition class="px-5 pb-4">
                <div class="p-3 rounded-lg text-sm font-medium"
                     :class="clearSuccess ? 'bg-emerald-50 border border-emerald-200 text-emerald-800' : 'bg-red-50 border border-red-200 text-red-800'"
                     x-text="clearMessage"></div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function importPerfumes() {
            return {
                file: null,
                fileName: '',
                importing: false,
                done: false,
                hasError: false,
                progress: 0,
                progressMessage: '',
                totalProducts: 0,
                processedProducts: 0,
                resultMessage: '',
                _pollTimer: null,

                onFileSelected(event) {
                    this.file = event.target.files[0] || null;
                    this.fileName = this.file ? this.file.name : '';
                    this.done = false;
                    this.hasError = false;
                    this.resultMessage = '';
                },

                async startImport() {
                    if (!this.file || this.importing) return;

                    this.importing = true;
                    this.done = false;
                    this.hasError = false;
                    this.progress = 0;
                    this.progressMessage = 'Iniciando...';
                    this.totalProducts = 0;
                    this.processedProducts = 0;
                    this.resultMessage = '';

                    this.startPolling();

                    try {
                        const formData = new FormData();
                        formData.append('pdf_file', this.file);

                        const response = await fetch('{{ route("admin.perfumes.import.store") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: formData,
                        });

                        const data = await response.json();

                        this.stopPolling();

                        if (data.success) {
                            this.progress = 100;
                            this.progressMessage = 'Concluído!';
                            this.resultMessage = data.message;
                            this.totalProducts = data.total || 0;
                            this.processedProducts = data.total || 0;
                        } else {
                            this.hasError = true;
                            this.progress = 100;
                            this.progressMessage = 'Erro';
                            this.resultMessage = data.message || 'Erro desconhecido.';
                        }
                    } catch (error) {
                        this.stopPolling();
                        this.hasError = true;
                        this.progress = 100;
                        this.progressMessage = 'Erro';
                        this.resultMessage = 'Erro de conexão. Tente novamente.';
                        console.error(error);
                    } finally {
                        this.importing = false;
                        this.done = true;
                    }
                },

                startPolling() {
                    this._pollTimer = setInterval(() => this.fetchProgress(), 500);
                },

                stopPolling() {
                    if (this._pollTimer) {
                        clearInterval(this._pollTimer);
                        this._pollTimer = null;
                    }
                },

                async fetchProgress() {
                    try {
                        const res = await fetch('{{ route("admin.perfumes.import.progress") }}');
                        const data = await res.json();

                        if (data.status !== 'idle' && data.status !== 'done') {
                            this.progress = data.progress;
                            this.progressMessage = data.message;
                            this.totalProducts = data.total || this.totalProducts;
                            this.processedProducts = data.processed || this.processedProducts;
                        }
                    } catch (e) {
                        // Ignora erros de polling silenciosamente
                    }
                },

                reset() {
                    this.file = null;
                    this.fileName = '';
                    this.importing = false;
                    this.done = false;
                    this.hasError = false;
                    this.progress = 0;
                    this.progressMessage = '';
                    this.totalProducts = 0;
                    this.processedProducts = 0;
                    this.resultMessage = '';
                    if (this.$refs.pdfInput) {
                        this.$refs.pdfInput.value = '';
                    }
                },
            };
        }

        function clearProducts() {
            return {
                clearing: false,
                clearMessage: '',
                clearSuccess: false,

                async confirmClear() {
                    if (!confirm('Tem certeza que deseja ZERAR todos os produtos? Esta ação não pode ser desfeita.')) {
                        return;
                    }
                    this.clearing = true;
                    this.clearMessage = '';
                    try {
                        const res = await fetch('{{ route("admin.perfumes.import.clear") }}', {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                        });
                        const data = await res.json();
                        this.clearSuccess = data.success;
                        this.clearMessage = data.message;
                    } catch (e) {
                        this.clearSuccess = false;
                        this.clearMessage = 'Erro ao limpar produtos. Tente novamente.';
                    } finally {
                        this.clearing = false;
                    }
                },
            };
        }
    </script>
    @endpush
</x-perfumes-admin-layout>
