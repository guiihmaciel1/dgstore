<x-app-layout>
    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabeçalho -->
            <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                <a href="{{ route('stock.index') }}" style="margin-right: 1rem; padding: 0.5rem; color: #6b7280; border-radius: 0.5rem;"
                   onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                    <svg style="height: 1.5rem; width: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Nova Movimentação</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Registrar entrada, saída ou ajuste de estoque</p>
                </div>
            </div>

            <!-- Formulário -->
            <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                <form method="POST" action="{{ route('stock.store') }}">
                    @csrf
                    
                    <div style="padding: 1.5rem;">
                        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                            <!-- Produto -->
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                    Produto <span style="color: #dc2626;">*</span>
                                </label>
                                <select name="product_id" required
                                        style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: white;"
                                        onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                    <option value="">Selecione um produto...</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} (Estoque: {{ $product->stock_quantity }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id')<p style="margin-top: 0.25rem; font-size: 0.75rem; color: #dc2626;">{{ $message }}</p>@enderror
                            </div>

                            <!-- Tipo de Movimentação -->
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                    Tipo de Movimentação <span style="color: #dc2626;">*</span>
                                </label>
                                <select name="type" required
                                        style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: white;"
                                        onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                    @foreach($types as $type)
                                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                    @endforeach
                                </select>
                                @error('type')<p style="margin-top: 0.25rem; font-size: 0.75rem; color: #dc2626;">{{ $message }}</p>@enderror
                            </div>

                            <!-- Quantidade -->
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                    Quantidade <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="number" name="quantity" value="{{ old('quantity') }}" min="1" required
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                <p style="margin-top: 0.25rem; font-size: 0.75rem; color: #6b7280;">
                                    Para entrada, informe a quantidade a adicionar. Para ajuste, informe o novo valor total do estoque.
                                </p>
                                @error('quantity')<p style="margin-top: 0.25rem; font-size: 0.75rem; color: #dc2626;">{{ $message }}</p>@enderror
                            </div>

                            <!-- Motivo -->
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Motivo</label>
                                <textarea name="reason" rows="3" placeholder="Descreva o motivo da movimentação..."
                                          style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; resize: vertical;"
                                          onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">{{ old('reason') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Rodapé -->
                    <div style="padding: 1rem 1.5rem; background: #f9fafb; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 0.75rem;">
                        <a href="{{ route('stock.index') }}" 
                           style="padding: 0.625rem 1.5rem; background: white; color: #374151; font-weight: 500; border-radius: 0.5rem; text-decoration: none; border: 1px solid #e5e7eb;">
                            Cancelar
                        </a>
                        <button type="submit" 
                                style="padding: 0.625rem 1.5rem; background: #111827; color: white; font-weight: 500; border-radius: 0.5rem; border: none; cursor: pointer;"
                                onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                            Registrar Movimentação
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
