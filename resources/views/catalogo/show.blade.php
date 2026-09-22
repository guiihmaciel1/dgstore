@extends('catalogo.layouts.catalog')

@section('title', $fragrance->name . ' — ' . ($fragrance->brand ?? 'DG Imports'))
@section('description', Str::limit($fragrance->description ?? 'Conheça o perfume ' . $fragrance->name, 160))
@if($fragrance->image_url)
    @section('og_image', $fragrance->image_url)
@endif

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-5">

    {{-- Back --}}
    <div class="pt-6 pb-4 sm:pt-8">
        <a href="{{ route('catalogo.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium transition" style="color: var(--muted);"
           onmouseover="this.style.color='var(--cream)'" onmouseout="this.style.color='var(--muted)'">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Catálogo
        </a>
    </div>

    {{-- Hero --}}
    <div class="rounded-2xl overflow-hidden mb-6" style="background: var(--surface);">
        <div class="flex flex-col sm:flex-row">
            {{-- Image --}}
            <div class="flex items-center justify-center sm:w-72 md:w-80 p-8 sm:p-10" style="background: var(--surface-elevated);">
                @if($fragrance->image_url)
                    <img src="{{ $fragrance->image_url }}" alt="{{ $fragrance->name }}"
                         class="w-48 sm:w-56 h-auto object-contain drop-shadow-2xl">
                @else
                    <div class="w-48 h-64 rounded-lg flex items-center justify-center" style="background: rgba(255,255,255,0.03);">
                        <svg class="w-16 h-16" style="color: var(--muted); opacity: 0.2;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1 p-5 sm:p-8">
                {{-- Brand --}}
                @if($fragrance->brand)
                    <div class="flex items-center gap-2 mb-2">
                        @if($fragrance->brand_logo_url)
                            <img src="{{ $fragrance->brand_logo_url }}" alt="{{ $fragrance->brand }}" class="h-4 w-auto rounded opacity-60">
                        @endif
                        <span class="text-xs uppercase tracking-wider font-medium" style="color: var(--gold);">{{ $fragrance->brand }}</span>
                    </div>
                @endif

                {{-- Name --}}
                <h1 class="font-serif text-2xl sm:text-3xl font-semibold" style="color: var(--cream);">{{ $fragrance->name }}</h1>

                {{-- Meta badges --}}
                <div class="flex flex-wrap items-center gap-2 mt-3">
                    @php
                        $badgeClass = match($fragrance->gender->value) {
                            'masculino' => 'perfume-badge-masc',
                            'feminino' => 'perfume-badge-fem',
                            default => 'perfume-badge-uni',
                        };
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium {{ $badgeClass }}">
                        {{ $fragrance->gender->label() }}
                    </span>
                    @if($fragrance->concentration)
                        <span class="text-[11px] px-2.5 py-1 rounded-full" style="background: rgba(255,255,255,0.04); color: var(--muted);">{{ $fragrance->concentration }}</span>
                    @endif
                    @if($fragrance->year)
                        <span class="text-[11px] px-2.5 py-1 rounded-full" style="background: rgba(255,255,255,0.04); color: var(--muted);">{{ $fragrance->year }}</span>
                    @endif
                </div>

                {{-- Inspired by --}}
                @if($fragrance->inspired_by)
                    <div class="flex items-center gap-1.5 mt-3">
                        <span class="text-[11px]" style="color: var(--muted);">Inspirado em</span>
                        <span class="text-[11px] font-medium" style="color: var(--gold-light);">{{ $fragrance->inspired_by }}</span>
                    </div>
                @endif

                {{-- Rating --}}
                @if($fragrance->rating)
                    <div class="flex items-center gap-2 mt-4">
                        <div class="flex items-center gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= floor($fragrance->rating) ? 'text-amber-500' : ($i - $fragrance->rating < 0.5 ? 'text-amber-500' : 'text-white/10') }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <span class="text-sm font-semibold text-amber-500">{{ number_format($fragrance->rating, 1) }}</span>
                        <span class="text-[11px]" style="color: var(--muted);">{{ number_format($fragrance->votes_count, 0, ',', '.') }} votos</span>
                    </div>
                @endif

                {{-- Pricing --}}
                @if($fragrance->sale_price && $fragrance->pix_price)
                    @php
                        $originalPrice = $fragrance->original_price ?? (int)(ceil(((float)$fragrance->sale_price * 1.2) / 10) * 10);
                        $realDiscount = $originalPrice > 0 ? round((($originalPrice - (float)$fragrance->pix_price) / $originalPrice) * 100) : 0;
                    @endphp
                    <div class="mt-6 pt-5 border-t border-white/[0.04]">
                        {{-- Installment --}}
                        <div class="flex items-baseline gap-2">
                            <span class="text-xs line-through" style="color: var(--muted);">R$ {{ number_format($originalPrice, 0, ',', '.') }}</span>
                            <span class="text-2xl sm:text-3xl font-bold text-white">R$ {{ number_format($fragrance->sale_price, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-xs mt-1" style="color: var(--muted);">
                            em até <span class="font-semibold text-white">10x</span> de
                            <span class="font-semibold text-white">R$ {{ number_format((float)$fragrance->sale_price / 10, 2, ',', '.') }}</span> sem juros
                        </p>

                        {{-- PIX --}}
                        <div class="mt-3 px-4 py-3 rounded-xl" style="background: rgba(16, 185, 129, 0.06); border: 1px solid rgba(16, 185, 129, 0.1);">
                            <div class="flex items-baseline gap-2">
                                <span class="text-lg sm:text-xl font-bold text-emerald-400">R$ {{ number_format($fragrance->pix_price, 0, ',', '.') }}</span>
                                <span class="text-xs text-emerald-400/70">à vista no PIX</span>
                            </div>
                            <p class="text-[11px] text-emerald-500/60 mt-0.5">{{ $realDiscount }}% de desconto</p>
                        </div>
                    </div>
                @elseif($fragrance->pix_price)
                    <div class="mt-6 pt-5 border-t border-white/[0.04]">
                        <span class="text-2xl font-bold text-emerald-400">R$ {{ number_format($fragrance->pix_price, 0, ',', '.') }}</span>
                        <span class="text-xs ml-1" style="color: var(--muted);">à vista no PIX</span>
                    </div>
                @endif

                {{-- WhatsApp --}}
                @if($whatsappNumber)
                    @php
                        $waNumber = preg_replace('/\D/', '', $whatsappNumber);
                        $waText = urlencode("Olá! Tenho interesse no perfume *{$fragrance->name}*" . ($fragrance->brand ? " da *{$fragrance->brand}*" : '') . ". Ele está disponível?");
                    @endphp
                    <a href="https://wa.me/55{{ $waNumber }}?text={{ $waText }}" target="_blank"
                       class="btn-whatsapp mt-5 inline-flex items-center gap-2 px-6 py-3 text-white text-sm font-semibold rounded-xl">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Tenho interesse
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Content sections --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Main column --}}
        <div class="lg:col-span-2 space-y-5">
            {{-- Acordes --}}
            @if($fragrance->accords->isNotEmpty())
                <div class="rounded-2xl p-5 sm:p-6" style="background: var(--surface);">
                    <h2 class="text-sm font-semibold mb-4" style="color: var(--gold);">Principais Acordes</h2>
                    <div class="space-y-2">
                        @foreach($fragrance->accords as $accord)
                            <div class="flex items-center gap-3">
                                <div class="flex-1 h-7 rounded-lg overflow-hidden" style="background: rgba(255,255,255,0.03);">
                                    <div class="h-full rounded-lg flex items-center px-3 accord-bar"
                                         style="width: {{ $accord->percentage }}%; background: {{ $accord->color }};">
                                        <span class="text-[11px] font-medium text-white truncate" style="text-shadow: 0 1px 3px rgba(0,0,0,0.5);">
                                            {{ $accord->name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Pirâmide Olfativa --}}
            @if($fragrance->notes->isNotEmpty())
                <div class="rounded-2xl p-5 sm:p-6" style="background: var(--surface);">
                    <h2 class="text-sm font-semibold mb-5" style="color: var(--gold);">Pirâmide Olfativa</h2>
                    @foreach([
                        'top' => ['label' => 'Notas de Topo', 'icon' => '△'],
                        'heart' => ['label' => 'Notas de Coração', 'icon' => '♡'],
                        'base' => ['label' => 'Notas de Base', 'icon' => '▽'],
                    ] as $layer => $meta)
                        @php $layerNotes = $fragrance->notes->where('layer', $layer); @endphp
                        @if($layerNotes->isNotEmpty())
                            <div class="mb-5 last:mb-0">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="text-xs" style="color: var(--gold);">{{ $meta['icon'] }}</span>
                                    <h3 class="text-[11px] font-semibold uppercase tracking-wider" style="color: var(--muted);">{{ $meta['label'] }}</h3>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($layerNotes as $note)
                                        <div class="flex items-center gap-2 px-3 py-2 rounded-xl transition" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.04);">
                                            @if($note->image_url)
                                                <img src="{{ $note->image_url }}" alt="{{ $note->name }}" class="w-7 h-7 rounded-full object-cover" style="background: rgba(255,255,255,0.05);">
                                            @endif
                                            <span class="text-xs" style="color: var(--cream);">{{ $note->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

            {{-- Descrição --}}
            @if($fragrance->description)
                <div class="rounded-2xl p-5 sm:p-6" style="background: var(--surface);">
                    <h2 class="text-sm font-semibold mb-3" style="color: var(--gold);">Sobre</h2>
                    <p class="text-sm leading-relaxed" style="color: var(--muted);">{{ $fragrance->description }}</p>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">
            {{-- Quando Usar --}}
            @if($fragrance->seasons || $fragrance->day_night)
                <div class="rounded-2xl p-5 sm:p-6" style="background: var(--surface);">
                    <h2 class="text-sm font-semibold mb-4" style="color: var(--gold);">Quando Usar</h2>

                    @if($fragrance->seasons)
                        <div class="mb-4">
                            <h3 class="text-[10px] font-semibold uppercase tracking-wider mb-3" style="color: var(--muted);">Estações</h3>
                            @php
                                $maxSeason = max($fragrance->seasons);
                                $seasonIcons = ['inverno' => '❄️', 'primavera' => '🌸', 'verao' => '☀️', 'outono' => '🍂'];
                            @endphp
                            <div class="space-y-2.5">
                                @foreach($fragrance->seasons as $season => $votes)
                                    <div>
                                        <div class="flex items-center justify-between text-[11px] mb-1">
                                            <span class="capitalize" style="color: var(--cream);">{{ $seasonIcons[$season] ?? '' }} {{ $season }}</span>
                                            <span style="color: var(--muted);">{{ number_format($votes, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="h-1.5 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.04);">
                                            <div class="h-full rounded-full accord-bar" style="width: {{ $maxSeason > 0 ? ($votes / $maxSeason) * 100 : 0 }}%; background: var(--gold);"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($fragrance->day_night)
                        <div>
                            <h3 class="text-[10px] font-semibold uppercase tracking-wider mb-3" style="color: var(--muted);">Horário</h3>
                            @php $maxDayNight = max($fragrance->day_night); @endphp
                            <div class="space-y-2.5">
                                @foreach($fragrance->day_night as $period => $votes)
                                    <div>
                                        <div class="flex items-center justify-between text-[11px] mb-1">
                                            <span class="capitalize" style="color: var(--cream);">{{ $period === 'dia' ? '🌤️' : '🌙' }} {{ $period }}</span>
                                            <span style="color: var(--muted);">{{ number_format($votes, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="h-1.5 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.04);">
                                            <div class="h-full rounded-full accord-bar" style="width: {{ $maxDayNight > 0 ? ($votes / $maxDayNight) * 100 : 0 }}%; background: var(--gold-dark);"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Ficha Técnica --}}
            <div class="rounded-2xl p-5 sm:p-6" style="background: var(--surface);">
                <h2 class="text-sm font-semibold mb-4" style="color: var(--gold);">Ficha Técnica</h2>
                <dl class="space-y-3 text-[13px]">
                    @if($fragrance->brand)
                        <div class="flex justify-between">
                            <dt style="color: var(--muted);">Marca</dt>
                            <dd style="color: var(--cream);">{{ $fragrance->brand }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <dt style="color: var(--muted);">Gênero</dt>
                        <dd style="color: var(--cream);">{{ $fragrance->gender->label() }}</dd>
                    </div>
                    @if($fragrance->concentration)
                        <div class="flex justify-between">
                            <dt style="color: var(--muted);">Concentração</dt>
                            <dd style="color: var(--cream);">{{ $fragrance->concentration }}</dd>
                        </div>
                    @endif
                    @if($fragrance->year)
                        <div class="flex justify-between">
                            <dt style="color: var(--muted);">Ano</dt>
                            <dd style="color: var(--cream);">{{ $fragrance->year }}</dd>
                        </div>
                    @endif
                    @if($fragrance->rating)
                        <div class="flex justify-between">
                            <dt style="color: var(--muted);">Avaliação</dt>
                            <dd class="text-amber-500 font-medium">{{ number_format($fragrance->rating, 1) }} / 5</dd>
                        </div>
                    @endif
                    @if($fragrance->inspired_by)
                        <div class="pt-3 mt-1 border-t border-white/[0.04]">
                            <dt class="text-[11px] mb-0.5" style="color: var(--muted);">Inspirado em</dt>
                            <dd class="font-medium" style="color: var(--gold-light);">{{ $fragrance->inspired_by }}</dd>
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
                   class="btn-whatsapp w-full flex items-center justify-center gap-2 px-5 py-3.5 text-white text-sm font-semibold rounded-xl">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Tenho interesse neste perfume
                </a>
            @endif
        </div>
    </div>

    {{-- Relacionados --}}
    @if($related->isNotEmpty())
        <div class="mt-12">
            <h2 class="text-sm font-semibold mb-5" style="color: var(--gold);">Você pode gostar</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                @foreach($related as $product)
                    <a href="{{ route('catalogo.show', $product->slug) }}" class="cat-card block rounded-xl overflow-hidden group">
                        <div class="aspect-square overflow-hidden flex items-center justify-center" style="background: var(--surface-elevated);">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                     class="w-full h-full object-contain p-4 group-hover:scale-105 transition-transform duration-500" loading="lazy">
                            @else
                                <svg class="w-10 h-10" style="color: var(--muted); opacity: 0.2;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                </svg>
                            @endif
                        </div>
                        <div class="p-3">
                            @if($product->brand)
                                <p class="text-[9px] uppercase tracking-wider mb-0.5" style="color: var(--gold);">{{ $product->brand }}</p>
                            @endif
                            <h3 class="text-xs font-semibold line-clamp-2" style="color: var(--cream);">{{ $product->name }}</h3>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
