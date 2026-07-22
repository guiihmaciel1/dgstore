<x-app-layout>
    <x-slot name="title">Novo Cliente</x-slot>
    <div class="py-4">
        <div class="px-6 lg:px-8">
            <!-- Cabeçalho compacto -->
            <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                <a href="{{ route('customers.index') }}" style="margin-right: 0.75rem; padding: 0.375rem; color: #818181; border-radius: 0.375rem;"
                   onmouseover="this.style.backgroundColor='#222222'" onmouseout="this.style.backgroundColor='transparent'">
                    <svg style="height: 1.25rem; width: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h1 style="font-size: 1.25rem; font-weight: 700; color: #e3e3e3;">Novo Cliente</h1>
            </div>

            <!-- Formulário -->
            <div style="background: #141414; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06);">
                <form method="POST" action="{{ route('customers.store') }}">
                    @csrf
                    
                    <div style="padding: 1rem 1.25rem;">
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.875rem;">
                            <!-- Nome -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">
                                    Nome Completo <span style="color: #fca5a5;">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                       style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;"
                                       onfocus="this.style.borderColor='#666666';this.style.boxShadow='0 0 0 1px rgba(255,255,255,0.1)'" onblur="this.style.borderColor='rgba(255,255,255,0.06)';this.style.boxShadow='none'">
                                @error('name')<p style="margin-top: 0.125rem; font-size: 0.75rem; color: #fca5a5;">{{ $message }}</p>@enderror
                            </div>

                            <!-- CPF -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">CPF</label>
                                <input type="text" name="cpf" value="{{ old('cpf') }}" placeholder="000.000.000-00"
                                       style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- Telefone -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">
                                    Telefone <span style="color: #fca5a5;">*</span>
                                </label>
                                <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="(00) 00000-0000"
                                       style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;">
                                @error('phone')<p style="margin-top: 0.125rem; font-size: 0.75rem; color: #fca5a5;">{{ $message }}</p>@enderror
                            </div>

                            <!-- Instagram -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">Instagram</label>
                                <input type="text" name="instagram" value="{{ old('instagram') }}" placeholder="@usuario"
                                       style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;"
                                       onfocus="this.style.borderColor='#666666';this.style.boxShadow='0 0 0 1px rgba(255,255,255,0.1)'" onblur="this.style.borderColor='rgba(255,255,255,0.06)';this.style.boxShadow='none'">
                                @error('instagram')<p style="margin-top: 0.125rem; font-size: 0.75rem; color: #fca5a5;">{{ $message }}</p>@enderror
                            </div>

                            <!-- Data de Nascimento -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">Data de Nascimento</label>
                                <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                                       style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;"
                                       onfocus="this.style.borderColor='#666666';this.style.boxShadow='0 0 0 1px rgba(255,255,255,0.1)'" onblur="this.style.borderColor='rgba(255,255,255,0.06)';this.style.boxShadow='none'">
                                @error('birth_date')<p style="margin-top: 0.125rem; font-size: 0.75rem; color: #fca5a5;">{{ $message }}</p>@enderror
                            </div>

                            <!-- Endereço -->
                            <div style="grid-column: span 2;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">Endereço</label>
                                <input type="text" name="address" value="{{ old('address') }}"
                                       style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- Observações -->
                            <div style="grid-column: span 2;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">Observações</label>
                                <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Informações adicionais"
                                       style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>
                        </div>
                    </div>

                    <!-- Rodapé -->
                    <div style="padding: 0.75rem 1.25rem; background: #1a1a1a; border-top: 1px solid rgba(255,255,255,0.06); display: flex; justify-content: flex-end; gap: 0.5rem;">
                        <a href="{{ route('customers.index') }}" 
                           style="padding: 0.5rem 1rem; background: #141414; color: #a4a4a4; font-weight: 500; font-size: 0.875rem; border-radius: 0.375rem; text-decoration: none; border: 1px solid rgba(255,255,255,0.08);">
                            Cancelar
                        </a>
                        <button type="submit" 
                                style="padding: 0.5rem 1.25rem; background: #111827; color: white; font-weight: 500; font-size: 0.875rem; border-radius: 0.375rem; border: none; cursor: pointer;"
                                onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                            Cadastrar Cliente
                        </button>
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
