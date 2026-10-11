{{-- Seção: Interesses de Produto (CRUD) --}}
<div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem;"
     x-data="{ showAddInterest: false }">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
        <span style="color: #555; font-weight: 600; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.05em;">
            Interesses de Produto
        </span>
        @if($deal->isOpen())
            <button @click="showAddInterest = !showAddInterest" type="button"
                    style="font-size: 0.65rem; font-weight: 600; color: #3b82f6; background: none; border: none; cursor: pointer; padding: 0.15rem 0.375rem; border-radius: 0.25rem; transition: background 0.15s;"
                    onmouseover="this.style.background='rgba(59,130,246,0.08)'" onmouseout="this.style.background='none'">
                <span x-show="!showAddInterest">+ Adicionar</span>
                <span x-show="showAddInterest">Cancelar</span>
            </button>
        @endif
    </div>

    {{-- Lista de interesses existentes --}}
    @forelse($deal->productInterests as $interest)
        @php $inStock = $interest->hasMatchInStock(); @endphp
        <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: {{ $inStock ? 'rgba(16,185,129,0.05)' : 'rgba(239,68,68,0.05)' }}; border: 1px solid {{ $inStock ? 'rgba(16,185,129,0.15)' : 'rgba(239,68,68,0.15)' }}; border-radius: 0.375rem; margin-bottom: 0.375rem;">
            <div style="flex: 1;">
                <div style="font-size: 0.8rem; font-weight: 600; color: #e3e3e3;">
                    {{ $interest->model }}
                    @if($interest->storage)
                        <span style="font-weight: 400; color: #666;">{{ $interest->storage }}</span>
                    @endif
                </div>
                <div style="display: flex; gap: 0.5rem; margin-top: 0.125rem; flex-wrap: wrap;">
                    @if($interest->color)
                        <span style="font-size: 0.7rem; color: #666;">{{ $interest->color }}</span>
                    @endif
                    @if($interest->condition)
                        <span style="font-size: 0.6rem; font-weight: 600; padding: 1px 6px; border-radius: 4px; {{ $interest->condition === 'novo' ? 'background: rgba(59,130,246,0.12); color: #93c5fd;' : 'background: rgba(251,191,36,0.12); color: #fbbf24;' }}">
                            {{ $interest->condition === 'novo' ? 'Novo' : 'Seminovo' }}
                        </span>
                    @endif
                    @if($interest->max_budget)
                        <span style="font-size: 0.7rem; color: #666;">Ate R$ {{ number_format((float)$interest->max_budget, 2, ',', '.') }}</span>
                    @endif
                </div>
            </div>
            <span style="font-size: 0.65rem; font-weight: 700; padding: 2px 8px; border-radius: 4px; white-space: nowrap; {{ $inStock ? 'background: rgba(16,185,129,0.12); color: #6ee7b7;' : 'background: rgba(239,68,68,0.12); color: #fca5a5;' }}">
                {{ $inStock ? 'Em estoque' : 'Sem estoque' }}
            </span>
            @if($deal->isOpen())
                <form method="POST" action="{{ route('crm.deals.interests.destroy', [$deal, $interest]) }}" style="display: inline;">
                    @csrf @method('DELETE')
                    <button type="submit" onclick="return confirm('Remover este interesse?')" title="Remover"
                            style="background: none; border: none; cursor: pointer; color: #555; padding: 0.15rem; transition: color 0.15s;"
                            onmouseover="this.style.color='#fca5a5'" onmouseout="this.style.color='#555'">
                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </form>
            @endif
        </div>
    @empty
        <div x-show="!showAddInterest" style="text-align: center; padding: 0.75rem; color: #444; font-size: 0.75rem;">
            Nenhum interesse cadastrado
        </div>
    @endforelse

    {{-- Formulario para adicionar interesse --}}
    <div x-show="showAddInterest" x-transition style="margin-top: 0.5rem; padding: 0.75rem; background: #1a1a1a; border: 1px solid rgba(59,130,246,0.15); border-radius: 0.5rem;">
        <form method="POST" action="{{ route('crm.deals.interests.store', $deal) }}">
            @csrf
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                <div>
                    <label style="font-size: 0.65rem; font-weight: 600; color: #555;">Modelo <span style="color: #fca5a5;">*</span></label>
                    <select name="model" required style="width: 100%; padding: 0.375rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; background: #0d0d0d; color: #e3e3e3;">
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
                    <label style="font-size: 0.65rem; font-weight: 600; color: #555;">Armazenamento</label>
                    <select name="storage" style="width: 100%; padding: 0.375rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; background: #0d0d0d; color: #e3e3e3;">
                        <option value="">Qualquer</option>
                        <option>64GB</option>
                        <option>128GB</option>
                        <option>256GB</option>
                        <option>512GB</option>
                        <option>1TB</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.65rem; font-weight: 600; color: #555;">Cor</label>
                    <input type="text" name="color" placeholder="Ex: Titanio Natural"
                           style="width: 100%; padding: 0.375rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; background: #0d0d0d; color: #e3e3e3;">
                </div>
                <div>
                    <label style="font-size: 0.65rem; font-weight: 600; color: #555;">Condicao</label>
                    <select name="condition" style="width: 100%; padding: 0.375rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; background: #0d0d0d; color: #e3e3e3;">
                        <option value="">Qualquer</option>
                        <option value="novo">Novo</option>
                        <option value="seminovo">Seminovo</option>
                    </select>
                </div>
            </div>
            <div style="margin-top: 0.5rem;">
                <label style="font-size: 0.65rem; font-weight: 600; color: #555;">Orcamento maximo (R$)</label>
                <input type="number" name="max_budget" step="0.01" placeholder="0,00"
                       style="width: 100%; padding: 0.375rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; background: #0d0d0d; color: #e3e3e3;">
            </div>
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 0.5rem;">
                <button @click.prevent="showAddInterest = false" type="button"
                        style="padding: 0.3rem 0.625rem; font-size: 0.7rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; background: #141414; color: #a4a4a4; cursor: pointer;">
                    Cancelar
                </button>
                <button type="submit"
                        style="padding: 0.3rem 0.625rem; font-size: 0.7rem; background: #3b82f6; color: white; border-radius: 0.375rem; border: none; cursor: pointer; font-weight: 600;">
                    Adicionar
                </button>
            </div>
        </form>
    </div>
</div>
