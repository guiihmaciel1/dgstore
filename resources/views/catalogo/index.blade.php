@extends('catalogo.layouts.catalog')

@section('title', 'DG Perfumes — Catálogo')
@section('description', 'Explore nosso catálogo completo de perfumes importados com detalhes olfativos, acordes e pirâmide de notas.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Tabs de categorias --}}
    @if($tags->isNotEmpty())
        <div class="mb-6 flex flex-wrap gap-2">
            <a href="{{ route('catalogo.index', request()->only('search')) }}"
               class="px-4 py-2 rounded-full text-sm font-medium transition
                      {{ !request('tag') && !request('gender') ? 'bg-pink-600 text-white' : 'bg-white/5 text-gray-400 hover:bg-white/10 hover:text-white border border-white/10' }}">
                Todos
            </a>
            <a href="{{ route('catalogo.index', array_merge(request()->only('search'), ['gender' => 'masculino'])) }}"
               class="px-4 py-2 rounded-full text-sm font-medium transition
                      {{ request('gender') === 'masculino' ? 'bg-blue-600 text-white' : 'bg-white/5 text-gray-400 hover:bg-white/10 hover:text-white border border-white/10' }}">
                Masculino
            </a>
            <a href="{{ route('catalogo.index', array_merge(request()->only('search'), ['gender' => 'feminino'])) }}"
               class="px-4 py-2 rounded-full text-sm font-medium transition
                      {{ request('gender') === 'feminino' ? 'bg-pink-600 text-white' : 'bg-white/5 text-gray-400 hover:bg-white/10 hover:text-white border border-white/10' }}">
                Feminino
            </a>
            @foreach($tags as $tag)
                <a href="{{ route('catalogo.index', array_merge(request()->only('search'), ['tag' => $tag->slug])) }}"
                   class="px-4 py-2 rounded-full text-sm font-medium transition
                          {{ request('tag') === $tag->slug ? 'text-white' : 'bg-white/5 text-gray-400 hover:bg-white/10 hover:text-white border border-white/10' }}"
                   @if(request('tag') === $tag->slug) style="background: {{ $tag->color }};" @endif>
                    @if($tag->icon) {{ $tag->icon }} @endif
                    {{ $tag->name }}
                    @if($tag->products_count > 0)
                        <span class="text-xs opacity-70">({{ $tag->products_count }})</span>
                    @endif
                </a>
            @endforeach
        </div>
    @endif

    <!-- Busca -->
    <div class="mb-8">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            @if(request('tag')) <input type="hidden" name="tag" value="{{ request('tag') }}"> @endif
            @if(request('gender')) <input type="hidden" name="gender" value="{{ request('gender') }}"> @endif
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar perfume ou marca..."
                   class="rounded-lg border border-white/10 bg-white/5 text-sm text-white placeholder-gray-500 focus:ring-pink-500 focus:border-pink-500 w-64 px-4 py-2.5">
            <button type="submit" class="px-5 py-2.5 bg-pink-600 text-white text-sm font-semibold rounded-lg hover:bg-pink-700 transition">
                Buscar
            </button>
            @if(request('search'))
                <a href="{{ route('catalogo.index', request()->only('tag', 'gender')) }}" class="text-sm text-gray-400 hover:text-white transition">Limpar</a>
            @endif
        </form>
    </div>

    @if($products->isEmpty())
        <div class="text-center py-20">
            <svg class="mx-auto h-20 w-20 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
            </svg>
            <p class="mt-4 text-gray-400 text-lg">Nenhum perfume encontrado.</p>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach($products as $product)
                <a href="{{ route('catalogo.show', $product->slug) }}" class="card-hover block rounded-xl overflow-hidden border border-white/5" style="background: #151515;">
                    {{-- Foto --}}
                    <div class="aspect-[3/4] bg-white/5 overflow-hidden flex items-center justify-center">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-contain p-4" loading="lazy">
                        @else
                            <svg class="w-16 h-16 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-2">
                            @php
                                $catColor = match($product->gender->value) {
                                    'masculino' => 'blue',
                                    'feminino' => 'pink',
                                    default => 'purple',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-{{ $catColor }}-500/20 text-{{ $catColor }}-400">
                                {{ $product->gender->label() }}
                            </span>
                            @if($product->rating)
                                <span class="text-xs text-amber-400 font-medium">★ {{ number_format($product->rating, 1) }}</span>
                            @endif
                        </div>
                        <h3 class="text-sm font-semibold text-white line-clamp-2 leading-tight">{{ $product->name }}</h3>
                        @if($product->brand)
                            <p class="text-xs text-gray-500 mt-1">{{ $product->brand }}</p>
                        @endif
                        @if($product->sale_price && $product->pix_price)
                            @php $origPrice = $product->original_price ?? (int)(ceil(((float)$product->sale_price * 1.2) / 10) * 10); @endphp
                            <div class="mt-2">
                                <p class="text-[11px] text-gray-500 line-through">R${{ number_format($origPrice, 0, ',', '.') }}</p>
                                <p class="text-sm font-bold text-white">R$ {{ number_format($product->sale_price, 0, ',', '.') }} <span class="text-[10px] font-normal text-gray-400">10x s/ juros</span></p>
                                <p class="text-xs text-green-400">R$ {{ number_format($product->pix_price, 2, ',', '.') }} <span class="text-green-500/70">no PIX</span></p>
                            </div>
                        @elseif($product->pix_price)
                            <p class="mt-2 text-base font-bold text-green-400">R$ {{ number_format($product->pix_price, 2, ',', '.') }}</p>
                        @endif

                        {{-- Mini acordes --}}
                        @if($product->accords->isNotEmpty())
                            <div class="mt-3 space-y-1">
                                @foreach($product->accords->take(3) as $accord)
                                    <div class="h-1.5 rounded-full overflow-hidden bg-white/5">
                                        <div class="h-full rounded-full accord-bar" style="width: {{ $accord->percentage }}%; background: {{ $accord->color }};"></div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
