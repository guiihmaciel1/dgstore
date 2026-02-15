<x-app-layout>
    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8" x-data="priceTable()">

            <!-- Header -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Catálogo de Produtos</h1>
                    <p style="font-size: 0.8rem; color: #9ca3af;" x-text="filtered.length + ' produto(s)'"></p>
                </div>
                <div style="position: relative; width: 100%; max-width: 320px;">
                    <svg style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: #9ca3af; pointer-events: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" x-model="search" placeholder="Buscar produto, SKU..."
                           style="width: 100%; padding: 0.5rem 0.75rem 0.5rem 2.25rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                           onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                </div>
            </div>

            <!-- Tabs de categoria -->
            <div style="display: flex; gap: 0.375rem; margin-bottom: 1rem; overflow-x: auto; padding-bottom: 0.25rem;">
                <template x-for="tab in tabs" :key="tab.key">
                    <button @click="category = tab.key" type="button"
                            :style="category === tab.key
                                ? 'padding: 0.375rem 0.875rem; font-size: 0.8rem; font-weight: 600; border-radius: 9999px; border: none; cursor: pointer; background: #111827; color: white; white-space: nowrap;'
                                : 'padding: 0.375rem 0.875rem; font-size: 0.8rem; font-weight: 500; border-radius: 9999px; border: 1px solid #e5e7eb; cursor: pointer; background: white; color: #6b7280; white-space: nowrap;'"
                            x-text="tab.label"></button>
                </template>
            </div>

            <!-- Tabela Desktop -->
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden;" class="hidden sm:block">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                            <th style="padding: 0.625rem 1rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Produto</th>
                            <th style="padding: 0.625rem 0.75rem; text-align: center; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase; width: 80px;">Storage</th>
                            <th style="padding: 0.625rem 0.75rem; text-align: center; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase; width: 80px;">Cond.</th>
                            <th style="padding: 0.625rem 0.75rem; text-align: center; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase; width: 70px;">Estoque</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="p in filtered" :key="p.id">
                            <tr style="border-bottom: 1px solid #f3f4f6;"
                                onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                <td style="padding: 0.5rem 1rem;">
                                    <div style="font-size: 0.875rem; font-weight: 500; color: #111827;" x-text="p.name"></div>
                                    <div style="font-size: 0.7rem; color: #9ca3af;" x-text="p.sku"></div>
                                </td>
                                <td style="padding: 0.5rem 0.75rem; text-align: center; font-size: 0.8rem; color: #6b7280;" x-text="p.storage || '-'"></td>
                                <td style="padding: 0.5rem 0.75rem; text-align: center;">
                                    <span :style="p.condition === 'new' ? 'font-size:0.7rem;font-weight:600;padding:2px 8px;border-radius:9999px;background:#dcfce7;color:#166534;' : p.condition === 'used' ? 'font-size:0.7rem;font-weight:600;padding:2px 8px;border-radius:9999px;background:#fef3c7;color:#92400e;' : 'font-size:0.7rem;font-weight:600;padding:2px 8px;border-radius:9999px;background:#dbeafe;color:#1e40af;'"
                                          x-text="p.condition === 'new' ? 'Novo' : p.condition === 'used' ? 'Usado' : 'Recond.'"></span>
                                </td>
                                <td style="padding: 0.5rem 0.75rem; text-align: center;">
                                    <span :style="p.stock > 0 ? 'font-size:0.75rem;font-weight:700;padding:2px 10px;border-radius:9999px;background:#dcfce7;color:#166534;' : 'font-size:0.75rem;font-weight:700;padding:2px 10px;border-radius:9999px;background:#fef2f2;color:#991b1b;'"
                                          x-text="p.stock"></span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <div x-show="filtered.length === 0" style="padding: 2rem; text-align: center; color: #9ca3af; font-size: 0.875rem;">
                    Nenhum produto encontrado
                </div>
            </div>

            <!-- Cards Mobile -->
            <div class="sm:hidden" style="display: flex; flex-direction: column; gap: 0.5rem;">
                <template x-for="p in filtered" :key="p.id">
                    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 0.75rem 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem;">
                            <div style="min-width: 0; flex: 1;">
                                <div style="font-size: 0.875rem; font-weight: 600; color: #111827; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" x-text="p.name"></div>
                                <div style="font-size: 0.7rem; color: #9ca3af; margin-top: 2px;" x-text="p.sku + (p.storage ? ' · ' + p.storage : '')"></div>
                            </div>
                            <div style="text-align: right; flex-shrink: 0;">
                                <span :style="p.stock > 0 ? 'font-size:0.75rem;font-weight:600;padding:2px 8px;border-radius:9999px;background:#dcfce7;color:#166534;' : 'font-size:0.75rem;font-weight:600;padding:2px 8px;border-radius:9999px;background:#fef2f2;color:#991b1b;'"
                                      x-text="'Est: ' + p.stock"></span>
                            </div>
                        </div>
                    </div>
                </template>
                <div x-show="filtered.length === 0" style="padding: 2rem; text-align: center; color: #9ca3af; font-size: 0.875rem;">
                    Nenhum produto encontrado
                </div>
            </div>
        </div>
    </div>

    <script>
    function priceTable() {
        const allProducts = @json($productsJson);

        return {
            search: '',
            category: 'all',
            tabs: [
                { key: 'all', label: 'Todos' },
                { key: 'smartphone', label: 'iPhones' },
                { key: 'tablet', label: 'iPads' },
                { key: 'notebook', label: 'Macs' },
                { key: 'smartwatch', label: 'Watch' },
                { key: 'headphone', label: 'AirPods' },
            ],

            get filtered() {
                let list = allProducts;
                if (this.category !== 'all') {
                    list = list.filter(p => p.category === this.category);
                }
                if (this.search.trim()) {
                    const q = this.search.toLowerCase().trim();
                    list = list.filter(p => p.name.toLowerCase().includes(q) || p.sku.toLowerCase().includes(q));
                }
                return list;
            },
        };
    }
    </script>
</x-app-layout>
