{{-- Period Pills --}}
<div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
    <span style="font-size: 0.75rem; color: #64748b; margin-right: 0.25rem;">Período:</span>
    <template x-for="p in [{v:'today',l:'Hoje'},{v:'week',l:'Semana'},{v:'month',l:'Mês'},{v:'year',l:'Ano'}]" :key="'dp'+p.v">
        <button @click="switchExecPeriod(p.v)"
                :style="execPeriod === p.v
                    ? 'padding: 0.375rem 0.875rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 600; background: rgba(99,102,241,0.2); color: #a5b4fc; border: 1px solid rgba(99,102,241,0.3);'
                    : 'padding: 0.375rem 0.875rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 500; background: rgba(255,255,255,0.03); color: #94a3b8; border: 1px solid rgba(255,255,255,0.06); cursor: pointer;'"
                x-text="p.l"></button>
    </template>
    <span style="font-size: 0.6875rem; color: #475569; margin-left: 0.5rem;" x-text="execData?.period_label || ''"></span>
</div>

{{-- Search --}}
<div style="margin-bottom: 1rem; max-width: 24rem;">
    <div style="position: relative;">
        <div style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); pointer-events: none;">
            <svg style="width: 0.875rem; height: 0.875rem; color: #475569;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <input type="text" x-model="execSearch"
               placeholder="Buscar vendedor, cliente, produto..."
               style="width: 100%; padding: 0.5rem 0.875rem 0.5rem 2.25rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); border-radius: 8px; font-size: 0.75rem; color: #e2e8f0; outline: none;"
               onfocus="this.style.borderColor='rgba(99,102,241,0.4)'" onblur="this.style.borderColor='rgba(255,255,255,0.08)'">
    </div>
</div>

