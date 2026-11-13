{{-- Modal: Cadastrar Cliente Rápido --}}
<div class="fixed inset-0 z-[50] flex items-center justify-center bg-black/40" x-show="showCustomerForm" x-transition.opacity x-cloak>
    <div class="bg-surface-raised rounded-xl p-6 w-full max-w-[28rem] shadow-[0_25px_50px_rgba(0,0,0,0.25)] max-h-[90vh] overflow-y-auto" @click.away="showCustomerForm = false">
        <h2 class="text-lg font-bold text-dg-100 mb-4 flex items-center gap-1.5">
            <svg class="w-[18px] h-[18px] text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Cadastrar Cliente
        </h2>
        <form method="POST" action="{{ route('customers.store') }}">
            @csrf
            <input type="hidden" name="_redirect" value="{{ route('crm.board') }}">
            <div class="flex flex-col gap-3">
                <div>
                    <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Nome <span class="text-red-300">*</span></label>
                    <input class="w-full p-2 border border-border rounded-md text-sm" type="text" name="name" required placeholder="Nome completo">
                </div>
                <div class="grid grid-cols-[1fr_1fr] gap-3">
                    <div>
                        <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Telefone <span class="text-red-300">*</span></label>
                        <input class="w-full p-2 border border-border rounded-md text-sm" type="text" name="phone" required placeholder="(17) 99649-8338">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-dg-400 block mb-[2px]">CPF</label>
                        <input class="w-full p-2 border border-border rounded-md text-sm" type="text" name="cpf" placeholder="000.000.000-00">
                    </div>
                </div>
                <div class="grid grid-cols-[1fr_1fr] gap-3">
                    <div>
                        <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Instagram</label>
                        <input class="w-full p-2 border border-border rounded-md text-sm" type="text" name="instagram" placeholder="@usuario">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Data de Nascimento</label>
                        <input class="w-full p-2 border border-border rounded-md text-sm" type="date" name="birth_date">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Observações</label>
                    <textarea class="w-full p-2 border border-border rounded-md text-sm resize-y" name="notes" rows="2" placeholder="Notas sobre o cliente..."></textarea>
                </div>
            </div>
            <div class="flex gap-2 justify-end mt-4">
                <button class="py-2 px-4 text-[0.8rem] border border-border rounded-md bg-surface-raised text-dg-400 cursor-pointer" @click.prevent="showCustomerForm = false" type="button">
                    Cancelar
                </button>
                <button class="py-2 px-4 text-[0.8rem] bg-blue-500 text-white rounded-md border-0 cursor-pointer font-semibold" type="submit">
                    Cadastrar Cliente
                </button>
            </div>
        </form>
    </div>
</div>
