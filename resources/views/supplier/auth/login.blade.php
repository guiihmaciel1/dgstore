<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Login - Portal Fornecedor DG Store</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        * {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "SF Pro Text", "Helvetica Neue", Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        body {
            background: linear-gradient(180deg, #F5F5F7 0%, #E8E8ED 100%);
        }
        
        .apple-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        
        .apple-button {
            background: #007AFF;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .apple-button:hover {
            background: #0051D5;
            transform: translateY(-1px);
        }
        
        .apple-button:active {
            transform: translateY(0);
        }
        
        .apple-input {
            transition: all 0.2s ease;
        }
        
        .apple-input:focus {
            border-color: #007AFF;
            box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.1);
        }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 antialiased">
    <div class="w-full max-w-md">
        <div class="text-center mb-10">
            <h1 class="text-5xl font-semibold mb-3" style="color: #1D1D1F; letter-spacing: -0.5px;">DG Store</h1>
            <p class="text-lg" style="color: #86868B;">Portal do Fornecedor</p>
        </div>
        
        <div class="apple-card rounded-3xl shadow-xl p-10">
            <h2 class="text-3xl font-semibold mb-8 text-center" style="color: #1D1D1F; letter-spacing: -0.5px;">Bem-vindo</h2>
            
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl" style="background: #FFEBEE; border: 1px solid #FFCDD2;">
                    <p class="text-sm" style="color: #C62828;">{{ $errors->first() }}</p>
                </div>
            @endif
            
            <form method="POST" action="{{ route('supplier.login') }}" class="space-y-6">
                @csrf
                
                <div>
                    <label for="email" class="block text-sm font-medium mb-2" style="color: #1D1D1F;">
                        Email
                    </label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        value="{{ old('email') }}"
                        required 
                        autofocus
                        class="apple-input w-full px-4 py-3.5 border border-gray-300 rounded-xl focus:outline-none text-base"
                        style="background: #F5F5F7; border-color: #D1D1D6;"
                        placeholder="seu@email.com"
                    >
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium mb-2" style="color: #1D1D1F;">
                        Senha
                    </label>
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        required
                        class="apple-input w-full px-4 py-3.5 border border-gray-300 rounded-xl focus:outline-none text-base"
                        style="background: #F5F5F7; border-color: #D1D1D6;"
                        placeholder="••••••••"
                    >
                </div>
                
                <div class="flex items-center">
                    <input 
                        id="remember" 
                        name="remember" 
                        type="checkbox"
                        class="h-4 w-4 rounded"
                        style="color: #007AFF; border-color: #D1D1D6;"
                    >
                    <label for="remember" class="ml-3 block text-sm" style="color: #1D1D1F;">
                        Lembrar-me
                    </label>
                </div>
                
                <button 
                    type="submit"
                    class="apple-button w-full text-white font-semibold py-3.5 px-4 rounded-xl text-base shadow-sm"
                >
                    Entrar
                </button>
            </form>
            
            <div class="mt-8 text-center">
                <p class="text-sm" style="color: #86868B;">
                    Problemas para acessar? Entre em contato com a DG Store
                </p>
            </div>
        </div>
    </div>
</body>
</html>
