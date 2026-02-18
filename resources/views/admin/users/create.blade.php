<x-app-layout>
    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Cabeçalho -->
            <div class="flex items-center gap-3 mb-6">
                <a href="{{ route('admin.users.index') }}" style="display: flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 0.5rem; background: #f3f4f6; color: #6b7280; text-decoration: none;" onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Novo Usuário</h1>
                    <p class="text-sm text-gray-500">Crie um novo acesso ao sistema</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <!-- Card: Dados pessoais -->
                <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1rem;">
                    <div style="padding: 0.875rem 1.25rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                        <h3 style="font-size: 0.875rem; font-weight: 600; color: #111827;">Dados Pessoais</h3>
                    </div>
                    <div style="padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem;">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Nome completo</label>
                            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:border-gray-900 focus:outline-none"
                                   placeholder="Ex: João Silva">
                            @error('name')
                                <p style="margin-top: 0.375rem; font-size: 0.75rem; color: #dc2626;">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">E-mail</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:border-gray-900 focus:outline-none"
                                   placeholder="usuario@empresa.com.br">
                            @error('email')
                                <p style="margin-top: 0.375rem; font-size: 0.75rem; color: #dc2626;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Card: Perfil de acesso -->
                <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1rem;">
                    <div style="padding: 0.875rem 1.25rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                        <h3 style="font-size: 0.875rem; font-weight: 600; color: #111827;">Perfil de Acesso</h3>
                    </div>
                    <div style="padding: 1.25rem;">
                        <select name="role" required
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:border-gray-900 focus:outline-none">
                            <option value="">Selecione um perfil...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->value }}" {{ old('role') === $role->value ? 'selected' : '' }}>
                                    {{ $role->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <p style="margin-top: 0.375rem; font-size: 0.75rem; color: #dc2626;">{{ $message }}</p>
                        @enderror
                        <div style="margin-top: 0.75rem; font-size: 0.75rem; color: #6b7280; line-height: 1.5;">
                            <strong style="color: #374151;">Admin Geral:</strong> acesso total (DG Store + B2B).
                            <strong style="color: #374151;">Admin Distribuidora:</strong> apenas B2B.
                            <strong style="color: #374151;">Vendedor:</strong> PDV DG Store.
                        </div>
                    </div>
                </div>

                <!-- Card: Senha -->
                <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1rem;">
                    <div style="padding: 0.875rem 1.25rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                        <h3 style="font-size: 0.875rem; font-weight: 600; color: #111827;">Senha de Acesso</h3>
                    </div>
                    <div style="padding: 1.25rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Senha</label>
                            <input type="password" name="password" required minlength="8"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:border-gray-900 focus:outline-none"
                                   placeholder="Mínimo 8 caracteres">
                            @error('password')
                                <p style="margin-top: 0.375rem; font-size: 0.75rem; color: #dc2626;">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Confirmar senha</label>
                            <input type="password" name="password_confirmation" required
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:border-gray-900 focus:outline-none"
                                   placeholder="Repita a senha">
                        </div>
                    </div>
                </div>

                <!-- Card: Ativo -->
                <div style="background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1.5rem; padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <div style="font-size: 0.875rem; font-weight: 500; color: #111827;">Conta ativa</div>
                        <div style="font-size: 0.75rem; color: #6b7280;">Usuários inativos não conseguem fazer login.</div>
                    </div>
                    <div>
                        <input type="hidden" name="active" value="0">
                        <label style="position: relative; display: inline-flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="active" value="1" checked style="position: absolute; opacity: 0; width: 0; height: 0;" onchange="this.parentElement.querySelector('.toggle-bg').style.background = this.checked ? '#2563eb' : '#d1d5db'; this.parentElement.querySelector('.toggle-dot').style.transform = this.checked ? 'translateX(1.25rem)' : 'translateX(0)'">
                            <div class="toggle-bg" style="width: 2.5rem; height: 1.25rem; background: #2563eb; border-radius: 9999px; transition: background 0.2s;"></div>
                            <div class="toggle-dot" style="position: absolute; left: 0.125rem; width: 1rem; height: 1rem; background: white; border-radius: 9999px; transition: transform 0.2s; transform: translateX(1.25rem); box-shadow: 0 1px 3px rgba(0,0,0,0.2);"></div>
                        </label>
                    </div>
                </div>

                <!-- Ações -->
                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <a href="{{ route('admin.users.index') }}"
                       style="padding: 0.625rem 1rem; font-size: 0.875rem; font-weight: 500; color: #374151; background: white; border: 1px solid #d1d5db; border-radius: 0.5rem; text-decoration: none; display: inline-block;"
                       onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                        Cancelar
                    </a>
                    <button type="submit"
                            style="padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 600; color: white; background: #111827; border: none; border-radius: 0.5rem; cursor: pointer;"
                            onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                        Criar Usuário
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
