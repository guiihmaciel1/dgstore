<x-app-layout>
    <div class="py-4" x-data="scheduleApp()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 0.5rem; color: #065f46;">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div style="margin-bottom: 1rem; padding: 1rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem; color: #991b1b;">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Cabeçalho --}}
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Agenda</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Controle de agendamentos · {{ $date->translatedFormat('l, d \d\e F \d\e Y') }}</p>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                    <input type="date" value="{{ $date->format('Y-m-d') }}"
                           style="padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;"
                           onchange="window.location.href='{{ route('schedule.index') }}?date='+this.value+'&attendant={{ $attendantFilter }}'">
                    <select style="padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; background: white;"
                            onchange="window.location.href='{{ route('schedule.index') }}?date={{ $date->format('Y-m-d') }}&attendant='+this.value">
                        <option value="">Todos</option>
                        @foreach($attendants as $key => $name)
                            <option value="{{ $key }}" {{ $attendantFilter === $key ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <button @click="openNewModal()" type="button"
                            style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1rem; background: #111827; color: white; font-size: 0.8rem; font-weight: 600; border-radius: 0.5rem; border: none; cursor: pointer;"
                            onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Novo Agendamento
                    </button>
                </div>
            </div>

            {{-- Navegação de data + resumo --}}
            <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1rem; margin-bottom: 1rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <a href="{{ route('schedule.index', ['date' => $date->copy()->subDay()->format('Y-m-d'), 'attendant' => $attendantFilter]) }}"
                           style="padding: 0.375rem; border-radius: 0.375rem; color: #374151; text-decoration: none; border: 1px solid #e5e7eb; background: white; display: inline-flex;"
                           onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='white'">
                            <svg style="width: 1.125rem; height: 1.125rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                        <div style="text-align: center;">
                            <span style="font-size: 1rem; font-weight: 700; color: #111827;">{{ $date->translatedFormat('l') }}</span>
                            <span style="font-size: 0.8rem; color: #6b7280; margin-left: 0.375rem;">{{ $date->format('d/m/Y') }}</span>
                        </div>
                        <a href="{{ route('schedule.index', ['date' => $date->copy()->addDay()->format('Y-m-d'), 'attendant' => $attendantFilter]) }}"
                           style="padding: 0.375rem; border-radius: 0.375rem; color: #374151; text-decoration: none; border: 1px solid #e5e7eb; background: white; display: inline-flex;"
                           onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='white'">
                            <svg style="width: 1.125rem; height: 1.125rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        @if(!$date->isToday())
                            <a href="{{ route('schedule.index') }}" style="font-size: 0.75rem; color: #4f46e5; text-decoration: none; font-weight: 600; margin-left: 0.25rem;">Hoje</a>
                        @endif
                    </div>
                    <div style="display: flex; align-items: center; gap: 1rem; font-size: 0.8rem;">
                        @foreach($attendants as $key => $name)
                            @php $count = $appointments->where('attendant', $key)->whereNotIn('status', [\App\Domain\Schedule\Enums\AppointmentStatus::Cancelled])->count(); @endphp
                            <span style="display: inline-flex; align-items: center; gap: 0.375rem;">
                                <span style="width: 0.5rem; height: 0.5rem; border-radius: 9999px; background: {{ $key === 'danilo' ? '#2563eb' : '#7c3aed' }};"></span>
                                <span style="font-weight: 600; color: #374151;">{{ $name }}:</span>
                                <span style="color: #6b7280;">{{ $count }}</span>
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Grade de horários --}}
            <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden;">
                {{-- Header da grade --}}
                <div style="display: grid; grid-template-columns: 70px {{ $attendantFilter ? '1fr' : '1fr 1fr' }}; border-bottom: 1px solid #e5e7eb;">
                    <div style="padding: 0.75rem; background: #f9fafb; text-align: center; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Hora</div>
                    @foreach($attendants as $key => $name)
                        @if(!$attendantFilter || $attendantFilter === $key)
                            <div style="padding: 0.75rem; background: #f9fafb; text-align: center; font-size: 0.8rem; font-weight: 600; color: #374151; border-left: 1px solid #e5e7eb;">
                                <span style="display: inline-flex; align-items: center; gap: 0.375rem;">
                                    <span style="width: 0.5rem; height: 0.5rem; border-radius: 9999px; background: {{ $key === 'danilo' ? '#2563eb' : '#7c3aed' }};"></span>
                                    {{ $name }}
                                </span>
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Linhas de horários --}}
                @foreach($timeSlots as $slot)
                    <div style="display: grid; grid-template-columns: 70px {{ $attendantFilter ? '1fr' : '1fr 1fr' }}; border-bottom: 1px solid #f3f4f6; min-height: 48px;" class="schedule-row">
                        <div style="padding: 0.5rem; font-size: 0.75rem; font-family: monospace; color: #9ca3af; text-align: center; display: flex; align-items: center; justify-content: center; background: #fafafa;">
                            {{ $slot }}
                        </div>

                        @foreach($attendants as $attKey => $attName)
                            @if(!$attendantFilter || $attendantFilter === $attKey)
                                <div style="padding: 0.25rem 0.375rem; border-left: 1px solid #f3f4f6; position: relative;">
                                    @php
                                        $appt = ($appointmentsByAttendant[$attKey] ?? collect())->get($slot . ':00') ?? ($appointmentsByAttendant[$attKey] ?? collect())->get($slot);
                                    @endphp

                                    @if($appt)
                                        @php
                                            $slotColors = [
                                                'scheduled' => ['bg' => '#eff6ff', 'border' => '#3b82f6', 'badge_bg' => '#dbeafe', 'badge_color' => '#1d4ed8'],
                                                'confirmed' => ['bg' => '#f0fdf4', 'border' => '#16a34a', 'badge_bg' => '#dcfce7', 'badge_color' => '#166534'],
                                                'completed' => ['bg' => '#f9fafb', 'border' => '#9ca3af', 'badge_bg' => '#f3f4f6', 'badge_color' => '#6b7280'],
                                                'cancelled' => ['bg' => '#fef2f2', 'border' => '#dc2626', 'badge_bg' => '#fee2e2', 'badge_color' => '#991b1b'],
                                                'no_show'   => ['bg' => '#fefce8', 'border' => '#d97706', 'badge_bg' => '#fef3c7', 'badge_color' => '#92400e'],
                                            ];
                                            $sc = $slotColors[$appt->status->value] ?? $slotColors['scheduled'];
                                        @endphp
                                        <div style="background: {{ $sc['bg'] }}; border-left: 3px solid {{ $sc['border'] }}; border-radius: 0.375rem; padding: 0.5rem 0.625rem; cursor: pointer; transition: box-shadow 0.15s; {{ $appt->duration_minutes > 30 ? 'min-height: ' . (($appt->duration_minutes / 30) * 40) . 'px;' : '' }}"
                                             @click="openEditModal({{ $appt->toJson() }})"
                                             onmouseover="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='none'">
                                            <div style="display: flex; align-items: start; justify-content: space-between; gap: 0.375rem;">
                                                <div style="min-width: 0; flex: 1;">
                                                    <p style="font-size: 0.8rem; font-weight: 600; color: #111827; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $appt->customer_name }}</p>
                                                    <p style="font-size: 0.7rem; color: #6b7280; margin: 0.125rem 0 0;">{{ $appt->formatted_start_time }} - {{ $appt->formatted_end_time }}</p>
                                                    @if($appt->service_description)
                                                        <p style="font-size: 0.675rem; color: #6b7280; margin: 0.25rem 0 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $appt->service_description }}</p>
                                                    @endif
                                                </div>
                                                <span style="display: inline-block; padding: 0.125rem 0.375rem; border-radius: 9999px; font-size: 0.6rem; font-weight: 600; white-space: nowrap; background: {{ $sc['badge_bg'] }}; color: {{ $sc['badge_color'] }};">
                                                    {{ $appt->status->label() }}
                                                </span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="schedule-add-btn" style="height: 100%; display: flex; align-items: center; justify-content: center;">
                                            <button @click="openNewModalAt('{{ $attKey }}', '{{ $slot }}')" type="button"
                                                    style="opacity: 0; padding: 0.25rem; color: #9ca3af; background: none; border: 1px dashed #d1d5db; border-radius: 0.25rem; cursor: pointer; transition: opacity 0.15s;"
                                                    onmouseover="this.style.color='#4f46e5'; this.style.borderColor='#818cf8'" onmouseout="this.style.color='#9ca3af'; this.style.borderColor='#d1d5db'">
                                                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Modal: Novo Agendamento --}}
        <div x-show="showNewModal" x-transition.opacity style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: flex-start; justify-content: center; background: rgba(0,0,0,0.4); padding-top: 5vh; overflow-y: auto;" x-cloak>
            <div @click.away="showNewModal = false" style="background: white; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 40rem; box-shadow: 0 25px 50px rgba(0,0,0,0.25); max-height: 90vh; overflow-y: auto; margin-bottom: 2rem;">
                <h2 style="font-size: 1.125rem; font-weight: 700; color: #111827; margin-bottom: 1.25rem;">Novo Agendamento</h2>

                <form method="POST" action="{{ route('schedule.store') }}">
                    @csrf

                    {{-- Atendente, Data, Duração --}}
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Atendente *</label>
                            <select name="attendant" x-model="newForm.attendant" @change="fetchSlots()" required
                                    style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; background: white;">
                                <option value="">Selecione...</option>
                                @foreach($attendants as $key => $name)
                                    <option value="{{ $key }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Data *</label>
                            <input type="date" name="date" x-model="newForm.date" @change="fetchSlots()" required
                                   style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Duração *</label>
                            <select name="duration_minutes" x-model="newForm.duration_minutes" @change="fetchSlots()" required
                                    style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; background: white;">
                                @foreach($durationOptions as $minutes => $label)
                                    <option value="{{ $minutes }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Horário --}}
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.5rem;">Horário *</label>
                        <div x-show="availableSlots.length > 0" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 0.375rem;">
                            <template x-for="slot in availableSlots" :key="slot.start">
                                <label style="cursor: pointer;">
                                    <input type="radio" name="start_time" :value="slot.start" x-model="newForm.start_time" style="display: none;" required>
                                    <div :style="newForm.start_time === slot.start
                                            ? 'text-align: center; padding: 0.375rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600; border: 2px solid #111827; background: #111827; color: white;'
                                            : 'text-align: center; padding: 0.375rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; border: 1px solid #e5e7eb; color: #374151; background: white;'"
                                         x-text="slot.label"
                                         onmouseover="if(!this.previousElementSibling.checked) this.style.borderColor='#9ca3af'" onmouseout="if(!this.previousElementSibling.checked) this.style.borderColor='#e5e7eb'">
                                    </div>
                                </label>
                            </template>
                        </div>
                        <p x-show="availableSlots.length === 0 && newForm.attendant && newForm.date" style="font-size: 0.8rem; color: #6b7280; font-style: italic;">Nenhum horário disponível.</p>
                        <p x-show="!newForm.attendant || !newForm.date" style="font-size: 0.8rem; color: #9ca3af; font-style: italic;">Selecione atendente, data e duração para ver os horários.</p>
                    </div>

                    {{-- Cliente --}}
                    <div style="border-top: 1px solid #f3f4f6; padding-top: 1rem; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                            <span style="font-size: 0.8rem; font-weight: 700; color: #111827;">Dados do Cliente</span>
                            <div style="display: flex; border: 1px solid #e5e7eb; border-radius: 0.375rem; overflow: hidden;">
                                <button type="button" @click="customerMode = 'search'"
                                        :style="customerMode === 'search' ? 'padding: 0.25rem 0.625rem; font-size: 0.7rem; font-weight: 600; background: #111827; color: white; border: none; cursor: pointer;' : 'padding: 0.25rem 0.625rem; font-size: 0.7rem; font-weight: 500; background: white; color: #6b7280; border: none; cursor: pointer;'">
                                    Buscar existente
                                </button>
                                <button type="button" @click="customerMode = 'new'"
                                        :style="customerMode === 'new' ? 'padding: 0.25rem 0.625rem; font-size: 0.7rem; font-weight: 600; background: #111827; color: white; border: none; border-left: 1px solid #e5e7eb; cursor: pointer;' : 'padding: 0.25rem 0.625rem; font-size: 0.7rem; font-weight: 500; background: white; color: #6b7280; border: none; border-left: 1px solid #e5e7eb; cursor: pointer;'">
                                    Cadastrar novo
                                </button>
                            </div>
                        </div>

                        {{-- Buscar existente --}}
                        <div x-show="customerMode === 'search'">
                            <div style="position: relative; margin-bottom: 0.5rem;">
                                <svg style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); width: 1rem; height: 1rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <input type="text" x-model="customerSearch" @input.debounce.300ms="searchCustomers()"
                                       placeholder="Buscar por nome ou telefone..."
                                       style="width: 100%; padding: 0.5rem 0.75rem 0.5rem 2.25rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                            </div>
                            <div x-show="customerResults.length > 0" style="border: 1px solid #e5e7eb; border-radius: 0.5rem; max-height: 10rem; overflow-y: auto; margin-bottom: 0.5rem;">
                                <template x-for="c in customerResults" :key="c.id">
                                    <button type="button" @click="selectCustomer(c)"
                                            style="display: flex; align-items: center; justify-content: space-between; width: 100%; text-align: left; padding: 0.5rem 0.75rem; border: none; border-bottom: 1px solid #f3f4f6; background: white; cursor: pointer; font-size: 0.8rem;"
                                            onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                        <span><strong x-text="c.name"></strong> <span style="color: #6b7280; margin-left: 0.25rem;" x-text="c.phone"></span></span>
                                        <svg style="width: 0.875rem; height: 0.875rem; color: #d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                </template>
                            </div>
                            <div x-show="selectedCustomer" style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0.75rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 0.5rem;">
                                <div>
                                    <span style="font-size: 0.8rem; font-weight: 600; color: #166534;" x-text="selectedCustomer?.name"></span>
                                    <span style="font-size: 0.75rem; color: #15803d; margin-left: 0.25rem;" x-text="selectedCustomer?.phone"></span>
                                </div>
                                <button type="button" @click="clearCustomer()" style="color: #6b7280; background: none; border: none; cursor: pointer; padding: 0.25rem;">
                                    <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <input type="hidden" name="customer_id" :value="selectedCustomer?.id || ''">
                            <input type="hidden" name="customer_name" :value="selectedCustomer?.name || ''">
                            <input type="hidden" name="customer_phone" :value="selectedCustomer?.phone || ''">
                        </div>

                        {{-- Cadastrar novo --}}
                        <div x-show="customerMode === 'new'">
                            <input type="hidden" name="create_customer" :value="customerMode === 'new' ? 1 : 0">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.625rem;">
                                <div>
                                    <label style="display: block; font-size: 0.7rem; font-weight: 600; color: #6b7280; margin-bottom: 0.125rem;">Nome Completo *</label>
                                    <input type="text" name="customer_name" x-model="newCustomer.name" required
                                           style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.7rem; font-weight: 600; color: #6b7280; margin-bottom: 0.125rem;">Telefone</label>
                                    <input type="text" name="customer_phone" x-model="newCustomer.phone" placeholder="(00) 00000-0000"
                                           style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.7rem; font-weight: 600; color: #6b7280; margin-bottom: 0.125rem;">Instagram</label>
                                    <input type="text" name="customer_instagram" x-model="newCustomer.instagram" placeholder="@usuario"
                                           style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.7rem; font-weight: 600; color: #6b7280; margin-bottom: 0.125rem;">Data de Nascimento</label>
                                    <input type="date" name="customer_birth_date" x-model="newCustomer.birth_date"
                                           style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.7rem; font-weight: 600; color: #6b7280; margin-bottom: 0.125rem;">Instagram</label>
                                    <input type="text" name="customer_instagram" x-model="newCustomer.instagram" placeholder="@usuario"
                                           style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.7rem; font-weight: 600; color: #6b7280; margin-bottom: 0.125rem;">Endereço</label>
                                    <input type="text" name="customer_address" x-model="newCustomer.address"
                                           style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Serviço e Observações --}}
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.25rem;">
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Produto/Serviço de Interesse</label>
                            <input type="text" name="service_description" placeholder="Ex: iPhone 15 Pro Max..."
                                   style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Observações</label>
                            <input type="text" name="notes" placeholder="Informações adicionais..."
                                   style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                        </div>
                    </div>

                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                        <button @click.prevent="showNewModal = false" type="button"
                                style="padding: 0.5rem 1rem; font-size: 0.8rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; background: white; color: #6b7280; cursor: pointer;">
                            Cancelar
                        </button>
                        <button type="submit"
                                style="padding: 0.5rem 1rem; font-size: 0.8rem; background: #111827; color: white; border-radius: 0.375rem; border: none; cursor: pointer; font-weight: 600;">
                            Criar Agendamento
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal: Detalhes / Editar / WhatsApp --}}
        <div x-show="showEditModal" x-transition.opacity style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: flex-start; justify-content: center; background: rgba(0,0,0,0.4); padding-top: 5vh; overflow-y: auto;" x-cloak>
            <div @click.away="showEditModal = false" style="background: white; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 40rem; box-shadow: 0 25px 50px rgba(0,0,0,0.25); max-height: 90vh; overflow-y: auto; margin-bottom: 2rem;">

                {{-- Tabs --}}
                <div style="display: flex; gap: 0; border-bottom: 1px solid #e5e7eb; margin-bottom: 1.25rem;">
                    <button type="button" @click="editTab = 'details'"
                            :style="editTab === 'details' ? 'padding: 0.5rem 1rem; font-size: 0.8rem; font-weight: 600; color: #111827; border-bottom: 2px solid #111827; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer;' : 'padding: 0.5rem 1rem; font-size: 0.8rem; font-weight: 500; color: #6b7280; border: none; background: none; cursor: pointer;'">
                        Detalhes
                    </button>
                    <button type="button" @click="editTab = 'edit'"
                            :style="editTab === 'edit' ? 'padding: 0.5rem 1rem; font-size: 0.8rem; font-weight: 600; color: #111827; border-bottom: 2px solid #111827; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer;' : 'padding: 0.5rem 1rem; font-size: 0.8rem; font-weight: 500; color: #6b7280; border: none; background: none; cursor: pointer;'">
                        Editar
                    </button>
                    <button type="button" @click="editTab = 'whatsapp'"
                            :style="editTab === 'whatsapp' ? 'padding: 0.5rem 1rem; font-size: 0.8rem; font-weight: 600; color: #111827; border-bottom: 2px solid #111827; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer;' : 'padding: 0.5rem 1rem; font-size: 0.8rem; font-weight: 500; color: #6b7280; border: none; background: none; cursor: pointer;'">
                        WhatsApp
                    </button>
                </div>

                {{-- Tab: Detalhes --}}
                <div x-show="editTab === 'details'">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
                        <div style="padding: 0.75rem; background: #f9fafb; border-radius: 0.5rem;">
                            <p style="font-size: 0.7rem; color: #6b7280; margin: 0 0 0.25rem;">Cliente</p>
                            <p style="font-size: 0.875rem; font-weight: 600; color: #111827; margin: 0;" x-text="editAppt?.customer_name"></p>
                            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.125rem 0 0;" x-text="editAppt?.customer_phone"></p>
                        </div>
                        <div style="padding: 0.75rem; background: #f9fafb; border-radius: 0.5rem;">
                            <p style="font-size: 0.7rem; color: #6b7280; margin: 0 0 0.25rem;">Atendente</p>
                            <p style="font-size: 0.875rem; font-weight: 600; color: #111827; margin: 0;" x-text="getAttendantName(editAppt?.attendant)"></p>
                        </div>
                        <div style="padding: 0.75rem; background: #f9fafb; border-radius: 0.5rem;">
                            <p style="font-size: 0.7rem; color: #6b7280; margin: 0 0 0.25rem;">Data e Horário</p>
                            <p style="font-size: 0.875rem; font-weight: 600; color: #111827; margin: 0;">
                                <span x-text="formatDate(editAppt?.date)"></span> · <span x-text="formatTime(editAppt?.start_time)"></span> - <span x-text="formatTime(editAppt?.end_time)"></span>
                            </p>
                        </div>
                        <div style="padding: 0.75rem; background: #f9fafb; border-radius: 0.5rem;">
                            <p style="font-size: 0.7rem; color: #6b7280; margin: 0 0 0.25rem;">Duração</p>
                            <p style="font-size: 0.875rem; font-weight: 600; color: #111827; margin: 0;" x-text="getDurationLabel(editAppt?.duration_minutes)"></p>
                        </div>
                    </div>

                    <template x-if="editAppt?.service_description">
                        <div style="padding: 0.75rem; background: #f9fafb; border-radius: 0.5rem; margin-bottom: 0.75rem;">
                            <p style="font-size: 0.7rem; color: #6b7280; margin: 0 0 0.25rem;">Produto/Serviço</p>
                            <p style="font-size: 0.8rem; color: #111827; margin: 0;" x-text="editAppt?.service_description"></p>
                        </div>
                    </template>

                    <template x-if="editAppt?.notes">
                        <div style="padding: 0.75rem; background: #f9fafb; border-radius: 0.5rem; margin-bottom: 0.75rem;">
                            <p style="font-size: 0.7rem; color: #6b7280; margin: 0 0 0.25rem;">Observações</p>
                            <p style="font-size: 0.8rem; color: #111827; margin: 0;" x-text="editAppt?.notes"></p>
                        </div>
                    </template>

                    {{-- Status Actions --}}
                    <div style="display: flex; flex-wrap: wrap; gap: 0.375rem; padding-top: 1rem; border-top: 1px solid #f3f4f6;">
                        <template x-if="editAppt?.status !== 'confirmed' && editAppt?.status !== 'completed' && editAppt?.status !== 'cancelled'">
                            <form :action="'/schedule/' + editAppt?.id + '/status'" method="POST" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit" style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.375rem 0.75rem; font-size: 0.75rem; font-weight: 600; background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; border-radius: 0.375rem; cursor: pointer;"
                                        onmouseover="this.style.background='#bbf7d0'" onmouseout="this.style.background='#dcfce7'">
                                    <svg style="width: 0.8rem; height: 0.8rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Confirmar
                                </button>
                            </form>
                        </template>
                        <template x-if="editAppt?.status !== 'completed' && editAppt?.status !== 'cancelled'">
                            <form :action="'/schedule/' + editAppt?.id + '/status'" method="POST" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.375rem 0.75rem; font-size: 0.75rem; font-weight: 600; background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; border-radius: 0.375rem; cursor: pointer;"
                                        onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                                    <svg style="width: 0.8rem; height: 0.8rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Concluir
                                </button>
                            </form>
                        </template>
                        <template x-if="editAppt?.status !== 'no_show' && editAppt?.status !== 'cancelled'">
                            <form :action="'/schedule/' + editAppt?.id + '/status'" method="POST" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="no_show">
                                <button type="submit" style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.375rem 0.75rem; font-size: 0.75rem; font-weight: 600; background: #fef3c7; color: #92400e; border: 1px solid #fde68a; border-radius: 0.375rem; cursor: pointer;"
                                        onmouseover="this.style.background='#fde68a'" onmouseout="this.style.background='#fef3c7'">
                                    <svg style="width: 0.8rem; height: 0.8rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Não Compareceu
                                </button>
                            </form>
                        </template>
                        <template x-if="editAppt?.status !== 'cancelled'">
                            <form :action="'/schedule/' + editAppt?.id" method="POST" style="display: inline;" onsubmit="return confirm('Cancelar este agendamento?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.375rem 0.75rem; font-size: 0.75rem; font-weight: 600; background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; border-radius: 0.375rem; cursor: pointer;"
                                        onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'">
                                    <svg style="width: 0.8rem; height: 0.8rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Cancelar
                                </button>
                            </form>
                        </template>
                    </div>
                </div>

                {{-- Tab: Editar --}}
                <div x-show="editTab === 'edit'">
                    <form :action="'/schedule/' + editAppt?.id" method="POST" style="display: flex; flex-direction: column; gap: 0.75rem;">
                        @csrf
                        @method('PUT')
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;">
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Atendente *</label>
                                <select name="attendant" x-model="editForm.attendant" required
                                        style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; background: white;">
                                    @foreach($attendants as $key => $name)
                                        <option value="{{ $key }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Data *</label>
                                <input type="date" name="date" x-model="editForm.date" required
                                       style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Duração *</label>
                                <select name="duration_minutes" x-model="editForm.duration_minutes" required
                                        style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; background: white;">
                                    @foreach($durationOptions as $minutes => $label)
                                        <option value="{{ $minutes }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Horário *</label>
                            <input type="time" name="start_time" x-model="editForm.start_time" step="1800" required
                                   style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Nome do Cliente *</label>
                                <input type="text" name="customer_name" x-model="editForm.customer_name" required
                                       style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Telefone</label>
                                <input type="text" name="customer_phone" x-model="editForm.customer_phone"
                                       style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Produto/Serviço</label>
                                <input type="text" name="service_description" x-model="editForm.service_description"
                                       style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Observações</label>
                                <input type="text" name="notes" x-model="editForm.notes"
                                       style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                            </div>
                        </div>
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 0.5rem;">
                            <button type="button" @click="editTab = 'details'"
                                    style="padding: 0.5rem 1rem; font-size: 0.8rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; background: white; color: #6b7280; cursor: pointer;">
                                Voltar
                            </button>
                            <button type="submit"
                                    style="padding: 0.5rem 1rem; font-size: 0.8rem; background: #111827; color: white; border-radius: 0.375rem; border: none; cursor: pointer; font-weight: 600;">
                                Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Tab: WhatsApp --}}
                <div x-show="editTab === 'whatsapp'" style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <template x-for="msgType in whatsappTypes" :key="msgType.key">
                        <div style="border: 1px solid #e5e7eb; border-radius: 0.5rem; overflow: hidden;">
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0.75rem; background: #f9fafb;">
                                <span style="font-size: 0.8rem; font-weight: 600; color: #374151;" x-text="msgType.label"></span>
                                <button type="button" @click="copyWhatsapp(msgType.key)"
                                        :style="copiedType === msgType.key
                                            ? 'display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.625rem; font-size: 0.7rem; font-weight: 600; background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; border-radius: 0.375rem; cursor: pointer;'
                                            : 'display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.625rem; font-size: 0.7rem; font-weight: 500; background: white; color: #374151; border: 1px solid #e5e7eb; border-radius: 0.375rem; cursor: pointer;'">
                                    <template x-if="copiedType !== msgType.key">
                                        <span style="display: flex; align-items: center; gap: 0.25rem;">
                                            <svg style="width: 0.75rem; height: 0.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            Copiar
                                        </span>
                                    </template>
                                    <template x-if="copiedType === msgType.key">
                                        <span style="display: flex; align-items: center; gap: 0.25rem;">
                                            <svg style="width: 0.75rem; height: 0.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Copiado!
                                        </span>
                                    </template>
                                </button>
                            </div>
                            <div style="padding: 0.75rem;">
                                <pre style="font-size: 0.8rem; color: #374151; white-space: pre-wrap; font-family: inherit; line-height: 1.5; margin: 0;" x-text="whatsappMessages[msgType.key] || 'Carregando...'"></pre>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <style>
        .schedule-row:hover .schedule-add-btn button { opacity: 1 !important; }
    </style>

    @push('scripts')
    <script>
        function scheduleApp() {
            return {
                showNewModal: false,
                showEditModal: false,
                editTab: 'details',
                customerMode: 'search',
                customerSearch: '',
                customerResults: [],
                selectedCustomer: null,
                copiedType: null,
                editAppt: null,
                availableSlots: [],

                newForm: {
                    attendant: '',
                    date: '{{ $date->format("Y-m-d") }}',
                    duration_minutes: '30',
                    start_time: '',
                },

                editForm: {
                    attendant: '',
                    date: '',
                    duration_minutes: '',
                    start_time: '',
                    customer_name: '',
                    customer_phone: '',
                    service_description: '',
                    notes: '',
                },

                newCustomer: { name: '', phone: '', instagram: '', birth_date: '', address: '' },

                whatsappTypes: [
                    { key: 'confirmation', label: 'Confirmação de Agendamento' },
                    { key: 'reminder', label: 'Lembrete Pré-Atendimento' },
                    { key: 'followup', label: 'Follow-up Pós-Atendimento' },
                ],
                whatsappMessages: {},
                attendantNames: @json(\App\Domain\Schedule\Models\Appointment::ATTENDANTS),
                durationLabels: @json(\App\Domain\Schedule\Models\Appointment::DURATION_OPTIONS),

                openNewModal() {
                    this.showNewModal = true;
                    this.customerMode = 'search';
                    this.selectedCustomer = null;
                    this.customerSearch = '';
                    this.customerResults = [];
                    this.newForm.start_time = '';
                    this.newCustomer = { name: '', phone: '', instagram: '', birth_date: '', address: '' };
                    if (this.newForm.attendant && this.newForm.date) this.fetchSlots();
                },

                openNewModalAt(attendant, time) {
                    this.newForm.attendant = attendant;
                    this.newForm.start_time = time;
                    this.openNewModal();
                    this.newForm.start_time = time;
                },

                openEditModal(appt) {
                    this.editAppt = appt;
                    this.editTab = 'details';
                    this.editForm = {
                        attendant: appt.attendant,
                        date: appt.date?.split('T')[0] || appt.date,
                        duration_minutes: String(appt.duration_minutes),
                        start_time: (appt.start_time || '').substring(0, 5),
                        customer_name: appt.customer_name,
                        customer_phone: appt.customer_phone || '',
                        service_description: appt.service_description || '',
                        notes: appt.notes || '',
                    };
                    this.whatsappMessages = {};
                    this.copiedType = null;
                    this.showEditModal = true;
                    this.loadWhatsappMessages(appt.id);
                },

                async fetchSlots() {
                    if (!this.newForm.attendant || !this.newForm.date || !this.newForm.duration_minutes) return;
                    try {
                        const params = new URLSearchParams({
                            attendant: this.newForm.attendant,
                            date: this.newForm.date,
                            duration_minutes: this.newForm.duration_minutes,
                        });
                        const res = await fetch(`{{ route('schedule.available-slots') }}?${params}`);
                        this.availableSlots = await res.json();
                    } catch (e) { this.availableSlots = []; }
                },

                async searchCustomers() {
                    if (this.customerSearch.length < 2) { this.customerResults = []; return; }
                    try {
                        const res = await fetch(`{{ route('customers.search') }}?q=${encodeURIComponent(this.customerSearch)}`);
                        this.customerResults = await res.json();
                    } catch (e) { this.customerResults = []; }
                },

                selectCustomer(c) { this.selectedCustomer = c; this.customerResults = []; this.customerSearch = ''; },
                clearCustomer() { this.selectedCustomer = null; },

                async loadWhatsappMessages(id) {
                    for (const type of this.whatsappTypes) {
                        try {
                            const res = await fetch(`/schedule/${id}/whatsapp/${type.key}`);
                            const data = await res.json();
                            this.whatsappMessages[type.key] = data.message;
                        } catch (e) { this.whatsappMessages[type.key] = 'Erro ao carregar mensagem.'; }
                    }
                },

                async copyWhatsapp(type) {
                    const msg = this.whatsappMessages[type];
                    if (!msg) return;
                    try {
                        await navigator.clipboard.writeText(msg);
                    } catch (e) {
                        const ta = document.createElement('textarea');
                        ta.value = msg; document.body.appendChild(ta); ta.select();
                        document.execCommand('copy'); document.body.removeChild(ta);
                    }
                    this.copiedType = type;
                    setTimeout(() => { this.copiedType = null; }, 2000);
                },

                getAttendantName(key) { return this.attendantNames[key] || key; },
                formatDate(d) {
                    if (!d) return '';
                    const p = d.split('T')[0].split('-');
                    return `${p[2]}/${p[1]}/${p[0]}`;
                },
                formatTime(t) { return t ? t.substring(0, 5) : ''; },
                getDurationLabel(m) { return this.durationLabels[m] || m + ' min'; },
            };
        }
    </script>
    @endpush
</x-app-layout>
