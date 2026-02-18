<x-b2b-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Pagamento PIX</h2>
                <p class="text-sm text-gray-500 mt-0.5">Pedido {{ $order->order_number }} - Aguardando pagamento</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto" x-data="pixPayment()">
        <!-- Card principal do PIX -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Header com valor -->
            <div class="bg-gradient-to-r from-gray-900 to-gray-800 px-6 py-6 text-center">
                <p class="text-sm text-gray-400 mb-1">Valor a pagar</p>
                <p class="text-4xl font-extrabold text-white">{{ $order->formatted_total }}</p>
                <p class="text-xs text-gray-400 mt-2">Pedido {{ $order->order_number }}</p>
            </div>

            <div class="p-6 sm:p-8">
                <!-- QR Code simulado -->
                <div class="flex flex-col items-center mb-8">
                    <div class="relative">
                        <div class="w-52 h-52 bg-white border-2 border-gray-200 rounded-xl p-3 shadow-inner">
                            <!-- QR Code SVG simulado -->
                            <svg viewBox="0 0 200 200" class="w-full h-full">
                                <!-- Pattern de QR code simulado -->
                                <rect fill="#000" x="10" y="10" width="50" height="50" rx="4"/>
                                <rect fill="#fff" x="18" y="18" width="34" height="34" rx="2"/>
                                <rect fill="#000" x="24" y="24" width="22" height="22" rx="2"/>

                                <rect fill="#000" x="140" y="10" width="50" height="50" rx="4"/>
                                <rect fill="#fff" x="148" y="18" width="34" height="34" rx="2"/>
                                <rect fill="#000" x="154" y="24" width="22" height="22" rx="2"/>

                                <rect fill="#000" x="10" y="140" width="50" height="50" rx="4"/>
                                <rect fill="#fff" x="18" y="148" width="34" height="34" rx="2"/>
                                <rect fill="#000" x="24" y="154" width="22" height="22" rx="2"/>

                                <!-- Data cells -->
                                @for($i = 0; $i < 8; $i++)
                                    @for($j = 0; $j < 8; $j++)
                                        @if(($i + $j) % 3 !== 0 && !($i < 3 && $j < 3) && !($i < 3 && $j > 4) && !($i > 4 && $j < 3))
                                            <rect fill="#000" x="{{ 70 + $j * 10 }}" y="{{ 70 + $i * 10 }}" width="8" height="8" rx="1"/>
                                        @endif
                                    @endfor
                                @endfor

                                <!-- Timing patterns -->
                                @for($i = 0; $i < 6; $i++)
                                    @if($i % 2 === 0)
                                        <rect fill="#000" x="{{ 66 + $i * 10 }}" y="10" width="8" height="8"/>
                                        <rect fill="#000" x="10" y="{{ 66 + $i * 10 }}" width="8" height="8"/>
                                    @endif
                                @endfor

                                <!-- Extra random-ish blocks -->
                                <rect fill="#000" x="70" y="10" width="8" height="8"/>
                                <rect fill="#000" x="90" y="10" width="8" height="8"/>
                                <rect fill="#000" x="110" y="10" width="8" height="8"/>
                                <rect fill="#000" x="70" y="30" width="8" height="8"/>
                                <rect fill="#000" x="100" y="30" width="8" height="8"/>
                                <rect fill="#000" x="120" y="30" width="8" height="8"/>
                                <rect fill="#000" x="80" y="50" width="8" height="8"/>
                                <rect fill="#000" x="110" y="50" width="8" height="8"/>
                                <rect fill="#000" x="10" y="70" width="8" height="8"/>
                                <rect fill="#000" x="30" y="70" width="8" height="8"/>
                                <rect fill="#000" x="10" y="90" width="8" height="8"/>
                                <rect fill="#000" x="40" y="100" width="8" height="8"/>
                                <rect fill="#000" x="10" y="120" width="8" height="8"/>
                                <rect fill="#000" x="30" y="110" width="8" height="8"/>
                                <rect fill="#000" x="150" y="70" width="8" height="8"/>
                                <rect fill="#000" x="170" y="90" width="8" height="8"/>
                                <rect fill="#000" x="150" y="110" width="8" height="8"/>
                                <rect fill="#000" x="180" y="100" width="8" height="8"/>
                                <rect fill="#000" x="70" y="150" width="8" height="8"/>
                                <rect fill="#000" x="90" y="170" width="8" height="8"/>
                                <rect fill="#000" x="110" y="150" width="8" height="8"/>
                                <rect fill="#000" x="130" y="170" width="8" height="8"/>
                                <rect fill="#000" x="150" y="150" width="8" height="8"/>
                                <rect fill="#000" x="170" y="170" width="8" height="8"/>
                                <rect fill="#000" x="180" y="150" width="8" height="8"/>
                                <rect fill="#000" x="160" y="160" width="8" height="8"/>

                                <!-- PIX text in center -->
                                <rect fill="#fff" x="75" y="88" width="50" height="24" rx="4"/>
                                <text x="100" y="105" text-anchor="middle" fill="#000" font-weight="bold" font-size="14" font-family="sans-serif">PIX</text>
                            </svg>
                        </div>
                        <!-- Badge de status -->
                        <div class="absolute -bottom-3 left-1/2 -translate-x-1/2">
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200 shadow-sm">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
                                </span>
                                Aguardando pagamento
                            </span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-6">Escaneie o QR Code com o app do seu banco</p>
                </div>

                <!-- Código PIX Copia e Cola -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ou copie o código PIX</label>
                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-sm font-mono text-gray-700 truncate select-all">
                            {{ $order->pix_code }}
                        </div>
                        <button type="button" @click="copyPix('{{ $order->pix_code }}')"
                                class="shrink-0 px-4 py-3 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-xl transition active:scale-95 flex items-center gap-2">
                            <template x-if="!copied">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                            </template>
                            <template x-if="copied">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </template>
                            <span x-text="copied ? 'Copiado!' : 'Copiar'"></span>
                        </button>
                    </div>
                </div>

                <!-- Timer -->
                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-sm text-gray-600">Tempo para pagamento</span>
                        </div>
                        <span class="text-lg font-bold font-mono" :class="minutes < 5 ? 'text-red-600' : 'text-gray-900'" x-text="timer"></span>
                    </div>
                    <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
                        <div class="bg-yellow-500 h-1.5 rounded-full transition-all duration-1000" :style="'width: ' + timerPercent + '%'"></div>
                    </div>
                </div>

                <!-- Instruções -->
                <div class="bg-blue-50 rounded-xl p-4 mb-6">
                    <h4 class="text-sm font-semibold text-blue-800 mb-2">Como pagar com PIX</h4>
                    <ol class="space-y-1.5 text-sm text-blue-700">
                        <li class="flex items-start gap-2">
                            <span class="shrink-0 w-5 h-5 rounded-full bg-blue-200 text-blue-800 text-xs font-bold flex items-center justify-center mt-0.5">1</span>
                            Abra o app do seu banco
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="shrink-0 w-5 h-5 rounded-full bg-blue-200 text-blue-800 text-xs font-bold flex items-center justify-center mt-0.5">2</span>
                            Escolha pagar com PIX (QR Code ou copia e cola)
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="shrink-0 w-5 h-5 rounded-full bg-blue-200 text-blue-800 text-xs font-bold flex items-center justify-center mt-0.5">3</span>
                            Confirme o pagamento de <strong>{{ $order->formatted_total }}</strong>
                        </li>
                    </ol>
                </div>

                <!-- Separador visual -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                    <div class="relative flex justify-center">
                        <span class="bg-white px-4 text-xs text-gray-400 uppercase tracking-wider">Simulação</span>
                    </div>
                </div>

                <!-- Botão de simular pagamento -->
                <form method="POST" action="{{ route('b2b.orders.simulate-payment', $order) }}">
                    @csrf
                    <button type="submit"
                            class="w-full py-4 px-6 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all active:scale-[0.98] text-base flex items-center justify-center gap-3 shadow-lg shadow-emerald-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Simular Pagamento PIX Recebido
                    </button>
                    <p class="text-center text-xs text-gray-400 mt-2">
                        Este botão simula a confirmação do pagamento pelo banco
                    </p>
                </form>
            </div>
        </div>

        <!-- Resumo do pedido -->
        <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Resumo do Pedido
            </h3>
            <div class="divide-y divide-gray-100">
                @foreach($order->items as $item)
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $item->product_snapshot['name'] ?? 'Produto' }}
                                @if(!empty($item->product_snapshot['storage'])) - {{ $item->product_snapshot['storage'] }} @endif
                                @if(!empty($item->product_snapshot['color'])) - {{ $item->product_snapshot['color'] }} @endif
                            </p>
                            <p class="text-xs text-gray-500">{{ $item->quantity }}x R$ {{ number_format((float) $item->unit_price, 2, ',', '.') }}</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">R$ {{ number_format((float) $item->subtotal, 2, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-3 pt-3 border-t border-gray-200 flex justify-between">
                <span class="font-bold text-gray-900">Total</span>
                <span class="font-extrabold text-xl text-gray-900">{{ $order->formatted_total }}</span>
            </div>
        </div>
    </div>

    <script>
        function pixPayment() {
            return {
                copied: false,
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
                copyPix(code) {
                    navigator.clipboard.writeText(code).then(() => {
                        this.copied = true;
                        setTimeout(() => { this.copied = false; }, 3000);
                    });
                }
            }
        }
    </script>
</x-b2b-app-layout>
