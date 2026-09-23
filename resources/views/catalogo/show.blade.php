@extends('catalogo.layouts.catalog')

@section('title', $fragrance->name . ' — ' . ($fragrance->brand ?? 'DG Imports'))
@section('description', Str::limit($fragrance->description ?? 'Conheça o perfume ' . $fragrance->name, 160))
@if($fragrance->image_url)
    @section('og_image', $fragrance->image_url)
@endif

@section('content')
<div class="max-w-5xl mx-auto px-5 sm:px-6">

    {{-- Back navigation --}}
    <div class="pt-6 pb-5 sm:pt-8">
        <a href="{{ route('catalogo.index') }}"
           class="inline-flex items-center gap-2 text-[11px] tracking-wide font-medium uppercase transition-colors duration-200"
           style="color: var(--muted);"
           onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='var(--muted)'">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Catálogo
        </a>
    </div>

    {{-- Product hero --}}
    <div class="info-card overflow-hidden mb-8 animate-fade-up">

        {{-- Image — centered, with ambient glow + discount pin --}}
        <div class="relative flex items-center justify-center p-10 sm:p-14"
             style="min-height: 280px;">
            <div class="absolute inset-0 pointer-events-none"
                 style="background: radial-gradient(ellipse at 50% 60%, rgba(212, 165, 64, 0.05) 0%, transparent 60%);"></div>

            @php
                $showOrigPrice = $fragrance->original_price ?? ($fragrance->sale_price ? (int)(ceil(((float)$fragrance->sale_price * 1.2) / 10) * 10) : null);
                $showDiscount = ($showOrigPrice && $fragrance->pix_price && $showOrigPrice > 0)
                    ? round(($showOrigPrice - (float)$fragrance->pix_price) / $showOrigPrice * 100)
                    : 0;
            @endphp
            @if($showDiscount >= 5)
                <div class="discount-pin discount-pin-lg">
                    <span class="discount-pin-value">{{ $showDiscount }}%</span>
                    <span class="discount-pin-label">OFF</span>
                </div>
            @endif

            @if($fragrance->image_url)
                <img src="{{ $fragrance->image_url }}" alt="{{ $fragrance->name }}"
                     class="w-56 sm:w-64 h-auto max-h-[320px] object-contain relative drop-shadow-2xl">
            @else
                <div class="w-40 h-56 rounded-2xl flex items-center justify-center" style="background: rgba(255,255,255,0.02);">
                    <svg class="w-14 h-14" style="color: var(--muted); opacity: 0.15;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                </div>
            @endif
        </div>

        {{-- Gold separator --}}
        <div class="mx-8">
            <div class="gold-line"></div>
        </div>

        {{-- Info — inside the same card --}}
        <div class="px-6 pt-6 pb-7 sm:px-8 sm:pt-7 sm:pb-8">
            {{-- Brand --}}
            @if($fragrance->brand)
                <p class="text-[11px] uppercase tracking-[0.2em] font-semibold mb-2" style="color: var(--gold);">
                    @if($fragrance->brand_logo_url)
                        <img src="{{ $fragrance->brand_logo_url }}" alt="{{ $fragrance->brand }}" class="h-4 w-auto rounded opacity-50 inline mr-2">
                    @endif
                    {{ $fragrance->brand }}
                </p>
            @endif

            {{-- Name --}}
            <h1 class="font-serif text-2xl sm:text-3xl font-semibold leading-tight" style="color: var(--cream);">
                {{ $fragrance->name }}
            </h1>

            {{-- Meta badges --}}
            <div class="flex flex-wrap items-center gap-2 mt-4">
                @php
                    $badgeClass = match($fragrance->gender->value) {
                        'masculino' => 'perfume-badge-masc',
                        'feminino' => 'perfume-badge-fem',
                        default => 'perfume-badge-uni',
                    };
                @endphp
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] tracking-wide font-semibold {{ $badgeClass }}">
                    {{ $fragrance->gender->label() }}
                </span>
                @if($fragrance->concentration)
                    <span class="text-[10px] tracking-wide px-3 py-1.5 rounded-full font-medium"
                          style="background: rgba(255,255,255,0.03); color: var(--muted); border: 1px solid rgba(255,255,255,0.04);">
                        {{ $fragrance->concentration }}
                    </span>
                @endif
                @if($fragrance->year)
                    <span class="text-[10px] tracking-wide px-3 py-1.5 rounded-full font-medium"
                          style="background: rgba(255,255,255,0.03); color: var(--muted); border: 1px solid rgba(255,255,255,0.04);">
                        {{ $fragrance->year }}
                    </span>
                @endif
            </div>

            {{-- Inspired by --}}
            @if($fragrance->inspired_by)
                <div class="flex items-center gap-2 mt-4">
                    <span class="text-[10px] tracking-wide uppercase" style="color: var(--muted);">Inspirado em</span>
                    <span class="text-[11px] font-semibold" style="color: var(--gold-light);">{{ $fragrance->inspired_by }}</span>
                </div>
            @endif

            {{-- Rating --}}
            @if($fragrance->rating)
                <div class="flex items-center gap-2.5 mt-5">
                    <div class="flex items-center gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= floor($fragrance->rating) ? 'text-amber-400' : ($i - $fragrance->rating < 0.5 ? 'text-amber-400' : 'text-white/[0.06]') }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <span class="text-sm font-bold text-amber-400">{{ number_format($fragrance->rating, 1) }}</span>
                    <span class="text-[10px]" style="color: var(--muted);">{{ number_format($fragrance->votes_count, 0, ',', '.') }} votos</span>
                </div>
            @endif

            {{-- Pricing --}}
            @if($fragrance->sale_price && $fragrance->pix_price)
                @php
                    $originalPrice = $fragrance->original_price ?? (int)(ceil(((float)$fragrance->sale_price * 1.2) / 10) * 10);
                    $realDiscount = $originalPrice > 0 ? round((($originalPrice - (float)$fragrance->pix_price) / $originalPrice) * 100) : 0;
                @endphp
                <div class="mt-6 pt-5" style="border-top: 1px solid rgba(212, 165, 64, 0.06);">
                    <div class="flex items-baseline gap-2.5">
                        <span class="text-xs line-through font-light" style="color: var(--muted);">R$ {{ number_format($originalPrice, 0, ',', '.') }}</span>
                        <span class="text-2xl sm:text-3xl font-bold text-white tracking-tight">R$ {{ number_format($fragrance->sale_price, 0, ',', '.') }}</span>
                    </div>
                    <p class="text-[11px] mt-1.5 font-light" style="color: var(--muted);">
                        em até <span class="font-semibold text-white/90">10x</span> de
                        <span class="font-semibold text-white/90">R$ {{ number_format((float)$fragrance->sale_price / 10, 2, ',', '.') }}</span> sem juros
                    </p>

                    {{-- PIX --}}
                    <div class="mt-4 px-5 py-4 rounded-2xl pix-badge">
                        <div class="flex items-baseline gap-2.5">
                            <span class="text-xl sm:text-2xl font-bold" style="color: var(--teal);">R$ {{ number_format($fragrance->pix_price, 0, ',', '.') }}</span>
                            <span class="text-[10px] font-semibold tracking-wide" style="color: var(--teal); opacity: 0.7;">à vista no PIX</span>
                        </div>
                        @if($realDiscount >= 5)
                            <div class="flex items-center gap-2 mt-2">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold"
                                      style="background: rgba(46, 196, 182, 0.15); color: var(--teal-light);">
                                    🔥 Economia de {{ $realDiscount }}%
                                </span>
                            </div>
                        @endif
                    </div>

                    @if($fragrance->stock_quantity <= 0)
                        <div class="mt-3 px-5 py-3 rounded-2xl flex items-center gap-3" style="background: var(--gold-glow); border: 1px solid rgba(212, 165, 64, 0.08);">
                            <span class="text-base">📦</span>
                            <div>
                                <p class="text-[11px] font-semibold" style="color: var(--gold);">Sob encomenda</p>
                                <p class="text-[10px]" style="color: var(--muted);">Prazo: 3 a 5 dias úteis</p>
                            </div>
                        </div>
                    @endif
                </div>
            @elseif($fragrance->pix_price)
                <div class="mt-6 pt-5" style="border-top: 1px solid rgba(212, 165, 64, 0.06);">
                    <div class="px-5 py-4 rounded-2xl pix-badge">
                        <span class="text-2xl font-bold" style="color: var(--teal);">R$ {{ number_format($fragrance->pix_price, 0, ',', '.') }}</span>
                        <span class="text-[11px] ml-1.5 font-medium" style="color: var(--muted);">à vista no PIX</span>
                    </div>
                    @if($fragrance->stock_quantity <= 0)
                        <div class="mt-3 px-5 py-3 rounded-2xl flex items-center gap-3" style="background: var(--gold-glow); border: 1px solid rgba(212, 165, 64, 0.08);">
                            <span class="text-base">📦</span>
                            <div>
                                <p class="text-[11px] font-semibold" style="color: var(--gold);">Sob encomenda</p>
                                <p class="text-[10px]" style="color: var(--muted);">Prazo: 3 a 5 dias úteis</p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- WhatsApp --}}
            @if($whatsappNumber)
                @php
                    $waNumber = preg_replace('/\D/', '', $whatsappNumber);
                    $waText = urlencode("Olá! Tenho interesse no perfume *{$fragrance->name}*" . ($fragrance->brand ? " da *{$fragrance->brand}*" : '') . ". Ele está disponível?");
                @endphp
                <a href="https://wa.me/55{{ $waNumber }}?text={{ $waText }}" target="_blank"
                   class="btn-whatsapp mt-6 w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-7 py-3.5 text-white text-sm font-semibold rounded-2xl">
                    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Tenho interesse
                </a>
            @endif
        </div>
    </div>

    {{-- Content sections --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 lg:gap-6">
        {{-- Main column --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Acordes --}}
            @if($fragrance->accords->isNotEmpty())
                <div class="rounded-[1.25rem] p-6 sm:p-7 info-card animate-fade-up" style="animation-delay: 0.1s;">
                    <h2 class="text-[11px] uppercase tracking-[0.2em] font-semibold mb-5" style="color: var(--gold);">Principais Acordes</h2>
                    <div class="space-y-3">
                        @foreach($fragrance->accords as $accord)
                            <div class="flex items-center gap-3">
                                <div class="flex-1 h-8 rounded-xl overflow-hidden" style="background: rgba(255,255,255,0.02);">
                                    <div class="h-full rounded-xl flex items-center px-3.5 accord-bar"
                                         style="width: {{ $accord->percentage }}%; background: {{ $accord->color }};">
                                        <span class="text-[10px] font-semibold text-white truncate tracking-wide" style="text-shadow: 0 1px 3px rgba(0,0,0,0.6);">
                                            {{ $accord->name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Piramide Olfativa --}}
            @if($fragrance->notes->isNotEmpty())
                <div class="rounded-[1.25rem] p-6 sm:p-7 info-card animate-fade-up" style="animation-delay: 0.15s;">
                    <h2 class="text-[11px] uppercase tracking-[0.2em] font-semibold mb-6" style="color: var(--gold);">Pirâmide Olfativa</h2>
                    @foreach([
                        'top' => ['label' => 'Notas de Topo', 'icon' => '△'],
                        'heart' => ['label' => 'Notas de Coração', 'icon' => '♡'],
                        'base' => ['label' => 'Notas de Base', 'icon' => '▽'],
                    ] as $layer => $meta)
                        @php $layerNotes = $fragrance->notes->where('layer', $layer); @endphp
                        @if($layerNotes->isNotEmpty())
                            <div class="mb-6 last:mb-0">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="text-xs" style="color: var(--gold); opacity: 0.6;">{{ $meta['icon'] }}</span>
                                    <h3 class="text-[10px] font-semibold uppercase tracking-[0.15em]" style="color: var(--muted);">{{ $meta['label'] }}</h3>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($layerNotes as $note)
                                        <div class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl transition-colors"
                                             style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.04);">
                                            @if($note->image_url)
                                                <img src="{{ $note->image_url }}" alt="{{ $note->name }}" class="w-7 h-7 rounded-full object-cover" style="background: rgba(255,255,255,0.03);">
                                            @endif
                                            <span class="text-[11px] font-medium" style="color: var(--cream);">{{ $note->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

            {{-- Descricao --}}
            @if($fragrance->description)
                <div class="rounded-[1.25rem] p-6 sm:p-7 info-card animate-fade-up" style="animation-delay: 0.2s;">
                    <h2 class="text-[11px] uppercase tracking-[0.2em] font-semibold mb-4" style="color: var(--gold);">Sobre</h2>
                    <p class="text-[13px] leading-relaxed" style="color: var(--muted);">{{ $fragrance->description }}</p>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">

            {{-- Quando Usar (esconde se tudo for zero/null) --}}
            @php
                $hasSeasons = is_array($fragrance->seasons) && array_sum($fragrance->seasons) > 0;
                $hasDayNight = is_array($fragrance->day_night) && array_sum($fragrance->day_night) > 0;
            @endphp
            @if($hasSeasons || $hasDayNight)
                <div class="rounded-[1.25rem] p-6 sm:p-7 info-card animate-fade-up" style="animation-delay: 0.1s;">
                    <h2 class="text-[11px] uppercase tracking-[0.2em] font-semibold mb-5" style="color: var(--gold);">Quando Usar</h2>

                    @if($hasSeasons)
                        <div class="mb-5">
                            <h3 class="text-[9px] font-semibold uppercase tracking-[0.15em] mb-3" style="color: var(--muted);">Estações</h3>
                            @php
                                $maxSeason = max($fragrance->seasons);
                                $seasonIcons = ['inverno' => '❄️', 'primavera' => '🌸', 'verao' => '☀️', 'outono' => '🍂'];
                            @endphp
                            <div class="space-y-3">
                                @foreach($fragrance->seasons as $season => $votes)
                                    <div>
                                        <div class="flex items-center justify-between text-[11px] mb-1.5">
                                            <span class="capitalize font-medium" style="color: var(--cream);">{{ $seasonIcons[$season] ?? '' }} {{ $season }}</span>
                                            <span class="text-[10px]" style="color: var(--muted);">{{ number_format($votes, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="h-1.5 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.03);">
                                            <div class="h-full rounded-full accord-bar" style="width: {{ $maxSeason > 0 ? ($votes / $maxSeason) * 100 : 0 }}%; background: linear-gradient(90deg, var(--gold-dark), var(--gold));"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($hasDayNight)
                        <div>
                            <h3 class="text-[9px] font-semibold uppercase tracking-[0.15em] mb-3" style="color: var(--muted);">Horário</h3>
                            @php $maxDayNight = max($fragrance->day_night); @endphp
                            <div class="space-y-3">
                                @foreach($fragrance->day_night as $period => $votes)
                                    <div>
                                        <div class="flex items-center justify-between text-[11px] mb-1.5">
                                            <span class="capitalize font-medium" style="color: var(--cream);">{{ $period === 'dia' ? '🌤️' : '🌙' }} {{ $period }}</span>
                                            <span class="text-[10px]" style="color: var(--muted);">{{ number_format($votes, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="h-1.5 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.03);">
                                            <div class="h-full rounded-full accord-bar" style="width: {{ $maxDayNight > 0 ? ($votes / $maxDayNight) * 100 : 0 }}%; background: linear-gradient(90deg, var(--gold-dark), var(--gold));"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Ficha Tecnica --}}
            <div class="rounded-[1.25rem] p-6 sm:p-7 info-card animate-fade-up" style="animation-delay: 0.15s;">
                <h2 class="text-[11px] uppercase tracking-[0.2em] font-semibold mb-5" style="color: var(--gold);">Ficha Técnica</h2>
                <dl class="space-y-4 text-[12px]">
                    @if($fragrance->brand)
                        <div class="flex justify-between items-center">
                            <dt class="font-medium" style="color: var(--muted);">Marca</dt>
                            <dd class="font-semibold" style="color: var(--cream);">{{ $fragrance->brand }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between items-center">
                        <dt class="font-medium" style="color: var(--muted);">Gênero</dt>
                        <dd class="font-semibold" style="color: var(--cream);">{{ $fragrance->gender->label() }}</dd>
                    </div>
                    @if($fragrance->concentration)
                        <div class="flex justify-between items-center">
                            <dt class="font-medium" style="color: var(--muted);">Concentração</dt>
                            <dd class="font-semibold" style="color: var(--cream);">{{ $fragrance->concentration }}</dd>
                        </div>
                    @endif
                    @if($fragrance->year)
                        <div class="flex justify-between items-center">
                            <dt class="font-medium" style="color: var(--muted);">Ano</dt>
                            <dd class="font-semibold" style="color: var(--cream);">{{ $fragrance->year }}</dd>
                        </div>
                    @endif
                    @if($fragrance->rating)
                        <div class="flex justify-between items-center">
                            <dt class="font-medium" style="color: var(--muted);">Avaliação</dt>
                            <dd class="text-amber-400 font-bold">{{ number_format($fragrance->rating, 1) }} / 5</dd>
                        </div>
                    @endif
                    @if($fragrance->inspired_by)
                        <div class="pt-4 mt-1" style="border-top: 1px solid rgba(212, 165, 64, 0.06);">
                            <dt class="text-[10px] mb-1 tracking-wide uppercase" style="color: var(--muted);">Inspirado em</dt>
                            <dd class="font-semibold" style="color: var(--gold-light);">{{ $fragrance->inspired_by }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- WhatsApp sidebar --}}
            @if($whatsappNumber)
                @php
                    $waNumber = preg_replace('/\D/', '', $whatsappNumber);
                    $waText = urlencode("Olá! Tenho interesse no perfume *{$fragrance->name}*" . ($fragrance->brand ? " da *{$fragrance->brand}*" : '') . ". Ele está disponível?");
                @endphp
                <a href="https://wa.me/55{{ $waNumber }}?text={{ $waText }}" target="_blank"
                   class="btn-whatsapp w-full flex items-center justify-center gap-2.5 px-6 py-4 text-white text-sm font-semibold rounded-2xl">
                    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Tenho interesse neste perfume
                </a>
            @endif
        </div>
    </div>

    {{-- Relacionados --}}
    @if($related->isNotEmpty())
        <div class="mt-16 animate-fade-up" style="animation-delay: 0.25s;">
            <div class="flex items-center gap-4 mb-6">
                <h2 class="text-[11px] uppercase tracking-[0.2em] font-semibold" style="color: var(--gold);">Você pode gostar</h2>
                <div class="flex-1 gold-line"></div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($related as $product)
                    <a href="{{ route('catalogo.show', $product->slug) }}" class="cat-card block group">
                        <div class="cat-card-img aspect-[4/5] flex items-center justify-center p-4">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                     class="w-full h-full object-contain drop-shadow-lg group-hover:scale-105 transition-transform duration-700 ease-out" loading="lazy">
                            @else
                                <svg class="w-8 h-8" style="color: var(--muted); opacity: 0.15;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                </svg>
                            @endif
                        </div>
                        <div class="px-4 pt-3 pb-4">
                            @if($product->brand)
                                <p class="text-[9px] uppercase tracking-[0.15em] font-medium mb-1" style="color: var(--gold);">{{ $product->brand }}</p>
                            @endif
                            <h3 class="text-[11px] font-semibold line-clamp-2 leading-snug" style="color: var(--cream);">{{ $product->name }}</h3>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
