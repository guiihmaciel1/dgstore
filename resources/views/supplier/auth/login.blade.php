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
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 antialiased">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">DG Store</h1>
            <p class="text-white text-opacity-90">Portal do Fornecedor</p>
        </div>
        
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-6 text-center">Bem-vindo</h2>
            
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-800">{{ $errors->first() }}</p>
                </div>
            @endif
            
            <form method="POST" action="{{ route('supplier.login') }}" class="space-y-5">
                @csrf
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email
                    </label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        value="{{ old('email') }}"
                        required 
                        autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        placeholder="seu@email.com"
                    >
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Senha
                    </label>
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        placeholder="••••••••"
                    >
                </div>
                
                <div class="flex items-center">
                    <input 
                        id="remember" 
                        name="remember" 
                        type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    >
                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                        Lembrar-me
                    </label>
                </div>
                
                <button 
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200 shadow-lg hover:shadow-xl"
                >
                    Entrar
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    Problemas para acessar? Entre em contato com a DG Store
                </p>
            </div>
        </div>
        
        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm text-white hover:underline">
                ← Voltar ao sistema principal
            </a>
        </div>
    </div>
</body>
</html>
