<x-app-layout>
    <x-slot name="title">Editar Fornecedor</x-slot>
    <div class="py-4">
        <div class="px-6 lg:px-8">
            <!-- Cabeçalho compacto -->
            <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                <a href="{{ route('suppliers.show', $supplier) }}" style="margin-right: 0.75rem; padding: 0.375rem; color: #818181; border-radius: 0.375rem;"
                   onmouseover="this.style.backgroundColor='#222222'" onmouseout="this.style.backgroundColor='transparent'">
                    <svg style="height: 1.25rem; width: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 style="font-size: 1.25rem; font-weight: 700; color: #e3e3e3;">Editar Fornecedor</h1>
                    <p style="font-size: 0.75rem; color: #818181;">{{ $supplier->name }}</p>
                </div>
            </div>

            <!-- Formulário -->
            <div style="background: #141414; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06);">
                <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
                    @csrf
                    @method('PUT')
                    
                    <div style="padding: 1rem 1.25rem;">
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.875rem;">
                            <!-- Nome -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">
                                    Nome do Fornecedor <span style="color: #fca5a5;">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name', $supplier->name) }}" required
                                       style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;"
                                       onfocus="this.style.borderColor='#666666';this.style.boxShadow='0 0 0 1px rgba(255,255,255,0.1)'" onblur="this.style.borderColor='rgba(255,255,255,0.06)';this.style.boxShadow='none'">
                            </div>

                            <!-- Origem -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">Origem</label>
                                <select name="origin"
                                        style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem; background: #141414;">
                                    <option value="">Selecione...</option>
                                    <option value="py" {{ old('origin', $supplier->origin?->value) === 'py' ? 'selected' : '' }}>Paraguai (PY) - com frete 4%</option>
                                    <option value="br" {{ old('origin', $supplier->origin?->value) === 'br' ? 'selected' : '' }}>Brasil (BR) - sem frete</option>
                                </select>
                                @error('origin')<p style="margin-top: 0.125rem; font-size: 0.75rem; color: #fca5a5;">{{ $message }}</p>@enderror
                            </div>

                            <!-- CNPJ -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">CNPJ</label>
                                <input type="text" name="cnpj" value="{{ old('cnpj', $supplier->formatted_cnpj) }}" placeholder="00.000.000/0000-00"
                                       style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- Telefone -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">Telefone</label>
                                <input type="text" name="phone" value="{{ old('phone', $supplier->formatted_phone) }}" placeholder="(00) 00000-0000"
                                       style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- E-mail -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">E-mail</label>
                                <input type="email" name="email" value="{{ old('email', $supplier->email) }}"
                                       style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- Pessoa de Contato -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">Pessoa de Contato</label>
                                <input type="text" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}"
                                       style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- Endereço -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">Endereço</label>
                                <input type="text" name="address" value="{{ old('address', $supplier->address) }}"
                                       style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- Observações -->
                            <div style="grid-column: span 2;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">Observações</label>
                                <input type="text" name="notes" value="{{ old('notes', $supplier->notes) }}" placeholder="Informações adicionais"
                                       style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>
                        </div>
                    </div>

                    <!-- Rodapé -->
                    <div style="padding: 0.75rem 1.25rem; background: #1a1a1a; border-top: 1px solid rgba(255,255,255,0.06); display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <button type="button" onclick="if(confirm('Tem certeza que deseja excluir este fornecedor?')) document.getElementById('delete-form').submit();"
                                    style="padding: 0.5rem 0.75rem; background: rgba(239,68,68,0.15); color: #fca5a5; font-weight: 500; font-size: 0.875rem; border-radius: 0.375rem; border: none; cursor: pointer;"
                                    onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'">
                                Excluir
                            </button>
                            <label style="display: flex; align-items: center; cursor: pointer;">
                                <input type="hidden" name="active" value="0">
                                <input type="checkbox" name="active" value="1" {{ old('active', $supplier->active) ? 'checked' : '' }}
                                       style="width: 1rem; height: 1rem; border-radius: 0.25rem; margin-right: 0.375rem;">
                                <span style="font-size: 0.875rem; color: #a4a4a4;">Ativo</span>
                            </label>
                        </div>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="{{ route('suppliers.show', $supplier) }}" 
                               style="padding: 0.5rem 1rem; background: #141414; color: #a4a4a4; font-weight: 500; font-size: 0.875rem; border-radius: 0.375rem; text-decoration: none; border: 1px solid rgba(255,255,255,0.08);">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    style="padding: 0.5rem 1.25rem; background: #111827; color: white; font-weight: 500; font-size: 0.875rem; border-radius: 0.375rem; border: none; cursor: pointer;"
                                    onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                                Salvar Alterações
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Form de exclusão oculto -->
            <form id="delete-form" method="POST" action="{{ route('suppliers.destroy', $supplier) }}" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

    <style>
        @media (max-width: 640px) {
            div[style*="grid-template-columns: repeat(2"] { grid-template-columns: 1fr !important; }
            div[style*="grid-column: span 2"] { grid-column: span 1 !important; }
        }
    </style>
</x-app-layout>
