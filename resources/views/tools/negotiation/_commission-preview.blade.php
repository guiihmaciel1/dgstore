@if(in_array(auth()->user()->role->value, ['seller', 'intern']))
<div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl border border-emerald-200 p-4 sm:p-5 mb-3"
     x-show="productPrice > 0"
     x-transition>
    <div class="flex items-center gap-2 mb-3">
        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h3 class="text-sm font-bold text-emerald-900">Sua Comissão Estimada</h3>
    </div>

    <div class="space-y-2">
        {{-- Comissão sobre Lucro --}}
        <div class="flex items-center justify-between text-sm" x-show="commissionEstimate.profit > 0">
            <span class="text-gray-700">
                <span class="font-medium">Aparelho</span>
                <span class="text-gray-400 text-xs ml-1">(10% do lucro)</span>
            </span>
            <span class="font-bold text-emerald-700" x-text="'R$ ' + fmt(commissionEstimate.profit)"></span>
        </div>

        {{-- Comissão sobre Trade-in --}}
        <div class="flex items-center justify-between text-sm" x-show="commissionEstimate.tradein > 0">
            <span class="text-gray-700">
                <span class="font-medium">Trade-in</span>
                <span class="text-gray-400 text-xs ml-1">(10% da economia)</span>
            </span>
            <span class="font-bold text-emerald-700" x-text="'R$ ' + fmt(commissionEstimate.tradein)"></span>
        </div>

        {{-- Sem comissão --}}
        <div x-show="commissionEstimate.total === 0 && productCost > 0" class="text-sm text-amber-700">
            <span class="font-medium">Margem insuficiente</span>
            <span class="text-xs text-gray-500 ml-1">(mín. 30% sobre o custo)</span>
        </div>

        {{-- Info de custo necessário --}}
        <div x-show="productCost <= 0" class="text-sm text-gray-500 italic">
            Selecione um seminovo do estoque para calcular a comissão.
        </div>

        {{-- Total --}}
        <div class="pt-2 mt-2 border-t border-emerald-200 flex items-center justify-between"
             x-show="commissionEstimate.total > 0">
            <span class="text-sm font-bold text-emerald-900">Total Estimado</span>
            <span class="text-lg font-black text-emerald-700" x-text="'R$ ' + fmt(commissionEstimate.total)"></span>
        </div>
    </div>
</div>
@endif
