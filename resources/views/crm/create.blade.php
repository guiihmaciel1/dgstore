<x-app-layout>
    <x-slot name="title">CRM - Novo Lead</x-slot>

    <div class="p-6">
        <div class="max-w-[32rem] my-0 mx-auto">

            <div class="flex items-center gap-3 mb-6">
                <a class="inline-flex items-center justify-center w-[30px] h-[30px] rounded-md border border-border bg-surface-raised text-dg-300 no-underline" href="{{ route('crm.board') }}" title="Voltar ao Pipeline">
                    <svg class="w-[16px] h-[16px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="text-xl font-bold text-dg-100">Novo Lead</h1>
            </div>

            @if($errors->any())
                <div class="mb-4 py-3 px-4 bg-red-500/[0.08] border border-red-500/20 rounded-lg text-red-300 text-[0.8rem]">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('crm.deals.store') }}" x-data="createLead()">
                @csrf
                <div class="bg-surface-raised border border-border rounded-xl p-5 flex flex-col gap-3">

                    <div>
                        <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Titulo <span class="text-red-300">*</span></label>
                        <input class="w-full p-2" type="text" name="title" required value="{{ old('title') }}" placeholder="Ex: iPhone 16 Pro Max - Joao"
                               style="border: 1px solid {{ $errors->has('title') ? '#fca5a5' : 'rgba(255,255,255,0.06)' }}; border-radius: 0.375rem; font-size: 0.875rem; background: #0d0d0d; color: #e3e3e3;">
                    </div>

                    <div class="grid grid-cols-[1fr_1fr] gap-3">
                        <div>
                            <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Telefone / WhatsApp <span class="text-red-300">*</span></label>
                            <input class="w-full p-2" type="text" name="phone" required value="{{ old('phone') }}" placeholder="(00) 00000-0000"
                                   maxlength="15"
                                   @input="$el.value = $el.value.replace(/\D/g,'').replace(/^(\d{2})(\d)/,'($1) $2').replace(/(\d{5})(\d)/,'$1-$2').substring(0,15)"
                                   style="border: 1px solid {{ $errors->has('phone') ? '#fca5a5' : 'rgba(255,255,255,0.06)' }}; border-radius: 0.375rem; font-size: 0.875rem; background: #0d0d0d; color: #e3e3e3;">
                            @error('phone')
                                <p class="text-[0.7rem] text-red-300 mt-1 mx-0 mb-0">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Etapa</label>
                            <select class="w-full p-2 border border-border rounded-md text-sm bg-surface text-dg-100" name="pipeline_stage_id">
                                @foreach($activeStages as $stage)
                                    <option value="{{ $stage->id }}" {{ $stage->is_default ? 'selected' : '' }}>{{ $stage->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Buscar Cliente</label>
                        <div class="relative">
                            <input class="w-full p-2 border border-border rounded-md text-sm bg-surface text-dg-100" type="text" x-model="customerSearch" @input.debounce.300ms="searchCustomers()"
                                   placeholder="Digite o nome do cliente...">
                            <input type="hidden" name="customer_id" x-model="selectedCustomerId">
                            <div class="absolute z-[10] w-full bg-surface-raised border border-border rounded-md mt-[2px] max-h-[150px] overflow-y-auto shadow-[0_4px_6px_rgba(0,0,0,0.1)]" x-show="customerResults.length > 0">
                                <template x-for="c in customerResults" :key="c.id">
                                    <div @click="selectCustomer(c)"
                                         class="hover:bg-surface p-2 cursor-pointer text-[0.8rem] border-b border-border-subtle">
                                        <span class="font-semibold" x-text="c.name"></span>
                                        <span class="text-dg-400" x-text="c.phone ? ' - ' + c.phone : ''"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="mt-1 text-xs text-emerald-600" x-show="selectedCustomerName">
                            Selecionado: <strong x-text="selectedCustomerName"></strong>
                            <button class="text-red-300 ml-2 text-[0.7rem] border-0 bg-transparent cursor-pointer" type="button" @click="clearCustomer()">[remover]</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-[1fr_1fr] gap-3">
                        <div>
                            <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Origem do Lead</label>
                            <select class="w-full p-2 border border-border rounded-md text-sm bg-surface text-dg-100" name="lead_source">
                                <option value="">Nao informada</option>
                                @foreach(\App\Domain\CRM\Enums\LeadSource::cases() as $src)
                                    <option value="{{ $src->value }}" {{ old('lead_source') === $src->value ? 'selected' : '' }}>{{ $src->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Temperatura</label>
                            <select class="w-full p-2 border border-border rounded-md text-sm bg-surface text-dg-100" name="temperature">
                                <option value="hot" {{ old('temperature') === 'hot' ? 'selected' : '' }}>Quente</option>
                                <option value="warm" {{ old('temperature', 'warm') === 'warm' ? 'selected' : '' }}>Morno</option>
                                <option value="cold" {{ old('temperature') === 'cold' ? 'selected' : '' }}>Frio</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-dg-400 block mb-[2px]">Observacoes</label>
                        <textarea class="w-full p-2 border border-border rounded-md text-sm resize-y bg-surface text-dg-100" name="description" rows="2" placeholder="Detalhes sobre o interesse, condicoes, etc.">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="flex gap-2 justify-end mt-4">
                    <a class="py-2 px-4 text-[0.8rem] border border-border rounded-md bg-surface-raised text-dg-400 cursor-pointer no-underline" href="{{ route('crm.board') }}">
                        Cancelar
                    </a>
                    <button class="py-2 px-4 text-[0.8rem] bg-[#111827] text-white rounded-md border-0 cursor-pointer font-semibold" type="submit">
                        Criar Lead
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function createLead() {
        return {
            customerSearch: '',
            customerResults: [],
            selectedCustomerId: '',
            selectedCustomerName: '',

            async searchCustomers() {
                if (this.customerSearch.length < 2) { this.customerResults = []; return; }
                try {
                    const res = await fetch(`/api/customers/search?q=${encodeURIComponent(this.customerSearch)}`);
                    this.customerResults = (await res.json()).slice(0, 5);
                } catch (e) { this.customerResults = []; }
            },

            selectCustomer(customer) {
                this.selectedCustomerId = customer.id;
                this.selectedCustomerName = customer.name;
                this.customerSearch = '';
                this.customerResults = [];

                if (customer.phone) {
                    const phoneField = document.querySelector('input[name="phone"]');
                    if (phoneField && !phoneField.value) {
                        let p = customer.phone.replace(/\D/g, '');
                        if (p.length === 11) p = p.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
                        else if (p.length === 10) p = p.replace(/^(\d{2})(\d{4})(\d{4})$/, '($1) $2-$3');
                        phoneField.value = p || customer.phone;
                    }
                }
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
