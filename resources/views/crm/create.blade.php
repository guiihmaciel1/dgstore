<x-app-layout>
    <x-slot name="title">CRM - Novo Lead</x-slot>

    <div style="padding: 1.5rem;">
        <div style="max-width: 32rem; margin: 0 auto;">

            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                <a href="{{ route('crm.board') }}" style="display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 0.375rem; border: 1px solid rgba(255,255,255,0.06); background: #141414; color: #a4a4a4; text-decoration: none;" title="Voltar ao Pipeline">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 style="font-size: 1.25rem; font-weight: 700; color: #e3e3e3;">Novo Lead</h1>
            </div>

            @if($errors->any())
                <div style="margin-bottom: 1rem; padding: 0.75rem 1rem; background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); border-radius: 0.5rem; color: #fca5a5; font-size: 0.8rem;">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('crm.deals.store') }}" x-data="createLead()">
                @csrf
                <div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1.25rem; display: flex; flex-direction: column; gap: 0.75rem;">

                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: #818181; display: block; margin-bottom: 2px;">Titulo <span style="color: #fca5a5;">*</span></label>
                        <input type="text" name="title" required value="{{ old('title') }}" placeholder="Ex: iPhone 16 Pro Max - Joao"
                               style="width: 100%; padding: 0.5rem; border: 1px solid {{ $errors->has('title') ? '#fca5a5' : 'rgba(255,255,255,0.06)' }}; border-radius: 0.375rem; font-size: 0.875rem; background: #0d0d0d; color: #e3e3e3;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 600; color: #818181; display: block; margin-bottom: 2px;">Telefone / WhatsApp <span style="color: #fca5a5;">*</span></label>
                            <input type="text" name="phone" required value="{{ old('phone') }}" placeholder="(00) 00000-0000"
                                   maxlength="15"
                                   @input="$el.value = $el.value.replace(/\D/g,'').replace(/^(\d{2})(\d)/,'($1) $2').replace(/(\d{5})(\d)/,'$1-$2').substring(0,15)"
                                   style="width: 100%; padding: 0.5rem; border: 1px solid {{ $errors->has('phone') ? '#fca5a5' : 'rgba(255,255,255,0.06)' }}; border-radius: 0.375rem; font-size: 0.875rem; background: #0d0d0d; color: #e3e3e3;">
                            @error('phone')
                                <p style="font-size: 0.7rem; color: #fca5a5; margin: 0.25rem 0 0 0;">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 600; color: #818181; display: block; margin-bottom: 2px;">Etapa</label>
                            <select name="pipeline_stage_id" style="width: 100%; padding: 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.875rem; background: #0d0d0d; color: #e3e3e3;">
                                @foreach($activeStages as $stage)
                                    <option value="{{ $stage->id }}" {{ $stage->is_default ? 'selected' : '' }}>{{ $stage->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: #818181; display: block; margin-bottom: 2px;">Buscar Cliente</label>
                        <div style="position: relative;">
                            <input type="text" x-model="customerSearch" @input.debounce.300ms="searchCustomers()"
                                   placeholder="Digite o nome do cliente..."
                                   style="width: 100%; padding: 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.875rem; background: #0d0d0d; color: #e3e3e3;">
                            <input type="hidden" name="customer_id" x-model="selectedCustomerId">
                            <div x-show="customerResults.length > 0" style="position: absolute; z-index: 10; width: 100%; background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; margin-top: 2px; max-height: 150px; overflow-y: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                <template x-for="c in customerResults" :key="c.id">
                                    <div @click="selectCustomer(c)" style="padding: 0.5rem; cursor: pointer; font-size: 0.8rem; border-bottom: 1px solid rgba(255,255,255,0.04);"
                                         class="hover:bg-surface">
                                        <span x-text="c.name" style="font-weight: 600;"></span>
                                        <span x-text="c.phone ? ' - ' + c.phone : ''" style="color: #818181;"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div x-show="selectedCustomerName" style="margin-top: 0.25rem; font-size: 0.75rem; color: #059669;">
                            Selecionado: <strong x-text="selectedCustomerName"></strong>
                            <button type="button" @click="clearCustomer()" style="color: #fca5a5; margin-left: 0.5rem; font-size: 0.7rem; border: none; background: none; cursor: pointer;">[remover]</button>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 600; color: #818181; display: block; margin-bottom: 2px;">Origem do Lead</label>
                            <select name="lead_source" style="width: 100%; padding: 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.875rem; background: #0d0d0d; color: #e3e3e3;">
                                <option value="">Nao informada</option>
                                @foreach(\App\Domain\CRM\Enums\LeadSource::cases() as $src)
                                    <option value="{{ $src->value }}" {{ old('lead_source') === $src->value ? 'selected' : '' }}>{{ $src->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 600; color: #818181; display: block; margin-bottom: 2px;">Temperatura</label>
                            <select name="temperature" style="width: 100%; padding: 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.875rem; background: #0d0d0d; color: #e3e3e3;">
                                <option value="hot" {{ old('temperature') === 'hot' ? 'selected' : '' }}>Quente</option>
                                <option value="warm" {{ old('temperature', 'warm') === 'warm' ? 'selected' : '' }}>Morno</option>
                                <option value="cold" {{ old('temperature') === 'cold' ? 'selected' : '' }}>Frio</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: #818181; display: block; margin-bottom: 2px;">Observacoes</label>
                        <textarea name="description" rows="2" placeholder="Detalhes sobre o interesse, condicoes, etc."
                                  style="width: 100%; padding: 0.5rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.875rem; resize: vertical; background: #0d0d0d; color: #e3e3e3;">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 1rem;">
                    <a href="{{ route('crm.board') }}"
                       style="padding: 0.5rem 1rem; font-size: 0.8rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; background: #141414; color: #818181; cursor: pointer; text-decoration: none;">
                        Cancelar
                    </a>
                    <button type="submit"
                            style="padding: 0.5rem 1rem; font-size: 0.8rem; background: #111827; color: white; border-radius: 0.375rem; border: none; cursor: pointer; font-weight: 600;">
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
