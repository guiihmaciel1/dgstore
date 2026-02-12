<x-app-layout>
    <div class="py-4">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabeçalho compacto -->
            <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                <a href="{{ route('stock.index') }}" style="margin-right: 0.75rem; padding: 0.375rem; color: #6b7280; border-radius: 0.375rem;"
                   onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                    <svg style="height: 1.25rem; width: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h1 style="font-size: 1.25rem; font-weight: 700; color: #111827;">Nova Movimentação de Estoque</h1>
            </div>

            <!-- Formulário -->
            <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb;">
                <form method="POST" action="{{ route('stock.store') }}">
                    @csrf
                    
                    <div style="padding: 1rem 1.25rem;">
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.875rem;">
                            <!-- Produto (Select com busca) -->
                            <div style="grid-column: span 2;"
                                 x-data="productSearch()"
                                 @click.away="open = false">
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">
                                    Produto <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="hidden" name="product_id" :value="selectedId" x-ref="productInput">

                                <div style="position: relative;">
                                    <!-- Campo de busca -->
                                    <div style="position: relative;">
                                        <svg style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: #9ca3af; pointer-events: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                        <input type="text"
                                               x-model="search"
                                               @focus="open = true"
                                               @input="open = true"
                                               @keydown.escape="open = false"
                                               @keydown.arrow-down.prevent="moveDown()"
                                               @keydown.arrow-up.prevent="moveUp()"
                                               @keydown.enter.prevent="selectHighlighted()"
                                               :placeholder="selectedId ? '' : 'Buscar produto por nome, SKU...'"
                                               autocomplete="off"
                                               style="width: 100%; padding: 0.5rem 0.625rem 0.5rem 2rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem; background: white; outline: none;"
                                               :style="open ? 'border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59,130,246,0.15);' : ''"
                                               x-ref="searchInput">

                                        <!-- Chip do produto selecionado -->
                                        <div x-show="selectedId && !open" @click="clearAndFocus()"
                                             style="position: absolute; inset: 0; display: flex; align-items: center; padding: 0 0.625rem 0 2rem; background: white; border-radius: 0.375rem; cursor: text;">
                                            <span style="flex: 1; font-size: 0.875rem; color: #111827; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                                                  x-text="selectedLabel"></span>
                                            <button type="button" @click.stop="clear()" style="padding: 2px; color: #9ca3af; background: none; border: none; cursor: pointer; line-height: 1;"
                                                    onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#9ca3af'">
                                                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Dropdown de resultados -->
                                    <div x-show="open" x-transition.opacity.duration.150ms
                                         style="position: absolute; z-index: 50; width: 100%; margin-top: 4px; background: white; border: 1px solid #e5e7eb; border-radius: 0.5rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1); max-height: 280px; overflow-y: auto;"
                                         x-ref="dropdown">

                                        <!-- Contador de resultados -->
                                        <div style="padding: 6px 12px; font-size: 0.7rem; color: #9ca3af; border-bottom: 1px solid #f3f4f6; position: sticky; top: 0; background: white; z-index: 1;">
                                            <span x-text="filtered().length"></span> produto(s) encontrado(s)
                                        </div>

                                        <template x-for="(item, idx) in filtered()" :key="item.id">
                                            <button type="button"
                                                    @click="select(item)"
                                                    @mouseenter="highlighted = idx"
                                                    :style="highlighted === idx
                                                        ? 'width: 100%; text-align: left; padding: 8px 12px; font-size: 0.85rem; border: none; cursor: pointer; background: #eff6ff; outline: none;'
                                                        : 'width: 100%; text-align: left; padding: 8px 12px; font-size: 0.85rem; border: none; cursor: pointer; background: white; outline: none;'"
                                                    onmouseover="if(this.style.background==='white') this.style.background='#f9fafb'"
                                                    onmouseout="this.style.background=''">
                                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                                                    <div style="min-width: 0; flex: 1;">
                                                        <div style="font-weight: 500; color: #111827; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" x-text="item.name"></div>
                                                        <div style="font-size: 0.75rem; color: #9ca3af; margin-top: 1px;" x-text="item.sku"></div>
                                                    </div>
                                                    <div style="flex-shrink: 0; display: flex; align-items: center; gap: 6px;">
                                                        <span :style="item.stock > 0
                                                            ? 'font-size: 0.7rem; font-weight: 600; padding: 2px 8px; border-radius: 9999px; background: #dcfce7; color: #166534;'
                                                            : 'font-size: 0.7rem; font-weight: 600; padding: 2px 8px; border-radius: 9999px; background: #fef2f2; color: #991b1b;'"
                                                            x-text="'Est: ' + item.stock"></span>
                                                    </div>
                                                </div>
                                            </button>
                                        </template>

                                        <!-- Sem resultados -->
                                        <div x-show="filtered().length === 0" style="padding: 20px 12px; text-align: center;">
                                            <svg style="width: 24px; height: 24px; color: #d1d5db; margin: 0 auto 6px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                            </svg>
                                            <p style="font-size: 0.8rem; color: #9ca3af;">Nenhum produto encontrado</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tipo de Movimentação -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">
                                    Tipo <span style="color: #dc2626;">*</span>
                                </label>
                                <select name="type" required
                                        style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem; background: white;">
                                    @foreach($types as $type)
                                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Quantidade -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">
                                    Quantidade <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="number" name="quantity" value="{{ old('quantity') }}" min="1" required
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- Motivo -->
                            <div style="grid-column: span 2;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Motivo</label>
                                <input type="text" name="reason" value="{{ old('reason') }}" placeholder="Descreva o motivo da movimentação..."
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>
                        </div>
                        <p style="margin-top: 0.5rem; font-size: 0.75rem; color: #6b7280;">
                            Para entrada, informe a quantidade a adicionar. Para ajuste, informe o novo valor total do estoque.
                        </p>
                    </div>

                    <!-- Rodapé -->
                    <div style="padding: 0.75rem 1.25rem; background: #f9fafb; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 0.5rem;">
                        <a href="{{ route('stock.index') }}" 
                           style="padding: 0.5rem 1rem; background: white; color: #374151; font-weight: 500; font-size: 0.875rem; border-radius: 0.375rem; text-decoration: none; border: 1px solid #d1d5db;">
                            Cancelar
                        </a>
                        <button type="submit" 
                                style="padding: 0.5rem 1.25rem; background: #111827; color: white; font-weight: 500; font-size: 0.875rem; border-radius: 0.375rem; border: none; cursor: pointer;"
                                onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                            Registrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 640px) {
            div[style*="grid-template-columns: repeat(2"] { grid-template-columns: 1fr !important; }
            div[style*="grid-column: span 2"] { grid-column: span 1 !important; }
        }
    </style>

    <script>
        function productSearch() {
            const products = @json($products->map(fn($p) => [
                'id'    => $p->id,
                'name'  => $p->name,
                'sku'   => $p->sku,
                'stock' => $p->stock_quantity,
            ]));

            const preselected = '{{ request('product_id') }}';

            return {
                products,
                search: '',
                open: false,
                selectedId: '',
                selectedLabel: '',
                highlighted: 0,

                init() {
                    if (preselected) {
                        const found = this.products.find(p => String(p.id) === preselected);
                        if (found) this.select(found, false);
                    }
                },

                filtered() {
                    if (!this.search.trim()) return this.products;
                    const q = this.search.toLowerCase().trim();
                    return this.products.filter(p =>
                        p.name.toLowerCase().includes(q) || p.sku.toLowerCase().includes(q)
                    );
                },

                select(item, closeDropdown = true) {
                    this.selectedId = item.id;
                    this.selectedLabel = `${item.name} (Est: ${item.stock})`;
                    this.search = '';
                    this.highlighted = 0;
                    if (closeDropdown) this.open = false;
                },

                clear() {
                    this.selectedId = '';
                    this.selectedLabel = '';
                    this.search = '';
                },

                clearAndFocus() {
                    this.search = '';
                    this.open = true;
                    this.$nextTick(() => this.$refs.searchInput.focus());
                },

                moveDown() {
                    const list = this.filtered();
                    if (this.highlighted < list.length - 1) this.highlighted++;
                    this.scrollToHighlighted();
                },

                moveUp() {
                    if (this.highlighted > 0) this.highlighted--;
                    this.scrollToHighlighted();
                },

                selectHighlighted() {
                    const list = this.filtered();
                    if (list[this.highlighted]) this.select(list[this.highlighted]);
                },

                scrollToHighlighted() {
                    this.$nextTick(() => {
                        const container = this.$refs.dropdown;
                        if (!container) return;
                        const items = container.querySelectorAll('button');
                        if (items[this.highlighted]) {
                            items[this.highlighted].scrollIntoView({ block: 'nearest' });
                        }
                    });
                },
            };
        }
    </script>
</x-app-layout>
