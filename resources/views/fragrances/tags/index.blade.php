<x-app-layout>
    <x-slot name="title">Tags de Perfumaria</x-slot>
    <div class="py-6">
        <div class="px-6 lg:px-8 max-w-4xl">
            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: rgba(16,185,129,0.1); border: 1px solid #a7f3d0; border-radius: 0.5rem; color: #6ee7b7;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-lg font-bold text-dg-100">Tags de Perfumaria</h1>
                    <p class="text-sm text-dg-500 mt-0.5">Organize os perfumes em categorias para o catálogo público.</p>
                </div>
                <a href="{{ route('fragrances.index') }}" class="text-sm text-dg-500 hover:text-dg-300 transition">← Voltar</a>
            </div>

            {{-- Formulário nova tag --}}
            <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06);" class="p-6 mb-6">
                <h3 class="text-sm font-semibold text-dg-300 mb-4">Nova Tag</h3>
                <form method="POST" action="{{ route('fragrance-tags.store') }}" class="flex flex-wrap items-end gap-3">
                    @csrf
                    <div class="flex-1 min-w-[150px]">
                        <label class="block text-xs text-dg-500 mb-1">Nome</label>
                        <input type="text" name="name" required placeholder="Ex: Árabe"
                               class="w-full px-3 py-2 border border-border-strong rounded-lg text-sm bg-surface-raised focus:border-pink-500 focus:outline-none">
                    </div>
                    <div class="w-20">
                        <label class="block text-xs text-dg-500 mb-1">Cor</label>
                        <input type="color" name="color" value="#ec4899"
                               class="w-full h-[38px] rounded-lg border border-border-strong bg-surface-raised cursor-pointer">
                    </div>
                    <div class="w-20">
                        <label class="block text-xs text-dg-500 mb-1">Ícone</label>
                        <input type="text" name="icon" placeholder="🕌" maxlength="10"
                               class="w-full px-3 py-2 border border-border-strong rounded-lg text-sm bg-surface-raised focus:border-pink-500 focus:outline-none text-center">
                    </div>
                    <div class="w-20">
                        <label class="block text-xs text-dg-500 mb-1">Ordem</label>
                        <input type="number" name="sort_order" value="0" min="0"
                               class="w-full px-3 py-2 border border-border-strong rounded-lg text-sm bg-surface-raised focus:border-pink-500 focus:outline-none">
                    </div>
                    <button type="submit"
                            class="px-4 py-2 bg-pink-600 text-white text-sm font-semibold rounded-lg hover:bg-pink-700 transition">
                        Criar
                    </button>
                </form>
            </div>

            {{-- Lista de tags --}}
            <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06);" class="overflow-hidden">
                @if($tags->isEmpty())
                    <div class="p-8 text-center text-dg-500 text-sm">Nenhuma tag criada ainda.</div>
                @else
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="border-b border-border text-xs text-dg-500 uppercase">
                                <th class="px-6 py-3">Tag</th>
                                <th class="px-6 py-3 text-center">Perfumes</th>
                                <th class="px-6 py-3 text-center">Ordem</th>
                                <th class="px-6 py-3 text-center">Status</th>
                                <th class="px-6 py-3 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tags as $tag)
                                <tr class="border-b border-border/50 hover:bg-surface-overlay/50 transition">
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-4 h-4 rounded-full" style="background: {{ $tag->color }};"></div>
                                            <span class="text-dg-200 font-medium">
                                                @if($tag->icon) {{ $tag->icon }} @endif
                                                {{ $tag->name }}
                                            </span>
                                            <span class="text-xs text-dg-600">({{ $tag->slug }})</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 text-center text-dg-400">{{ $tag->products_count }}</td>
                                    <td class="px-6 py-3 text-center text-dg-400">{{ $tag->sort_order }}</td>
                                    <td class="px-6 py-3 text-center">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs {{ $tag->active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                            {{ $tag->active ? 'Ativa' : 'Inativa' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            {{-- Inline edit via modal/form --}}
                                            <form method="POST" action="{{ route('fragrance-tags.update', $tag) }}" class="flex items-center gap-1" x-data="{ editing: false }">
                                                @csrf @method('PUT')
                                                <template x-if="editing">
                                                    <div class="flex items-center gap-1">
                                                        <input type="text" name="name" value="{{ $tag->name }}" class="w-24 px-2 py-1 border border-border-strong rounded text-xs bg-surface-raised focus:border-pink-500 focus:outline-none">
                                                        <input type="color" name="color" value="{{ $tag->color }}" class="w-8 h-7 rounded border border-border-strong bg-surface-raised cursor-pointer">
                                                        <input type="text" name="icon" value="{{ $tag->icon }}" class="w-10 px-1 py-1 border border-border-strong rounded text-xs bg-surface-raised text-center" maxlength="10">
                                                        <input type="number" name="sort_order" value="{{ $tag->sort_order }}" class="w-12 px-1 py-1 border border-border-strong rounded text-xs bg-surface-raised" min="0">
                                                        <input type="hidden" name="active" value="{{ $tag->active ? '1' : '0' }}">
                                                        <button type="submit" class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">✓</button>
                                                        <button type="button" @click="editing = false" class="px-2 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700">✕</button>
                                                    </div>
                                                </template>
                                                <template x-if="!editing">
                                                    <button type="button" @click="editing = true" class="text-xs text-dg-500 hover:text-dg-300 transition">Editar</button>
                                                </template>
                                            </form>

                                            {{-- Toggle ativo --}}
                                            <form method="POST" action="{{ route('fragrance-tags.update', $tag) }}">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="name" value="{{ $tag->name }}">
                                                <input type="hidden" name="color" value="{{ $tag->color }}">
                                                <input type="hidden" name="sort_order" value="{{ $tag->sort_order }}">
                                                <input type="hidden" name="active" value="{{ $tag->active ? '0' : '1' }}">
                                                <button type="submit" class="text-xs {{ $tag->active ? 'text-amber-500 hover:text-amber-400' : 'text-green-500 hover:text-green-400' }} transition">
                                                    {{ $tag->active ? 'Desativar' : 'Ativar' }}
                                                </button>
                                            </form>

                                            {{-- Delete --}}
                                            <form method="POST" action="{{ route('fragrance-tags.destroy', $tag) }}">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs text-red-500 hover:text-red-400 transition"
                                                        onclick="return confirm('Remover tag \'{{ $tag->name }}\'? Os perfumes não serão excluídos.')">
                                                    Excluir
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
