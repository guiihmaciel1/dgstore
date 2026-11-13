{{-- Seção: Interesses de Produto (CRUD) --}}
<div class="bg-surface-raised border border-border rounded-xl p-4"
     x-data="{ showAddInterest: false }">
    <div class="flex items-center justify-between mb-2">
        <span class="text-dg-500 font-semibold text-[0.65rem] uppercase tracking-wider">
            Interesses de Produto
        </span>
        @if($deal->isOpen())
            <button class="text-[0.65rem] font-semibold text-blue-500 bg-transparent border-0 cursor-pointer py-[0.15rem] px-1.5 rounded transition-colors hover:bg-blue-500/[0.08]" @click="showAddInterest = !showAddInterest" type="button">
                <span x-show="!showAddInterest">+ Adicionar</span>
                <span x-show="showAddInterest">Cancelar</span>
            </button>
        @endif
    </div>

    {{-- Lista de interesses existentes --}}
    @forelse($deal->productInterests as $interest)
        @php $inStock = $interest->hasMatchInStock(); @endphp
        <div class="flex items-center gap-2 p-2" style="background: {{ $inStock ? 'rgba(16,185,129,0.05)' : 'rgba(239,68,68,0.05)' }}; border: 1px solid {{ $inStock ? 'rgba(16,185,129,0.15)' : 'rgba(239,68,68,0.15)' }}; border-radius: 0.375rem; margin-bottom: 0.375rem;">
            <div class="flex-1">
                <div class="text-[0.8rem] font-semibold text-dg-100">
                    {{ $interest->model }}
                    @if($interest->storage)
                        <span class="font-normal text-dg-500">{{ $interest->storage }}</span>
                    @endif
                </div>
                <div class="flex gap-2 mt-0.5 flex-wrap">
                    @if($interest->color)
                        <span class="text-[0.7rem] text-dg-500">{{ $interest->color }}</span>
                    @endif
                    @if($interest->condition)
                        <span class="text-[0.6rem] font-semibold py-[1px] px-[6px] rounded-[4px]" style="{{ $interest->condition === 'novo' ? 'background: rgba(59,130,246,0.12); color: #93c5fd;' : 'background: rgba(251,191,36,0.12); color: #fbbf24;' }}">
                            {{ $interest->condition === 'novo' ? 'Novo' : 'Seminovo' }}
                        </span>
                    @endif
                    @if($interest->max_budget)
                        <span class="text-[0.7rem] text-dg-500">Ate R$ {{ number_format((float)$interest->max_budget, 2, ',', '.') }}</span>
                    @endif
                </div>
            </div>
            <span class="text-[0.65rem] font-bold py-[2px] px-[8px] rounded-[4px] whitespace-nowrap" style="{{ $inStock ? 'background: rgba(16,185,129,0.12); color: #6ee7b7;' : 'background: rgba(239,68,68,0.12); color: #fca5a5;' }}">
                {{ $inStock ? 'Em estoque' : 'Sem estoque' }}
            </span>
            @if($deal->isOpen())
                <form class="inline" method="POST" action="{{ route('crm.deals.interests.destroy', [$deal, $interest]) }}">
                    @csrf @method('DELETE')
                    <button class="bg-transparent border-0 cursor-pointer text-dg-500 p-[0.15rem] transition-colors hover:text-red-300" type="submit" onclick="return confirm('Remover este interesse?')" title="Remover">
                        <svg class="w-[14px] h-[14px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </form>
            @endif
        </div>
    @empty
        <div class="text-center p-3 text-[#444] text-xs" x-show="!showAddInterest">
            Nenhum interesse cadastrado
        </div>
    @endforelse

    {{-- Formulario para adicionar interesse --}}
    <div class="mt-2 p-3 bg-surface-overlay border border-blue-500/15 rounded-lg" x-show="showAddInterest" x-transition>
        <form method="POST" action="{{ route('crm.deals.interests.store', $deal) }}">
            @csrf
            <div class="grid grid-cols-[1fr_1fr] gap-2">
                <div>
                    <label class="text-[0.65rem] font-semibold text-dg-500">Modelo <span class="text-red-300">*</span></label>
                    <select class="w-full p-1.5 border border-border rounded-md text-[0.8rem] bg-surface text-dg-100" name="model" required>
                        <option value="">Selecione...</option>
                        <optgroup label="iPhone">
                            <option>iPhone 18 Pro Max</option>
                            <option>iPhone 18 Pro</option>
                            <option>iPhone 17 Pro Max</option>
                            <option>iPhone 17 Pro</option>
                            <option>iPhone 17 Air</option>
                            <option>iPhone 17</option>
                            <option>iPhone 16 Pro Max</option>
                            <option>iPhone 16 Pro</option>
                            <option>iPhone 16 Plus</option>
                            <option>iPhone 16</option>
                            <option>iPhone 15 Pro Max</option>
                            <option>iPhone 15 Pro</option>
                            <option>iPhone 15 Plus</option>
                            <option>iPhone 15</option>
                            <option>iPhone 14 Pro Max</option>
                            <option>iPhone 14 Pro</option>
                            <option>iPhone 14 Plus</option>
                            <option>iPhone 14</option>
                            <option>iPhone 13</option>
                            <option>iPhone 12</option>
                            <option>iPhone 11</option>
                            <option>iPhone SE</option>
                        </optgroup>
                        <optgroup label="Outros Apple">
                            <option>iPad</option>
                            <option>MacBook</option>
                            <option>Apple Watch</option>
                            <option>AirPods</option>
                        </optgroup>
                    </select>
                </div>
                <div>
                    <label class="text-[0.65rem] font-semibold text-dg-500">Armazenamento</label>
                    <select class="w-full p-1.5 border border-border rounded-md text-[0.8rem] bg-surface text-dg-100" name="storage">
                        <option value="">Qualquer</option>
                        <option>64GB</option>
                        <option>128GB</option>
                        <option>256GB</option>
                        <option>512GB</option>
                        <option>1TB</option>
                    </select>
                </div>
                <div>
                    <label class="text-[0.65rem] font-semibold text-dg-500">Cor</label>
                    <input class="w-full p-1.5 border border-border rounded-md text-[0.8rem] bg-surface text-dg-100" type="text" name="color" placeholder="Ex: Titanio Natural">
                </div>
                <div>
                    <label class="text-[0.65rem] font-semibold text-dg-500">Condicao</label>
                    <select class="w-full p-1.5 border border-border rounded-md text-[0.8rem] bg-surface text-dg-100" name="condition">
                        <option value="">Qualquer</option>
                        <option value="novo">Novo</option>
                        <option value="seminovo">Seminovo</option>
                    </select>
                </div>
            </div>
            <div class="mt-2">
                <label class="text-[0.65rem] font-semibold text-dg-500">Orcamento maximo (R$)</label>
                <input class="w-full p-1.5 border border-border rounded-md text-[0.8rem] bg-surface text-dg-100" type="number" name="max_budget" step="0.01" placeholder="0,00">
            </div>
            <div class="flex gap-2 justify-end mt-2">
                <button class="py-[0.3rem] px-2.5 text-[0.7rem] border border-border rounded-md bg-surface-raised text-dg-300 cursor-pointer" @click.prevent="showAddInterest = false" type="button">
                    Cancelar
                </button>
                <button class="py-[0.3rem] px-2.5 text-[0.7rem] bg-blue-500 text-white rounded-md border-0 cursor-pointer font-semibold" type="submit">
                    Adicionar
                </button>
            </div>
        </form>
    </div>
</div>
