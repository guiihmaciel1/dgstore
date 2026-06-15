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

    {{-- Valorizar trade-in (jogo de números) --}}
    <div x-show="tradeInValue > 0" x-cloak class="mt-3">
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer select-none">
                <input type="checkbox" x-model="numberGame.enabled"
                       class="w-4 h-4 accent-violet-600 cursor-pointer rounded">
                <span class="text-[13px] font-semibold text-gray-700">Valorizar trade-in</span>
            </label>
            <span x-show="numberGame.enabled && numberGame.boost > 0"
                  class="text-[11px] font-bold text-violet-600 bg-violet-50 px-2 py-0.5 rounded-full"
                  x-text="'+ R$ ' + fmt(numberGame.boost)"></span>
        </div>

        <div x-show="numberGame.enabled" x-collapse class="mt-2 p-3 bg-violet-50/60 border border-violet-200 rounded-lg space-y-2">
            <div class="flex items-center justify-between text-[12px] text-gray-500">
                <span>Valorização</span>
                <span class="font-bold text-violet-700" x-text="'R$ ' + fmt(numberGame.boost)"></span>
            </div>
            <input type="range" x-model.number="numberGame.boost"
                   min="0" :max="boostMax" step="50"
                   class="w-full h-1.5 bg-violet-200 rounded-full appearance-none cursor-pointer accent-violet-600">
            <div class="flex justify-between text-[11px] text-gray-400">
                <span>R$ 0</span>
                <span x-text="'R$ ' + fmt(boostMax)"></span>
            </div>
            <div x-show="numberGame.boost > 0" x-collapse class="mt-1 p-2 bg-white rounded-md border border-violet-100 text-[12px] space-y-1">
                <div class="flex justify-between text-gray-500">
                    <span>Trade-in real:</span>
                    <span class="font-semibold" x-text="'R$ ' + fmt(tradeInValue)"></span>
                </div>
                <div class="flex justify-between text-violet-700">
                    <span>Cliente vê:</span>
                    <span class="font-bold" x-text="'R$ ' + fmt(displayTradeInValue)"></span>
                </div>
            </div>
            <p class="text-[11px] text-gray-400 leading-snug">O cliente vê um trade-in maior. O valor final e as parcelas não mudam.</p>
        </div>
    </div>

    {{-- Avaliador DGiFipe --}}
    @include('tools.negotiation._evaluator')

    {{-- Resumo saldo --}}
    <div x-show="cardBalance > 0" class="mt-3 p-3 bg-gray-50 rounded-lg flex justify-between text-[13px]">
        <span class="text-gray-500">Saldo no cartão:</span>
        <span class="font-bold text-gray-900" x-text="'R$ ' + fmt(cardBalance)"></span>
    </div>
</div>
