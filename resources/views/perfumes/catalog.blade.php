<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $storeName }} - Catálogo de Perfumes</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logodg.png') }}?v={{ filemtime(public_path('images/logodg.png')) }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Header -->
    <header class="bg-gradient-to-r from-[#1a1025] to-[#2d1a3e] sticky top-0 z-30 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logodg.png') }}?v={{ filemtime(public_path('images/logodg.png')) }}" alt="Logo" class="h-8 w-auto brightness-0 invert">
                    <div>
                        <span class="text-sm font-bold text-white">{{ $storeName }}</span>
                        <span class="block text-[10px] font-semibold text-pink-300 tracking-widest uppercase">Catálogo de Perfumes</span>
                    </div>
                </div>
                <a href="{{ route('login') }}" class="text-sm text-pink-300/70 hover:text-white font-medium transition">
                    Área Admin
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filtros -->
        <div class="mb-8">
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar perfume..."
                       class="rounded-lg border-gray-300 text-sm focus:ring-pink-500 focus:border-pink-500 w-64">
                <select name="category" onchange="this.form.submit()"
                        class="rounded-lg border-gray-300 text-sm focus:ring-pink-500 focus:border-pink-500">
                    <option value="">Todas as categorias</option>
                    <option value="masculino" {{ request('category') === 'masculino' ? 'selected' : '' }}>Masculino</option>
                    <option value="feminino" {{ request('category') === 'feminino' ? 'selected' : '' }}>Feminino</option>
                    <option value="unissex" {{ request('category') === 'unissex' ? 'selected' : '' }}>Unissex</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-pink-600 to-rose-500 text-white text-sm font-semibold rounded-lg hover:from-pink-500 hover:to-rose-400 shadow-md shadow-pink-500/20 transition">
                    Buscar
                </button>
                @if(request('search') || request('category'))
                    <a href="{{ route('perfumes.catalog') }}" class="text-sm text-gray-500 hover:text-gray-700">Limpar</a>
                @endif
            </form>
        </div>

        <!-- Grid de Produtos -->
        @if($products->isEmpty())
            <div class="text-center py-16">
                <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
                <p class="mt-4 text-gray-500">Nenhum perfume encontrado.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition group">
                        @if($product->photo_url)
                            <div class="aspect-square bg-gray-100 overflow-hidden">
                                <img src="{{ $product->photo_url }}" alt="{{ $product->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </div>
                        @else
                            <div class="aspect-square bg-gradient-to-br from-pink-50 to-pink-100 flex items-center justify-center">
                                <svg class="w-16 h-16 text-pink-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                </svg>
                            </div>
                        @endif

                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                @php
                                    $catColor = match($product->category?->value) {
                                        'masculino' => 'blue',
                                        'feminino' => 'pink',
                                        default => 'purple',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $catColor }}-100 text-{{ $catColor }}-700">
                                    {{ $product->category?->label() ?? 'Unissex' }}
                                </span>
                                @if($product->size_ml)
                                    <span class="text-xs text-gray-400">{{ $product->size_ml }}ml</span>
                                @endif
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900 line-clamp-2">{{ $product->name }}</h3>
                            @if($product->brand)
                                <p class="text-xs text-gray-500 mt-0.5">{{ $product->brand }}</p>
                            @endif
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-lg font-bold text-pink-600">R$ {{ number_format($product->sale_price, 2, ',', '.') }}</span>
                                @if($product->stock_quantity > 0)
                                    <span class="text-xs text-green-600 font-medium">Em estoque</span>
                                @else
                                    <span class="text-xs text-red-500 font-medium">Esgotado</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @endif
    </main>

    <footer class="border-t border-gray-200 bg-white mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center">
            <p class="text-xs text-gray-400">&copy; {{ date('Y') }} {{ $storeName }}. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>
