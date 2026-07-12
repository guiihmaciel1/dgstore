{{-- Tabela Detalhada --}}
<div>
    {{-- Busca Rápida --}}
    <div style="margin-bottom: 1rem;">
        <div style="position: relative; max-width: 24rem;">
            <div style="position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); pointer-events: none;">
                <svg style="width: 1rem; height: 1rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text"
                   x-model="searchQuery"
                   placeholder="Buscar por vendedor, cliente, produto..."
                   style="width: 100%; padding: 0.625rem 1rem 0.625rem 2.5rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.8125rem; outline: none;"
                   onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e5e7eb'">
        </div>
    </div>

    {{-- Tabela --}}
    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
                <thead>
                    <tr style="background: #f9fafb; border-bottom: 2px solid #e5e7eb;">
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: #374151; white-space: nowrap;">Data</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: #374151; white-space: nowrap;">Vendedor</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: #374151; white-space: nowrap;">Cliente</th>
                        <th style="padding: 0.75rem 1rem; text-align: center; font-weight: 600; color: #374151; white-space: nowrap;">Tipo</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: #374151; white-space: nowrap;">Produto</th>
                        <th style="padding: 0.75rem 1rem; text-align: center; font-weight: 600; color: #374151; white-space: nowrap;">Cond.</th>
                        <th style="padding: 0.75rem 1rem; text-align: right; font-weight: 600; color: #374151; white-space: nowrap;">Valor</th>
                        <th style="padding: 0.75rem 1rem; text-align: right; font-weight: 600; color: #374151; white-space: nowrap;">Custo</th>
                        <th style="padding: 0.75rem 1rem; text-align: right; font-weight: 600; color: #374151; white-space: nowrap;">Lucro</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: #374151; white-space: nowrap;">Pagamento</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(sale, idx) in filteredSales" :key="idx">
                        <tr :style="idx % 2 === 0 ? 'border-bottom: 1px solid #f3f4f6;' : 'border-bottom: 1px solid #f3f4f6; background: #f9fafb;'">
                            <td style="padding: 0.625rem 1rem; white-space: nowrap; color: #374151;" x-text="sale.date"></td>
                            <td style="padding: 0.625rem 1rem; white-space: nowrap; color: #374151;" x-text="sale.seller"></td>
                            <td style="padding: 0.625rem 1rem; white-space: nowrap; color: #374151; max-width: 10rem; overflow: hidden; text-overflow: ellipsis;" x-text="sale.customer"></td>
                            <td style="padding: 0.625rem 1rem; text-align: center; white-space: nowrap;">
                                <span :style="sale.type_raw === 'repasse'
                                    ? 'display: inline-block; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 600; background: #ede9fe; color: #7c3aed;'
                                    : 'display: inline-block; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 600; background: #ecfdf5; color: #059669;'"
                                    x-text="sale.type"></span>
                            </td>
                            <td style="padding: 0.625rem 1rem; color: #374151; max-width: 14rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" x-text="sale.product"></td>
                            <td style="padding: 0.625rem 1rem; text-align: center; white-space: nowrap; color: #6b7280;" x-text="sale.condition"></td>
                            <td style="padding: 0.625rem 1rem; text-align: right; white-space: nowrap; font-weight: 500; color: #111827;" x-text="money(sale.value)"></td>
                            <td style="padding: 0.625rem 1rem; text-align: right; white-space: nowrap; color: #6b7280;" x-text="money(sale.cost)"></td>
                            <td style="padding: 0.625rem 1rem; text-align: right; white-space: nowrap; font-weight: 600;"
                                :style="sale.profit >= 0 ? 'color: #059669;' : 'color: #dc2626;'"
                                x-text="money(sale.profit)"></td>
                            <td style="padding: 0.625rem 1rem; white-space: nowrap; color: #6b7280;" x-text="sale.payment"></td>
                        </tr>
                    </template>
                    <template x-if="filteredSales.length === 0">
                        <tr>
                            <td colspan="10" style="padding: 2rem; text-align: center; color: #9ca3af;">Nenhuma venda encontrada.</td>
                        </tr>
                    </template>
                </tbody>
                {{-- Totalizadores --}}
                <tfoot>
                    <tr style="border-top: 2px solid #e5e7eb; background: #f9fafb;">
                        <td colspan="6" style="padding: 0.75rem 1rem; font-weight: 700; color: #111827;">
                            Total (<span x-text="filteredSales.length"></span> vendas)
                        </td>
                        <td style="padding: 0.75rem 1rem; text-align: right; font-weight: 700; color: #111827;" x-text="money(detailedTotals.value)"></td>
                        <td style="padding: 0.75rem 1rem; text-align: right; font-weight: 700; color: #6b7280;" x-text="money(detailedTotals.cost)"></td>
                        <td style="padding: 0.75rem 1rem; text-align: right; font-weight: 700;"
                            :style="detailedTotals.profit >= 0 ? 'color: #059669;' : 'color: #dc2626;'"
                            x-text="money(detailedTotals.profit)"></td>
                        <td style="padding: 0.75rem 1rem;"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
