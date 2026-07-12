{{-- Dashboard Estagiárias --}}
<div>
    {{-- KPI Cards --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb;">
            <p style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Vendas do Time</p>
            <p style="font-size: 1.5rem; font-weight: 700; color: #111827;" x-text="data.estagiarias?.combined?.total_sales ?? 0"></p>
        </div>
        <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb;">
            <p style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Receita Total</p>
            <p style="font-size: 1.5rem; font-weight: 700; color: #111827;" x-text="money(data.estagiarias?.combined?.total_revenue)"></p>
        </div>
        <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb;">
            <p style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Comissões Geradas</p>
            <p style="font-size: 1.5rem; font-weight: 700; color: #10b981;" x-text="money(data.estagiarias?.combined?.total_commission)"></p>
        </div>
        <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb;">
            <p style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Vendas na Semana</p>
            <p style="font-size: 1.5rem; font-weight: 700; color: #6366f1;" x-text="data.estagiarias?.combined?.sales_this_week ?? 0"></p>
        </div>
    </div>

    {{-- Ranking + Metas --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
        {{-- Ranking --}}
        <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb;">
            <h3 style="font-size: 0.9375rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">🏆 Ranking Estagiárias</h3>
            <template x-if="(data.estagiarias?.interns || []).length === 0">
                <p style="color: #9ca3af; font-size: 0.875rem;">Nenhuma estagiária ativa.</p>
            </template>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <template x-for="(intern, idx) in (data.estagiarias?.interns || [])" :key="idx">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span style="font-size: 1.25rem; font-weight: 700; color: #6366f1; min-width: 2rem; text-align: center;" x-text="idx + 1 + 'º'"></span>
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                                <span style="font-size: 0.8125rem; font-weight: 600; color: #111827;" x-text="intern.name"></span>
                                <span style="font-size: 0.8125rem; font-weight: 700; color: #4f46e5;" x-text="intern.sales_count + ' vendas'"></span>
                            </div>
                            <div style="height: 0.375rem; background: #e5e7eb; border-radius: 9999px; overflow: hidden;">
                                <div style="height: 100%; background: #6366f1; border-radius: 9999px; transition: width 0.3s;"
                                     :style="'width: ' + Math.min(100, (intern.sales_count / Math.max(1, (data.estagiarias?.interns?.[0]?.sales_count || 1))) * 100) + '%'"></div>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-top: 0.25rem;">
                                <span style="font-size: 0.6875rem; color: #6b7280;" x-text="money(intern.total_revenue)"></span>
                                <span style="font-size: 0.6875rem; color: #10b981;" x-text="'Comissão: ' + money(intern.commission_earned)"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Metas --}}
        <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb;">
            <h3 style="font-size: 0.9375rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">🎯 Metas</h3>

            {{-- Metas Mensais --}}
            <p style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Metas do Mês</p>
            <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.25rem;">
                <template x-for="(goal, idx) in (data.estagiarias?.goals?.monthly || [])" :key="'mg'+idx">
                    <div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                            <span style="font-size: 0.8125rem; font-weight: 500; color: #374151;" x-text="goal.target + ' vendas'"></span>
                            <span style="font-size: 0.75rem; font-weight: 600;"
                                  :style="goal.reached ? 'color: #059669;' : 'color: #6b7280;'"
                                  x-text="goal.reached ? '✅ Atingida!' : goal.progress + '%'"></span>
                        </div>
                        <div style="height: 0.5rem; background: #e5e7eb; border-radius: 9999px; overflow: hidden;">
                            <div style="height: 100%; border-radius: 9999px; transition: width 0.3s;"
                                 :style="'width: ' + goal.progress + '%; background: ' + (goal.reached ? '#10b981' : '#6366f1')"></div>
                        </div>
                        <p style="font-size: 0.6875rem; color: #9ca3af; margin-top: 0.125rem;" x-text="'🎁 ' + goal.reward"></p>
                    </div>
                </template>
            </div>

            {{-- Meta Semanal --}}
            <p style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Meta da Semana</p>
            <template x-if="data.estagiarias?.goals?.weekly">
                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                        <span style="font-size: 0.8125rem; font-weight: 500; color: #374151;" x-text="(data.estagiarias.goals.weekly.current ?? 0) + ' / ' + data.estagiarias.goals.weekly.target + ' vendas'"></span>
                        <span style="font-size: 0.75rem; font-weight: 600;"
                              :style="data.estagiarias.goals.weekly.reached ? 'color: #059669;' : 'color: #6b7280;'"
                              x-text="data.estagiarias.goals.weekly.reached ? '✅ Atingida!' : 'Faltam ' + data.estagiarias.goals.weekly.remaining"></span>
                    </div>
                    <div style="height: 0.5rem; background: #e5e7eb; border-radius: 9999px; overflow: hidden;">
                        <div style="height: 100%; border-radius: 9999px; transition: width 0.3s;"
                             :style="'width: ' + (data.estagiarias.goals.weekly.progress ?? 0) + '%; background: ' + (data.estagiarias.goals.weekly.reached ? '#10b981' : '#f59e0b')"></div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Gráfico Diário --}}
    <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb;">
        <h3 style="font-size: 0.9375rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">📊 Vendas Diárias</h3>
        <div style="height: 250px;">
            <canvas id="dailyChartCanvas"></canvas>
        </div>
    </div>
</div>
