<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center mb-6">
                <a href="{{ route('stock.consignment.create') }}" class="mr-3 p-2 text-gray-500 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Divergência de Preço Detectada</h1>
                    <p class="text-sm text-gray-500">Existem itens disponíveis do mesmo produto com preço diferente</p>
                </div>
            </div>

            {{-- Resumo da nova entrada --}}
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 0.75rem; padding: 1.25rem; margin-bottom: 1.5rem;">
                <div style="font-size: 0.8rem; font-weight: 600; color: #1e40af; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.375rem;">
                    <svg style="width: 1.125rem; height: 1.125rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Nova Entrada
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 0.75rem;">
                    <div>
                        <span style="font-size: 0.7rem; color: #6b7280; text-transform: uppercase; font-weight: 500;">Produto</span>
                        <div style="font-weight: 600; color: #111827; font-size: 0.875rem;">
                            {{ $formData['name'] }}
                            @if($formData['storage'] ?? null) {{ $formData['storage'] }} @endif
                            @if($formData['color'] ?? null) {{ $formData['color'] }} @endif
                        </div>
                    </div>
                    <div>
                        <span style="font-size: 0.7rem; color: #6b7280; text-transform: uppercase; font-weight: 500;">Fornecedor</span>
                        <div style="font-weight: 600; color: #111827; font-size: 0.875rem;">{{ $selectedSupplier->name }}</div>
                    </div>
                    <div>
                        <span style="font-size: 0.7rem; color: #6b7280; text-transform: uppercase; font-weight: 500;">Novo Custo</span>
                        <div style="font-weight: 700; color: #16a34a; font-size: 1rem;">R$ {{ number_format($formData['supplier_cost'], 2, ',', '.') }}</div>
                    </div>
                    <div>
                        <span style="font-size: 0.7rem; color: #6b7280; text-transform: uppercase; font-weight: 500;">Quantidade</span>
                        <div style="font-weight: 600; color: #111827; font-size: 0.875rem;">{{ $formData['quantity'] }}</div>
                    </div>
                </div>
            </div>

            {{-- Alerta de divergência --}}
            <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 0.75rem; padding: 1.25rem; margin-bottom: 1.5rem;">
                <div style="font-size: 0.875rem; font-weight: 600; color: #92400e; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.375rem;">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    {{ $divergentItems->count() }} {{ $divergentItems->count() === 1 ? 'item disponível tem' : 'itens disponíveis têm' }} preço diferente
                </div>
                <p style="font-size: 0.8125rem; color: #78350f; line-height: 1.5;">
                    Os itens abaixo do mesmo produto estão com custo de fornecedor diferente do novo lote.
                    Você pode atualizar o preço deles para o novo valor ou manter como estão.
                </p>
            </div>

            {{-- Tabela de itens divergentes --}}
            <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1.5rem;">
                <div style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">
                    <h3 style="font-size: 0.875rem; font-weight: 600; color: #111827;">Itens com Preço Divergente</h3>
                </div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <th style="padding: 0.625rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Produto</th>
                                <th style="padding: 0.625rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Lote</th>
                                <th style="padding: 0.625rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">IMEI</th>
                                <th style="padding: 0.625rem 0.75rem; text-align: right; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Custo Atual</th>
                                <th style="padding: 0.625rem 0.75rem; text-align: center; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Disp.</th>
                                <th style="padding: 0.625rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Entrada</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($divergentItems as $divergent)
                                <tr style="border-bottom: 1px solid #f3f4f6;">
                                    <td style="padding: 0.625rem 0.75rem;">
                                        <div style="font-weight: 600; color: #111827; font-size: 0.8125rem;">{{ $divergent->full_name }}</div>
                                    </td>
                                    <td style="padding: 0.625rem 0.75rem; font-size: 0.75rem; color: #6b7280; font-family: monospace;">
                                        {{ $divergent->batch?->batch_code ?? '—' }}
                                    </td>
                                    <td style="padding: 0.625rem 0.75rem; font-size: 0.75rem; color: #6b7280; font-family: monospace;">
                                        {{ $divergent->imei ?? '—' }}
                                    </td>
                                    <td style="padding: 0.625rem 0.75rem; text-align: right;">
                                        @php
                                            $diff = $formData['supplier_cost'] - $divergent->supplier_cost;
                                        @endphp
                                        <div style="font-weight: 700; color: #111827; font-size: 0.875rem;">
                                            R$ {{ number_format($divergent->supplier_cost, 2, ',', '.') }}
                                        </div>
                                        <div style="font-size: 0.7rem; font-weight: 600; {{ $diff > 0 ? 'color: #dc2626;' : 'color: #16a34a;' }}">
                                            {{ $diff > 0 ? '+' : '' }}R$ {{ number_format(abs($diff), 2, ',', '.') }}
                                            {{ $diff > 0 ? 'mais caro' : 'mais barato' }}
                                        </div>
                                    </td>
                                    <td style="padding: 0.625rem 0.75rem; text-align: center; font-size: 0.8125rem;">
                                        {{ $divergent->available_quantity }}
                                    </td>
                                    <td style="padding: 0.625rem 0.75rem; font-size: 0.75rem; color: #6b7280;">
                                        {{ $divergent->received_at->format('d/m/Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Formulário de confirmação --}}
            <form method="POST" action="{{ route('stock.consignment.store-confirmed') }}"
                  x-data="{ updatePrices: true }">
                @csrf

                {{-- Dados do formulário original como hidden --}}
                @foreach($formData as $key => $value)
                    @if(!is_null($value) && $value !== '')
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach

                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.25rem; display: flex; flex-direction: column; gap: 1.25rem;">

                    {{-- Opção de atualizar preços --}}
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.75rem;">
                            O que deseja fazer com os preços dos itens anteriores?
                        </label>
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <label :style="updatePrices ? 'display:flex;align-items:flex-start;gap:0.625rem;padding:0.875rem;border:2px solid #111827;border-radius:0.5rem;cursor:pointer;background:#f9fafb;' : 'display:flex;align-items:flex-start;gap:0.625rem;padding:0.875rem;border:2px solid #e5e7eb;border-radius:0.5rem;cursor:pointer;'">
                                <input type="radio" name="update_prices" value="1" x-model="updatePrices"
                                       :value="true" style="accent-color: #111827; margin-top: 2px;">
                                <div>
                                    <div style="font-weight: 600; font-size: 0.875rem; color: #111827;">Atualizar preços</div>
                                    <div style="font-size: 0.75rem; color: #6b7280; margin-top: 2px;">
                                        Todos os {{ $divergentItems->count() }} {{ $divergentItems->count() === 1 ? 'item' : 'itens' }} acima terão o custo atualizado para
                                        <strong style="color: #16a34a;">R$ {{ number_format($formData['supplier_cost'], 2, ',', '.') }}</strong>
                                    </div>
                                </div>
                            </label>
                            <label :style="!updatePrices ? 'display:flex;align-items:flex-start;gap:0.625rem;padding:0.875rem;border:2px solid #111827;border-radius:0.5rem;cursor:pointer;background:#f9fafb;' : 'display:flex;align-items:flex-start;gap:0.625rem;padding:0.875rem;border:2px solid #e5e7eb;border-radius:0.5rem;cursor:pointer;'">
                                <input type="radio" name="update_prices" value="0" x-model="updatePrices"
                                       :value="false" style="accent-color: #111827; margin-top: 2px;">
                                <div>
                                    <div style="font-weight: 600; font-size: 0.875rem; color: #111827;">Manter preços atuais</div>
                                    <div style="font-size: 0.75rem; color: #6b7280; margin-top: 2px;">
                                        Registrar a nova entrada sem alterar os preços dos itens existentes
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Motivo da alteração (visível apenas quando atualizar preços) --}}
                    <div x-show="updatePrices" x-cloak>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem;">
                            Motivo da alteração de preço <span style="color: #dc2626;">*</span>
                        </label>
                        <textarea name="price_update_reason" rows="2"
                                  placeholder="Ex: Novo lote recebido com preço atualizado pelo fornecedor"
                                  :required="updatePrices"
                                  style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none; resize: vertical;"
                                  onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'"></textarea>
                        <p style="font-size: 0.7rem; color: #9ca3af; margin-top: 0.25rem;">
                            Este motivo será registrado no histórico de alterações de preço para rastreabilidade.
                        </p>
                    </div>

                    {{-- Botões --}}
                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 0.5rem;">
                        <a href="{{ route('stock.consignment.create') }}"
                           style="padding: 0.625rem 1.5rem; color: #6b7280; font-size: 0.875rem; text-decoration: none; border: 1px solid #e5e7eb; border-radius: 0.5rem;">
                            Cancelar
                        </a>
                        <button type="submit"
                                style="padding: 0.625rem 1.5rem; background: #111827; color: white; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;"
                                onmouseover="this.style.background='#1f2937'" onmouseout="this.style.background='#111827'">
                            Confirmar e Registrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