{{-- Table --}}
<div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; overflow: hidden;">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 0.75rem;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                    <th style="padding: 0.75rem 0.875rem; text-align: left; font-weight: 600; color: #94a3b8; white-space: nowrap; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em;">Data</th>
                    <th style="padding: 0.75rem 0.875rem; text-align: left; font-weight: 600; color: #94a3b8; white-space: nowrap; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em;">Vendedor</th>
                    <th style="padding: 0.75rem 0.875rem; text-align: left; font-weight: 600; color: #94a3b8; white-space: nowrap; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em;">Cliente</th>
                    <th style="padding: 0.75rem 0.875rem; text-align: center; font-weight: 600; color: #94a3b8; white-space: nowrap; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em;">Tipo</th>
                    <th style="padding: 0.75rem 0.875rem; text-align: left; font-weight: 600; color: #94a3b8; white-space: nowrap; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em;">Produto</th>
                    <th style="padding: 0.75rem 0.875rem; text-align: center; font-weight: 600; color: #94a3b8; white-space: nowrap; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em;">Cond.</th>
                    <th style="padding: 0.75rem 0.875rem; text-align: right; font-weight: 600; color: #94a3b8; white-space: nowrap; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em;">Valor</th>
                    <th style="padding: 0.75rem 0.875rem; text-align: right; font-weight: 600; color: #94a3b8; white-space: nowrap; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em;">Custo</th>
                    <th style="padding: 0.75rem 0.875rem; text-align: right; font-weight: 600; color: #94a3b8; white-space: nowrap; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em;">Lucro</th>
                    <th style="padding: 0.75rem 0.875rem; text-align: left; font-weight: 600; color: #94a3b8; white-space: nowrap; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em;">Pgto</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(sale, idx) in filteredExecSales" :key="'ds'+idx">
                    <tr :style="'border-bottom: 1px solid rgba(255,255,255,0.03);' + (idx % 2 === 1 ? ' background: rgba(255,255,255,0.01);' : '')">
                        <td style="padding: 0.5rem 0.875rem; white-space: nowrap; color: #cbd5e1;" x-text="sale.date"></td>
                        <td style="padding: 0.5rem 0.875rem; white-space: nowrap; color: #cbd5e1;" x-text="sale.seller"></td>
                        <td style="padding: 0.5rem 0.875rem; white-space: nowrap; color: #cbd5e1; max-width: 9rem; overflow: hidden; text-overflow: ellipsis;" x-text="sale.customer"></td>
                        <td style="padding: 0.5rem 0.875rem; text-align: center; white-space: nowrap;">
                            <span :style="sale.type_raw === 'repasse'
                                ? 'display: inline-block; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.625rem; font-weight: 600; background: rgba(139,92,246,0.15); color: #c4b5fd;'
                                : 'display: inline-block; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.625rem; font-weight: 600; background: rgba(16,185,129,0.15); color: #6ee7b7;'"
                                x-text="sale.type"></span>
                        </td>
                        <td style="padding: 0.5rem 0.875rem; color: #cbd5e1; max-width: 12rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" x-text="sale.product"></td>
                        <td style="padding: 0.5rem 0.875rem; text-align: center; white-space: nowrap; color: #64748b;" x-text="sale.condition"></td>
                        <td style="padding: 0.5rem 0.875rem; text-align: right; white-space: nowrap; font-weight: 500; color: #f1f5f9;">
                            <span x-show="!hideValues" x-text="formatCurrency(sale.value)"></span>
                            <span x-show="hideValues" x-cloak>••••</span>
                        </td>
                        <td style="padding: 0.5rem 0.875rem; text-align: right; white-space: nowrap; color: #64748b;">
                            <span x-show="!hideValues" x-text="formatCurrency(sale.cost)"></span>
                            <span x-show="hideValues" x-cloak>••••</span>
                        </td>
                        <td style="padding: 0.5rem 0.875rem; text-align: right; white-space: nowrap; font-weight: 600;"
                            :style="sale.profit >= 0 ? 'color: #34d399;' : 'color: #f87171;'">
                            <span x-show="!hideValues" x-text="formatCurrency(sale.profit)"></span>
                            <span x-show="hideValues" x-cloak>••••</span>
                        </td>
                        <td style="padding: 0.5rem 0.875rem; white-space: nowrap; color: #64748b;" x-text="sale.payment"></td>
                    </tr>
                </template>
                <template x-if="filteredExecSales.length === 0">
                    <tr>
                        <td colspan="10" style="padding: 2rem; text-align: center; color: #475569;">Nenhuma venda encontrada.</td>
                    </tr>
                </template>
            </tbody>
            <tfoot>
                <tr style="border-top: 1px solid rgba(255,255,255,0.08);">
                    <td colspan="6" style="padding: 0.75rem 0.875rem; font-weight: 700; color: #e2e8f0; font-size: 0.75rem;">
                        Total (<span x-text="filteredExecSales.length"></span> vendas)
                    </td>
                    <td style="padding: 0.75rem 0.875rem; text-align: right; font-weight: 700; color: #f1f5f9; font-size: 0.75rem;">
                        <span x-show="!hideValues" x-text="formatCurrency(execTotals.value)"></span>
                        <span x-show="hideValues" x-cloak>••••</span>
                    </td>
                    <td style="padding: 0.75rem 0.875rem; text-align: right; font-weight: 700; color: #64748b; font-size: 0.75rem;">
                        <span x-show="!hideValues" x-text="formatCurrency(execTotals.cost)"></span>
                        <span x-show="hideValues" x-cloak>••••</span>
                    </td>
                    <td style="padding: 0.75rem 0.875rem; text-align: right; font-weight: 700; font-size: 0.75rem;"
                        :style="execTotals.profit >= 0 ? 'color: #34d399;' : 'color: #f87171;'">
                        <span x-show="!hideValues" x-text="formatCurrency(execTotals.profit)"></span>
                        <span x-show="hideValues" x-cloak>••••</span>
                    </td>
                    <td style="padding: 0.75rem 0.875rem;"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
