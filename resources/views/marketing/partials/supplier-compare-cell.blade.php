{{-- Célula "Santa Fé" da tabela de Novos (dentro do x-for de "row"). --}}
<template x-if="compareStatus(row) === 'new'">
    <span style="font-size: 0.68rem; color: #666;">salve para comparar</span>
</template>
<template x-if="compareStatus(row) === 'skip'">
    <span style="font-size: 0.68rem; color: #555;" title="Fora da comparação (encomenda ou outra categoria)">—</span>
</template>
<template x-if="compareStatus(row) === 'none'">
    <span style="font-size: 0.68rem; color: #666;">sem cotação</span>
</template>
<template x-if="['match', 'diff', 'color_missing'].includes(compareStatus(row))">
    <div style="display: flex; align-items: center; gap: 0.375rem;">
        <div style="display: flex; flex-direction: column; line-height: 1.15; min-width: 70px;">
            <span style="font-size: 0.8rem; font-weight: 700; color: #e3e3e3;"
                  :title="compareInfo(row).supplier_color ? 'Cor no fornecedor: ' + compareInfo(row).supplier_color : 'Menor custo do modelo'"
                  x-text="compareFmt(compareCostOf(row))"></span>
            <span style="font-size: 0.6rem; font-weight: 700;"
                  :style="{ color: { match: '#4ade80', diff: '#fbbf24', color_missing: '#f87171' }[compareStatus(row)] }"
                  x-text="{ match: 'custo igual', diff: 'custo diferente', color_missing: 'cor não cotada' }[compareStatus(row)]"></span>
        </div>
        <span style="font-size: 0.7rem; font-weight: 700;"
              :style="{ color: compareRealMargin(row) === null ? '#666' : (compareRealMargin(row) < 0 ? '#f87171' : '#4ade80') }"
              :title="'Margem sobre o custo Santa Fé'"
              x-text="compareRealMargin(row) === null ? '' : (compareRealMargin(row) >= 0 ? '+' : '') + compareFmt(compareRealMargin(row))"></span>
        <button type="button" @click="compareApplyRow(row)"
                :title="compareMarginValue() !== null ? 'Usar custo Santa Fé e venda = custo + margem' : 'Usar custo Santa Fé'"
                style="margin-left: auto; padding: 0.15rem 0.45rem; background: #222; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.25rem; font-size: 0.65rem; cursor: pointer;">
            usar
        </button>
    </div>
</template>
