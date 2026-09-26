<x-app-layout>
    <x-slot name="title">Solicitação #{{ $purchaseRequest->request_number }}</x-slot>
    <div class="py-6">
        <div class="px-6 lg:px-8">
            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); border-radius: 0.5rem; color: #6ee7b7;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-radius: 0.5rem; color: #fca5a5;">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Cabeçalho -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-6">
                <div class="flex items-center">
                    <a href="{{ route('purchase-requests.index') }}" style="margin-right: 1rem; padding: 0.5rem; color: #818181; border-radius: 0.5rem;"
                       onmouseover="this.style.backgroundColor='#222222'" onmouseout="this.style.backgroundColor='transparent'">
                        <svg style="height: 1.5rem; width: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 style="font-size: 1.5rem; font-weight: 700; color: #e3e3e3;">{{ $purchaseRequest->request_number }}</h1>
                            <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; {{ $purchaseRequest->status->color() }}">
                                {{ $purchaseRequest->status->label() }}
                            </span>
                            <span style="display: inline-block; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 600; {{ $purchaseRequest->isUpgrade() ? 'background: rgba(124,58,237,0.12); color: #a78bfa;' : 'background: rgba(59,130,246,0.12); color: #60a5fa;' }}">
                                {{ $purchaseRequest->type->label() }}
                            </span>
                        </div>
                        <p style="font-size: 0.875rem; color: #818181; margin-top: 0.25rem;">
                            Criada em {{ $purchaseRequest->created_at->format('d/m/Y \à\s H:i') }} por {{ $purchaseRequest->seller_name }}
                        </p>
                    </div>
                </div>

                @php
                    $isAdmin = auth()->user()->isAdmin();
                    $isOwnerOrAdmin = $isAdmin || auth()->id() === $purchaseRequest->seller_id;
                @endphp

                <!-- Ações -->
                <div class="flex flex-wrap gap-2" x-data="{ showCancelModal: false, showRejectModal: false, showApproveModal: false }">
                    {{-- Admin: Aprovar/Rejeitar --}}
                    @if($isAdmin && $purchaseRequest->canBeApproved())
                        <button type="button" @click="showApproveModal = true"
                                style="padding: 0.625rem 1.25rem; background: rgba(22,163,106,0.15); color: #4ade80; border: 1px solid rgba(22,163,106,0.3); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.375rem;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Aprovar
                        </button>
                        <button type="button" @click="showRejectModal = true"
                                style="padding: 0.625rem 1.25rem; background: rgba(220,38,38,0.1); color: #f87171; border: 1px solid rgba(220,38,38,0.2); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.375rem;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Rejeitar
                        </button>
                    @endif

                    {{-- Marcar como comprada --}}
                    @if($purchaseRequest->canBePurchased())
                        <form method="POST" action="{{ route('purchase-requests.mark-purchased', $purchaseRequest) }}">
                            @csrf
                            <button type="submit"
                                    style="padding: 0.625rem 1.25rem; background: rgba(124,58,237,0.15); color: #a78bfa; border: 1px solid rgba(124,58,237,0.3); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.375rem;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                                </svg>
                                Comprada no Fornecedor
                            </button>
                        </form>
                    @endif

                    {{-- Marcar como entregue --}}
                    @if($purchaseRequest->canBeDelivered())
                        <form method="POST" action="{{ route('purchase-requests.mark-delivered', $purchaseRequest) }}">
                            @csrf
                            <button type="submit"
                                    style="padding: 0.625rem 1.25rem; background: rgba(6,182,212,0.15); color: #22d3ee; border: 1px solid rgba(6,182,212,0.3); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.375rem;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Entregue ao Cliente
                            </button>
                        </form>
                    @endif

                    {{-- Efetivar como venda --}}
                    @if($purchaseRequest->canBeConverted())
                        <form method="POST" action="{{ route('purchase-requests.convert', $purchaseRequest) }}">
                            @csrf
                            <button type="submit"
                                    style="padding: 0.625rem 1.25rem; background: rgba(22,163,106,0.15); color: #4ade80; border: 1px solid rgba(22,163,106,0.3); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.375rem;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Efetivar Venda
                            </button>
                        </form>
                    @endif

                    {{-- Cancelar --}}
                    @if($isOwnerOrAdmin && $purchaseRequest->canBeCancelled())
                        <button type="button" @click="showCancelModal = true"
                                style="padding: 0.625rem 1.25rem; background: rgba(220,38,38,0.1); color: #f87171; border: 1px solid rgba(220,38,38,0.2); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.375rem;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancelar
                        </button>
                    @endif

                    <!-- Modal Aprovar -->
                    <div x-show="showApproveModal" x-cloak style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.7);">
                        <div @click.outside="showApproveModal = false" style="background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 1rem; padding: 1.5rem; width: 100%; max-width: 28rem;">
                            <h3 style="font-size: 1.125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 1rem;">Aprovar Solicitação</h3>
                            <p style="font-size: 0.875rem; color: #818181; margin-bottom: 1rem;">Ao aprovar, a vendedora estará autorizada a comprar no fornecedor.</p>
                            <form method="POST" action="{{ route('purchase-requests.approve', $purchaseRequest) }}">
                                @csrf
                                <div style="margin-bottom: 1rem;">
                                    <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Observações do admin (opcional)</label>
                                    <textarea name="admin_notes" rows="3"
                                              style="width: 100%; padding: 0.75rem; background: #141414; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem; resize: none;"
                                              placeholder="Ex: Verificar preço com fornecedor X..."></textarea>
                                </div>
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                    <button type="button" @click="showApproveModal = false"
                                            style="padding: 0.625rem 1.25rem; color: #818181; font-size: 0.875rem; cursor: pointer; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem;">
                                        Voltar
                                    </button>
                                    <button type="submit"
                                            style="padding: 0.625rem 1.25rem; background: rgba(22,163,106,0.15); color: #4ade80; border: 1px solid rgba(22,163,106,0.3); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
                                        Confirmar Aprovação
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Modal Rejeitar -->
                    <div x-show="showRejectModal" x-cloak style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.7);">
                        <div @click.outside="showRejectModal = false" style="background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 1rem; padding: 1.5rem; width: 100%; max-width: 28rem;">
                            <h3 style="font-size: 1.125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 1rem;">Rejeitar Solicitação</h3>
                            <form method="POST" action="{{ route('purchase-requests.reject', $purchaseRequest) }}">
                                @csrf
                                <div style="margin-bottom: 1rem;">
                                    <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Motivo da rejeição</label>
                                    <textarea name="reason" rows="3"
                                              style="width: 100%; padding: 0.75rem; background: #141414; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem; resize: none;"
                                              placeholder="Informe o motivo..."></textarea>
                                </div>
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                    <button type="button" @click="showRejectModal = false"
                                            style="padding: 0.625rem 1.25rem; color: #818181; font-size: 0.875rem; cursor: pointer; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem;">
                                        Voltar
                                    </button>
                                    <button type="submit"
                                            style="padding: 0.625rem 1.25rem; background: rgba(220,38,38,0.15); color: #f87171; border: 1px solid rgba(220,38,38,0.3); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
                                        Confirmar Rejeição
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Modal Cancelar -->
                    <div x-show="showCancelModal" x-cloak style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.7);">
                        <div @click.outside="showCancelModal = false" style="background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 1rem; padding: 1.5rem; width: 100%; max-width: 28rem;">
                            <h3 style="font-size: 1.125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 1rem;">Cancelar Solicitação</h3>
                            <p style="font-size: 0.875rem; color: #818181; margin-bottom: 1rem;">Atenção: o sinal já recebido precisará ser tratado manualmente (devolver ou reter).</p>
                            <form method="POST" action="{{ route('purchase-requests.cancel', $purchaseRequest) }}">
                                @csrf
                                <div style="margin-bottom: 1rem;">
                                    <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.375rem;">Motivo do cancelamento</label>
                                    <textarea name="reason" rows="3"
                                              style="width: 100%; padding: 0.75rem; background: #141414; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem; resize: none;"
                                              placeholder="Informe o motivo do cancelamento..."></textarea>
                                </div>
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                    <button type="button" @click="showCancelModal = false"
                                            style="padding: 0.625rem 1.25rem; color: #818181; font-size: 0.875rem; cursor: pointer; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem;">
                                        Voltar
                                    </button>
                                    <button type="submit"
                                            style="padding: 0.625rem 1.25rem; background: rgba(220,38,38,0.15); color: #f87171; border: 1px solid rgba(220,38,38,0.3); border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
                                        Confirmar Cancelamento
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline de Status -->
            <div style="margin-bottom: 1.5rem; padding: 1.25rem; background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06);">
                <div style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181; margin-bottom: 1rem;">Progresso</div>
                @php
                    $steps = [
                        ['status' => 'pending', 'label' => 'Solicitada', 'date' => $purchaseRequest->created_at, 'color' => '#fbbf24'],
                        ['status' => 'approved', 'label' => 'Aprovada', 'date' => $purchaseRequest->approved_at, 'color' => '#60a5fa'],
                        ['status' => 'purchased', 'label' => 'Comprada', 'date' => $purchaseRequest->purchased_at, 'color' => '#a78bfa'],
                        ['status' => 'delivered', 'label' => 'Entregue', 'date' => $purchaseRequest->delivered_at, 'color' => '#22d3ee'],
                        ['status' => 'converted', 'label' => 'Efetivada', 'date' => $purchaseRequest->converted_at, 'color' => '#4ade80'],
                    ];
                    $statusOrder = ['pending', 'approved', 'purchased', 'delivered', 'converted'];
                    $currentIdx = array_search($purchaseRequest->status->value, $statusOrder);
                    $isTerminal = $purchaseRequest->isTerminal() && !$purchaseRequest->isConverted();
                @endphp
                <div style="display: flex; align-items: center; gap: 0; overflow-x: auto;">
                    @foreach($steps as $idx => $step)
                        @php
                            $isDone = $step['date'] !== null;
                            $isCurrent = ($currentIdx !== false && $currentIdx === $idx) || ($purchaseRequest->status->value === $step['status']);
                        @endphp
                        <div style="display: flex; align-items: center; {{ $idx < count($steps) - 1 ? 'flex: 1;' : '' }}">
                            <div style="display: flex; flex-direction: column; align-items: center; min-width: 4.5rem;">
                                <div style="width: 1.75rem; height: 1.75rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.6875rem; font-weight: 700;
                                    {{ $isDone ? 'background: ' . $step['color'] . '; color: #000;' : ($isCurrent && !$isTerminal ? 'border: 2px solid ' . $step['color'] . '; color: ' . $step['color'] . ';' : 'border: 2px solid #333; color: #515151;') }}">
                                    @if($isDone)
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    @else
                                        {{ $idx + 1 }}
                                    @endif
                                </div>
                                <div style="font-size: 0.625rem; font-weight: 600; margin-top: 0.375rem; text-align: center; {{ $isDone || $isCurrent ? 'color: #a4a4a4;' : 'color: #515151;' }}">
                                    {{ $step['label'] }}
                                </div>
                                @if($step['date'])
                                    <div style="font-size: 0.5625rem; color: #666; margin-top: 0.125rem;">{{ $step['date']->format('d/m H:i') }}</div>
                                @endif
                            </div>
                            @if($idx < count($steps) - 1)
                                <div style="flex: 1; height: 2px; margin: 0 0.25rem; margin-bottom: 1.5rem; {{ $isDone ? 'background: ' . $step['color'] . ';' : 'background: #333;' }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if($isTerminal)
                    <div style="margin-top: 0.75rem; padding: 0.5rem 0.75rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 600; {{ $purchaseRequest->status->color() }}">
                        {{ $purchaseRequest->status->label() }}
                        @if($purchaseRequest->cancelled_at)
                            em {{ $purchaseRequest->cancelled_at->format('d/m/Y \à\s H:i') }}
                        @endif
                    </div>
                @endif
            </div>

            <!-- Venda convertida -->
            @if($purchaseRequest->isConverted() && $purchaseRequest->convertedSale)
                <div style="margin-bottom: 1.5rem; padding: 1rem; background: rgba(22,163,106,0.08); border: 1px solid rgba(22,163,106,0.2); border-radius: 0.75rem; display: flex; align-items: center; gap: 0.75rem;">
                    <svg style="width: 1.25rem; height: 1.25rem; color: #4ade80; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <div>
                        <span style="font-weight: 600; color: #4ade80;">Efetivada como venda</span>
                        <a href="{{ route('sales.show', $purchaseRequest->convertedSale) }}" style="margin-left: 0.5rem; font-size: 0.875rem; color: #60a5fa; text-decoration: underline;">
                            #{{ $purchaseRequest->convertedSale->sale_number }}
                        </a>
                    </div>
                </div>
            @endif

            <!-- Rejeição -->
            @if($purchaseRequest->isRejected())
                <div style="margin-bottom: 1.5rem; padding: 1rem; background: rgba(220,38,38,0.08); border: 1px solid rgba(220,38,38,0.2); border-radius: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg style="width: 1.25rem; height: 1.25rem; color: #f87171; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span style="font-weight: 600; color: #f87171;">Rejeitada</span>
                        @if($purchaseRequest->approver)
                            <span style="font-size: 0.8125rem; color: #818181;">por {{ $purchaseRequest->approver->name }}</span>
                        @endif
                    </div>
                    @if($purchaseRequest->admin_notes)
                        <div style="margin-top: 0.5rem; font-size: 0.875rem; color: #a4a4a4; padding-left: 1.75rem;">
                            Motivo: {{ $purchaseRequest->admin_notes }}
                        </div>
                    @endif
                </div>
            @endif

            <!-- Cancelamento -->
            @if($purchaseRequest->isCancelled())
                <div style="margin-bottom: 1.5rem; padding: 1rem; background: rgba(220,38,38,0.08); border: 1px solid rgba(220,38,38,0.2); border-radius: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg style="width: 1.25rem; height: 1.25rem; color: #f87171; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span style="font-weight: 600; color: #f87171;">Cancelada</span>
                        <span style="font-size: 0.8125rem; color: #818181;">
                            em {{ $purchaseRequest->cancelled_at?->format('d/m/Y \à\s H:i') }}
                        </span>
                    </div>
                    @if($purchaseRequest->cancelled_reason)
                        <div style="margin-top: 0.5rem; font-size: 0.875rem; color: #a4a4a4; padding-left: 1.75rem;">
                            Motivo: {{ $purchaseRequest->cancelled_reason }}
                        </div>
                    @endif
                </div>
            @endif

            <!-- Expirada -->
            @if($purchaseRequest->isExpired())
                <div style="margin-bottom: 1.5rem; padding: 1rem; background: rgba(107,114,128,0.08); border: 1px solid rgba(107,114,128,0.2); border-radius: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg style="width: 1.25rem; height: 1.25rem; color: #9ca3af; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span style="font-weight: 600; color: #9ca3af;">Expirada</span>
                        <span style="font-size: 0.8125rem; color: #818181;">
                            — a solicitação não foi aprovada dentro do prazo
                        </span>
                    </div>
                </div>
            @endif

            <!-- Admin notes (quando aprovada) -->
            @if($purchaseRequest->isApproved() && $purchaseRequest->admin_notes)
                <div style="margin-bottom: 1.5rem; padding: 1rem; background: rgba(59,130,246,0.06); border: 1px solid rgba(59,130,246,0.15); border-radius: 0.75rem;">
                    <div style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #60a5fa; margin-bottom: 0.375rem;">Observações do Admin</div>
                    <div style="font-size: 0.875rem; color: #a4a4a4; white-space: pre-line;">{{ $purchaseRequest->admin_notes }}</div>
                    @if($purchaseRequest->approver)
                        <div style="font-size: 0.75rem; color: #515151; margin-top: 0.375rem;">— {{ $purchaseRequest->approver->name }}</div>
                    @endif
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Produto Desejado -->
                <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.25rem;">
                    <div style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181; margin-bottom: 1rem;">Produto Desejado</div>

                    <div style="font-size: 1rem; font-weight: 600; color: #e3e3e3;">{{ $purchaseRequest->desired_product }}</div>

                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; margin-top: 1rem;">
                        @if($purchaseRequest->desired_model)
                        <div>
                            <span style="font-size: 0.6875rem; color: #515151;">Modelo</span>
                            <div style="font-size: 0.8125rem; color: #a4a4a4;">{{ $purchaseRequest->desired_model }}</div>
                        </div>
                        @endif
                        @if($purchaseRequest->desired_storage)
                        <div>
                            <span style="font-size: 0.6875rem; color: #515151;">Armazenamento</span>
                            <div style="font-size: 0.8125rem; color: #a4a4a4;">{{ $purchaseRequest->desired_storage }}</div>
                        </div>
                        @endif
                        @if($purchaseRequest->desired_color)
                        <div>
                            <span style="font-size: 0.6875rem; color: #515151;">Cor</span>
                            <div style="font-size: 0.8125rem; color: #a4a4a4;">{{ $purchaseRequest->desired_color }}</div>
                        </div>
                        @endif
                        <div>
                            <span style="font-size: 0.6875rem; color: #515151;">Condição</span>
                            <div style="font-size: 0.8125rem; {{ $purchaseRequest->desired_condition === 'new' ? 'color: #60a5fa;' : 'color: #fbbf24;' }}">
                                {{ $purchaseRequest->desired_condition_label }}
                            </div>
                        </div>
                        @if($purchaseRequest->estimated_cost)
                        <div>
                            <span style="font-size: 0.6875rem; color: #515151;">Custo estimado</span>
                            <div style="font-size: 0.8125rem; color: #fbbf24;">{{ $purchaseRequest->formatted_estimated_cost }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Cliente -->
                <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.25rem;">
                    <div style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181; margin-bottom: 1rem;">Cliente</div>

                    @if($purchaseRequest->customer)
                        <div style="font-size: 1rem; font-weight: 600; color: #e3e3e3;">{{ $purchaseRequest->customer->name }}</div>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; margin-top: 1rem;">
                            <div>
                                <span style="font-size: 0.6875rem; color: #515151;">Telefone</span>
                                <div style="font-size: 0.8125rem; color: #a4a4a4;">{{ $purchaseRequest->customer->formatted_phone ?? $purchaseRequest->customer->phone }}</div>
                            </div>
                            @if($purchaseRequest->customer->cpf)
                                <div>
                                    <span style="font-size: 0.6875rem; color: #515151;">CPF</span>
                                    <div style="font-size: 0.8125rem; color: #a4a4a4;">{{ $purchaseRequest->customer->formatted_cpf }}</div>
                                </div>
                            @endif
                            @if($purchaseRequest->customer->instagram)
                                <div>
                                    <span style="font-size: 0.6875rem; color: #515151;">Instagram</span>
                                    <div style="font-size: 0.8125rem; color: #a4a4a4;">{{ $purchaseRequest->customer->formatted_instagram }}</div>
                                </div>
                            @endif
                            @if($purchaseRequest->customer->address)
                                <div class="col-span-2">
                                    <span style="font-size: 0.6875rem; color: #515151;">Endereço</span>
                                    <div style="font-size: 0.8125rem; color: #a4a4a4;">{{ $purchaseRequest->customer->address }}</div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div style="font-size: 0.875rem; color: #515151;">Cliente removido</div>
                    @endif
                </div>
            </div>

            <!-- Proposta Financeira -->
            <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.25rem; margin-top: 1.5rem;">
                <div style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181; margin-bottom: 1rem;">Proposta Financeira</div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Valores -->
                    <div style="display: grid; gap: 0.75rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.8125rem; color: #818181;">Preço de venda:</span>
                            <span style="font-size: 0.9375rem; font-weight: 600; color: #e3e3e3;">{{ $purchaseRequest->formatted_sale_price }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.8125rem; color: #818181;">Sinal ({{ $purchaseRequest->down_payment_method_label }}):</span>
                            <span style="font-size: 0.9375rem; font-weight: 600; color: #60a5fa;">- {{ $purchaseRequest->formatted_down_payment }}</span>
                        </div>
                        @if($purchaseRequest->isUpgrade() && $purchaseRequest->trade_in_value)
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 0.8125rem; color: #818181;">Trade-in:</span>
                                <span style="font-size: 0.9375rem; font-weight: 600; color: #a78bfa;">- {{ $purchaseRequest->formatted_trade_in_value }}</span>
                            </div>
                        @endif
                        <div style="border-top: 1px solid rgba(255,255,255,0.06); padding-top: 0.75rem; display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.875rem; font-weight: 600; color: #a4a4a4;">Saldo restante:</span>
                            <span style="font-size: 1.25rem; font-weight: 700; color: #4ade80;">{{ $purchaseRequest->formatted_remaining_balance }}</span>
                        </div>
                    </div>

                    <!-- Pagamento -->
                    <div style="display: grid; gap: 0.75rem; align-content: start;">
                        <div>
                            <span style="font-size: 0.6875rem; color: #515151;">Pagamento do restante</span>
                            <div style="font-size: 0.875rem; color: #a4a4a4; margin-top: 0.125rem;">{{ $purchaseRequest->payment_method_label }}</div>
                        </div>
                        @if($purchaseRequest->payment_method === 'credit_card' && $purchaseRequest->installments)
                            <div>
                                <span style="font-size: 0.6875rem; color: #515151;">Parcelas</span>
                                <div style="font-size: 0.875rem; color: #a4a4a4; margin-top: 0.125rem;">{{ $purchaseRequest->installments }}x</div>
                            </div>
                        @endif
                    </div>

                    <!-- Upgrade -->
                    <div style="display: grid; gap: 0.75rem; align-content: start;">
                        @if($purchaseRequest->isUpgrade())
                            <div>
                                <span style="font-size: 0.6875rem; color: #515151;">Aparelho do Cliente</span>
                                <div style="font-size: 0.875rem; color: #a78bfa; margin-top: 0.125rem;">{{ $purchaseRequest->trade_in_device }}</div>
                            </div>
                            <div>
                                <span style="font-size: 0.6875rem; color: #515151;">Valor Avaliado</span>
                                <div style="font-size: 0.875rem; color: #a78bfa; margin-top: 0.125rem;">{{ $purchaseRequest->formatted_trade_in_value }}</div>
                            </div>
                            @if($purchaseRequest->upgrade_difference)
                            <div>
                                <span style="font-size: 0.6875rem; color: #515151;">Diferença (cliente paga)</span>
                                <div style="font-size: 0.875rem; font-weight: 600; color: #c4b5fd; margin-top: 0.125rem;">{{ $purchaseRequest->formatted_upgrade_difference }}</div>
                            </div>
                            @endif
                        @else
                            <div style="font-size: 0.8125rem; color: #515151;">Compra direta — sem trade-in</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Entrega -->
            @if($purchaseRequest->isDelivery())
                <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.25rem; margin-top: 1.5rem;">
                    <div style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181; margin-bottom: 1rem;">Dados de Entrega</div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(12rem, 1fr)); gap: 0.75rem;">
                        <div>
                            <span style="font-size: 0.6875rem; color: #515151;">Endereço</span>
                            <div style="font-size: 0.875rem; color: #a4a4a4;">{{ $purchaseRequest->delivery_address }}</div>
                        </div>
                        @if($purchaseRequest->delivery_time)
                        <div>
                            <span style="font-size: 0.6875rem; color: #515151;">Horário</span>
                            <div style="font-size: 0.875rem; color: #a4a4a4;">{{ $purchaseRequest->delivery_time }}</div>
                        </div>
                        @endif
                        @if($purchaseRequest->delivery_notes)
                        <div class="col-span-full">
                            <span style="font-size: 0.6875rem; color: #515151;">Observações</span>
                            <div style="font-size: 0.875rem; color: #a4a4a4; white-space: pre-line;">{{ $purchaseRequest->delivery_notes }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Observações -->
            @if($purchaseRequest->notes)
                <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.25rem; margin-top: 1.5rem;">
                    <div style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181; margin-bottom: 0.5rem;">Observações</div>
                    <div style="font-size: 0.875rem; color: #a4a4a4; white-space: pre-line;">{{ $purchaseRequest->notes }}</div>
                </div>
            @endif

            <!-- Info -->
            <div style="background: #141414; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.06); padding: 1.25rem; margin-top: 1.5rem;">
                <div style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #818181; margin-bottom: 0.75rem;">Informações</div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(10rem, 1fr)); gap: 0.75rem;">
                    <div>
                        <span style="font-size: 0.6875rem; color: #515151;">Vendedora</span>
                        <div style="font-size: 0.8125rem; color: #a4a4a4;">{{ $purchaseRequest->seller_name }}</div>
                    </div>
                    <div>
                        <span style="font-size: 0.6875rem; color: #515151;">Criada em</span>
                        <div style="font-size: 0.8125rem; color: #a4a4a4;">{{ $purchaseRequest->created_at->format('d/m/Y \à\s H:i') }}</div>
                    </div>
                    @if($purchaseRequest->approver)
                    <div>
                        <span style="font-size: 0.6875rem; color: #515151;">{{ $purchaseRequest->isRejected() ? 'Rejeitada' : 'Aprovada' }} por</span>
                        <div style="font-size: 0.8125rem; color: #a4a4a4;">{{ $purchaseRequest->approver->name }}</div>
                    </div>
                    @endif
                    @if($purchaseRequest->expires_at && $purchaseRequest->isPending())
                    <div>
                        <span style="font-size: 0.6875rem; color: #515151;">Expira em</span>
                        <div style="font-size: 0.8125rem; color: #fbbf24;">{{ $purchaseRequest->expires_at->format('d/m/Y \à\s H:i') }}</div>
                    </div>
                    @endif
                    <div>
                        <span style="font-size: 0.6875rem; color: #515151;">Entrega</span>
                        <div style="font-size: 0.8125rem; color: #a4a4a4;">{{ $purchaseRequest->delivery_type->label() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
