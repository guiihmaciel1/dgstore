<x-app-layout>
    <x-slot name="title">Nova compra</x-slot>
    @php
        $blankItem = ['category' => 'smartphone', 'condition' => 'new', 'model' => '', 'storage' => '', 'color' => '', 'unit_cost' => '', 'quantity' => 1];
        $oldItems = old('items');
        $initialItems = is_array($oldItems) && count($oldItems) ? array_values(array_map(function ($i) use ($blankItem) {
            $i = array_merge($blankItem, $i);
            $i['unit_cost'] = $i['unit_cost'] === null || $i['unit_cost'] === '' ? '' : number_format((float) $i['unit_cost'], 2, ',', '.');
            return $i;
        }, $oldItems)) : [$blankItem];
        $field = 'w-full rounded-lg border border-border-strong px-3 py-2 text-sm text-dg-100 placeholder:text-dg-500 focus:border-indigo-400 focus:ring-0';
        $label = 'mb-1.5 block text-xs font-medium text-dg-300';
    @endphp
    <div class="py-6">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('purchases.index') }}" class="text-xs font-medium text-dg-400 underline-offset-4 hover:text-dg-100 hover:underline">← Compras</a>
                <h1 class="mt-2 text-2xl font-bold text-dg-50">Nova compra</h1>
                <p class="mt-1 text-sm text-dg-400">Registre no momento em que pagar. O IMEI e os demais dados você completa quando o produto chegar.</p>
            </div>

            @if($errors->any())
                <div class="mb-4 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                    <p class="font-medium">Confira os campos destacados:</p>
                    <ul class="mt-1 list-inside list-disc text-xs text-red-300">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('purchases.store') }}"
                  x-data="purchaseForm(@js($initialItems), @js($unitCategories))" @submit="saving = true">
                @csrf

                {{-- Dados da compra --}}
                <section class="rounded-xl border border-border bg-surface-raised p-4 sm:p-5">
                    <h2 class="text-base font-semibold text-dg-100">Compra</h2>
                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="sm:col-span-2" x-data="{ other: @js(old('supplier_name') && !old('supplier_id')) }">
                            <label for="supplier_id" class="{{ $label }}">Fornecedor</label>
                            <select id="supplier_id" name="supplier_id" class="{{ $field }}" x-show="!other" :disabled="other">
                                <option value="">Selecione…</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @selected(old('supplier_id') === $supplier->id)>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="supplier_name" value="{{ old('supplier_name') }}" placeholder="Nome do fornecedor" maxlength="255"
                                   class="{{ $field }}" x-show="other" x-cloak :disabled="!other">
                            <button type="button" @click="other = !other" class="mt-1.5 text-xs font-medium text-indigo-300 underline-offset-4 hover:underline"
                                    x-text="other ? 'Escolher da lista' : 'Fornecedor não está na lista'"></button>
                            @error('supplier_name')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="purchased_at" class="{{ $label }}">Data da compra</label>
                            <input type="date" id="purchased_at" name="purchased_at" value="{{ old('purchased_at', now()->toDateString()) }}" max="{{ now()->toDateString() }}" required class="{{ $field }}">
                        </div>
                        <div>
                            <label for="expected_at" class="{{ $label }}">Previsão de chegada <span class="font-normal text-dg-500">(opcional)</span></label>
                            <input type="date" id="expected_at" name="expected_at" value="{{ old('expected_at') }}" class="{{ $field }}">
                            @error('expected_at')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2" x-data="{ register: @js((bool) old('register_expense', true)) }">
                        <div>
                            <label for="account_id" class="{{ $label }}">Saiu de qual conta</label>
                            <select id="account_id" name="account_id" class="{{ $field }}" :disabled="!register">
                                <option value="">Selecione…</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" @selected(old('account_id', $defaultAccountId) === $account->id)>{{ $account->name }}</option>
                                @endforeach
                            </select>
                            @error('account_id')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
                        </div>
                        <div class="flex items-end">
                            <label class="flex cursor-pointer items-start gap-2.5 text-sm text-dg-200">
                                <input type="hidden" name="register_expense" value="0">
                                <input type="checkbox" name="register_expense" value="1" x-model="register"
                                       class="mt-0.5 h-4 w-4 rounded border-border-strong text-indigo-500 focus:ring-indigo-400">
                                <span>Lançar no financeiro como despesa paga<span class="block text-xs text-dg-400">Desmarque só se o pagamento já foi lançado à mão.</span></span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="notes" class="{{ $label }}">Observação <span class="font-normal text-dg-500">(opcional)</span></label>
                        <input type="text" id="notes" name="notes" value="{{ old('notes') }}" maxlength="2000" placeholder="Ex.: pago no Pix, retirada em SP" class="{{ $field }}">
                    </div>
                </section>

                {{-- Itens --}}
                <section class="mt-4 rounded-xl border border-border bg-surface-raised p-4 sm:p-5">
                    <div class="flex items-baseline justify-between gap-3">
                        <h2 class="text-base font-semibold text-dg-100">Itens</h2>
                        <span class="text-xs text-dg-400">Aparelhos viram uma entrada por unidade (cada um com seu IMEI).</span>
                    </div>

                    <div class="mt-4 space-y-3">
                        <template x-for="(item, index) in items" :key="item.key">
                            <div class="rounded-lg border border-border bg-surface-overlay p-3 sm:p-4">
                                <div class="grid grid-cols-2 gap-3 md:grid-cols-12">
                                    <div class="col-span-1 md:col-span-2">
                                        <label class="{{ $label }}" :for="'cat-' + item.key">Categoria</label>
                                        <select :id="'cat-' + item.key" :name="`items[${index}][category]`" x-model="item.category" class="{{ $field }}">
                                            @foreach($categories as $category)
                                                <option value="{{ $category->value }}">{{ $category->label() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-span-1 md:col-span-2">
                                        <label class="{{ $label }}" :for="'cond-' + item.key">Condição</label>
                                        <select :id="'cond-' + item.key" :name="`items[${index}][condition]`" x-model="item.condition" class="{{ $field }}">
                                            @foreach($conditions as $condition)
                                                <option value="{{ $condition->value }}">{{ $condition->label() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-span-2 md:col-span-3">
                                        <label class="{{ $label }}" :for="'model-' + item.key">Modelo</label>
                                        <input :id="'model-' + item.key" type="text" :name="`items[${index}][model]`" x-model="item.model" required maxlength="100" placeholder="Ex.: 17 Pro Max" class="{{ $field }}">
                                    </div>
                                    <div class="col-span-1 md:col-span-2">
                                        <label class="{{ $label }}" :for="'storage-' + item.key">Armaz.</label>
                                        <input :id="'storage-' + item.key" type="text" :name="`items[${index}][storage]`" x-model="item.storage" maxlength="50" placeholder="256GB" class="{{ $field }}">
                                    </div>
                                    <div class="col-span-1 md:col-span-3">
                                        <label class="{{ $label }}" :for="'color-' + item.key">Cor</label>
                                        <input :id="'color-' + item.key" type="text" :name="`items[${index}][color]`" x-model="item.color" maxlength="50" placeholder="Laranja" class="{{ $field }}">
                                    </div>
                                    <div class="col-span-1 md:col-span-3">
                                        <label class="{{ $label }}" :for="'cost-' + item.key">Custo unitário</label>
                                        <div class="flex items-center rounded-lg border border-border-strong bg-surface-overlay focus-within:border-indigo-400">
                                            <span class="pl-3 text-sm text-dg-400">R$</span>
                                            <input :id="'cost-' + item.key" type="text" inputmode="decimal" :name="`items[${index}][unit_cost]`" x-model="item.unit_cost" @blur="item.unit_cost = format(item.unit_cost)" required placeholder="0,00"
                                                   class="w-full border-0 !bg-transparent px-2 py-2 text-right text-sm tabular-nums text-dg-100 placeholder:text-dg-500 focus:ring-0">
                                        </div>
                                    </div>
                                    <div class="col-span-1 md:col-span-2">
                                        <label class="{{ $label }}" :for="'qty-' + item.key">Qtd.</label>
                                        <input :id="'qty-' + item.key" type="number" min="1" max="200" :name="`items[${index}][quantity]`" x-model.number="item.quantity" required class="{{ $field }} text-right">
                                    </div>
                                    <div class="col-span-2 flex items-end justify-between gap-3 md:col-span-7">
                                        <p class="pb-2 text-xs text-dg-400">
                                            Subtotal <span class="font-semibold tabular-nums text-dg-100" x-text="'R$ ' + money(parse(item.unit_cost) * (item.quantity || 0))"></span>
                                            <span x-show="isUnit(item) && item.quantity > 1" x-cloak> · vira <span x-text="item.quantity"></span> entradas com IMEI</span>
                                        </p>
                                        <button type="button" @click="remove(index)" x-show="items.length > 1"
                                                class="rounded-md px-2.5 py-1.5 text-xs font-medium text-dg-400 transition-colors hover:bg-red-500/10 hover:text-red-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-red-300">
                                            Remover
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="add()"
                            class="mt-3 inline-flex items-center gap-1.5 rounded-lg border border-dashed border-border-strong px-3 py-2 text-sm font-medium text-dg-300 transition-colors hover:border-indigo-400 hover:text-dg-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Adicionar item
                    </button>
                </section>

                {{-- Total e envio --}}
                <div class="sticky bottom-0 mt-4 flex flex-col gap-3 rounded-xl border border-border-strong bg-surface-raised/95 p-4 backdrop-blur sm:flex-row sm:items-center sm:justify-between sm:p-5">
                    <p class="text-sm text-dg-300">
                        <span x-text="units()"></span> <span x-text="units() === 1 ? 'item' : 'itens'"></span> ·
                        Total <span class="text-lg font-bold tabular-nums text-dg-50" x-text="'R$ ' + money(total())"></span>
                    </p>
                    <div class="flex gap-2">
                        <a href="{{ route('purchases.index') }}" class="rounded-lg px-4 py-2.5 text-sm font-medium text-dg-300 transition-colors hover:bg-surface-elevated hover:text-dg-100">Cancelar</a>
                        <button type="submit" :disabled="saving"
                                class="rounded-lg bg-indigo-500 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-300 disabled:cursor-wait disabled:opacity-60"
                                x-text="saving ? 'Salvando…' : 'Registrar compra'">Registrar compra</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function purchaseForm(initialItems, unitCategories) {
            const parse = (v) => {
                v = String(v ?? '').trim().replace(/[^\d,.\-]/g, '');
                if (v === '') return 0;
                if (v.includes(',')) v = v.replace(/\./g, '').replace(',', '.');
                else if (/^-?\d{1,3}(\.\d{3})+$/.test(v)) v = v.replace(/\./g, '');
                const n = parseFloat(v);
                return isNaN(n) ? 0 : n;
            };
            const money = (n) => Number(n).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            let seq = 0;
            const withKey = (item) => ({ ...item, key: ++seq });

            return {
                saving: false,
                items: initialItems.map(withKey),
                parse, money,
                format(v) { return String(v ?? '').trim() === '' ? '' : money(parse(v)); },
                isUnit(item) { return unitCategories.includes(item.category); },
                add() {
                    const last = this.items[this.items.length - 1] || {};
                    this.items.push(withKey({ category: last.category || 'smartphone', condition: last.condition || 'new', model: '', storage: '', color: '', unit_cost: '', quantity: 1 }));
                },
                remove(index) { this.items.splice(index, 1); },
                total() { return this.items.reduce((s, i) => s + parse(i.unit_cost) * (Number(i.quantity) || 0), 0); },
                units() { return this.items.reduce((s, i) => s + (Number(i.quantity) || 0), 0); },
            };
        }
    </script>
</x-app-layout>
