<x-app-layout>
    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabeçalho -->
            <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                <a href="{{ route('customers.index') }}" style="margin-right: 1rem; padding: 0.5rem; color: #6b7280; border-radius: 0.5rem;"
                   onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                    <svg style="height: 1.5rem; width: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Novo Cliente</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Cadastre um novo cliente no sistema</p>
                </div>
            </div>

            <!-- Formulário -->
            <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
                <form method="POST" action="{{ route('customers.store') }}">
                    @csrf
                    
                    <div style="padding: 1.5rem;">
                        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                            <!-- Nome -->
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                    Nome Completo <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                @error('name')<p style="margin-top: 0.25rem; font-size: 0.75rem; color: #dc2626;">{{ $message }}</p>@enderror
                            </div>

                            <!-- Telefone -->
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                    Telefone <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="(00) 00000-0000"
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                @error('phone')<p style="margin-top: 0.25rem; font-size: 0.75rem; color: #dc2626;">{{ $message }}</p>@enderror
                            </div>

                            <!-- E-mail -->
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">E-mail</label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                @error('email')<p style="margin-top: 0.25rem; font-size: 0.75rem; color: #dc2626;">{{ $message }}</p>@enderror
                            </div>

                            <!-- CPF -->
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">CPF</label>
                                <input type="text" name="cpf" value="{{ old('cpf') }}" placeholder="000.000.000-00"
                                       style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                @error('cpf')<p style="margin-top: 0.25rem; font-size: 0.75rem; color: #dc2626;">{{ $message }}</p>@enderror
                            </div>

                            <!-- Endereço -->
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Endereço</label>
                                <textarea name="address" rows="2"
                                          style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; resize: vertical;"
                                          onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">{{ old('address') }}</textarea>
                            </div>

                            <!-- Observações -->
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Observações</label>
                                <textarea name="notes" rows="3"
                                          style="width: 100%; padding: 0.625rem 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; resize: vertical;"
                                          onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Rodapé -->
                    <div style="padding: 1rem 1.5rem; background: #f9fafb; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 0.75rem;">
                        <a href="{{ route('customers.index') }}" 
                           style="padding: 0.625rem 1.5rem; background: white; color: #374151; font-weight: 500; border-radius: 0.5rem; text-decoration: none; border: 1px solid #e5e7eb;">
                            Cancelar
                        </a>
                        <button type="submit" 
                                style="padding: 0.625rem 1.5rem; background: #111827; color: white; font-weight: 500; border-radius: 0.5rem; border: none; cursor: pointer;"
                                onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                            Cadastrar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
