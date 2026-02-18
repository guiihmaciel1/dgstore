<x-perfumes-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900">Importar Produtos via PDF</h2>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Upload de PDF</h3>
                <p class="text-sm text-gray-500 mt-1">Envie um arquivo PDF com a lista de perfumes. O sistema usa IA (Gemini) para extrair nome, marca, código de barras, tamanho, preço e categoria automaticamente.</p>
            </div>

            <form action="{{ route('admin.perfumes.import.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div x-data="{ fileName: '' }" class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Arquivo PDF</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-pink-400 transition">
                        <input type="file" name="pdf_file" accept=".pdf" required
                               class="hidden" id="pdf-file"
                               @change="fileName = $event.target.files[0]?.name || ''">
                        <label for="pdf-file" class="cursor-pointer">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">
                                <span class="text-pink-600 font-medium">Clique para selecionar</span> ou arraste o arquivo
                            </p>
                            <p class="mt-1 text-xs text-gray-400">PDF até 10MB</p>
                        </label>
                        <p x-show="fileName" x-text="fileName" class="mt-3 text-sm font-medium text-pink-600"></p>
                    </div>
                    @error('pdf_file')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-4 mt-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-pink-600 bg-gradient-to-r from-pink-600 to-rose-500 text-white text-sm font-bold rounded-lg hover:from-pink-500 hover:to-rose-400 shadow-lg shadow-pink-500/25 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Importar PDF
                    </button>
                    <a href="{{ route('admin.perfumes.products.index') }}" class="text-sm text-gray-500 hover:text-pink-600 font-medium">
                        Voltar aos Produtos
                    </a>
                </div>
            </form>
        </div>

        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4 space-y-3">
            <h4 class="text-sm font-semibold text-blue-800">Como funciona</h4>
            <p class="text-xs text-blue-600">O sistema extrai o texto do PDF e envia para a IA (Gemini) analisar. A IA identifica automaticamente os produtos independente do formato da lista, extraindo: nome, marca, código de barras, tamanho (ML), preço (US$) e categoria (masculino/feminino/unissex).</p>
            <p class="text-xs text-blue-600">Produtos com o mesmo código de barras já existentes no sistema serão atualizados. Novos produtos serão criados.</p>
            <p class="text-xs text-blue-500 italic">Após a importação, revise os dados na lista de produtos.</p>
        </div>
    </div>
</x-perfumes-admin-layout>
