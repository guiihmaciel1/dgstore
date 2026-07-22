<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div>
            <label for="email" style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.5rem;">Email</label>
            <div style="position: relative;">
                <span style="position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); pointer-events: none; color: #4a4a4a;">
                    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </span>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                       class="login-input" placeholder="seu@email.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <!-- Password -->
        <div style="margin-top: 1.25rem;">
            <label for="password" style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.5rem;">Senha</label>
            <div style="position: relative;">
                <span style="position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); pointer-events: none; color: #4a4a4a;">
                    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </span>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       class="login-input" placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <!-- Remember + Forgot -->
        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 1.25rem;">
            <label for="remember_me" style="display: inline-flex; align-items: center; cursor: pointer;">
                <input id="remember_me" type="checkbox" name="remember" 
                       style="width: 0.875rem; height: 0.875rem; border-radius: 0.25rem; border: 1px solid rgba(255,255,255,0.12); background: #0d0d0d; accent-color: #e3e3e3; cursor: pointer;">
                <span style="margin-left: 0.5rem; font-size: 0.8rem; color: #666666;">Lembrar-me</span>
            </label>
            
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" style="font-size: 0.8rem; color: #666666; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#e3e3e3'" onmouseout="this.style.color='#666666'">
                    Esqueceu a senha?
                </a>
            @endif
        </div>

        <!-- Submit -->
        <button type="submit" class="login-btn" style="margin-top: 1.5rem;">
            Entrar
        </button>

        <!-- B2B Portal -->
        <div style="margin-top: 1.25rem; padding-top: 1.25rem; border-top: 1px solid rgba(255,255,255,0.06);">
            <a href="{{ route('b2b.login') }}" class="login-b2b">
                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                {{ \App\Domain\B2B\Models\B2BSetting::getCompanyName() }}
            </a>
        </div>

        <!-- Dev quick access -->
        @if(app()->environment('local', 'development'))
        <div style="margin-top: 1.25rem; padding-top: 1.25rem; border-top: 1px solid rgba(255,255,255,0.04);">
            <p style="font-size: 0.65rem; color: #4a4a4a; text-align: center; margin-bottom: 0.75rem;">Acesso rápido</p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                <button type="button" onclick="fillCredentials('admin@dgstore.com.br', 'password')"
                        style="padding: 0.5rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.04); border-radius: 0.5rem; color: #666666; font-size: 0.75rem; font-weight: 500; cursor: pointer; transition: all 0.2s;"
                        onmouseover="this.style.background='rgba(255,255,255,0.06)';this.style.color='#e3e3e3'" onmouseout="this.style.background='rgba(255,255,255,0.03)';this.style.color='#666666'">
                    Admin
                </button>
                <button type="button" onclick="fillCredentials('vendedor@dgstore.com.br', 'password')"
                        style="padding: 0.5rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.04); border-radius: 0.5rem; color: #666666; font-size: 0.75rem; font-weight: 500; cursor: pointer; transition: all 0.2s;"
                        onmouseover="this.style.background='rgba(255,255,255,0.06)';this.style.color='#e3e3e3'" onmouseout="this.style.background='rgba(255,255,255,0.03)';this.style.color='#666666'">
                    Vendedor
                </button>
            </div>
        </div>
        @endif
    </form>

    <script>
        function fillCredentials(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
            document.getElementById('email').focus();
        }
    </script>
</x-guest-layout>
