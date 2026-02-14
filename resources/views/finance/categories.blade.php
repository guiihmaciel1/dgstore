<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ showForm: false, newType: 'expense' }">
            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 0.5rem; color: #065f46;">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem; color: #991b1b;">
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div style="margin-bottom: 1rem; padding: 1rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem; color: #991b1b;">
                    <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.875rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Cabeçalho -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Categorias</h1>
                    <p class="text-sm text-gray-500">Organize receitas e despesas por categoria</p>
                </div>
                <button @click="showForm = !showForm"
                        style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.25rem; background: #111827; color: white; font-weight: 600; border-radius: 0.75rem; border: none; cursor: pointer; font-size: 0.875rem;"
                        onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nova Categoria
                </button>
            </div>

            <!-- Form Nova Categoria -->
            <div x-show="showForm" x-cloak style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem; margin-bottom: 1.5rem;">
                <form method="POST" action="{{ route('finance.categories.store') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                    @csrf
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 500; color: #6b7280;">Nome *</label>
                        <input type="text" name="name" required placeholder="Ex: Material de escritório" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; margin-top: 0.25rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 500; color: #6b7280;">Tipo *</label>
                        <select name="type" x-model="newType" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; background: white; margin-top: 0.25rem;">
                            <option value="expense">Despesa</option>
                            <option value="income">Receita</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 500; color: #6b7280;">Cor</label>
                        <input type="color" name="color" value="#6b7280" style="width: 100%; height: 2.375rem; border: 1px solid #d1d5db; border-radius: 0.5rem; margin-top: 0.25rem; cursor: pointer;">
                    </div>
                    <button type="submit" style="padding: 0.5rem 1rem; background: #111827; color: white; font-weight: 600; border-radius: 0.5rem; border: none; cursor: pointer; font-size: 0.875rem;">Criar</button>
                </form>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Categorias de Receita -->
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden;">
                    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #e5e7eb; background: #f0fdf4;">
                        <h2 style="font-size: 0.9375rem; font-weight: 600; color: #16a34a;">Receitas</h2>
                    </div>
                    <div>
                        @foreach($categories->where('type.value', 'income') as $cat)
                            <div style="padding: 0.75rem 1.25rem; border-bottom: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <span style="width: 0.75rem; height: 0.75rem; border-radius: 50%; background: {{ $cat->color ?? '#6b7280' }}; flex-shrink: 0;"></span>
                                    <span style="font-size: 0.875rem; font-weight: 500; color: #111827;">{{ $cat->name }}</span>
                                    @if($cat->is_system)
                                        <span style="font-size: 0.5625rem; padding: 0.0625rem 0.375rem; border-radius: 9999px; background: #f3f4f6; color: #6b7280; font-weight: 500;">SISTEMA</span>
                                    @endif
                                </div>
                                @if(!$cat->is_system)
                                    <form method="POST" action="{{ route('finance.categories.destroy', $cat) }}" onsubmit="return confirm('Remover esta categoria?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="font-size: 0.75rem; color: #dc2626; background: none; border: none; cursor: pointer;">Remover</button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Categorias de Despesa -->
                <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden;">
                    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #e5e7eb; background: #fef2f2;">
                        <h2 style="font-size: 0.9375rem; font-weight: 600; color: #dc2626;">Despesas</h2>
                    </div>
                    <div>
                        @foreach($categories->where('type.value', 'expense') as $cat)
                            <div style="padding: 0.75rem 1.25rem; border-bottom: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <span style="width: 0.75rem; height: 0.75rem; border-radius: 50%; background: {{ $cat->color ?? '#6b7280' }}; flex-shrink: 0;"></span>
                                    <span style="font-size: 0.875rem; font-weight: 500; color: #111827;">{{ $cat->name }}</span>
                                    @if($cat->is_system)
                                        <span style="font-size: 0.5625rem; padding: 0.0625rem 0.375rem; border-radius: 9999px; background: #f3f4f6; color: #6b7280; font-weight: 500;">SISTEMA</span>
                                    @endif
                                </div>
                                @if(!$cat->is_system)
                                    <form method="POST" action="{{ route('finance.categories.destroy', $cat) }}" onsubmit="return confirm('Remover esta categoria?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="font-size: 0.75rem; color: #dc2626; background: none; border: none; cursor: pointer;">Remover</button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <style>[x-cloak] { display: none !important; }</style>
    @endpush
</x-app-layout>
