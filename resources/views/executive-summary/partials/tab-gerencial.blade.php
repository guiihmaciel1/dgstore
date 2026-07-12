{{-- Dashboard Gerencial --}}
<div>
    {{-- KPI Cards - Linha 1 --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
        <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb;">
            <p style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Total Vendas</p>
            <p style="font-size: 1.5rem; font-weight: 700; color: #111827;" x-text="data.gerencial?.total_sales ?? 0"></p>
            <p style="font-size: 0.8125rem; color: #6b7280;" x-text="money(data.gerencial?.total_revenue)"></p>
        </div>
        <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb;">
            <p style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Valor Investido</p>
            <p style="font-size: 1.5rem; font-weight: 700; color: #ef4444;" x-text="money(data.gerencial?.total_cost)"></p>
        </div>
        <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb;">
            <p style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Lucro Bruto</p>
            <p style="font-size: 1.5rem; font-weight: 700;"
               :style="(data.gerencial?.total_profit ?? 0) >= 0 ? 'color: #059669;' : 'color: #dc2626;'"
               x-text="money(data.gerencial?.total_profit)"></p>
        </div>
        <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb;">
            <p style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Margem</p>
            <p style="font-size: 1.5rem; font-weight: 700;"
               :style="(data.gerencial?.margin ?? 0) >= 0 ? 'color: #059669;' : 'color: #dc2626;'"
               x-text="(data.gerencial?.margin ?? 0) + '%'"></p>
        </div>
    </div>

    {{-- KPI Cards - Linha 2 --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb;">
            <p style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Ticket Médio</p>
            <p style="font-size: 1.5rem; font-weight: 700; color: #111827;" x-text="money(data.gerencial?.average_ticket)"></p>
        </div>
        <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb;">
            <p style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">iPhones Novos</p>
            <p style="font-size: 1.5rem; font-weight: 700; color: #6366f1;" x-text="data.gerencial?.iphone_new?.qty ?? 0"></p>
            <p style="font-size: 0.8125rem; color: #6b7280;" x-text="money(data.gerencial?.iphone_new?.value)"></p>
        </div>
        <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb;">
            <p style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Seminovos</p>
            <p style="font-size: 1.5rem; font-weight: 700; color: #f59e0b;" x-text="data.gerencial?.iphone_used?.qty ?? 0"></p>
            <p style="font-size: 0.8125rem; color: #6b7280;" x-text="money(data.gerencial?.iphone_used?.value)"></p>
        </div>
        <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb;">
            <p style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Acessórios</p>
            <p style="font-size: 1.5rem; font-weight: 700; color: #8b5cf6;" x-text="data.gerencial?.accessories?.qty ?? 0"></p>
            <p style="font-size: 0.8125rem; color: #6b7280;" x-text="money(data.gerencial?.accessories?.value)"></p>
        </div>
    </div>

    {{-- Charts Grid --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
        {{-- Mix de Vendas: Novos vs Seminovos --}}
        <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb;">
            <h3 style="font-size: 0.9375rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Novos vs Seminovos</h3>
            <div style="height: 220px;">
                <canvas id="mixChartCanvas"></canvas>
            </div>
        </div>

        {{-- Cliente Final vs Repasse --}}
        <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb;">
            <h3 style="font-size: 0.9375rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Cliente Final vs Repasse</h3>
            <div style="height: 220px;">
                <canvas id="typeChartCanvas"></canvas>
            </div>
        </div>
    </div>

    {{-- Formas de Pagamento --}}
    <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb; margin-bottom: 1.5rem;">
        <h3 style="font-size: 0.9375rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Formas de Pagamento</h3>
        <div style="height: 220px;">
            <canvas id="paymentChartCanvas"></canvas>
        </div>
    </div>

    {{-- Top 5 Modelos + Top 5 Vendedores --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        {{-- Top 5 Modelos --}}
        <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb;">
            <h3 style="font-size: 0.9375rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">📱 Top 5 Modelos</h3>
            <template x-if="(data.gerencial?.top_models || []).length === 0">
                <p style="color: #9ca3af; font-size: 0.875rem;">Sem dados.</p>
            </template>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <template x-for="(model, idx) in (data.gerencial?.top_models || [])" :key="'tm'+idx">
                    <div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                            <span style="font-size: 0.8125rem; font-weight: 500; color: #374151;" x-text="model.name"></span>
                            <span style="font-size: 0.8125rem; font-weight: 700; color: #4f46e5;" x-text="model.qty + ' un.'"></span>
                        </div>
                        <div style="height: 0.375rem; background: #e5e7eb; border-radius: 9999px; overflow: hidden;">
                            <div style="height: 100%; background: #6366f1; border-radius: 9999px; transition: width 0.3s;"
                                 :style="'width: ' + Math.min(100, (model.qty / Math.max(1, data.gerencial?.top_models?.[0]?.qty || 1)) * 100) + '%'"></div>
                        </div>
                        <p style="font-size: 0.6875rem; color: #9ca3af; margin-top: 0.125rem;" x-text="money(model.total)"></p>
                    </div>
                </template>
            </div>
        </div>

        {{-- Top 5 Vendedores --}}
        <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb;">
            <h3 style="font-size: 0.9375rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">👑 Top 5 Vendedores</h3>
            <template x-if="(data.gerencial?.top_sellers || []).length === 0">
                <p style="color: #9ca3af; font-size: 0.875rem;">Sem dados.</p>
            </template>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <template x-for="(seller, idx) in (data.gerencial?.top_sellers || [])" :key="'ts'+idx">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span style="font-size: 1.25rem; font-weight: 700; min-width: 2rem; text-align: center;"
                              :style="idx === 0 ? 'color: #f59e0b;' : idx === 1 ? 'color: #9ca3af;' : idx === 2 ? 'color: #b45309;' : 'color: #6b7280;'"
                              x-text="idx + 1 + 'º'"></span>
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                                <span style="font-size: 0.8125rem; font-weight: 600; color: #111827;" x-text="seller.name"></span>
                                <span style="font-size: 0.8125rem; font-weight: 700; color: #4f46e5;" x-text="seller.count + ' vendas'"></span>
                            </div>
                            <div style="height: 0.375rem; background: #e5e7eb; border-radius: 9999px; overflow: hidden;">
                                <div style="height: 100%; background: linear-gradient(to right, #6366f1, #8b5cf6); border-radius: 9999px; transition: width 0.3s;"
                                     :style="'width: ' + Math.min(100, (seller.count / Math.max(1, data.gerencial?.top_sellers?.[0]?.count || 1)) * 100) + '%'"></div>
                            </div>
                            <p style="font-size: 0.6875rem; color: #9ca3af; margin-top: 0.125rem;" x-text="money(seller.total)"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
