<x-app-layout>
    <div class="py-4">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabeçalho compacto -->
            <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                <a href="{{ route('suppliers.index') }}" style="margin-right: 0.75rem; padding: 0.375rem; color: #6b7280; border-radius: 0.375rem;"
                   onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                    <svg style="height: 1.25rem; width: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h1 style="font-size: 1.25rem; font-weight: 700; color: #111827;">Novo Fornecedor</h1>
            </div>

            <!-- Formulário -->
            <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb;">
                <form method="POST" action="{{ route('suppliers.store') }}">
                    @csrf
                    
                    <div style="padding: 1rem 1.25rem;">
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.875rem;">
                            <!-- Nome -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">
                                    Nome do Fornecedor <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;"
                                       onfocus="this.style.borderColor='#111827';this.style.boxShadow='0 0 0 1px #111827'" onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                @error('name')<p style="margin-top: 0.125rem; font-size: 0.75rem; color: #dc2626;">{{ $message }}</p>@enderror
                            </div>

                            <!-- Origem -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Origem</label>
                                <select name="origin"
                                        style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem; background: white;">
                                    <option value="">Selecione...</option>
                                    <option value="py" {{ old('origin') === 'py' ? 'selected' : '' }}>Paraguai (PY) - com frete 4%</option>
                                    <option value="br" {{ old('origin') === 'br' ? 'selected' : '' }}>Brasil (BR) - sem frete</option>
                                </select>
                                @error('origin')<p style="margin-top: 0.125rem; font-size: 0.75rem; color: #dc2626;">{{ $message }}</p>@enderror
                            </div>

                            <!-- CNPJ -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">CNPJ</label>
                                <input type="text" name="cnpj" value="{{ old('cnpj') }}" placeholder="00.000.000/0000-00"
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- Telefone -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Telefone</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="(00) 00000-0000"
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- E-mail -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">E-mail</label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- Pessoa de Contato -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Pessoa de Contato</label>
                                <input type="text" name="contact_person" value="{{ old('contact_person') }}"
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- Endereço -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Endereço</label>
                                <input type="text" name="address" value="{{ old('address') }}"
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- Observações -->
                            <div style="grid-column: span 2;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Observações</label>
                                <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Informações adicionais"
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>
                        </div>
                    </div>

                    <!-- Rodapé -->
                    <div style="padding: 0.75rem 1.25rem; background: #f9fafb; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="hidden" name="active" value="0">
                            <input type="checkbox" name="active" value="1" checked
                                   style="width: 1rem; height: 1rem; border-radius: 0.25rem; margin-right: 0.375rem;">
                            <span style="font-size: 0.875rem; color: #374151;">Fornecedor ativo</span>
                        </label>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="{{ route('suppliers.index') }}" 
                               style="padding: 0.5rem 1rem; background: white; color: #374151; font-weight: 500; font-size: 0.875rem; border-radius: 0.375rem; text-decoration: none; border: 1px solid #d1d5db;">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    style="padding: 0.5rem 1.25rem; background: #111827; color: white; font-weight: 500; font-size: 0.875rem; border-radius: 0.375rem; border: none; cursor: pointer;"
                                    onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                                Cadastrar Fornecedor
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 640px) {
            div[style*="grid-template-columns: repeat(2"] { grid-template-columns: 1fr !important; }
            div[style*="grid-column: span 2"] { grid-column: span 1 !important; }
        }
    </style>
</x-app-layout>
