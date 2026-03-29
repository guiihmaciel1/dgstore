<x-b2b-app-layout>
    <x-slot name="header">
        <div class="max-w-2xl mx-auto w-full flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
            <div class="flex items-start sm:items-center gap-3 min-w-0">
                <div class="shrink-0 w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-gray-100 border border-gray-200/80 flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-gray-900 tracking-tight">Pagamento PIX</h2>
                    <p class="text-sm text-gray-500 mt-0.5 truncate sm:whitespace-normal">
                        Pedido <span class="font-medium text-gray-700">{{ $order->order_number }}</span>
                    </p>
                </div>
            </div>
            <span class="apple-badge shrink-0 self-start sm:self-center bg-amber-50 text-amber-800 border border-amber-200/80">Aguardando pagamento</span>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-0 sm:px-0" x-data="pixPayment()">
        {{-- Valor --}}
        <div class="apple-card rounded-2xl overflow-hidden mb-4 sm:mb-5">
            <div class="px-5 py-6 sm:px-8 sm:py-8 text-center border-b border-gray-100 bg-gradient-to-b from-white to-gray-50/80">
                <p class="text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wide">Valor a pagar</p>
                <p class="mt-2 text-3xl sm:text-4xl font-semibold text-gray-900 tracking-tight tabular-nums">{{ $order->formatted_total }}</p>
                <p class="mt-2 text-xs text-gray-400">Pedido {{ $order->order_number }}</p>
            </div>

            {{-- QR (simulação visual) --}}
            <div class="p-5 sm:p-8 flex flex-col items-center">
                <p class="text-sm font-medium text-gray-600 mb-4 tracking-tight">Escaneie no app do banco</p>
                <div class="w-full max-w-[220px] sm:max-w-[240px] aspect-square rounded-2xl border border-gray-200 bg-white p-4 sm:p-5 shadow-inner flex items-center justify-center">
                    <svg viewBox="0 0 100 100" class="w-full h-full text-gray-900" fill="currentColor" aria-hidden="true">
                        <rect width="100" height="100" fill="#ffffff"/>
                        <g fill="#111827">
                            <rect x="4" y="4" width="28" height="28" rx="2"/>
                            <rect x="10" y="10" width="16" height="16" fill="#ffffff"/>
                            <rect x="14" y="14" width="8" height="8"/>
                            <rect x="68" y="4" width="28" height="28" rx="2"/>
                            <rect x="74" y="10" width="16" height="16" fill="#ffffff"/>
                            <rect x="78" y="14" width="8" height="8"/>
                            <rect x="4" y="68" width="28" height="28" rx="2"/>
                            <rect x="10" y="74" width="16" height="16" fill="#ffffff"/>
                            <rect x="14" y="78" width="8" height="8"/>
                            <rect x="40" y="4" width="6" height="6"/>
                            <rect x="52" y="4" width="6" height="6"/>
                            <rect x="40" y="16" width="6" height="6"/>
                            <rect x="46" y="22" width="6" height="6"/>
                            <rect x="58" y="16" width="6" height="6"/>
                            <rect x="40" y="40" width="6" height="6"/>
                            <rect x="52" y="40" width="6" height="6"/>
                            <rect x="64" y="40" width="6" height="6"/>
                            <rect x="40" y="52" width="6" height="6"/>
                            <rect x="58" y="52" width="6" height="6"/>
                            <rect x="76" y="40" width="6" height="6"/>
                            <rect x="88" y="40" width="6" height="6"/>
                            <rect x="76" y="52" width="6" height="6"/>
                            <rect x="88" y="52" width="6" height="6"/>
                            <rect x="40" y="64" width="6" height="6"/>
                            <rect x="52" y="64" width="6" height="6"/>
                            <rect x="64" y="64" width="6" height="6"/>
                            <rect x="76" y="64" width="6" height="6"/>
                            <rect x="88" y="64" width="6" height="6"/>
                            <rect x="40" y="76" width="6" height="6"/>
                            <rect x="52" y="76" width="6" height="6"/>
                            <rect x="70" y="76" width="6" height="6"/>
                            <rect x="82" y="76" width="6" height="6"/>
                            <rect x="88" y="82" width="6" height="6"/>
                            <rect x="40" y="88" width="6" height="6"/>
                            <rect x="52" y="88" width="6" height="6"/>
                            <rect x="64" y="88" width="6" height="6"/>
                        </g>
                    </svg>
                </div>
                <p class="mt-4 text-xs text-center text-gray-400 max-w-xs leading-relaxed">Ilustração do QR — use a chave ou o código abaixo para concluir o PIX.</p>
            </div>
        </div>

        {{-- Chave PIX + código --}}
        <div class="apple-card rounded-2xl p-5 sm:p-8 mb-4 sm:mb-5 space-y-6 sm:space-y-8">
            @if($pixKey)
                <div>
                    <label class="apple-label">Chave PIX</label>
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                        <div class="apple-input flex-1 min-w-0 font-mono text-xs sm:text-sm py-3 truncate select-all text-gray-800" title="{{ $pixKey }}">
                            {{ $pixKey }}
                        </div>
                        <button type="button"
                                @click="copyPix(@js($pixKey), 'key')"
                                class="apple-btn-primary rounded-xl py-3 px-4 sm:px-5 text-sm shrink-0 w-full sm:w-auto justify-center">
                            <template x-if="!copiedKey">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                </svg>
                            </template>
                            <template x-if="copiedKey">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </template>
                            <span x-text="copiedKey ? 'Copiado!' : 'Copiar'"></span>
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Beneficiário: <span class="font-medium text-gray-700">{{ $companyName }}</span></p>
                </div>
            @else
                <div class="rounded-2xl border border-amber-200/80 bg-amber-50/90 p-4 sm:p-5">
                    <p class="text-sm font-medium text-amber-900 tracking-tight">Chave PIX não configurada</p>
                    <p class="text-xs text-amber-800/90 mt-1.5 leading-relaxed">Entre em contato pelo WhatsApp para obter os dados de pagamento.</p>
                </div>
            @endif

            @if($order->pix_code)
                <div>
                    <label class="apple-label">Código de referência do pedido</label>
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                        <div class="apple-input flex-1 min-w-0 font-mono text-xs sm:text-sm py-3 truncate select-all text-gray-800" title="{{ $order->pix_code }}">
                            {{ $order->pix_code }}
                        </div>
                        <button type="button"
                                @click="copyPix(@js($order->pix_code), 'code')"
                                class="apple-btn-secondary rounded-xl py-3 px-4 sm:px-5 text-sm shrink-0 w-full sm:w-auto justify-center border border-gray-200/80">
                            <template x-if="!copiedCode">
                                <svg class="w-4 h-4 shrink-0 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                </svg>
                            </template>
                            <template x-if="copiedCode">
                                <svg class="w-4 h-4 shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </template>
                            <span x-text="copiedCode ? 'Copiado!' : 'Copiar'"></span>
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Informe este código na transferência para facilitar a identificação.</p>
                </div>
            @endif

            {{-- Timer --}}
            <div class="rounded-2xl border border-gray-100 bg-gray-50/90 px-4 py-4 sm:px-5 sm:py-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2 min-w-0">
                        <svg class="w-5 h-5 shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-600 truncate">Tempo para pagamento</span>
                    </div>
                    <span class="text-lg sm:text-xl font-semibold font-mono tabular-nums shrink-0 tracking-tight"
                          :class="minutes < 5 ? 'text-red-600' : 'text-gray-900'"
                          x-text="timer"></span>
                </div>
                <div class="mt-3 h-2 w-full rounded-full bg-gray-200/90 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-1000 ease-linear"
                         :class="minutes < 5 ? 'bg-red-400' : 'bg-yellow-400'"
                         :style="'width: ' + timerPercent + '%'"></div>
                </div>
            </div>

            {{-- Instruções --}}
            <div class="rounded-xl bg-blue-50/80 border border-blue-100/80 px-4 py-4 sm:px-5 sm:py-5">
                <h3 class="text-sm font-semibold text-gray-900 tracking-tight mb-3 sm:mb-4">Como pagar</h3>
                <ol class="space-y-3 sm:space-y-3.5 text-sm text-gray-700">
                    <li class="flex gap-3">
                        <span class="shrink-0 w-7 h-7 rounded-xl bg-white border border-blue-100 text-blue-600 text-xs font-bold flex items-center justify-center shadow-sm">1</span>
                        <span class="pt-0.5 leading-relaxed">Abra o app do seu banco e escolha pagar com PIX.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="shrink-0 w-7 h-7 rounded-xl bg-white border border-blue-100 text-blue-600 text-xs font-bold flex items-center justify-center shadow-sm">2</span>
                        <span class="pt-0.5 leading-relaxed">Use a chave PIX acima para transferir <strong class="font-semibold text-gray-900">{{ $order->formatted_total }}</strong>.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="shrink-0 w-7 h-7 rounded-xl bg-white border border-blue-100 text-blue-600 text-xs font-bold flex items-center justify-center shadow-sm">3</span>
                        <span class="pt-0.5 leading-relaxed">Envie o comprovante pelo WhatsApp para confirmar o pagamento.</span>
                    </li>
                </ol>
            </div>

            @php
                $waMessage = "Olá! Realizei o pagamento PIX do pedido *{$order->order_number}*.\n\nValor: *{$order->formatted_total}*\nCódigo: {$order->pix_code}\n\nSegue o comprovante.";
                $waLink = "https://wa.me/{$adminWhatsapp}?text=" . urlencode($waMessage);
            @endphp

            <form method="POST" action="{{ route('b2b.orders.simulate-payment', $order) }}" class="space-y-3">
                @csrf
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl py-3.5 px-5 text-sm font-semibold text-white bg-emerald-500 hover:bg-emerald-600 shadow-lg shadow-emerald-500/25 transition-all duration-200 active:scale-[0.98]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Simular pagamento (teste)
                </button>
            </form>

            <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
               class="w-full inline-flex items-center justify-center gap-2.5 rounded-xl py-3.5 px-5 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 shadow-lg shadow-green-600/20 transition-all duration-200 active:scale-[0.98]">
                <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                </svg>
                Enviar comprovante pelo WhatsApp
            </a>
            <p class="text-center text-xs text-gray-400 leading-relaxed px-1">
                Após a confirmação, o status do pedido será atualizado automaticamente.
            </p>
        </div>

        {{-- Resumo --}}
        <div class="apple-card rounded-2xl p-5 sm:p-8">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 tracking-tight mb-4 sm:mb-5 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Resumo do pedido
            </h3>
            <div class="divide-y divide-gray-100 border-t border-b border-gray-100">
                @foreach($order->items as $item)
                    <div class="py-3.5 sm:py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 sm:gap-4">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 leading-snug">
                                {{ $item->product_snapshot['name'] ?? 'Produto' }}
                                @if(!empty($item->product_snapshot['storage'])) - {{ $item->product_snapshot['storage'] }} @endif
                                @if(!empty($item->product_snapshot['color'])) - {{ $item->product_snapshot['color'] }} @endif
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $item->quantity }}x R$ {{ number_format((float) $item->unit_price, 2, ',', '.') }}</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-900 tabular-nums sm:text-right shrink-0">R$ {{ number_format((float) $item->subtotal, 2, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 sm:mt-5 pt-4 flex items-center justify-between gap-4 border-t border-gray-200">
                <span class="text-sm font-semibold text-gray-600 tracking-tight">Total</span>
                <span class="text-lg sm:text-xl font-semibold text-gray-900 tracking-tight tabular-nums">{{ $order->formatted_total }}</span>
            </div>
        </div>
    </div>

    <script>
        function pixPayment() {
            return {
                copiedKey: false,
                copiedCode: false,
                minutes: 30,
                seconds: 0,
                totalSeconds: 30 * 60,
                elapsed: 0,
                get timer() {
                    const m = Math.floor((this.totalSeconds - this.elapsed) / 60);
                    const s = (this.totalSeconds - this.elapsed) % 60;
                    this.minutes = m;
                    return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                },
                get timerPercent() {
                    return Math.max(0, ((this.totalSeconds - this.elapsed) / this.totalSeconds) * 100);
                },
                init() {
                    setInterval(() => {
                        if (this.elapsed < this.totalSeconds) this.elapsed++;
                    }, 1000);
                },
                copyPix(text, type) {
                    navigator.clipboard.writeText(text).then(() => {
                        if (type === 'key') {
                            this.copiedKey = true;
                            setTimeout(() => { this.copiedKey = false; }, 3000);
                        } else {
                            this.copiedCode = true;
                            setTimeout(() => { this.copiedCode = false; }, 3000);
                        }
                    });
                }
            }
        }
    </script>
</x-b2b-app-layout>
