<x-app-layout>
    <x-slot name="title">Entrada Rápida</x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8" x-data="quickEntry()">

            {{-- Header --}}
            <div class="flex items-center mb-6">
                <a href="{{ route('stock.consignment.index') }}" class="mr-3 p-2 text-gray-500 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Entrada Rápida</h1>
                    <p class="text-sm text-gray-500">Adicione produtos lacrados apenas com modelo e quantidade</p>
                </div>
            </div>

            @if($errors->any())
                <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1rem;">
                    <div style="font-weight: 600; color: #991b1b; margin-bottom: 0.5rem;">Verifique os erros abaixo:</div>
                    <ul style="list-style: disc; padding-left: 1.25rem; color: #991b1b; font-size: 0.875rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('stock.consignment.quick-store') }}">
                @csrf

                {{-- Formulário Simplificado --}}
                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 1rem; padding: 1.5rem; margin-bottom: 1.5rem;">
                    
                    {{-- Fornecedor --}}
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem;">
                            Fornecedor <span style="color: #dc2626;">*</span>
                        </label>
                        <select name="supplier_id" x-model="form.supplier_id" required
                                style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e5e7eb'">
                            <option value="">Selecione o fornecedor</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Produto --}}
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem;">
                            Nome do Produto <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="text" name="name" x-model="form.name" required
                               placeholder="Ex: iPhone 17 Pro Max"
                               list="recent-products"
                               style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                               onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e5e7eb'">
                        
                        <datalist id="recent-products">
                            @foreach($recentProducts as $product)
                                <option value="{{ $product['name'] }}">
                            @endforeach
                        </datalist>
                    </div>

                    {{-- Modelo --}}
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem;">
                            Modelo <span style="color: #6b7280; font-weight: 400;">(opcional)</span>
                        </label>
                        <input type="text" name="model" x-model="form.model"
                               placeholder="Ex: A3234"
                               style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                               onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e5e7eb'">
                    </div>

                    {{-- Storage + Cor (lado a lado) --}}
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem;">
                                Armazenamento <span style="color: #6b7280; font-weight: 400;">(opcional)</span>
                            </label>
                            <input type="text" name="storage" x-model="form.storage"
                                   placeholder="Ex: 256GB"
                                   style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                   onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e5e7eb'">
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem;">
                                Cor <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="text" name="color" x-model="form.color" required
                                   placeholder="Ex: Preto"
                                   style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                   onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e5e7eb'">
                        </div>
                    </div>

                    {{-- Condição --}}
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem;">
                            Condição <span style="color: #dc2626;">*</span>
                        </label>
                        <div style="display: flex; gap: 1rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="radio" name="condition" value="new" x-model="form.condition" required
                                       style="width: 1rem; height: 1rem; accent-color: #10b981;">
                                <span style="font-size: 0.875rem; color: #374151;">Novo (Lacrado)</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="radio" name="condition" value="used" x-model="form.condition" required
                                       style="width: 1rem; height: 1rem; accent-color: #10b981;">
                                <span style="font-size: 0.875rem; color: #374151;">Usado</span>
                            </label>
                        </div>
                    </div>

                    {{-- Quantidade + Custo (lado a lado) --}}
                    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem; margin-bottom: 1.25rem;">
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem;">
                                Quantidade <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="number" name="quantity" x-model="form.quantity" required
                                   min="1" max="999" value="1"
                                   style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                   onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e5e7eb'">
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem;">
                                Custo do Fornecedor (R$) <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="number" name="supplier_cost" x-model="form.supplier_cost" required
                                   min="0" step="0.01" placeholder="0,00"
                                   style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                                   onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e5e7eb'">
                        </div>
                    </div>

                    {{-- Preço Sugerido (opcional) --}}
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem;">
                            Preço de Venda Sugerido (R$) <span style="color: #6b7280; font-weight: 400;">(opcional)</span>
                        </label>
                        <input type="number" name="suggested_price" x-model="form.suggested_price"
                               min="0" step="0.01" placeholder="0,00"
                               style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                               onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e5e7eb'">
                    </div>

                    {{-- IMEI (opcional) --}}
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem;">
                            IMEI <span style="color: #6b7280; font-weight: 400;">(opcional - deixe em branco para consolidar)</span>
                        </label>
                        <input type="text" name="imei" x-model="form.imei"
                               placeholder="Deixe em branco para produtos lacrados sem rastreamento"
                               style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                               onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e5e7eb'">
                    </div>

                    {{-- Observações (opcional) --}}
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem;">
                            Observações <span style="color: #6b7280; font-weight: 400;">(opcional)</span>
                        </label>
                        <textarea name="notes" x-model="form.notes" rows="2"
                                  placeholder="Notas adicionais sobre o produto"
                                  style="width: 100%; padding: 0.625rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none; resize: vertical;"
                                  onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e5e7eb'"></textarea>
                    </div>
                </div>

                {{-- Preview do que será salvo --}}
                <div x-show="form.name && form.color && form.quantity" 
                     style="background: linear-gradient(to right, #d1fae5, #a7f3d0); border: 1px solid #10b981; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1.5rem;">
                    <div style="font-size: 0.75rem; font-weight: 600; color: #065f46; margin-bottom: 0.25rem;">PREVIEW DA ENTRADA:</div>
                    <div style="font-weight: 700; color: #047857; font-size: 1rem;">
                        <span x-text="form.quantity"></span>x <span x-text="form.name"></span>
                        <span x-show="form.storage" x-text="' ' + form.storage"></span>
                        <span x-text="' ' + form.color"></span>
                    </div>
                    <div style="font-size: 0.75rem; color: #047857; margin-top: 0.25rem;">
                        Será <strong x-show="!form.imei">consolidado</strong><strong x-show="form.imei">rastreado</strong> com <span x-show="!form.imei">outras unidades iguais</span><span x-show="form.imei">IMEI individual</span>
                    </div>
                </div>

                {{-- Botões --}}
                <div style="display: flex; gap: 0.75rem;">
                    <a href="{{ route('stock.consignment.index') }}"
                       style="flex: 1; display: inline-flex; align-items: center; justify-content: center; padding: 0.875rem 1.5rem; background: white; border: 2px solid #e5e7eb; color: #374151; font-weight: 600; border-radius: 0.75rem; text-decoration: none;">
                        Cancelar
                    </a>
                    <button type="submit"
                            style="flex: 2; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.875rem 1.5rem; background: linear-gradient(to right, #10b981, #059669); color: white; font-weight: 600; border-radius: 0.75rem; border: none; cursor: pointer;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Registrar Entrada Rápida
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function quickEntry() {
            return {
                form: {
                    supplier_id: '',
                    name: '',
                    model: '',
                    storage: '',
                    color: '',
                    condition: 'new',
                    quantity: 1,
                    supplier_cost: '',
                    suggested_price: '',
                    imei: '',
                    notes: ''
                }
            }
        }
    </script>
</x-app-layout>
