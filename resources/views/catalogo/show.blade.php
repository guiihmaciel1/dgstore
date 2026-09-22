@extends('catalogo.layouts.catalog')

@section('title', $fragrance->name . ' — ' . ($fragrance->brand ?? 'DG Perfumes'))
@section('description', Str::limit($fragrance->description ?? 'Conheça o perfume ' . $fragrance->name, 160))
@if($fragrance->image_url)
    @section('og_image', $fragrance->image_url)
@endif

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Voltar -->
    <a href="{{ route('catalogo.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-white transition mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Voltar ao catálogo
    </a>

    <!-- Hero -->
    <div class="rounded-2xl overflow-hidden border border-white/5 mb-8" style="background: #151515;">
        <div class="flex flex-col md:flex-row gap-8 p-6 md:p-8">
            {{-- Foto --}}
            <div class="flex-shrink-0 flex items-center justify-center">
                @if($fragrance->image_url)
                    <img src="{{ $fragrance->image_url }}" alt="{{ $fragrance->name }}"
                         class="w-48 md:w-56 h-auto object-contain rounded-lg">
                @else
                    <div class="w-48 h-64 bg-white/5 rounded-lg flex items-center justify-center">
                        <svg class="w-20 h-20 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3 mb-2">
                    @php
                        $catColor = match($fragrance->gender->value) {
                            'masculino' => 'blue',
                            'feminino' => 'pink',
                            default => 'purple',
                        };
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-{{ $catColor }}-500/20 text-{{ $catColor }}-400">
                        {{ $fragrance->gender->label() }}
                    </span>
                    @if($fragrance->year)
                        <span class="text-xs text-gray-500">{{ $fragrance->year }}</span>
                    @endif
                    @if($fragrance->concentration)
                        <span class="text-xs text-gray-500">{{ $fragrance->concentration }}</span>
                    @endif
                </div>

                <h1 class="text-2xl md:text-3xl font-bold text-white">{{ $fragrance->name }}</h1>

                @if($fragrance->brand)
                    <div class="flex items-center gap-2 mt-2">
                        @if($fragrance->brand_logo_url)
                            <img src="{{ $fragrance->brand_logo_url }}" alt="{{ $fragrance->brand }}" class="h-5 w-auto rounded">
                        @endif
                        <span class="text-gray-400">{{ $fragrance->brand }}</span>
                    </div>
                @endif

                @if($fragrance->rating)
                    <div class="flex items-center gap-3 mt-4">
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($fragrance->rating))
                                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @elseif($i - $fragrance->rating < 0.5)
                                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endif
                            @endfor
                        </div>
                        <span class="text-lg font-bold text-amber-400">{{ number_format($fragrance->rating, 2) }}</span>
                        <span class="text-sm text-gray-500">({{ number_format($fragrance->votes_count, 0, ',', '.') }} votos)</span>
                    </div>
                @endif

                @if($fragrance->sale_price)
                    <div class="mt-5">
                        <span class="text-3xl font-bold text-pink-400">R$ {{ number_format($fragrance->sale_price, 2, ',', '.') }}</span>
                    </div>
                @endif

                {{-- Botão WhatsApp --}}
                @if($whatsappNumber)
                    <div class="mt-6">
                        @php
                            $waNumber = preg_replace('/\D/', '', $whatsappNumber);
                            $waText = urlencode("Olá! Tenho interesse no perfume *{$fragrance->name}*" . ($fragrance->brand ? " da *{$fragrance->brand}*" : '') . ". Ele está disponível?");
                        @endphp
                        <a href="https://wa.me/55{{ $waNumber }}?text={{ $waText }}" target="_blank"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition shadow-lg shadow-green-600/20">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            Tenho interesse
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Coluna principal --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Principais Acordes --}}
            @if($fragrance->accords->isNotEmpty())
                <div class="rounded-2xl border border-white/5 p-6" style="background: #151515;">
                    <h2 class="text-lg font-bold text-white mb-5">Principais Acordes</h2>
                    <div class="space-y-2.5">
                        @foreach($fragrance->accords as $accord)
                            <div class="flex items-center gap-3">
                                <div class="flex-1 h-7 rounded-r-lg overflow-hidden bg-white/5">
                                    <div class="h-full rounded-r-lg flex items-center px-3 accord-bar"
                                         style="width: {{ $accord->percentage }}%; background: {{ $accord->color }};">
                                        <span class="text-xs font-medium text-white truncate" style="text-shadow: 0 1px 3px rgba(0,0,0,0.6);">
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
                <div class="rounded-2xl border border-white/5 p-6" style="background: #151515;">
                    <h2 class="text-lg font-bold text-white mb-5">Pirâmide Olfativa</h2>
                    @foreach([
                        'top' => ['label' => 'Notas de Topo', 'icon' => '△', 'color' => 'pink'],
                        'heart' => ['label' => 'Notas de Coração', 'icon' => '♡', 'color' => 'red'],
                        'base' => ['label' => 'Notas de Base', 'icon' => '▽', 'color' => 'amber'],
                    ] as $layer => $meta)
                        @php $layerNotes = $fragrance->notes->where('layer', $layer); @endphp
                        @if($layerNotes->isNotEmpty())
                            <div class="mb-6 last:mb-0">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="text-{{ $meta['color'] }}-400">{{ $meta['icon'] }}</span>
                                    <h3 class="text-sm font-semibold text-gray-300 uppercase tracking-wider">{{ $meta['label'] }}</h3>
                                </div>
                                <div class="flex flex-wrap gap-3">
                                    @foreach($layerNotes as $note)
                                        <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-white/5 border border-white/5 hover:border-white/10 transition">
                                            @if($note->image_url)
                                                <img src="{{ $note->image_url }}" alt="{{ $note->name }}" class="w-8 h-8 rounded-full object-cover bg-white/10">
                                            @endif
                                            <span class="text-sm text-gray-300">{{ $note->name }}</span>
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
                <div class="rounded-2xl border border-white/5 p-6" style="background: #151515;">
                    <h2 class="text-lg font-bold text-white mb-4">Sobre o Perfume</h2>
                    <p class="text-gray-400 leading-relaxed">{{ $fragrance->description }}</p>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Quando Usar --}}
            @if($fragrance->seasons || $fragrance->day_night)
                <div class="rounded-2xl border border-white/5 p-6" style="background: #151515;">
                    <h2 class="text-sm font-bold text-white mb-4">Quando Usar</h2>

                    @if($fragrance->seasons)
                        <div class="mb-4">
                            <h3 class="text-xs text-gray-500 uppercase tracking-wider mb-3">Estações</h3>
                            @php
                                $maxSeason = max($fragrance->seasons);
                                $seasonIcons = [
                                    'inverno' => '❄️',
                                    'primavera' => '🌸',
                                    'verao' => '☀️',
                                    'outono' => '🍂',
                                ];
                            @endphp
                            <div class="space-y-2">
                                @foreach($fragrance->seasons as $season => $votes)
                                    <div>
                                        <div class="flex items-center justify-between text-xs mb-1">
                                            <span class="text-gray-300 capitalize">{{ $seasonIcons[$season] ?? '' }} {{ $season }}</span>
                                            <span class="text-gray-500">{{ number_format($votes, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="h-2 rounded-full bg-white/5 overflow-hidden">
                                            <div class="h-full rounded-full bg-pink-500/60 accord-bar" style="width: {{ $maxSeason > 0 ? ($votes / $maxSeason) * 100 : 0 }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($fragrance->day_night)
                        <div>
                            <h3 class="text-xs text-gray-500 uppercase tracking-wider mb-3">Horário</h3>
                            @php
                                $maxDayNight = max($fragrance->day_night);
                                $periodIcons = ['dia' => '🌤️', 'noite' => '🌙'];
                            @endphp
                            <div class="space-y-2">
                                @foreach($fragrance->day_night as $period => $votes)
                                    <div>
                                        <div class="flex items-center justify-between text-xs mb-1">
                                            <span class="text-gray-300 capitalize">{{ $periodIcons[$period] ?? '' }} {{ $period }}</span>
                                            <span class="text-gray-500">{{ number_format($votes, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="h-2 rounded-full bg-white/5 overflow-hidden">
                                            <div class="h-full rounded-full bg-indigo-500/60 accord-bar" style="width: {{ $maxDayNight > 0 ? ($votes / $maxDayNight) * 100 : 0 }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Ficha Técnica --}}
            <div class="rounded-2xl border border-white/5 p-6" style="background: #151515;">
                <h2 class="text-sm font-bold text-white mb-4">Ficha Técnica</h2>
                <dl class="space-y-3 text-sm">
                    @if($fragrance->brand)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Marca</dt>
                            <dd class="text-gray-300">{{ $fragrance->brand }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Gênero</dt>
                        <dd class="text-gray-300">{{ $fragrance->gender->label() }}</dd>
                    </div>
                    @if($fragrance->concentration)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Concentração</dt>
                            <dd class="text-gray-300">{{ $fragrance->concentration }}</dd>
                        </div>
                    @endif
                    @if($fragrance->year)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Ano</dt>
                            <dd class="text-gray-300">{{ $fragrance->year }}</dd>
                        </div>
                    @endif
                    @if($fragrance->rating)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Avaliação</dt>
                            <dd class="text-amber-400 font-medium">{{ number_format($fragrance->rating, 2) }} / 5</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Botão WhatsApp (sidebar) --}}
            @if($whatsappNumber)
                @php
                    $waNumber = preg_replace('/\D/', '', $whatsappNumber);
                    $waText = urlencode("Olá! Tenho interesse no perfume *{$fragrance->name}*" . ($fragrance->brand ? " da *{$fragrance->brand}*" : '') . ". Ele está disponível?");
                @endphp
                <a href="https://wa.me/55{{ $waNumber }}?text={{ $waText }}" target="_blank"
                   class="w-full flex items-center justify-center gap-2 px-6 py-3.5 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition shadow-lg shadow-green-600/20">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Tenho interesse neste perfume
                </a>
            @endif
        </div>
    </div>

    {{-- Perfumes Relacionados --}}
    @if($related->isNotEmpty())
        <div class="mt-12">
            <h2 class="text-lg font-bold text-white mb-6">Você pode gostar</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($related as $product)
                    <a href="{{ route('catalogo.show', $product->slug) }}" class="card-hover block rounded-xl overflow-hidden border border-white/5" style="background: #151515;">
                        <div class="aspect-[3/4] bg-white/5 overflow-hidden flex items-center justify-center">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-contain p-3" loading="lazy">
                            @else
                                <svg class="w-12 h-12 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                </svg>
                            @endif
                        </div>
                        <div class="p-3">
                            <h3 class="text-xs font-semibold text-white line-clamp-2">{{ $product->name }}</h3>
                            @if($product->brand)
                                <p class="text-[10px] text-gray-500 mt-0.5">{{ $product->brand }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
