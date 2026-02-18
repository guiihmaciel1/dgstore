<x-perfumes-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800">Importar Produtos via PDF</h2>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Upload de PDF</h3>
                <p class="text-sm text-gray-500 mt-1">Envie um arquivo PDF com a lista de perfumes. O sistema tentará extrair nome, marca, tamanho e preço automaticamente.</p>
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

                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="px-6 py-2.5 bg-pink-600 text-white text-sm font-semibold rounded-lg hover:bg-pink-700 transition">
                        Importar
                    </button>
                    <a href="{{ route('admin.perfumes.products.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                        Voltar aos Produtos
                    </a>
                </div>
            </form>
        </div>

        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
            <h4 class="text-sm font-semibold text-blue-800">Formato esperado do PDF</h4>
            <p class="text-xs text-blue-600 mt-1">O sistema tenta identificar automaticamente: Nome do perfume, Marca, Tamanho (ml) e Preço (R$). Cada produto deve estar em uma linha separada. Após a importação, revise os dados na lista de produtos.</p>
        </div>
    </div>
</x-perfumes-admin-layout>
