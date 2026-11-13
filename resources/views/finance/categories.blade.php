<x-app-layout>
    <x-slot name="title">Categorias Financeiras</x-slot>
    <div class="py-6">
        <div class="px-4 sm:px-6 lg:px-8" x-data="{ showForm: false, newType: 'expense' }">
            <x-flash class="mb-4" />

            <x-page-header title="Categorias" subtitle="Organize receitas e despesas por categoria">
                <x-slot name="actions">
                    <button type="button" @click="showForm = !showForm" class="dg-btn-primary">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Nova categoria
                    </button>
                </x-slot>
            </x-page-header>

            <!-- Form Nova Categoria -->
            <div x-show="showForm" x-cloak class="dg-card-flat mb-6 p-5">
                <form method="POST" action="{{ route('finance.categories.store') }}" class="grid grid-cols-1 items-end gap-4 sm:grid-cols-4">
                    @csrf
                    <div>
                        <label for="cat-name" class="dg-label">Nome *</label>
                        <input id="cat-name" type="text" name="name" required placeholder="Ex: Material de escritório" class="dg-input py-2.5">
                    </div>
                    <div>
                        <label for="cat-type" class="dg-label">Tipo *</label>
                        <select id="cat-type" name="type" x-model="newType" class="dg-select py-2.5">
                            <option value="expense">Despesa</option>
                            <option value="income">Receita</option>
                        </select>
                    </div>
                    <div>
                        <label for="cat-color" class="dg-label">Cor</label>
                        <input id="cat-color" type="color" name="color" value="#6b7280" class="h-[2.625rem] w-full cursor-pointer rounded-xl border border-border bg-surface-overlay p-1">
                    </div>
                    <button type="submit" class="dg-btn-primary py-2.5">Criar</button>
                </form>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                @foreach([
                    ['type' => 'income', 'label' => 'Receitas', 'tone' => 'text-emerald-400', 'dot' => 'bg-emerald-400'],
                    ['type' => 'expense', 'label' => 'Despesas', 'tone' => 'text-red-300', 'dot' => 'bg-red-400'],
                ] as $group)
                    @php $items = $categories->where('type.value', $group['type']); @endphp
                    <section class="dg-card-flat overflow-hidden" aria-labelledby="cat-{{ $group['type'] }}">
                        <h2 id="cat-{{ $group['type'] }}" class="flex items-center gap-2 border-b border-border px-5 py-3.5 text-[0.9375rem] font-semibold {{ $group['tone'] }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $group['dot'] }}" aria-hidden="true"></span>
                            {{ $group['label'] }}
                            <span class="ml-auto text-xs font-medium text-dg-500">{{ $items->count() }}</span>
                        </h2>
                        <ul class="divide-y divide-border-subtle">
                            @forelse($items as $cat)
                                <li class="flex items-center justify-between gap-3 px-5 py-3">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <span class="h-3 w-3 flex-shrink-0 rounded-full" style="background: {{ $cat->color ?? '#6b7280' }};" aria-hidden="true"></span>
                                        <span class="truncate text-sm font-medium text-dg-100">{{ $cat->name }}</span>
                                        @if($cat->is_system)
                                            <span class="rounded-full bg-surface-elevated px-1.5 py-px text-[0.625rem] font-medium uppercase tracking-wide text-dg-400">Sistema</span>
                                        @endif
                                    </div>
                                    @if(!$cat->is_system)
                                        <form method="POST" action="{{ route('finance.categories.destroy', $cat) }}" onsubmit="return confirm('Remover esta categoria?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-md px-2 py-1 text-xs font-medium text-dg-400 transition-colors hover:bg-red-500/10 hover:text-red-300">Remover</button>
                                        </form>
                                    @endif
                                </li>
                            @empty
                                <li class="px-5 py-6 text-center text-sm text-dg-500">Nenhuma categoria.</li>
                            @endforelse
                        </ul>
                    </section>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
