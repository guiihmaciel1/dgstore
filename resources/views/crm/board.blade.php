<x-app-layout>
    <x-slot name="title">CRM - Pipeline</x-slot>
    @php
        $activeStages = \App\Domain\CRM\Models\PipelineStage::where('is_won', false)->where('is_lost', false)->orderBy('position')->get();
    @endphp
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
                    <select onchange="window.location.href='{{ route('crm.board') }}?user_id=' + this.value"
                            style="padding: 0.4rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.8rem; background: white;">
                        <option value="">Todos os vendedores</option>
                        @foreach($sellers as $seller)
                            <option value="{{ $seller->id }}" {{ $filterUserId === $seller->id ? 'selected' : '' }}>
                                {{ $seller->name }}
                            </option>
                        @endforeach
                    </select>
                    <a href="{{ route('crm.history') }}" style="padding: 0.5rem 1rem; font-size: 0.8rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; color: #374151; text-decoration: none; background: white; font-weight: 500;">
                        Histórico
                    </a>
                    <button @click="showCustomerForm = true" type="button"
                            style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1rem; background: white; color: #374151; font-size: 0.8rem; font-weight: 500; border-radius: 0.5rem; border: 1px solid #e5e7eb; cursor: pointer;">
                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        + Cliente
                    </button>
                    <button @click="showScheduleForm = true" type="button"
                            style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1rem; background: white; color: #374151; font-size: 0.8rem; font-weight: 500; border-radius: 0.5rem; border: 1px solid #e5e7eb; cursor: pointer;">
                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        + Agendamento
                    </button>
                    <button @click="showNewDealForm = true" type="button"
                            style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1rem; background: #111827; color: white; font-size: 0.8rem; font-weight: 600; border-radius: 0.5rem; border: none; cursor: pointer;">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Novo Negócio
                    </button>
                </div>
            </div>

            @include('crm.partials.modal-new-deal')
            @include('crm.partials.modal-new-customer')
            @include('crm.partials.modal-new-schedule')

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
                                @php $interest = $deal->productInterests->first(); @endphp
                                <a href="{{ route('crm.show', $deal) }}" class="deal-card" data-deal-id="{{ $deal->id }}"
                                   style="display: block; background: white; border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 0.75rem; cursor: grab; text-decoration: none; transition: box-shadow 0.15s; {{ $deal->isOverdue() ? 'border-left: 3px solid #dc2626;' : '' }}"
                                   onmouseover="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'" onmouseout="this.style.boxShadow='none'">

                                    {{-- Badges: produto + condição --}}
                                    <div style="display: flex; gap: 0.25rem; flex-wrap: wrap; margin-bottom: 0.375rem;">
                                        @if($deal->product_interest)
                                            <span style="font-size: 0.65rem; font-weight: 600; color: #374151; background: #f3f4f6; padding: 2px 6px; border-radius: 4px;">
                                                {{ $deal->product_interest }}
                                            </span>
                                        @endif
                                        @if($interest && $interest->condition)
                                            <span style="font-size: 0.6rem; font-weight: 600; padding: 2px 6px; border-radius: 4px; {{ $interest->condition === 'novo' ? 'background: #dbeafe; color: #1e40af;' : 'background: #fef3c7; color: #92400e;' }}">
                                                {{ $interest->condition === 'novo' ? 'Novo' : 'Seminovo' }}
                                            </span>
                                        @endif
                                    </div>

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

                                            @if($deal->days_since_last_activity >= 3)
                                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b;" title="{{ $deal->days_since_last_activity }} dias sem interação"></span>
                                            @endif
                                        </div>
                                    </div>

                                    @if($deal->user)
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
            showCustomerForm: false,
            showScheduleForm: false,
            customerSearch: '',
            customerResults: [],
            selectedCustomerId: '',
            selectedCustomerName: '',
            scheduleCustomerSearch: '',
            scheduleCustomerResults: [],

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

            async searchScheduleCustomers() {
                if (this.scheduleCustomerSearch.length < 2) {
                    this.scheduleCustomerResults = [];
                    return;
                }
                try {
                    const res = await fetch(`/api/customers/search?q=${encodeURIComponent(this.scheduleCustomerSearch)}`);
                    const data = await res.json();
                    this.scheduleCustomerResults = data.slice(0, 5);
                } catch (e) {
                    this.scheduleCustomerResults = [];
                }
            },

            selectScheduleCustomer(customer) {
                document.getElementById('schedule_customer_id').value = customer.id;
                document.getElementById('schedule_customer_name').value = customer.name;
                document.getElementById('schedule_customer_phone').value = customer.phone || '';
                this.scheduleCustomerSearch = '';
                this.scheduleCustomerResults = [];
            },
        };
    }
    </script>
</x-app-layout>
