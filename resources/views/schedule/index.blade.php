<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Agenda</h2>
    </x-slot>

    <div class="py-6" x-data="scheduleApp()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800 border border-green-200" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-800 border border-red-200" x-data="{ show: true }" x-show="show">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Header: navegação de data + filtros + botão novo --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('schedule.index', ['date' => $date->copy()->subDay()->format('Y-m-d'), 'attendant' => $attendantFilter]) }}"
                           class="p-2 rounded-lg hover:bg-gray-100 transition">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </a>

                        <div class="text-center">
                            <h3 class="text-lg font-bold text-gray-900">{{ $date->translatedFormat('l') }}</h3>
                            <p class="text-sm text-gray-500">{{ $date->format('d/m/Y') }}</p>
                        </div>

                        <a href="{{ route('schedule.index', ['date' => $date->copy()->addDay()->format('Y-m-d'), 'attendant' => $attendantFilter]) }}"
                           class="p-2 rounded-lg hover:bg-gray-100 transition">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>

                        @if(!$date->isToday())
                            <a href="{{ route('schedule.index') }}" class="ml-2 text-xs text-indigo-600 hover:text-indigo-800 font-medium">Hoje</a>
                        @endif
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="date" value="{{ $date->format('Y-m-d') }}"
                               class="rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                               onchange="window.location.href='{{ route('schedule.index') }}?date='+this.value+'&attendant={{ $attendantFilter }}'">

                        <select class="rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                onchange="window.location.href='{{ route('schedule.index') }}?date={{ $date->format('Y-m-d') }}&attendant='+this.value">
                            <option value="">Todos</option>
                            @foreach($attendants as $key => $name)
                                <option value="{{ $key }}" {{ $attendantFilter === $key ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>

                        <button @click="openNewModal()" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Novo Agendamento
                        </button>
                    </div>
                </div>

                {{-- Resumo do dia --}}
                <div class="mt-4 flex flex-wrap gap-4 pt-4 border-t border-gray-100">
                    @foreach($attendants as $key => $name)
                        @php
                            $count = $appointments->where('attendant', $key)->whereNotIn('status', [\App\Domain\Schedule\Enums\AppointmentStatus::Cancelled])->count();
                        @endphp
                        <div class="flex items-center gap-2 text-sm">
                            <span class="w-3 h-3 rounded-full {{ $key === 'danilo' ? 'bg-blue-500' : 'bg-purple-500' }}"></span>
                            <span class="font-medium text-gray-700">{{ $name }}:</span>
                            <span class="text-gray-500">{{ $count }} agendamento{{ $count !== 1 ? 's' : '' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Grade de horários --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="grid {{ $attendantFilter ? 'grid-cols-[80px_1fr]' : 'grid-cols-[80px_1fr_1fr]' }} border-b border-gray-200">
                    <div class="p-3 bg-gray-50 text-xs font-semibold text-gray-500 uppercase text-center">Hora</div>
                    @foreach($attendants as $key => $name)
                        @if(!$attendantFilter || $attendantFilter === $key)
                            <div class="p-3 bg-gray-50 text-sm font-semibold text-gray-700 text-center border-l border-gray-200">
                                <span class="inline-flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full {{ $key === 'danilo' ? 'bg-blue-500' : 'bg-purple-500' }}"></span>
                                    {{ $name }}
                                </span>
                            </div>
                        @endif
                    @endforeach
                </div>

                @foreach($timeSlots as $slot)
                    <div class="grid {{ $attendantFilter ? 'grid-cols-[80px_1fr]' : 'grid-cols-[80px_1fr_1fr]' }} border-b border-gray-100 min-h-[52px] group hover:bg-gray-50/50 transition">
                        <div class="p-2 text-xs font-mono text-gray-400 text-center flex items-center justify-center bg-gray-50/50">
                            {{ $slot }}
                        </div>

                        @foreach($attendants as $attKey => $attName)
                            @if(!$attendantFilter || $attendantFilter === $attKey)
                                <div class="p-1.5 border-l border-gray-100 relative">
                                    @php
                                        $appt = ($appointmentsByAttendant[$attKey] ?? collect())->get($slot . ':00') ?? ($appointmentsByAttendant[$attKey] ?? collect())->get($slot);
                                    @endphp

                                    @if($appt)
                                        <div class="rounded-lg p-2.5 cursor-pointer transition hover:shadow-md {{ $appt->status->slotBgClass() }}"
                                             @click="openEditModal({{ $appt->toJson() }})"
                                             @if($appt->duration_minutes > 30)
                                                 style="min-height: {{ ($appt->duration_minutes / 30) * 44 }}px"
                                             @endif>
                                            <div class="flex items-start justify-between gap-2">
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $appt->customer_name }}</p>
                                                    <p class="text-xs text-gray-500 mt-0.5">{{ $appt->formatted_start_time }} - {{ $appt->formatted_end_time }}</p>
                                                    @if($appt->service_description)
                                                        <p class="text-xs text-gray-600 mt-1 truncate">{{ $appt->service_description }}</p>
                                                    @endif
                                                </div>
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium whitespace-nowrap {{ $appt->status->bgClass() }}">
                                                    {{ $appt->status->label() }}
                                                </span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="h-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                            <button @click="openNewModalAt('{{ $attKey }}', '{{ $slot }}')"
                                                    class="text-xs text-gray-400 hover:text-indigo-600 transition p-1 rounded hover:bg-indigo-50">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
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
        <div x-show="showNewModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-start justify-center min-h-screen px-4 pt-20 pb-8">
                <div class="fixed inset-0 bg-gray-900/50" @click="showNewModal = false"></div>
                <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl" @click.stop>
                    <div class="flex items-center justify-between p-5 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Novo Agendamento</h3>
                        <button @click="showNewModal = false" class="p-1 rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('schedule.store') }}" class="p-5 space-y-5">
                        @csrf

                        {{-- Atendente e Data --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Atendente *</label>
                                <select name="attendant" x-model="newForm.attendant" @change="fetchSlots()" required
                                        class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Selecione...</option>
                                    @foreach($attendants as $key => $name)
                                        <option value="{{ $key }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Data *</label>
                                <input type="date" name="date" x-model="newForm.date" @change="fetchSlots()" required
                                       class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Duração *</label>
                                <select name="duration_minutes" x-model="newForm.duration_minutes" @change="fetchSlots()" required
                                        class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    @foreach($durationOptions as $minutes => $label)
                                        <option value="{{ $minutes }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Horário --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Horário *</label>
                            <div x-show="availableSlots.length > 0" class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                                <template x-for="slot in availableSlots" :key="slot.start">
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="start_time" :value="slot.start" x-model="newForm.start_time" class="peer sr-only" required>
                                        <div class="text-center py-2 px-1 rounded-lg border border-gray-200 text-xs font-medium text-gray-700 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 hover:border-gray-300 transition">
                                            <span x-text="slot.label"></span>
                                        </div>
                                    </label>
                                </template>
                            </div>
                            <div x-show="availableSlots.length === 0 && newForm.attendant && newForm.date" class="text-sm text-gray-500 italic py-2">
                                Nenhum horário disponível para esta data/atendente.
                            </div>
                            <div x-show="!newForm.attendant || !newForm.date" class="text-sm text-gray-400 italic py-2">
                                Selecione atendente, data e duração para ver os horários.
                            </div>
                        </div>

                        {{-- Cliente --}}
                        <div class="border-t border-gray-100 pt-5">
                            <div class="flex items-center gap-4 mb-4">
                                <h4 class="text-sm font-bold text-gray-900">Dados do Cliente</h4>
                                <div class="flex rounded-lg border border-gray-200 overflow-hidden text-xs">
                                    <button type="button" @click="customerMode = 'search'"
                                            :class="customerMode === 'search' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                                            class="px-3 py-1.5 font-medium transition">
                                        Buscar existente
                                    </button>
                                    <button type="button" @click="customerMode = 'new'"
                                            :class="customerMode === 'new' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                                            class="px-3 py-1.5 font-medium transition border-l border-gray-200">
                                        Cadastrar novo
                                    </button>
                                </div>
                            </div>

                            {{-- Buscar cliente existente --}}
                            <div x-show="customerMode === 'search'" class="space-y-3">
                                <div class="relative">
                                    <input type="text" x-model="customerSearch" @input.debounce.300ms="searchCustomers()"
                                           placeholder="Buscar por nome ou telefone..."
                                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 pl-10">
                                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <div x-show="customerResults.length > 0" class="bg-white border border-gray-200 rounded-lg divide-y divide-gray-100 max-h-40 overflow-y-auto">
                                    <template x-for="c in customerResults" :key="c.id">
                                        <button type="button" @click="selectCustomer(c)"
                                                class="w-full text-left px-4 py-2.5 hover:bg-indigo-50 transition flex items-center justify-between">
                                            <div>
                                                <span class="text-sm font-medium text-gray-900" x-text="c.name"></span>
                                                <span class="text-xs text-gray-500 ml-2" x-text="c.phone"></span>
                                            </div>
                                            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </button>
                                    </template>
                                </div>
                                <div x-show="selectedCustomer" class="bg-indigo-50 rounded-lg p-3 flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-indigo-900" x-text="selectedCustomer?.name"></p>
                                        <p class="text-xs text-indigo-600" x-text="selectedCustomer?.phone"></p>
                                    </div>
                                    <button type="button" @click="clearCustomer()" class="text-indigo-400 hover:text-indigo-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                                <input type="hidden" name="customer_id" :value="selectedCustomer?.id || ''">
                                <input type="hidden" name="customer_name" :value="selectedCustomer?.name || ''">
                                <input type="hidden" name="customer_phone" :value="selectedCustomer?.phone || ''">
                            </div>

                            {{-- Cadastrar novo cliente --}}
                            <div x-show="customerMode === 'new'" class="space-y-3">
                                <input type="hidden" name="create_customer" :value="customerMode === 'new' ? 1 : 0">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Nome Completo *</label>
                                        <input type="text" name="customer_name" x-model="newCustomer.name" required
                                               class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Telefone</label>
                                        <input type="text" name="customer_phone" x-model="newCustomer.phone"
                                               placeholder="(00) 00000-0000"
                                               class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Instagram</label>
                                        <input type="text" name="customer_instagram" x-model="newCustomer.instagram"
                                               placeholder="@usuario"
                                               class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Data de Nascimento</label>
                                        <input type="date" name="customer_birth_date" x-model="newCustomer.birth_date"
                                               class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">E-mail</label>
                                        <input type="email" name="customer_email" x-model="newCustomer.email"
                                               class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Endereço</label>
                                        <input type="text" name="customer_address" x-model="newCustomer.address"
                                               class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Serviço e Observações --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Produto/Serviço de Interesse</label>
                                <input type="text" name="service_description" class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="Ex: iPhone 15 Pro Max, Troca de bateria...">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                                <input type="text" name="notes" class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="Informações adicionais...">
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-3 border-t border-gray-100">
                            <button type="button" @click="showNewModal = false"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                Cancelar
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition">
                                Criar Agendamento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal: Editar/Detalhes do Agendamento --}}
        <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-start justify-center min-h-screen px-4 pt-20 pb-8">
                <div class="fixed inset-0 bg-gray-900/50" @click="showEditModal = false"></div>
                <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl" @click.stop>
                    <div class="flex items-center justify-between p-5 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Detalhes do Agendamento</h3>
                        <button @click="showEditModal = false" class="p-1 rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-5" x-show="editAppt">
                        {{-- Tabs --}}
                        <div class="flex border-b border-gray-200 mb-5">
                            <button type="button" @click="editTab = 'details'"
                                    :class="editTab === 'details' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                    class="px-4 py-2 text-sm font-medium border-b-2 transition">
                                Detalhes
                            </button>
                            <button type="button" @click="editTab = 'edit'"
                                    :class="editTab === 'edit' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                    class="px-4 py-2 text-sm font-medium border-b-2 transition">
                                Editar
                            </button>
                            <button type="button" @click="editTab = 'whatsapp'"
                                    :class="editTab === 'whatsapp' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                    class="px-4 py-2 text-sm font-medium border-b-2 transition">
                                WhatsApp
                            </button>
                        </div>

                        {{-- Tab: Detalhes --}}
                        <div x-show="editTab === 'details'" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 mb-1">Cliente</p>
                                    <p class="text-sm font-semibold text-gray-900" x-text="editAppt?.customer_name"></p>
                                    <p class="text-xs text-gray-500 mt-0.5" x-text="editAppt?.customer_phone"></p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 mb-1">Atendente</p>
                                    <p class="text-sm font-semibold text-gray-900" x-text="getAttendantName(editAppt?.attendant)"></p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 mb-1">Data e Horário</p>
                                    <p class="text-sm font-semibold text-gray-900">
                                        <span x-text="formatDate(editAppt?.date)"></span> &middot;
                                        <span x-text="formatTime(editAppt?.start_time)"></span> - <span x-text="formatTime(editAppt?.end_time)"></span>
                                    </p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 mb-1">Duração</p>
                                    <p class="text-sm font-semibold text-gray-900" x-text="getDurationLabel(editAppt?.duration_minutes)"></p>
                                </div>
                            </div>

                            <template x-if="editAppt?.service_description">
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 mb-1">Produto/Serviço</p>
                                    <p class="text-sm text-gray-900" x-text="editAppt?.service_description"></p>
                                </div>
                            </template>

                            <template x-if="editAppt?.notes">
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 mb-1">Observações</p>
                                    <p class="text-sm text-gray-900" x-text="editAppt?.notes"></p>
                                </div>
                            </template>

                            {{-- Status Actions --}}
                            <div class="flex flex-wrap gap-2 pt-4 border-t border-gray-100">
                                <template x-if="editAppt?.status !== 'confirmed' && editAppt?.status !== 'completed' && editAppt?.status !== 'cancelled'">
                                    <form :action="'/schedule/' + editAppt?.id + '/status'" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="confirmed">
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-green-100 text-green-800 hover:bg-green-200 transition">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Confirmar
                                        </button>
                                    </form>
                                </template>
                                <template x-if="editAppt?.status !== 'completed' && editAppt?.status !== 'cancelled'">
                                    <form :action="'/schedule/' + editAppt?.id + '/status'" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200 transition">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Concluir
                                        </button>
                                    </form>
                                </template>
                                <template x-if="editAppt?.status !== 'no_show' && editAppt?.status !== 'cancelled'">
                                    <form :action="'/schedule/' + editAppt?.id + '/status'" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="no_show">
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-yellow-100 text-yellow-800 hover:bg-yellow-200 transition">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Não Compareceu
                                        </button>
                                    </form>
                                </template>
                                <template x-if="editAppt?.status !== 'cancelled'">
                                    <form :action="'/schedule/' + editAppt?.id" method="POST" class="inline"
                                          onsubmit="return confirm('Tem certeza que deseja cancelar este agendamento?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-red-100 text-red-800 hover:bg-red-200 transition">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Cancelar
                                        </button>
                                    </form>
                                </template>
                            </div>
                        </div>

                        {{-- Tab: Editar --}}
                        <div x-show="editTab === 'edit'">
                            <form :action="'/schedule/' + editAppt?.id" method="POST" class="space-y-4">
                                @csrf
                                @method('PUT')
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Atendente *</label>
                                        <select name="attendant" x-model="editForm.attendant" required
                                                class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            @foreach($attendants as $key => $name)
                                                <option value="{{ $key }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Data *</label>
                                        <input type="date" name="date" x-model="editForm.date" required
                                               class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Duração *</label>
                                        <select name="duration_minutes" x-model="editForm.duration_minutes" required
                                                class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            @foreach($durationOptions as $minutes => $label)
                                                <option value="{{ $minutes }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Horário *</label>
                                    <input type="time" name="start_time" x-model="editForm.start_time" step="1800" required
                                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Cliente *</label>
                                        <input type="text" name="customer_name" x-model="editForm.customer_name" required
                                               class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                                        <input type="text" name="customer_phone" x-model="editForm.customer_phone"
                                               class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Produto/Serviço</label>
                                        <input type="text" name="service_description" x-model="editForm.service_description"
                                               class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                                        <input type="text" name="notes" x-model="editForm.notes"
                                               class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                                <div class="flex justify-end gap-3 pt-3 border-t border-gray-100">
                                    <button type="button" @click="editTab = 'details'"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                        Voltar
                                    </button>
                                    <button type="submit"
                                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition">
                                        Salvar Alterações
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Tab: WhatsApp --}}
                        <div x-show="editTab === 'whatsapp'" class="space-y-4">
                            <template x-for="msgType in whatsappTypes" :key="msgType.key">
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    <div class="flex items-center justify-between px-4 py-2.5 bg-gray-50">
                                        <span class="text-sm font-medium text-gray-700" x-text="msgType.label"></span>
                                        <button type="button"
                                                @click="copyWhatsapp(msgType.key)"
                                                :class="copiedType === msgType.key ? 'bg-green-100 text-green-700' : 'bg-white text-gray-600 hover:bg-gray-100'"
                                                class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-lg border border-gray-200 transition">
                                            <template x-if="copiedType !== msgType.key">
                                                <span class="flex items-center">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                    Copiar
                                                </span>
                                            </template>
                                            <template x-if="copiedType === msgType.key">
                                                <span class="flex items-center">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    Copiado!
                                                </span>
                                            </template>
                                        </button>
                                    </div>
                                    <div class="p-4">
                                        <pre class="text-sm text-gray-700 whitespace-pre-wrap font-sans leading-relaxed" x-text="whatsappMessages[msgType.key] || 'Carregando...'"></pre>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

                newCustomer: {
                    name: '',
                    phone: '',
                    instagram: '',
                    birth_date: '',
                    email: '',
                    address: '',
                },

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
                    this.newCustomer = { name: '', phone: '', instagram: '', birth_date: '', email: '', address: '' };
                    if (this.newForm.attendant && this.newForm.date) {
                        this.fetchSlots();
                    }
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
                    } catch (e) {
                        this.availableSlots = [];
                    }
                },

                async searchCustomers() {
                    if (this.customerSearch.length < 2) {
                        this.customerResults = [];
                        return;
                    }
                    try {
                        const res = await fetch(`{{ route('customers.search') }}?q=${encodeURIComponent(this.customerSearch)}`);
                        this.customerResults = await res.json();
                    } catch (e) {
                        this.customerResults = [];
                    }
                },

                selectCustomer(c) {
                    this.selectedCustomer = c;
                    this.customerResults = [];
                    this.customerSearch = '';
                },

                clearCustomer() {
                    this.selectedCustomer = null;
                },

                async loadWhatsappMessages(id) {
                    for (const type of this.whatsappTypes) {
                        try {
                            const res = await fetch(`/schedule/${id}/whatsapp/${type.key}`);
                            const data = await res.json();
                            this.whatsappMessages[type.key] = data.message;
                        } catch (e) {
                            this.whatsappMessages[type.key] = 'Erro ao carregar mensagem.';
                        }
                    }
                },

                async copyWhatsapp(type) {
                    const msg = this.whatsappMessages[type];
                    if (!msg) return;
                    try {
                        await navigator.clipboard.writeText(msg);
                        this.copiedType = type;
                        setTimeout(() => { this.copiedType = null; }, 2000);
                    } catch (e) {
                        const textarea = document.createElement('textarea');
                        textarea.value = msg;
                        document.body.appendChild(textarea);
                        textarea.select();
                        document.execCommand('copy');
                        document.body.removeChild(textarea);
                        this.copiedType = type;
                        setTimeout(() => { this.copiedType = null; }, 2000);
                    }
                },

                getAttendantName(key) {
                    return this.attendantNames[key] || key;
                },

                formatDate(d) {
                    if (!d) return '';
                    const parts = d.split('T')[0].split('-');
                    return `${parts[2]}/${parts[1]}/${parts[0]}`;
                },

                formatTime(t) {
                    if (!t) return '';
                    return t.substring(0, 5);
                },

                getDurationLabel(mins) {
                    return this.durationLabels[mins] || mins + ' min';
                },
            };
        }
    </script>
    @endpush
</x-app-layout>
