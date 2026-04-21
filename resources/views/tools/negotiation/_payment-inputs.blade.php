<div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5 mb-3">
    <div class="grid grid-cols-2 gap-3 sm:gap-4">
        <div>
            <label class="block text-xs font-semibold text-emerald-600 uppercase tracking-wider mb-1.5">Entrada (Pix)</label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-emerald-300 text-sm font-medium">R$</span>
                <input type="text" x-model="downPaymentInput"
                       placeholder="0,00"
                       class="w-full py-3 pl-10 pr-3.5 bg-green-50 border border-green-200 rounded-[10px] text-lg font-semibold text-emerald-600 outline-none text-right focus:border-emerald-600 focus:bg-white transition-colors">
            </div>
        </div>
        <div>
            <label class="block text-xs font-semibold text-amber-600 uppercase tracking-wider mb-1.5">Trade-in</label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-amber-300 text-sm font-medium">R$</span>
                <input type="text" x-model="tradeIn.offeredInput"
                       placeholder="0,00"
                       class="w-full py-3 pl-10 pr-3.5 bg-amber-50 border border-amber-200 rounded-[10px] text-lg font-semibold text-amber-600 outline-none text-right focus:border-amber-600 focus:bg-white transition-colors">
            </div>
        </div>
    </div>

    {{-- Desconto Pix --}}
    <div class="mt-3 flex items-center gap-2.5">
        <label class="apple-section-title whitespace-nowrap">Desconto Pix</label>
        <div class="relative w-[90px] shrink-0">
            <input type="text" x-model="pixDiscountPercent"
                   placeholder="0"
                   class="w-full py-2 pl-3 pr-7 bg-gray-50 border border-gray-200 rounded-lg text-sm font-semibold text-gray-900 outline-none text-right focus:border-gray-900 focus:bg-white transition-colors">
            <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-[13px] font-semibold pointer-events-none">%</span>
        </div>
        <span x-show="pixDiscount > 0 && cardBalance > 0"
              class="text-[13px] font-semibold text-emerald-600"
              x-text="'- R$ ' + fmt(cardBalance - pixPrice)"></span>
    </div>

    {{-- Avaliador DGiFipe --}}
    @include('tools.negotiation._evaluator')

    {{-- Resumo saldo --}}
    <div x-show="cardBalance > 0" class="mt-3 p-3 bg-gray-50 rounded-lg flex justify-between text-[13px]">
        <span class="text-gray-500">Saldo no cartão:</span>
        <span class="font-bold text-gray-900" x-text="'R$ ' + fmt(cardBalance)"></span>
    </div>
</div>
