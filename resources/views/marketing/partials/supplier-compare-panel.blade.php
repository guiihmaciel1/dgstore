{{-- Painel "Comparar Santa Fé" (admin_geral). Estado/métodos em supplier-compare-script. --}}
<div x-show="compareChangedCount() > 0" x-cloak
     style="margin-bottom: 0.75rem; padding: 0.5rem 0.75rem; background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.3); border-radius: 0.5rem; font-size: 0.78rem; color: #fbbf24;">
    <strong x-text="compareChangedCount()"></strong> item(ns) alterado(s) — revise e clique em <strong>Salvar Tabela</strong>.
    <span x-show="compareChangedHiddenCount() > 0" style="color: #f87171;">
        Atenção: <strong x-text="compareChangedHiddenCount()"></strong> alterado(s) estão fora do filtro atual e não serão salvos.
    </span>
</div>

<div x-show="compareMode" x-cloak
     style="margin-bottom: 1rem; padding: 0.875rem 1rem; background: #141414; border: 1px solid rgba(37,211,102,0.2); border-radius: 0.75rem;">

    <template x-if="!compareData">
        <div style="font-size: 0.8rem; color: #818181;">Nenhuma cotação do fornecedor Santa Fé encontrada.</div>
    </template>

    <template x-if="compareData">
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            {{-- Cabeçalho --}}
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                <span style="font-size: 0.8rem; font-weight: 700; color: #25D366;" x-text="compareData.supplier_name"></span>
                <span style="font-size: 0.7rem; color: #666;"
                      x-text="'Cotação ' + compareFormatDate(compareData.quoted_at) + ' · ' + compareData.items_count + ' itens'"></span>
                <span x-show="compareQuoteAgeDays() > 3"
                      style="font-size: 0.65rem; font-weight: 700; color: #d97706; background: rgba(217,119,6,0.12); padding: 0.125rem 0.5rem; border-radius: 9999px;"
                      x-text="'Cotação de ' + compareQuoteAgeDays() + ' dias atrás'"></span>
            </div>

            {{-- Contadores --}}
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; font-size: 0.72rem;">
                <span style="padding: 0.2rem 0.6rem; border-radius: 9999px; background: rgba(34,197,94,0.12); color: #4ade80;">
                    <strong x-text="compareSummary().match"></strong> custo igual
                </span>
                <span style="padding: 0.2rem 0.6rem; border-radius: 9999px; background: rgba(245,158,11,0.12); color: #fbbf24;">
                    <strong x-text="compareSummary().diff"></strong> custo diferente
                </span>
                <span style="padding: 0.2rem 0.6rem; border-radius: 9999px; background: rgba(239,68,68,0.1); color: #f87171;">
                    <strong x-text="compareSummary().color_missing"></strong> cor não cotada
                </span>
                <span style="padding: 0.2rem 0.6rem; border-radius: 9999px; background: rgba(255,255,255,0.05); color: #818181;">
                    <strong x-text="compareSummary().none"></strong> sem cotação
                </span>
            </div>

            {{-- Ações --}}
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                <label style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.75rem; color: #a4a4a4;">
                    Margem R$
                    <input type="number" step="1" min="0" x-model="compareMargin" placeholder="499"
                           @keydown.enter.prevent="compareApply(true)"
                           style="width: 90px; padding: 0.375rem 0.5rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.375rem; font-size: 0.8rem; text-align: right; outline: none;">
                </label>
                <button type="button" @click="compareApply(true)"
                        style="padding: 0.4rem 0.875rem; background: #16a34a; color: white; border: none; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                    Aplicar margem (custo + margem)
                </button>
                <button type="button" @click="compareApply(false)"
                        style="padding: 0.4rem 0.875rem; background: #222; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                    Só alinhar custos
                </button>
                <span style="width: 1px; height: 1.5rem; background: rgba(255,255,255,0.08);"></span>
                <label style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.75rem; color: #a4a4a4;">
                    Encomenda = Santa Fé − R$
                    <input type="number" step="1" min="0" x-model="compareEncomendaDiscount" placeholder="99"
                           @keydown.enter.prevent="compareApplyEncomenda()"
                           style="width: 70px; padding: 0.375rem 0.5rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.375rem; font-size: 0.8rem; text-align: right; outline: none;">
                </label>
                <button type="button" @click="compareApplyEncomenda()"
                        style="padding: 0.4rem 0.875rem; background: rgba(217,119,6,0.15); color: #fbbf24; border: 1px solid rgba(217,119,6,0.4); border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                    Preencher encomenda
                </button>
                <button type="button" @click="compareUndo()" x-show="compareUndoStack.length > 0"
                        style="padding: 0.4rem 0.875rem; background: transparent; color: #fbbf24; border: 1px solid rgba(245,158,11,0.3); border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                    Desfazer
                </button>
                <span x-show="compareFeedback" x-text="compareFeedback" style="font-size: 0.72rem; color: #4ade80;"></span>
            </div>
            <div style="font-size: 0.68rem; color: #666;">
                Afeta <strong x-text="compareEligibleRows().length"></strong> item(ns) ativos da lista atual com cotação (cor igual ou linha sem cor, usando o menor custo do modelo).
                Itens com cor não cotada ficam de fora; use o botão "usar" na linha se quiser.
            </div>

            {{-- Fornecedor tem, nós não --}}
            <div x-show="compareData.missing_in_marketing.length > 0">
                <button type="button" @click="compareShowMissing = !compareShowMissing"
                        style="background: none; border: none; padding: 0; color: #a4a4a4; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                    <span x-text="compareShowMissing ? '▾' : '▸'"></span>
                    Fornecedor tem, nós não (<span x-text="compareData.missing_in_marketing.length"></span>)
                </button>
                <div x-show="compareShowMissing" style="margin-top: 0.5rem; display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 0.375rem;">
                    <template x-for="(item, mIdx) in compareData.missing_in_marketing" :key="'miss_' + mIdx">
                        <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.375rem 0.5rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem;">
                            <span style="width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; border: 1px solid rgba(255,255,255,0.2);"
                                  :style="{ background: item.color_hex || '#6b7280' }"></span>
                            <span style="flex: 1; font-size: 0.72rem; color: #e3e3e3; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                                  :title="item.supplier_color"
                                  x-text="item.name + ' ' + item.storage + (item.color ? ' · ' + item.color : ' · ' + item.supplier_color)"></span>
                            <span style="font-size: 0.72rem; font-weight: 700; color: #a4a4a4;" x-text="compareFmt(item.price)"></span>
                            <button type="button" @click="compareAddMissing(item)" :disabled="item._added"
                                    :style="{ opacity: item._added ? 0.4 : 1, cursor: item._added ? 'default' : 'pointer' }"
                                    style="padding: 0.15rem 0.5rem; background: #222; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.25rem; font-size: 0.65rem;"
                                    x-text="item._added ? 'Adicionado' : 'Adicionar'"></button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>
</div>
