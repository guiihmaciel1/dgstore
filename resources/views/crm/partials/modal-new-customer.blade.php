{{-- Modal: Cadastrar Cliente Rápido --}}
<div x-show="showCustomerForm" x-transition.opacity style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.4);" x-cloak>
    <div @click.away="showCustomerForm = false" style="background: white; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 28rem; box-shadow: 0 25px 50px rgba(0,0,0,0.25); max-height: 90vh; overflow-y: auto;">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: #111827; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.375rem;">
            <svg style="width: 18px; height: 18px; color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Cadastrar Cliente
        </h2>
        <form method="POST" action="{{ route('customers.store') }}">
            @csrf
            <input type="hidden" name="_redirect" value="{{ route('crm.board') }}">
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Nome <span style="color: #dc2626;">*</span></label>
                    <input type="text" name="name" required placeholder="Nome completo"
                           style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Telefone <span style="color: #dc2626;">*</span></label>
                        <input type="text" name="phone" required placeholder="(17) 99649-8338"
                               style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">CPF</label>
                        <input type="text" name="cpf" placeholder="000.000.000-00"
                               style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Instagram</label>
                        <input type="text" name="instagram" placeholder="@usuario"
                               style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Data de Nascimento</label>
                        <input type="date" name="birth_date"
                               style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                </div>
                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: 2px;">Observações</label>
                    <textarea name="notes" rows="2" placeholder="Notas sobre o cliente..."
                              style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem; resize: vertical;"></textarea>
                </div>
            </div>
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 1rem;">
                <button @click.prevent="showCustomerForm = false" type="button"
                        style="padding: 0.5rem 1rem; font-size: 0.8rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; background: white; color: #6b7280; cursor: pointer;">
                    Cancelar
                </button>
                <button type="submit"
                        style="padding: 0.5rem 1rem; font-size: 0.8rem; background: #3b82f6; color: white; border-radius: 0.375rem; border: none; cursor: pointer; font-weight: 600;">
                    Cadastrar Cliente
                </button>
            </div>
        </form>
    </div>
</div>
