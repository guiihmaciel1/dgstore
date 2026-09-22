<x-app-layout>
    <x-slot name="title">Importar Perfume</x-slot>
    <div class="py-6">
        <div class="px-6 lg:px-8 max-w-2xl" x-data="fragranceImporter()">
            <div x-show="errorMsg" x-cloak
                 style="margin-bottom: 1rem; padding: 1rem; background: rgba(239,68,68,0.1); border: 1px solid #fca5a5; border-radius: 0.5rem; color: #fca5a5;">
                <span x-text="errorMsg"></span>
            </div>

            <div class="mb-6">
                <a href="{{ route('fragrances.index') }}" class="text-sm text-dg-500 hover:text-dg-300 transition">← Voltar para Perfumaria</a>
            </div>

            <div style="background: #141414; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06); overflow: hidden;">
                <div class="p-6 border-b border-border">
                    <h2 class="text-lg font-bold text-dg-100">Importar Perfume do Fragrantica</h2>
                    <p class="text-sm text-dg-500 mt-1">Cole a URL do perfume no Fragrantica e o sistema preencherá automaticamente todas as informações.</p>
                </div>

                <div class="p-6">
                    {{-- Step 1: URL --}}
                    <div class="mb-6">
                        <label for="url" class="block text-sm font-medium text-dg-300 mb-2">URL do Fragrantica</label>
                        <input type="url" id="url" x-model="url"
                               placeholder="https://www.fragrantica.com.br/perfume/Lattafa-Perfumes/Asad-72821.html"
                               class="w-full px-4 py-3 border border-border-strong rounded-lg text-sm focus:border-pink-500 focus:ring-1 focus:ring-pink-500 focus:outline-none bg-surface-raised text-dg-100 placeholder-dg-600"
                               :disabled="loading">
                        <p class="mt-2 text-xs text-dg-600">
                            Acesse <a href="https://www.fragrantica.com.br" target="_blank" class="text-pink-400 hover:text-pink-300">fragrantica.com.br</a>,
                            busque o perfume e copie a URL da página dele.
                        </p>
                    </div>

                    {{-- Código-fonte da página --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-dg-300 mb-2">Código-fonte da página</label>
                        <p class="text-xs text-dg-500 mb-2">
                            Abra a URL acima em outra aba, pressione
                            <kbd class="px-1.5 py-0.5 bg-surface-overlay rounded text-dg-300 font-mono text-[11px]">Ctrl+U</kbd>,
                            selecione tudo
                            <kbd class="px-1.5 py-0.5 bg-surface-overlay rounded text-dg-300 font-mono text-[11px]">Ctrl+A</kbd>
                            e cole aqui
                            <kbd class="px-1.5 py-0.5 bg-surface-overlay rounded text-dg-300 font-mono text-[11px]">Ctrl+V</kbd>
                        </p>
                        <textarea x-model="manualHtml" rows="6"
                                  placeholder="Cole aqui o código-fonte da página do Fragrantica..."
                                  class="w-full px-4 py-3 border border-border-strong rounded-lg text-xs font-mono focus:border-pink-500 focus:ring-1 focus:ring-pink-500 focus:outline-none bg-surface-raised text-dg-100 placeholder-dg-600 resize-y"
                                  :disabled="loading"></textarea>
                        <p class="mt-1 text-xs text-dg-600">
                            <span x-show="manualHtml.length > 0" x-text="(manualHtml.length).toLocaleString('pt-BR') + ' caracteres'"></span>
                        </p>
                    </div>

                    {{-- Status --}}
                    <div x-show="status" x-cloak class="mb-4 flex items-center gap-2 text-sm text-dg-400">
                        <svg x-show="loading" class="animate-spin w-4 h-4 text-pink-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span x-text="status"></span>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" @click="importFragrance()" :disabled="loading || !url"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-pink-600 text-white font-semibold rounded-lg hover:bg-pink-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <svg x-show="loading" x-cloak class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span x-text="loading ? 'Importando...' : 'Importar do Fragrantica'"></span>
                        </button>
                        <a href="{{ route('fragrances.index') }}" class="text-sm text-dg-500 hover:text-dg-300 transition">Cancelar</a>
                    </div>
                </div>
            </div>

            <div class="mt-6 p-4 bg-surface-overlay rounded-lg border border-border">
                <h3 class="text-sm font-semibold text-dg-300 mb-2">O que será importado:</h3>
                <ul class="text-xs text-dg-500 space-y-1">
                    <li>• Foto, nome, marca e gênero do perfume</li>
                    <li>• Descrição completa e ano de lançamento</li>
                    <li>• Principais acordes com barras coloridas</li>
                    <li>• Pirâmide olfativa (notas de topo, coração e base)</li>
                    <li>• Avaliação dos usuários e número de votos</li>
                    <li>• Recomendação de estações e horário (dia/noite)</li>
                </ul>
                <p class="text-xs text-dg-600 mt-2">Após importar, defina o preço de venda e quantidade em estoque.</p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function fragranceImporter() {
        return {
            url: '',
            loading: false,
            status: '',
            errorMsg: '',
            manualHtml: '',

            async importFragrance() {
                if (!this.url) return;

                const urlPattern = /^https?:\/\/(www\.)?fragrantica\.com(\.\w+)?\/perfume\/.+-\d+\.html$/i;
                if (!urlPattern.test(this.url)) {
                    this.errorMsg = 'URL inválida. Use o formato: https://www.fragrantica.com.br/perfume/Marca/Nome-12345.html';
                    return;
                }

                if (this.manualHtml.length < 1000) {
                    this.errorMsg = 'Cole o código-fonte da página do Fragrantica no campo acima.';
                    return;
                }

                this.loading = true;
                this.errorMsg = '';
                this.status = 'Processando perfume...';

                try {
                    const response = await fetch('{{ route("fragrances.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ url: this.url, html: this.manualHtml }),
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.status = 'Perfume importado! Redirecionando...';
                        window.location.href = data.redirect;
                    } else {
                        this.errorMsg = data.message || 'Erro ao importar perfume.';
                        this.loading = false;
                        this.status = '';
                    }
                } catch (err) {
                    this.errorMsg = 'Erro: ' + (err.message || 'Falha na importação');
                    this.loading = false;
                    this.status = '';
                }
            },
        };
    }
    </script>
    @endpush
</x-app-layout>
