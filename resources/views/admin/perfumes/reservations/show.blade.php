<x-perfumes-admin-layout>
    <div class="p-4 max-w-6xl mx-auto" x-data="{ 
        showPaymentForm: false,
        showConvertForm: false,
        paymentAmount: 0,
        paymentMethod: 'pix'
    }">
        <div class="mb-3">
            <a href="{{ route('admin.perfumes.reservations.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Voltar para encomendas
            </a>
        </div>

        @if(session('success'))
            <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-3 p-3 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-3 gap-4">
            <!-- Informações Principais -->
            <div class="col-span-2 space-y-3">
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">{{ $reservation->reservation_number }}</h1>
                            <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-medium rounded-full bg-{{ $reservation->status->badgeColor() }}-100 text-{{ $reservation->status->badgeColor() }}-800">
                                {{ $reservation->status->label() }}
                            </span>
                        </div>
                        @if($reservation->status->value === 'active')
                            <button @click="showPaymentForm = !showPaymentForm"
                                    class="px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition">
                                + Pagamento
                            </button>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-600">Cliente:</span>
                            <div class="font-medium text-gray-900 mt-1">
                                <a href="{{ route('admin.perfumes.customers.show', $reservation->customer) }}"
                                   class="text-pink-600 hover:text-pink-900">
                                    {{ $reservation->customer->name }}
                                </a>
                                <div class="text-xs text-gray-500">{{ $reservation->customer->formatted_phone }}</div>
                            </div>
                        </div>
                        <div>
                            <span class="text-gray-600">Data:</span>
                            <div class="font-medium text-gray-900 mt-1">{{ $reservation->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div>
                            <span class="text-gray-600">Produto:</span>
                            <div class="font-medium text-gray-900 mt-1">
                                @if($reservation->product)
                                    {{ $reservation->product->name }} @if($reservation->product->brand) - {{ $reservation->product->brand }}@endif
                                @else
                                    {{ $reservation->product_description }}
                                @endif
                            </div>
                        </div>
                        <div>
                            <span class="text-gray-600">Preço:</span>
                            <div class="font-medium text-gray-900 mt-1">R$ {{ number_format($reservation->product_price, 2, ',', '.') }}</div>
                        </div>
                        @if($reservation->expires_at)
                            <div>
                                <span class="text-gray-600">Vencimento:</span>
                                <div class="font-medium text-gray-900 mt-1">{{ $reservation->expires_at->format('d/m/Y') }}</div>
                            </div>
                        @endif
                        <div>
                            <span class="text-gray-600">Vendedor:</span>
                            <div class="font-medium text-gray-900 mt-1">{{ $reservation->user->name }}</div>
                        </div>
                    </div>

                    @if($reservation->notes)
                        <div class="mt-3 pt-3 border-t text-sm">
                            <span class="text-gray-600">Observações:</span>
                            <div class="text-gray-900 mt-1">{{ $reservation->notes }}</div>
                        </div>
                    @endif
                </div>

                <!-- Formulário de Pagamento -->
                <div x-show="showPaymentForm" class="bg-blue-50 rounded-lg border border-blue-200 p-4">
                    <form method="POST" action="{{ route('admin.perfumes.reservations.payments.store', $reservation) }}" class="space-y-3">
                        @csrf
                        <h3 class="text-sm font-bold text-gray-900">Adicionar Pagamento</h3>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Valor *</label>
                                <input type="number" name="amount" step="0.01" min="0.01" 
                                       :max="{{ $reservation->deposit_amount - $reservation->deposit_paid }}" required
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                                <p class="mt-1 text-[10px] text-gray-600">Restante: R$ {{ number_format($reservation->deposit_amount - $reservation->deposit_paid, 2, ',', '.') }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Forma *</label>
                                <select name="payment_method" required
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                                    <option value="pix">PIX</option>
                                    <option value="cash">Dinheiro</option>
                                    <option value="card">Cartão</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Observações</label>
                            <textarea name="notes" rows="2"
                                      class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500"></textarea>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit"
                                    class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                Confirmar
                            </button>
                            <button type="button" @click="showPaymentForm = false"
                                    class="px-4 py-2 text-sm bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Histórico de Pagamentos -->
                @if($reservation->payments->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm p-4">
                        <h3 class="text-base font-bold text-gray-900 mb-3">Histórico de Pagamentos</h3>
                        <div class="space-y-2">
                            @foreach($reservation->payments as $payment)
                                <div class="flex justify-between items-center p-2 bg-gray-50 rounded-lg">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900">R$ {{ number_format($payment->amount, 2, ',', '.') }}</div>
                                        <div class="text-xs text-gray-600">
                                            {{ $payment->payment_method->label() }} - 
                                            {{ $payment->paid_at->format('d/m/Y H:i') }}
                                        </div>
                                        @if($payment->notes)
                                            <div class="text-xs text-gray-500 mt-1">{{ $payment->notes }}</div>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $payment->user->name }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-3">
                <!-- Progresso do Sinal -->
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <h3 class="text-sm font-bold text-gray-900 mb-3">Progresso do Sinal</h3>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-600">Pago</span>
                            <span class="font-bold text-green-600">R$ {{ number_format($reservation->deposit_paid, 2, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full transition-all" style="width: {{ $reservation->progress_percentage }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-600">Total</span>
                            <span class="font-bold text-gray-900">R$ {{ number_format($reservation->deposit_amount, 2, ',', '.') }}</span>
                        </div>
                        <div class="text-center">
                            <span class="text-2xl font-bold text-gray-900">{{ $reservation->progress_percentage }}%</span>
                        </div>
                    </div>

                    @if($reservation->progress_percentage >= 100 && $reservation->status->value === 'active')
                        <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-xs text-green-800 mb-2">✓ Sinal completo! Você pode converter em venda.</p>
                            <button @click="showConvertForm = !showConvertForm"
                                    class="w-full px-3 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                                Converter em Venda
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Valor a Receber -->
                <div class="bg-yellow-50 rounded-lg border border-yellow-200 p-4">
                    <div class="text-xs text-yellow-600 font-medium">Falta Receber</div>
                    <div class="text-xl font-bold text-yellow-900 mt-1">
                        R$ {{ number_format($reservation->product_price - $reservation->deposit_paid, 2, ',', '.') }}
                    </div>
                    <div class="text-xs text-yellow-700 mt-1">Após completar o sinal</div>
                </div>

                <!-- Ações -->
                @if($reservation->status->value === 'active')
                    <div class="bg-white rounded-lg shadow-sm p-4">
                        <h3 class="text-sm font-bold text-gray-900 mb-2">Ações</h3>
                        <form method="POST" action="{{ route('admin.perfumes.reservations.cancel', $reservation) }}"
                              onsubmit="return confirm('Tem certeza que deseja cancelar esta encomenda?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full px-3 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                Cancelar Encomenda
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <!-- Modal de Conversão em Venda -->
        <div x-show="showConvertForm" 
             x-cloak
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
             @click.self="showConvertForm = false">
            <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Converter em Venda</h3>
                
                <form method="POST" action="{{ route('admin.perfumes.reservations.convert', $reservation) }}">
                    @csrf
                    
                    <div class="space-y-3 mb-4">
                        <div class="bg-gray-50 rounded-lg p-3 text-sm">
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600">Valor do Produto:</span>
                                <span class="font-semibold">R$ {{ number_format($reservation->product_price, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600">Sinal Pago:</span>
                                <span class="font-semibold text-green-600">- R$ {{ number_format($reservation->deposit_paid, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between pt-2 border-t">
                                <span class="text-gray-900 font-bold">Total a Receber:</span>
                                <span class="font-bold text-lg">R$ {{ number_format($reservation->product_price - $reservation->deposit_paid, 2, ',', '.') }}</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Forma de Pagamento *</label>
                            <select name="payment_method" required
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                                <option value="cash">Dinheiro</option>
                                <option value="card">Cartão</option>
                                <option value="pix">PIX</option>
                                <option value="mixed">Misto</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                            <textarea name="notes" rows="2"
                                      class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500"></textarea>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                                class="flex-1 px-4 py-2 text-sm bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition font-medium">
                            Confirmar Venda
                        </button>
                        <button type="button" @click="showConvertForm = false"
                                class="px-4 py-2 text-sm bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-perfumes-admin-layout>
