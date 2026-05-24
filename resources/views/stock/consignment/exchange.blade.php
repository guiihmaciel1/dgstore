<x-app-layout>
    <x-slot name="title">Troca de Aparelho</x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8" x-data="exchangeForm()">

            {{-- Header --}}
            <div class="flex items-center mb-6">
                <a href="{{ route('stock.consignment.index') }}" class="mr-3 p-2 text-gray-500 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Trocar Aparelho com Lojista</h1>
                    <p class="text-sm text-gray-500">O IMEI/Serial original sera preservado no historico do item</p>
                </div>
            </div>

            @if($errors->any())
                <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1rem;">
                    <ul style="list-style: disc; padding-left: 1.25rem; color: #991b1b; font-size: 0.875rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('stock.consignment.exchange-store', $item) }}">
                @csrf

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">

                    {{-- ─── Coluna esquerda: Item Atual (Read-Only) ─── --}}
                    <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 1rem; padding: 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid #e5e7eb; margin-bottom: 1rem;">
                            <span style="font-size: 1.25rem;">📦</span>
                            <h2 style="font-size: 1rem; font-weight: 700; color: #111827;">Item Atual</h2>
                        </div>

                        <dl style="display: flex; flex-direction: column; gap: 0.625rem;">
                            <div>
                                <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Lote</dt>
                                <dd style="font-size: 0.875rem; color: #111827; font-family: monospace;">{{ $item->batch?->batch_code ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Produto</dt>
                                <dd style="font-size: 0.875rem; font-weight: 600; color: #111827;">{{ $item->name }}</dd>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Storage</dt>
                                    <dd style="font-size: 0.875rem; color: #111827;">{{ $item->storage ?: '-' }}</dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Cor</dt>
                                    <dd style="font-size: 0.875rem; color: #111827;">{{ $item->color ?: '-' }}</dd>
                                </div>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Condicao</dt>
                                    <dd style="font-size: 0.875rem; color: #111827;">{{ $item->condition?->label() ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt style="font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase;">Custo</dt>
                                    <dd style="font-size: 0.875rem; color: #111827;">{{ $item->formatted_supplier_cost }}</dd>
                                </div>
                            </div>
                            <div style="background: white; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                                    <div>
                                        <dt style="font-size: 0.6875rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">IMEI</dt>
                                        <dd style="font-size: 0.8125rem; color: #111827; font-family: monospace; word-break: break-all;">{{ $item->imei ?: '—' }}</dd>
                                    </div>
                                    <div>
                                        <dt style="font-size: 0.6875rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Serial</dt>
                                        <dd style="font-size: 0.8125rem; color: #111827; font-family: monospace; word-break: break-all;">{{ $item->serial_number ?: '—' }}</dd>
                                    </div>
                                </div>
                            </div>

                            @if($item->hasBeenExchanged())
                                <div style="background: #fef3c7; border: 1px solid #fde68a; padding: 0.5rem 0.75rem; border-radius: 0.5rem; font-size: 0.75rem; color: #92400e;">
                                    Este item ja foi trocado {{ $item->exchanges->count() }} vez(es) anteriormente.
                                </div>
                            @endif
                        </dl>
                    </div>

                    {{-- ─── Coluna direita: Aparelho Recebido ─── --}}
                    <div style="background: white; border: 2px solid #16a34a; border-radius: 1rem; padding: 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid #f3f4f6; margin-bottom: 1rem;">
                            <span style="font-size: 1.25rem;">📥</span>
                            <h2 style="font-size: 1rem; font-weight: 700; color: #111827;">Aparelho Recebido</h2>
                        </div>

                        {{-- Busca catalogo --}}
                        <div style="position: relative; margin-bottom: 1rem;" @click.outside="searchOpen = false">
                            <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem;">
                                Buscar produto
                            </label>
                            <input type="text" placeholder="Digite o produto (ou preencha manualmente)"
                                   x-model="searchTerm"
                                   @focus="searchOpen = true; searchProducts()"
                                   @input.debounce.250ms="searchProducts()"
                                   style="width: 100%; padding: 0.5rem 0.5rem 0.5rem 2rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8125rem;">
                            <svg style="position: absolute; left: 0.5rem; top: 1.95rem; width: 0.875rem; height: 0.875rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
                            </svg>

                            <div x-show="searchOpen && searchResults.length > 0" x-cloak
                                 style="position: absolute; z-index: 30; left: 0; right: 0; margin-top: 0.25rem; background: white; border: 1px solid #e5e7eb; border-radius: 0.5rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); max-height: 14rem; overflow-y: auto;">
                                <template x-for="(item, idx) in searchResults" :key="idx">
                                    <button type="button" @click="selectProduct(item)"
                                            style="display: block; width: 100%; padding: 0.5rem 0.75rem; text-align: left; border-bottom: 1px solid #f3f4f6; cursor: pointer; background: white;"
                                            onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                        <span style="font-weight: 600; color: #111827; font-size: 0.8125rem;" x-text="item.name"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- Nome --}}
                        <div style="margin-bottom: 0.75rem;">
                            <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #374151; margin-bottom: 0.25rem;">
                                Produto <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="text" name="name" x-model="form.name" required placeholder="Ex: iPhone 17 Pro Max"
                                   style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8125rem;">
                        </div>

                        {{-- Storage / Cor --}}
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 0.75rem;">
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Storage</label>
                                <select x-show="storageOptions.length > 0" name="storage" x-model="form.storage"
                                        style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8125rem;">
                                    <option value="">-- Selecione --</option>
                                    <template x-for="opt in storageOptions" :key="opt">
                                        <option :value="opt" x-text="opt"></option>
                                    </template>
                                </select>
                                <input x-show="storageOptions.length === 0" type="text" name="storage" x-model="form.storage" placeholder="Ex: 256GB"
                                       style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8125rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Cor</label>
                                <select x-show="colorOptions.length > 0 && form.color !== '__custom'" name="color" x-model="form.color"
                                        style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8125rem;">
                                    <option value="">-- Selecione --</option>
                                    <template x-for="opt in colorOptions" :key="opt">
                                        <option :value="opt" x-text="opt"></option>
                                    </template>
                                    <option value="__custom">+ Outra cor...</option>
                                </select>
                                <input x-show="colorOptions.length === 0 || form.color === '__custom'" type="text" name="color"
                                       :value="form.color === '__custom' ? '' : form.color"
                                       @input="form.color = $event.target.value"
                                       placeholder="Ex: Silver"
                                       style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8125rem;">
                            </div>
                        </div>

                        {{-- Modelo / Condicao --}}
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 0.75rem;">
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Modelo (codigo)</label>
                                <input type="text" name="model" x-model="form.model" placeholder="Ex: A3106"
                                       style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8125rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Condicao</label>
                                <select name="condition" x-model="form.condition" required
                                        style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8125rem;">
                                    <option value="new">Novo</option>
                                    <option value="used">Seminovo</option>
                                </select>
                            </div>
                        </div>

                        {{-- IMEI / Serial --}}
                        <div style="background: #ecfdf5; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid #a7f3d0;">
                            <p style="font-size: 0.6875rem; font-weight: 700; color: #065f46; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">IMEI/Serial Recebido</p>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                                <div>
                                    <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">IMEI</label>
                                    <input type="text" name="imei" x-model="form.imei" placeholder="Numero IMEI"
                                           style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8125rem; font-family: monospace;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Serial</label>
                                    <input type="text" name="serial_number" x-model="form.serial_number" placeholder="Serial Number"
                                           style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8125rem; font-family: monospace;">
                                </div>
                            </div>
                            <p style="font-size: 0.6875rem; color: #065f46; margin-top: 0.5rem;">
                                Informe pelo menos um (IMEI ou Serial).
                            </p>
                        </div>
                    </div>
                </div>

                {{-- ─── Detalhes da Troca ─── --}}
                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 1rem; padding: 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid #f3f4f6; margin-bottom: 1rem;">
                        <span style="font-size: 1.25rem;">🔁</span>
                        <h2 style="font-size: 1rem; font-weight: 700; color: #111827;">Detalhes da Troca</h2>
                    </div>

                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem;">
                                Nome do Lojista <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="text" name="partner_name" required maxlength="255"
                                   value="{{ old('partner_name') }}"
                                   placeholder="Ex: Loja XYZ - Joao da Silva"
                                   style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem;">
                                Ajuste Financeiro (R$)
                            </label>
                            <input type="number" name="cost_adjustment" step="0.01" placeholder="0,00"
                                   value="{{ old('cost_adjustment', 0) }}"
                                   style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;">
                            <p style="font-size: 0.6875rem; color: #6b7280; margin-top: 0.25rem;">
                                Positivo = recebi volta. Negativo = paguei diferenca. Zero = troca neutra.
                            </p>
                        </div>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">Motivo / Observacoes</label>
                        <textarea name="reason" rows="2" placeholder="Ex: Cliente preferiu outra cor, defeito na tela, etc."
                                  style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; resize: vertical;">{{ old('reason') }}</textarea>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #f3f4f6;">
                        <a href="{{ route('stock.consignment.index') }}"
                           style="padding: 0.625rem 1.5rem; color: #6b7280; font-size: 0.875rem; text-decoration: none; border: 1px solid #e5e7eb; border-radius: 0.5rem;">
                            Cancelar
                        </a>
                        <button type="submit"
                                style="padding: 0.625rem 1.5rem; background: #16a34a; color: white; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
                            Confirmar Troca
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function exchangeForm() {
            return {
                form: {
                    name: @json($item->name),
                    storage: @json($item->storage ?? ''),
                    color: @json($item->color ?? ''),
                    model: @json($item->model ?? ''),
                    condition: @json($item->condition?->value ?? 'new'),
                    imei: '',
                    serial_number: '',
                },
                searchTerm: '',
                searchOpen: false,
                searchResults: [],
                colorOptions: [],
                storageOptions: [],

                async searchProducts() {
                    try {
                        const url = '{{ route('stock.consignment.catalog') }}?q=' + encodeURIComponent(this.searchTerm);
                        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                        if (!res.ok) return;
                        this.searchResults = await res.json();
                    } catch (e) {
                        this.searchResults = [];
                    }
                },

                selectProduct(item) {
                    this.form.name = item.name;
                    this.searchTerm = item.name;
                    this.colorOptions = item.colors || [];
                    this.storageOptions = item.storages || [];
                    if (this.storageOptions.length === 1) {
                        this.form.storage = this.storageOptions[0];
                    }
                    this.searchOpen = false;
                },
            };
        }
    </script>
</x-app-layout>
