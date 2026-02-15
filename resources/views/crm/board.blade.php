<x-app-layout>
    <div class="py-4" x-data="crmBoard()" x-init="init()">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 0.75rem 1rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 0.5rem; color: #166534; font-size: 0.875rem;">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Header --}}
            <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Pipeline de Vendas</h1>
                    <div style="display: flex; gap: 1rem; margin-top: 0.375rem; flex-wrap: wrap;">
                        <span style="font-size: 0.75rem; color: #6b7280;">
                            <strong style="color: #111827;">{{ $metrics['total_open'] }}</strong> em aberto
                            &middot;
                            <strong style="color: #059669;">R$ {{ number_format($metrics['total_value'], 2, ',', '.') }}</strong> no pipeline
                        </span>
                        <span style="font-size: 0.75rem; color: #6b7280;">
                            Mês:
                            <strong style="color: #059669;">{{ $metrics['won_month'] }} ganhos</strong> (R$ {{ number_format($metrics['won_value_month'], 2, ',', '.') }})
                            &middot;
                            <strong style="color: #dc2626;">{{ $metrics['lost_month'] }} perdidos</strong>
                        </span>
                    </div>
                </div>
                <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                    @if($isAdmin)
                        <select onchange="window.location.href='{{ route('crm.board') }}?user_id=' + this.value"
                                style="padding: 0.4rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.8rem; background: white;">
                            <option value="">Todos os vendedores</option>
                            @foreach($sellers as $seller)
                                <option value="{{ $seller->id }}" {{ $filterUserId === $seller->id ? 'selected' : '' }}>
                                    {{ $seller->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    <a href="{{ route('crm.history') }}" style="padding: 0.5rem 1rem; font-size: 0.8rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; color: #374151; text-decoration: none; background: white; font-weight: 500;">
                        Histórico
                    </a>
                    <button @click="showNewDealForm = true" type="button"
                            style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1rem; background: #111827; color: white; font-size: 0.8rem; font-weight: 600; border-radius: 0.5rem; border: none; cursor: pointer;">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Novo Negócio
                    </button>
                </div>
            </div>

            {{-- Modal: Novo Negócio --}}
            <div x-show="showNewDealForm" x-transition.opacity style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.4);" x-cloak>
                <div @click.away="showNewDealForm = false" style="background: white; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 32rem; box-shadow: 0 25px 50px rgba(0,0,0,0.25); max-height: 90vh; overflow-y: auto;">
                    <h2 style="font-size: 1.125rem; font-weight: 700; color: #111827; margin-bottom: 1rem;">Novo Negócio</h2>
                    <form method="POST" action="{{ route('crm.deals.store') }}">
                        @csrf
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <div>
                                <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Título <span style="color: #dc2626;">*</span></label>
                                <input type="text" name="title" required value="{{ old('title') }}" placeholder="Ex: iPhone 16 Pro Max - João"
                                       style="width: 100%; padding: 0.5rem; border: 1px solid {{ $errors->has('title') ? '#fca5a5' : '#e5e7eb' }}; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <div>
                                    <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Produto de Interesse</label>
                                    <input type="text" name="product_interest" value="{{ old('product_interest') }}" placeholder="iPhone 16 Pro Max 256GB"
                                           style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                                </div>
                                <div>
                                    <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Valor (R$)</label>
                                    <input type="number" name="value" step="0.01" value="{{ old('value') }}" placeholder="0,00"
                                           style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                                </div>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <div>
                                    <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Telefone / WhatsApp</label>
                                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="5511999999999"
                                           style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                                </div>
                                <div>
                                    <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Previsão de Fechamento</label>
                                    <input type="date" name="expected_close_date" value="{{ old('expected_close_date') }}"
                                           style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                                </div>
                            </div>
                            <div>
                                <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Etapa</label>
                                <select name="pipeline_stage_id" style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                                    @foreach($activeStages as $stage)
                                        <option value="{{ $stage->id }}" {{ $stage->is_default ? 'selected' : (old('pipeline_stage_id') === $stage->id ? 'selected' : '') }}>{{ $stage->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Buscar Cliente</label>
                                <div style="position: relative;">
                                    <input type="text" x-model="customerSearch" @input.debounce.300ms="searchCustomers()"
                                           placeholder="Digite o nome do cliente..."
                                           style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                                    <input type="hidden" name="customer_id" x-model="selectedCustomerId">
                                    <div x-show="customerResults.length > 0" style="position: absolute; z-index: 10; width: 100%; background: white; border: 1px solid #e5e7eb; border-radius: 0.375rem; margin-top: 2px; max-height: 150px; overflow-y: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                        <template x-for="c in customerResults" :key="c.id">
                                            <div @click="selectCustomer(c)" style="padding: 0.5rem; cursor: pointer; font-size: 0.8rem; border-bottom: 1px solid #f3f4f6;"
                                                 class="hover:bg-gray-50">
                                                <span x-text="c.name" style="font-weight: 600;"></span>
                                                <span x-text="c.phone ? ' - ' + c.phone : ''" style="color: #6b7280;"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <div x-show="selectedCustomerName" style="margin-top: 0.25rem; font-size: 0.75rem; color: #059669;">
                                    Selecionado: <strong x-text="selectedCustomerName"></strong>
                                    <button type="button" @click="clearCustomer()" style="color: #dc2626; margin-left: 0.5rem; font-size: 0.7rem; border: none; background: none; cursor: pointer;">[remover]</button>
                                </div>
                            </div>
                            <div>
                                <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Observações</label>
                                <textarea name="description" rows="2" placeholder="Detalhes sobre o interesse, condições, etc."
                                          style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem; resize: vertical;">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        @if($errors->any())
                            <div style="margin-top: 0.75rem; color: #dc2626; font-size: 0.75rem;">
                                @foreach($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 1rem;">
                            <button @click.prevent="showNewDealForm = false" type="button"
                                    style="padding: 0.5rem 1rem; font-size: 0.8rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; background: white; color: #6b7280; cursor: pointer;">
                                Cancelar
                            </button>
                            <button type="submit"
                                    style="padding: 0.5rem 1rem; font-size: 0.8rem; background: #111827; color: white; border-radius: 0.375rem; border: none; cursor: pointer; font-weight: 600;">
                                Criar Negócio
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Kanban Board --}}
            <div style="display: flex; gap: 0.75rem; overflow-x: auto; padding-bottom: 1rem; min-height: 65vh;">
                @foreach($activeStages as $stage)
                    <div style="min-width: 280px; max-width: 320px; flex-shrink: 0; display: flex; flex-direction: column; background: #f9fafb; border-radius: 0.75rem; border: 1px solid #e5e7eb;">
                        {{-- Stage Header --}}
                        <div style="padding: 0.75rem 1rem; border-bottom: 2px solid {{ $stage->color }};">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="width: 10px; height: 10px; border-radius: 50%; background: {{ $stage->color }};"></span>
                                    <span style="font-size: 0.8rem; font-weight: 700; color: #111827;">{{ $stage->name }}</span>
                                </div>
                                <span style="font-size: 0.7rem; font-weight: 600; color: #6b7280; background: #e5e7eb; padding: 2px 8px; border-radius: 9999px;">
                                    {{ ($dealsByStage[$stage->id] ?? collect())->count() }}
                                </span>
                            </div>
                            @php $stageValue = ($dealsByStage[$stage->id] ?? collect())->sum('value'); @endphp
                            @if($stageValue > 0)
                                <div style="font-size: 0.7rem; color: #6b7280; margin-top: 0.25rem;">
                                    R$ {{ number_format($stageValue, 2, ',', '.') }}
                                </div>
                            @endif
                        </div>

                        {{-- Cards Container (sortable) --}}
                        <div class="deal-column" data-stage-id="{{ $stage->id }}"
                             style="flex: 1; padding: 0.5rem; display: flex; flex-direction: column; gap: 0.5rem; overflow-y: auto; min-height: 100px;">
                            @foreach(($dealsByStage[$stage->id] ?? collect()) as $deal)
                                <a href="{{ route('crm.show', $deal) }}" class="deal-card" data-deal-id="{{ $deal->id }}"
                                   style="display: block; background: white; border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 0.75rem; cursor: grab; text-decoration: none; transition: box-shadow 0.15s; {{ $deal->isOverdue() ? 'border-left: 3px solid #dc2626;' : '' }}"
                                   onmouseover="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'" onmouseout="this.style.boxShadow='none'">
                                    {{-- Product interest badge --}}
                                    @if($deal->product_interest)
                                        <div style="font-size: 0.65rem; font-weight: 600; color: #374151; background: #f3f4f6; padding: 2px 6px; border-radius: 4px; display: inline-block; margin-bottom: 0.375rem;">
                                            {{ $deal->product_interest }}
                                        </div>
                                    @endif

                                    <div style="font-size: 0.8125rem; font-weight: 600; color: #111827; line-height: 1.3;">{{ $deal->title }}</div>

                                    @if($deal->customer)
                                        <div style="font-size: 0.7rem; color: #6b7280; margin-top: 0.25rem; display: flex; align-items: center; gap: 0.25rem;">
                                            <svg style="width: 12px; height: 12px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            {{ $deal->customer->name }}
                                        </div>
                                    @endif

                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.5rem;">
                                        @if($deal->value)
                                            <span style="font-size: 0.75rem; font-weight: 700; color: #059669;">
                                                R$ {{ number_format((float)$deal->value, 2, ',', '.') }}
                                            </span>
                                        @else
                                            <span></span>
                                        @endif

                                        <div style="display: flex; align-items: center; gap: 0.375rem;">
                                            @if($deal->isOverdue())
                                                <span style="font-size: 0.6rem; font-weight: 700; color: #dc2626;" title="Atrasado">
                                                    {{ $deal->expected_close_date->format('d/m') }}
                                                </span>
                                            @elseif($deal->expected_close_date)
                                                <span style="font-size: 0.6rem; color: #9ca3af;">
                                                    {{ $deal->expected_close_date->format('d/m') }}
                                                </span>
                                            @endif

                                            @if($deal->whatsapp_link)
                                                <span style="width: 14px; height: 14px; color: #16a34a;" title="Tem WhatsApp">
                                                    <svg viewBox="0 0 24 24" fill="currentColor" style="width: 14px; height: 14px;">
                                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                                    </svg>
                                                </span>
                                            @endif

                                            @if(!$isAdmin && $deal->days_since_last_activity >= 3)
                                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b;" title="{{ $deal->days_since_last_activity }} dias sem interação"></span>
                                            @endif
                                        </div>
                                    </div>

                                    @if($isAdmin && $deal->user)
                                        <div style="font-size: 0.6rem; color: #9ca3af; margin-top: 0.375rem;">
                                            {{ $deal->user->name }}
                                        </div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
    <script>
    function crmBoard() {
        return {
            showNewDealForm: {{ $errors->any() ? 'true' : 'false' }},
            customerSearch: '',
            customerResults: [],
            selectedCustomerId: '',
            selectedCustomerName: '',

            init() {
                this.initSortable();
            },

            initSortable() {
                document.querySelectorAll('.deal-column').forEach(column => {
                    new Sortable(column, {
                        group: 'deals',
                        animation: 150,
                        ghostClass: 'opacity-30',
                        dragClass: 'shadow-xl',
                        handle: '.deal-card',
                        draggable: '.deal-card',
                        onEnd: (evt) => {
                            const dealId = evt.item.dataset.dealId;
                            const newStageId = evt.to.dataset.stageId;
                            const newIndex = evt.newIndex;

                            // Prevent navigation on drag
                            evt.item.addEventListener('click', function preventClick(e) {
                                e.preventDefault();
                                evt.item.removeEventListener('click', preventClick);
                            }, { once: true });

                            fetch(`/crm/deals/${dealId}/move`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    pipeline_stage_id: newStageId,
                                    position: newIndex,
                                }),
                            }).catch(err => {
                                console.error('Erro ao mover deal:', err);
                                window.location.reload();
                            });
                        },
                    });
                });
            },

            async searchCustomers() {
                if (this.customerSearch.length < 2) {
                    this.customerResults = [];
                    return;
                }

                try {
                    const res = await fetch(`/api/customers/search?q=${encodeURIComponent(this.customerSearch)}`);
                    const data = await res.json();
                    this.customerResults = data.slice(0, 5);
                } catch (e) {
                    this.customerResults = [];
                }
            },

            selectCustomer(customer) {
                this.selectedCustomerId = customer.id;
                this.selectedCustomerName = customer.name;
                this.customerSearch = '';
                this.customerResults = [];
            },

            clearCustomer() {
                this.selectedCustomerId = '';
                this.selectedCustomerName = '';
                this.customerSearch = '';
            },
        };
    }
    </script>
</x-app-layout>
