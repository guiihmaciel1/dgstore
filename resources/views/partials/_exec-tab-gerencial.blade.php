{{-- Period Pills --}}
<div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
    <span style="font-size: 0.75rem; color: #64748b; margin-right: 0.25rem;">Período:</span>
    <template x-for="p in [{v:'today',l:'Hoje'},{v:'week',l:'Semana'},{v:'month',l:'Mês'},{v:'year',l:'Ano'}]" :key="p.v">
        <button @click="switchExecPeriod(p.v)"
                :style="execPeriod === p.v
                    ? 'padding: 0.375rem 0.875rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 600; background: rgba(99,102,241,0.2); color: #a5b4fc; border: 1px solid rgba(99,102,241,0.3);'
                    : 'padding: 0.375rem 0.875rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 500; background: rgba(255,255,255,0.03); color: #94a3b8; border: 1px solid rgba(255,255,255,0.06); cursor: pointer;'"
                x-text="p.l"></button>
    </template>
    <span style="font-size: 0.6875rem; color: #475569; margin-left: 0.5rem;" x-text="execData?.period_label || ''"></span>

    <div x-show="execLoading" style="margin-left: 0.5rem;">
        <svg style="width: 1rem; height: 1rem; color: #6366f1; animation: spin 1s linear infinite;" fill="none" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity: 0.25;"></circle>
            <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" style="opacity: 0.75;"></path>
        </svg>
    </div>
</div>

