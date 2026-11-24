@extends('catalogo.layouts.catalog')

@section('title', 'DG Imports — Catálogo')
@section('description', 'Explore nosso catálogo completo de perfumes importados com os melhores preços.')

@section('content')
<div class="max-w-6xl mx-auto px-5 sm:px-6">

    {{-- Intro + busca --}}
    <div class="pt-8 pb-6 sm:pt-12 sm:pb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-5">
        <div>
            <h1 class="font-serif text-3xl sm:text-4xl font-semibold text-gray-900 leading-tight">Catálogo de perfumes</h1>
            <p class="mt-2 text-sm" style="color: #6b6b6b;">
                Ribeirão Preto · Pronta entrega ou encomenda em 3–5 dias · 10x sem juros ou desconto no PIX
            </p>
        </div>
        <div class="w-full sm:max-w-sm">
            <form method="GET" class="relative">
                @if(request('tag')) <input type="hidden" name="tag" value="{{ request('tag') }}"> @endif
                @if(request('gender')) <input type="hidden" name="gender" value="{{ request('gender') }}"> @endif
                <input type="search" name="search" aria-label="Buscar perfume ou marca" value="{{ request('search') }}"
                       placeholder="Buscar perfume ou marca..."
                       class="search-input w-full pl-11 pr-10 py-3 rounded-full text-sm"
                       style="background: #fff !important; color: #1a1a1a !important; border: 1px solid #ddd !important;">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4" style="color: var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                @if(request('search'))
                    <a href="{{ route('catalogo.index', request()->only('tag', 'gender')) }}"
                       class="absolute right-3.5 top-1/2 -translate-y-1/2 w-6 h-6 flex items-center justify-center rounded-full transition"
                       style="background: #eee; color: #666;">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                @endif
            </form>
        </div>
    </div>

    {{-- Filter pills --}}
    @if($tags->isNotEmpty())
        <div class="mb-8 -mx-5 px-5 sm:mx-0 sm:px-0 animate-fade-up" style="animation-delay: 0.15s;">
            <div class="flex gap-2.5 overflow-x-auto hide-scrollbar pb-1 sm:flex-wrap">
                <a href="{{ route('catalogo.index', request()->only('search')) }}"
                   class="shrink-0 px-5 py-2.5 rounded-full text-xs tracking-wide font-medium transition-all duration-300
                          {{ !request('tag') && !request('gender') ? 'pill-active' : 'pill-inactive' }}">
                    Todos
                </a>
                <a href="{{ route('catalogo.index', array_merge(request()->only('search'), ['gender' => 'masculino'])) }}"
                   class="shrink-0 px-5 py-2.5 rounded-full text-xs tracking-wide font-medium transition-all duration-300
                          {{ request('gender') === 'masculino' ? 'pill-active' : 'pill-inactive' }}">
                    Masculino
                </a>
                <a href="{{ route('catalogo.index', array_merge(request()->only('search'), ['gender' => 'feminino'])) }}"
                   class="shrink-0 px-5 py-2.5 rounded-full text-xs tracking-wide font-medium transition-all duration-300
                          {{ request('gender') === 'feminino' ? 'pill-active' : 'pill-inactive' }}">
                    Feminino
                </a>
                @foreach($tags as $tag)
                    <a href="{{ route('catalogo.index', array_merge(request()->only('search'), ['tag' => $tag->slug])) }}"
                       class="shrink-0 px-5 py-2.5 rounded-full text-xs tracking-wide font-medium transition-all duration-300
                              {{ request('tag') === $tag->slug ? 'pill-active' : 'pill-inactive' }}">
                        @if($tag->icon) {{ $tag->icon }} @endif
                        {{ $tag->name }}
                        @if($tag->products_count > 0)
                            <span class="opacity-40 ml-1">{{ $tag->products_count }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Highlighted sections (first page only, no filters) --}}
    @include('catalogo.partials.highlights')

    {{-- Grid --}}
    @if($products->isEmpty())
        <div class="text-center py-28">
            <div class="w-20 h-20 mx-auto rounded-2xl flex items-center justify-center" style="background: var(--surface-elevated);">
                <svg class="w-8 h-8" style="color: var(--muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <p class="mt-5 text-sm font-medium" style="color: var(--muted);">Nenhum perfume encontrado</p>
            <a href="{{ route('catalogo.index') }}" class="inline-block mt-3 text-xs font-medium transition" style="color: var(--gold);">
                Ver todos os perfumes
            </a>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
            @foreach($products as $product)
                <a href="{{ route('catalogo.show', $product->slug) }}"
                   class="cat-card flex flex-col group animate-fade-up"
                   style="animation-delay: {{ min($loop->index * 0.05, 0.4) }}s;">

                    {{-- Product image --}}
                    <div class="cat-card-img aspect-[4/5] flex items-center justify-center p-5 sm:p-6 relative">
                        @if($product->catalog_discount_percent >= 5)
                            <div class="discount-pin">
                                <span class="discount-pin-value">{{ $product->catalog_discount_percent }}%</span>
                                <span class="discount-pin-label">OFF</span>
                            </div>
                        @endif
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                 class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-700 ease-out"
                                 loading="lazy">
                        @else
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10" style="color: var(--muted); opacity: 0.15;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Product info --}}
                    <div class="px-4 pt-4 pb-5 flex-1 flex flex-col">
                        {{-- Brand --}}
                        @if($product->brand)
                            <p class="text-[11px] sm:text-xs uppercase tracking-[0.15em] font-medium mb-1.5 truncate" style="color: var(--gold);">
                                {{ $product->brand }}
                            </p>
                        @endif

                        {{-- Name --}}
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 line-clamp-2 leading-snug">
                            {{ $product->name }}
                            @if($product->size_ml)
                                <span class="text-[10px] font-normal align-middle ml-1" style="color: var(--muted);">{{ $product->size_ml }}ml</span>
                            @endif
                        </h3>

                        {{-- Inspired by --}}
                        @if($product->inspired_by)
                            <p class="text-[11px] mt-1.5 truncate" style="color: var(--muted);"
                               title="Inspirado em {{ $product->inspired_by }}">
                                Inspirado em {{ $product->inspired_by }}
                            </p>
                        @endif

                        {{-- Rating --}}
                        @if($product->rating)
                            <div class="flex items-center gap-1.5 mt-2">
                                <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span class="text-xs text-amber-400 font-semibold">{{ number_format($product->rating, 1) }}</span>
                            </div>
                        @endif

                        @include('catalogo.partials.card-price', ['product' => $product])
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-12">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
