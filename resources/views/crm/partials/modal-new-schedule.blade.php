{{-- Modal: Novo Agendamento --}}
<div class="fixed inset-0 z-[50] flex items-center justify-center bg-black/40" x-show="showScheduleForm" x-transition.opacity x-cloak>
    <div class="bg-surface-raised rounded-xl p-6 w-full max-w-[28rem] shadow-[0_25px_50px_rgba(0,0,0,0.25)] max-h-[90vh] overflow-y-auto" @click.away="showScheduleForm = false">
        <h2 class="text-lg font-bold text-dg-100 mb-4 flex items-center gap-1.5">
            <svg class="w-[18px] h-[18px] text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Novo Agendamento
        </h2>
        <form method="POST" action="{{ route('schedule.store') }}">
            @csrf
            <input type="hidden" name="_redirect" value="{{ route('crm.board') }}">
            <div class="flex flex-col gap-3">
                <div>
                    <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Buscar Cliente</label>
                    <div class="relative">
                        <input class="w-full p-2 border border-border rounded-md text-sm" type="text" x-model="scheduleCustomerSearch" @input.debounce.300ms="searchScheduleCustomers()"
                               placeholder="Digite o nome do cliente...">
                        <input type="hidden" name="customer_id" id="schedule_customer_id">
                        <div class="absolute z-[10] w-full bg-surface-raised border border-border rounded-md mt-[2px] max-h-[150px] overflow-y-auto shadow-[0_4px_6px_rgba(0,0,0,0.1)]" x-show="scheduleCustomerResults.length > 0">
                            <template x-for="c in scheduleCustomerResults" :key="c.id">
                                <div @click="selectScheduleCustomer(c)"
                                     class="hover:bg-surface p-2 cursor-pointer text-[0.8rem] border-b border-border-subtle">
                                    <span class="font-semibold" x-text="c.name"></span>
                                    <span class="text-dg-400" x-text="c.phone ? ' - ' + c.phone : ''"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-[1fr_1fr] gap-3">
                    <div>
                        <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Nome <span class="text-red-300">*</span></label>
                        <input class="w-full p-2 border border-border rounded-md text-sm" type="text" name="customer_name" id="schedule_customer_name" required placeholder="Nome do cliente">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Telefone</label>
                        <input class="w-full p-2 border border-border rounded-md text-sm" type="text" name="customer_phone" id="schedule_customer_phone" placeholder="Telefone">
                    </div>
                </div>
                <div class="grid grid-cols-[1fr_1fr] gap-3">
                    <div>
                        <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Data <span class="text-red-300">*</span></label>
                        <input class="w-full p-2 border border-border rounded-md text-sm" type="date" name="date" required value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Atendente <span class="text-red-300">*</span></label>
                        <select class="w-full p-2 border border-border rounded-md text-sm" name="attendant" required>
                            @foreach($attendants as $key => $name)
                                <option value="{{ $key }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-[1fr_1fr_1fr] gap-3">
                    <div>
                        <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Horário Início <span class="text-red-300">*</span></label>
                        <input class="w-full p-2 border border-border rounded-md text-sm" type="time" name="start_time" required value="10:00">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Duração <span class="text-red-300">*</span></label>
                        <select class="w-full p-2 border border-border rounded-md text-sm" name="duration_minutes" required>
                            @foreach($durationOptions as $minutes => $label)
                                <option value="{{ $minutes }}" {{ $minutes === 60 ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Descrição do Serviço</label>
                    <textarea class="w-full p-2 border border-border rounded-md text-sm resize-y" name="service_description" rows="2" placeholder="Ex: Avaliação de iPhone para troca, atendimento VIP..."></textarea>
                </div>
            </div>
            <div class="flex gap-2 justify-end mt-4">
                <button class="py-2 px-4 text-[0.8rem] border border-border rounded-md bg-surface-raised text-dg-400 cursor-pointer" @click.prevent="showScheduleForm = false" type="button">
                    Cancelar
                </button>
                <button class="py-2 px-4 text-[0.8rem] bg-violet-500 text-white rounded-md border-0 cursor-pointer font-semibold" type="submit">
                    Criar Agendamento
                </button>
            </div>
        </form>
    </div>
</div>
