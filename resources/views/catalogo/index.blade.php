@extends('catalogo.layouts.catalog')

@section('title', 'DG Imports — Catálogo')
@section('description', 'Explore nosso catálogo completo de perfumes importados com os melhores preços.')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-5">

    {{-- Hero / Branding --}}
    <div class="pt-10 pb-6 sm:pt-14 sm:pb-8 text-center relative">
        <div class="absolute inset-0 -top-10" style="background: radial-gradient(ellipse at 50% 30%, rgba(212, 165, 64, 0.06) 0%, transparent 60%); pointer-events: none;"></div>
        <img src="{{ asset('images/logo-dg-imports.png') }}" alt="DG Imports" class="h-20 sm:h-28 w-auto mx-auto relative">
        <p class="text-xs sm:text-sm mt-2 relative" style="color: var(--muted);">Perfumes importados originais</p>
    </div>

    {{-- Search --}}
    <div class="mb-6 max-w-md mx-auto">
        <form method="GET" class="relative">
            @if(request('tag')) <input type="hidden" name="tag" value="{{ request('tag') }}"> @endif
            @if(request('gender')) <input type="hidden" name="gender" value="{{ request('gender') }}"> @endif
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Buscar perfume ou marca..."
                   class="w-full pl-10 pr-4 py-3 rounded-xl text-sm bg-white/[0.03] border border-white/[0.06] text-white placeholder-gray-600 focus:border-[var(--teal)] focus:ring-1 focus:ring-[var(--teal)] focus:outline-none transition">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color: var(--muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            @if(request('search'))
                <a href="{{ route('catalogo.index', request()->only('tag', 'gender')) }}"
                   class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </a>
            @endif
        </form>
    </div>

    {{-- Filter pills (horizontal scroll on mobile) --}}
    @if($tags->isNotEmpty())
        <div class="mb-8 -mx-4 px-4 sm:mx-0 sm:px-0">
            <div class="flex gap-2 overflow-x-auto hide-scrollbar pb-1 sm:flex-wrap sm:justify-center">
                <a href="{{ route('catalogo.index', request()->only('search')) }}"
                   class="shrink-0 px-4 py-2 rounded-full text-xs font-medium transition
                          {{ !request('tag') && !request('gender') ? 'pill-active' : 'pill-inactive' }}">
                    Todos
                </a>
                <a href="{{ route('catalogo.index', array_merge(request()->only('search'), ['gender' => 'masculino'])) }}"
                   class="shrink-0 px-4 py-2 rounded-full text-xs font-medium transition
                          {{ request('gender') === 'masculino' ? 'pill-active' : 'pill-inactive' }}">
                    Masculino
                </a>
                <a href="{{ route('catalogo.index', array_merge(request()->only('search'), ['gender' => 'feminino'])) }}"
                   class="shrink-0 px-4 py-2 rounded-full text-xs font-medium transition
                          {{ request('gender') === 'feminino' ? 'pill-active' : 'pill-inactive' }}">
                    Feminino
                </a>
                @foreach($tags as $tag)
                    <a href="{{ route('catalogo.index', array_merge(request()->only('search'), ['tag' => $tag->slug])) }}"
                       class="shrink-0 px-4 py-2 rounded-full text-xs font-medium transition
                              {{ request('tag') === $tag->slug ? 'pill-active' : 'pill-inactive' }}">
                        @if($tag->icon) {{ $tag->icon }} @endif
                        {{ $tag->name }}
                        @if($tag->products_count > 0)
                            <span class="opacity-50 ml-0.5">{{ $tag->products_count }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Grid --}}
    @if($products->isEmpty())
        <div class="text-center py-24">
            <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center" style="background: var(--surface);">
                <svg class="w-7 h-7" style="color: var(--muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <p class="mt-4 text-sm" style="color: var(--muted);">Nenhum perfume encontrado.</p>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-5">
            @foreach($products as $product)
                <a href="{{ route('catalogo.show', $product->slug) }}" class="cat-card block rounded-2xl overflow-hidden group">
                    {{-- Image --}}
                    <div class="aspect-square overflow-hidden flex items-center justify-center" style="background: var(--surface-elevated);">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                 class="w-full h-full object-contain p-6 group-hover:scale-105 transition-transform duration-500" loading="lazy">
                        @else
                            <svg class="w-12 h-12" style="color: var(--muted); opacity: 0.3;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="p-3 sm:p-4">
                        {{-- Brand --}}
                        @if($product->brand)
                            <p class="text-[10px] sm:text-[11px] uppercase tracking-wider mb-1 truncate" style="color: var(--gold);">{{ $product->brand }}</p>
                        @endif

                        {{-- Name --}}
                        <h3 class="text-xs sm:text-sm font-semibold text-white line-clamp-2 leading-snug">{{ $product->name }}</h3>

                        {{-- Inspired by --}}
                        @if($product->inspired_by)
                            <p class="text-[10px] mt-1 truncate" style="color: var(--muted);"
                               title="Inspirado em {{ $product->inspired_by }}">
                                💡 {{ $product->inspired_by }}
                            </p>
                        @endif

                        {{-- Rating --}}
                        @if($product->rating)
                            <div class="flex items-center gap-1 mt-1.5">
                                <svg class="w-3 h-3 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span class="text-[11px] text-amber-500 font-medium">{{ number_format($product->rating, 1) }}</span>
                            </div>
                        @endif

                        {{-- Pricing --}}
                        @if($product->sale_price && $product->pix_price)
                            @php $origPrice = $product->original_price ?? (int)(ceil(((float)$product->sale_price * 1.2) / 10) * 10); @endphp
                            <div class="mt-3 pt-3 border-t border-white/[0.04]">
                                <div class="flex items-baseline gap-1.5">
                                    <span class="text-[10px] line-through" style="color: var(--muted);">R${{ number_format($origPrice, 0, ',', '.') }}</span>
                                    <span class="text-sm sm:text-base font-bold text-white">R$ {{ number_format($product->sale_price, 0, ',', '.') }}</span>
                                </div>
                                <p class="text-[10px] mt-0.5" style="color: var(--muted);">10x sem juros</p>
                                <p class="text-[11px] font-semibold mt-1" style="color: var(--teal);">R$ {{ number_format($product->pix_price, 0, ',', '.') }} <span class="font-normal" style="color: var(--teal); opacity: 0.6;">no PIX</span></p>
                                @if($product->stock_quantity <= 0)
                                    <p class="text-[10px] mt-1.5 font-medium" style="color: var(--gold);">📦 Sob encomenda · 3 a 5 dias</p>
                                @endif
                            </div>
                        @elseif($product->pix_price)
                            <div class="mt-3 pt-3 border-t border-white/[0.04]">
                                <p class="text-sm font-bold" style="color: var(--teal);">R$ {{ number_format($product->pix_price, 0, ',', '.') }}</p>
                                @if($product->stock_quantity <= 0)
                                    <p class="text-[10px] mt-1.5 font-medium" style="color: var(--gold);">📦 Sob encomenda · 3 a 5 dias</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-10">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