{{-- KPI Cards - Linha 1 --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.25rem;">
        <p style="font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 0.25rem;">Total Vendas</p>
        <p style="font-size: 1.75rem; font-weight: 700; color: #f1f5f9;" x-text="execData?.gerencial?.total_sales ?? 0"></p>
        <p style="font-size: 0.75rem; color: #475569;">
            <span x-show="!hideValues" x-text="formatCurrency(execData?.gerencial?.total_revenue)"></span>
            <span x-show="hideValues" x-cloak>R$ ••••</span>
        </p>
    </div>
    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.25rem;">
        <p style="font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 0.25rem;">Valor Investido</p>
        <p style="font-size: 1.75rem; font-weight: 700; color: #f87171;">
            <span x-show="!hideValues" x-text="formatCurrency(execData?.gerencial?.total_cost)"></span>
            <span x-show="hideValues" x-cloak>R$ ••••</span>
        </p>
    </div>
    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.25rem;">
        <p style="font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 0.25rem;">Lucro Bruto</p>
        <p style="font-size: 1.75rem; font-weight: 700;"
           :style="(execData?.gerencial?.total_profit ?? 0) >= 0 ? 'color: #34d399;' : 'color: #f87171;'">
            <span x-show="!hideValues" x-text="formatCurrency(execData?.gerencial?.total_profit)"></span>
            <span x-show="hideValues" x-cloak>R$ ••••</span>
        </p>
        <p style="font-size: 0.6875rem; color: #64748b; margin-top: 0.25rem;" x-text="(execData?.gerencial?.margin ?? 0) + '% margem'"></p>
    </div>
    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.25rem;">
        <p style="font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 0.25rem;">Comissões</p>
        <p style="font-size: 1.75rem; font-weight: 700; color: #c084fc;">
            <span x-show="!hideValues" x-text="formatCurrency(execData?.gerencial?.total_commissions)"></span>
            <span x-show="hideValues" x-cloak>R$ ••••</span>
        </p>
    </div>
    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(6,182,212,0.15); border-radius: 12px; padding: 1.25rem;">
        <p style="font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 0.25rem;">Lucro Líquido</p>
        <p style="font-size: 1.75rem; font-weight: 700;"
           :style="(execData?.gerencial?.net_profit ?? 0) >= 0 ? 'color: #67e8f9;' : 'color: #f87171;'">
            <span x-show="!hideValues" x-text="formatCurrency(execData?.gerencial?.net_profit)"></span>
            <span x-show="hideValues" x-cloak>R$ ••••</span>
        </p>
        <p style="font-size: 0.6875rem; color: #64748b; margin-top: 0.25rem;" x-text="(execData?.gerencial?.net_margin ?? 0) + '% líquido'"></p>
    </div>
</div>

{{-- KPI Cards - Linha 2 --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.25rem;">
        <p style="font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 0.25rem;">Ticket Médio</p>
        <p style="font-size: 1.75rem; font-weight: 700; color: #f1f5f9;">
            <span x-show="!hideValues" x-text="formatCurrency(execData?.gerencial?.average_ticket)"></span>
            <span x-show="hideValues" x-cloak>R$ ••••</span>
        </p>
    </div>
    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.25rem;">
        <p style="font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 0.25rem;">iPhones Novos</p>
        <p style="font-size: 1.75rem; font-weight: 700; color: #818cf8;" x-text="execData?.gerencial?.iphone_new?.qty ?? 0"></p>
        <p style="font-size: 0.75rem; color: #475569;">
            <span x-show="!hideValues" x-text="formatCurrency(execData?.gerencial?.iphone_new?.value)"></span>
            <span x-show="hideValues" x-cloak>R$ ••••</span>
        </p>
    </div>
    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.25rem;">
        <p style="font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 0.25rem;">Seminovos</p>
        <p style="font-size: 1.75rem; font-weight: 700; color: #fbbf24;" x-text="execData?.gerencial?.iphone_used?.qty ?? 0"></p>
        <p style="font-size: 0.75rem; color: #475569;">
            <span x-show="!hideValues" x-text="formatCurrency(execData?.gerencial?.iphone_used?.value)"></span>
            <span x-show="hideValues" x-cloak>R$ ••••</span>
        </p>
    </div>
    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.25rem;">
        <p style="font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 0.25rem;">Acessórios</p>
        <p style="font-size: 1.75rem; font-weight: 700; color: #c084fc;" x-text="execData?.gerencial?.accessories?.qty ?? 0"></p>
        <p style="font-size: 0.75rem; color: #475569;">
            <span x-show="!hideValues" x-text="formatCurrency(execData?.gerencial?.accessories?.value)"></span>
            <span x-show="hideValues" x-cloak>R$ ••••</span>
        </p>
    </div>
</div>

{{-- Charts Grid --}}
<div class="intern-grid-2col" style="display: grid; gap: 1rem; margin-bottom: 1.5rem;">
    {{-- Mix: Novos vs Seminovos --}}
    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.5rem;">
        <h3 style="font-size: 0.8125rem; font-weight: 600; color: #e2e8f0; margin-bottom: 1rem;">Novos vs Seminovos</h3>
        <div style="height: 200px;"><canvas id="execMixCanvas"></canvas></div>
    </div>
    {{-- Mix: Cliente Final vs Repasse --}}
    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.5rem;">
        <h3 style="font-size: 0.8125rem; font-weight: 600; color: #e2e8f0; margin-bottom: 1rem;">Cliente Final vs Repasse</h3>
        <div style="height: 200px;"><canvas id="execTypeCanvas"></canvas></div>
    </div>
</div>

{{-- Formas de Pagamento --}}
<div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
    <h3 style="font-size: 0.8125rem; font-weight: 600; color: #e2e8f0; margin-bottom: 1rem;">Formas de Pagamento</h3>
    <div style="height: 200px;"><canvas id="execPayCanvas"></canvas></div>
</div>

{{-- Top 5 Modelos + Top 5 Vendedores --}}
<div class="intern-grid-2col" style="display: grid; gap: 1rem;">
    {{-- Top 5 Modelos --}}
    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.5rem;">
        <h3 style="font-size: 0.8125rem; font-weight: 600; color: #e2e8f0; margin-bottom: 1rem;">📱 Top 5 Modelos</h3>
        <template x-if="(execData?.gerencial?.top_models || []).length === 0">
            <p style="color: #475569; font-size: 0.8125rem;">Sem dados no período.</p>
        </template>
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <template x-for="(model, idx) in (execData?.gerencial?.top_models || [])" :key="'em'+idx">
                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                        <span style="font-size: 0.75rem; font-weight: 500; color: #cbd5e1;" x-text="model.name"></span>
                        <span style="font-size: 0.75rem; font-weight: 700; color: #a5b4fc;" x-text="model.qty + ' un.'"></span>
                    </div>
                    <div style="height: 4px; background: rgba(255,255,255,0.06); border-radius: 2px; overflow: hidden;">
                        <div style="height: 100%; background: linear-gradient(90deg, #6366f1, #818cf8); border-radius: 2px; transition: width 0.3s;"
                             :style="'width: ' + Math.min(100, (model.qty / Math.max(1, execData?.gerencial?.top_models?.[0]?.qty || 1)) * 100) + '%'"></div>
                    </div>
                    <p style="font-size: 0.625rem; color: #475569; margin-top: 0.125rem;">
                        <span x-show="!hideValues" x-text="formatCurrency(model.total)"></span>
                        <span x-show="hideValues" x-cloak>R$ ••••</span>
                    </p>
                </div>
            </template>
        </div>
    </div>

    {{-- Top 5 Vendedores --}}
    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.5rem;">
        <h3 style="font-size: 0.8125rem; font-weight: 600; color: #e2e8f0; margin-bottom: 1rem;">👑 Top 5 Vendedores</h3>
        <template x-if="(execData?.gerencial?.top_sellers || []).length === 0">
            <p style="color: #475569; font-size: 0.8125rem;">Sem dados no período.</p>
        </template>
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <template x-for="(seller, idx) in (execData?.gerencial?.top_sellers || [])" :key="'es'+idx">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <span style="font-size: 1.125rem; font-weight: 700; min-width: 1.75rem; text-align: center;"
                          :style="idx === 0 ? 'color: #fbbf24;' : idx === 1 ? 'color: #94a3b8;' : idx === 2 ? 'color: #d97706;' : 'color: #475569;'"
                          x-text="idx + 1 + 'º'"></span>
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                            <span style="font-size: 0.75rem; font-weight: 600; color: #e2e8f0;" x-text="seller.name"></span>
                            <span style="font-size: 0.75rem; font-weight: 700; color: #a5b4fc;" x-text="seller.count + ' vendas'"></span>
                        </div>
                        <div style="height: 4px; background: rgba(255,255,255,0.06); border-radius: 2px; overflow: hidden;">
                            <div style="height: 100%; background: linear-gradient(90deg, #6366f1, #8b5cf6); border-radius: 2px; transition: width 0.3s;"
                                 :style="'width: ' + Math.min(100, (seller.count / Math.max(1, execData?.gerencial?.top_sellers?.[0]?.count || 1)) * 100) + '%'"></div>
                        </div>
                        <p style="font-size: 0.625rem; color: #475569; margin-top: 0.125rem;">
                            <span x-show="!hideValues" x-text="formatCurrency(seller.total)"></span>
                            <span x-show="hideValues" x-cloak>R$ ••••</span>
                        </p>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
