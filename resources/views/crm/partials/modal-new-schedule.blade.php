{{-- Modal: Novo Agendamento --}}
<div x-show="showScheduleForm" x-transition.opacity style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.4);" x-cloak>
    <div @click.away="showScheduleForm = false" style="background: white; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 28rem; box-shadow: 0 25px 50px rgba(0,0,0,0.25); max-height: 90vh; overflow-y: auto;">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: #111827; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.375rem;">
            <svg style="width: 18px; height: 18px; color: #8b5cf6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Novo Agendamento
        </h2>
        <form method="POST" action="{{ route('schedule.store') }}">
            @csrf
            <input type="hidden" name="_redirect" value="{{ route('crm.board') }}">
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Buscar Cliente</label>
                    <div style="position: relative;">
                        <input type="text" x-model="scheduleCustomerSearch" @input.debounce.300ms="searchScheduleCustomers()"
                               placeholder="Digite o nome do cliente..."
                               style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                        <input type="hidden" name="customer_id" id="schedule_customer_id">
                        <div x-show="scheduleCustomerResults.length > 0" style="position: absolute; z-index: 10; width: 100%; background: white; border: 1px solid #e5e7eb; border-radius: 0.375rem; margin-top: 2px; max-height: 150px; overflow-y: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <template x-for="c in scheduleCustomerResults" :key="c.id">
                                <div @click="selectScheduleCustomer(c)" style="padding: 0.5rem; cursor: pointer; font-size: 0.8rem; border-bottom: 1px solid #f3f4f6;"
                                     class="hover:bg-gray-50">
                                    <span x-text="c.name" style="font-weight: 600;"></span>
                                    <span x-text="c.phone ? ' - ' + c.phone : ''" style="color: #6b7280;"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Nome <span style="color: #dc2626;">*</span></label>
                        <input type="text" name="customer_name" id="schedule_customer_name" required placeholder="Nome do cliente"
                               style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Telefone</label>
                        <input type="text" name="customer_phone" id="schedule_customer_phone" placeholder="Telefone"
                               style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Data <span style="color: #dc2626;">*</span></label>
                        <input type="date" name="date" required value="{{ now()->format('Y-m-d') }}"
                               style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Atendente <span style="color: #dc2626;">*</span></label>
                        <select name="attendant" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                            @foreach($attendants as $key => $name)
                                <option value="{{ $key }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Horário Início <span style="color: #dc2626;">*</span></label>
                        <input type="time" name="start_time" required value="10:00"
                               style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Duração <span style="color: #dc2626;">*</span></label>
                        <select name="duration_minutes" required style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                            @foreach($durationOptions as $minutes => $label)
                                <option value="{{ $minutes }}" {{ $minutes === 60 ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Descrição do Serviço</label>
                    <textarea name="service_description" rows="2" placeholder="Ex: Avaliação de iPhone para troca, atendimento VIP..."
                              style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem; resize: vertical;"></textarea>
                </div>
            </div>
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 1rem;">
                <button @click.prevent="showScheduleForm = false" type="button"
                        style="padding: 0.5rem 1rem; font-size: 0.8rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; background: white; color: #6b7280; cursor: pointer;">
                    Cancelar
                </button>
                <button type="submit"
                        style="padding: 0.5rem 1rem; font-size: 0.8rem; background: #8b5cf6; color: white; border-radius: 0.375rem; border: none; cursor: pointer; font-weight: 600;">
                    Criar Agendamento
                </button>
            </div>
        </form>
    </div>
</div>
