@php
    $isAdmin = auth()->check() && auth()->user()->isAdminGeral();
@endphp

@if($isAdmin)
@php
    $radarData = app(\App\Domain\System\Services\RadarPyService::class)->getCached();
    $hasData = !empty($radarData);
    $firstProduct = $hasData ? array_values($radarData)[0] : null;
    $bestOffer = $firstProduct ? ($firstProduct['offers'][0] ?? null) : null;
@endphp

@if($hasData)
<div x-data="{ radarOpen: false }">
    {{-- Banner compacto --}}
    <div class="bg-gradient-to-r from-blue-900/40 via-blue-800/30 to-blue-900/40 border-b border-blue-500/10">
        <div class="px-6 lg:px-8">
            <button @click="radarOpen = true" class="w-full flex items-center justify-between py-1.5 group">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <circle cx="12" cy="12" r="6"/>
                            <circle cx="12" cy="12" r="2"/>
                        </svg>
                        <span class="text-[11px] font-bold text-blue-400 tracking-wide uppercase">Radar PY</span>
                    </div>
                    <span class="text-[11px] text-dg-300 truncate">
                        @foreach($radarData as $data)
                            <span class="inline-flex items-center gap-1">
                                <span class="font-semibold text-white">{{ $data['product_name'] }}</span>
                                @if(!empty($data['offers']))
                                    @php $best = $data['offers'][0]; @endphp
                                    <span class="text-dg-400">a partir de</span>
                                    <span class="font-bold text-emerald-400">US$ {{ number_format($best['price_usd'], 0, ',', '.') }}</span>
                                    <span class="text-dg-500">(R$ {{ number_format($best['price_brl'], 0, ',', '.') }})</span>
                                    @if(!empty($best['store_name']))
                                        <span class="text-dg-400">-</span>
                                        <span class="text-blue-300">{{ $best['store_name'] }}</span>
                                    @endif
                                @endif
                            </span>
                            @if(!$loop->last)
                                <span class="text-dg-600 mx-2">|</span>
                            @endif
                        @endforeach
                    </span>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0 ml-3">
                    @if($firstProduct)
                        <span class="text-[9px] text-dg-500">{{ $firstProduct['fetched_at'] }}</span>
                    @endif
                    <span class="text-[10px] text-blue-400 group-hover:text-white transition font-medium">Ver detalhes →</span>
                </div>
            </button>
        </div>
    </div>

    {{-- Modal --}}
    <div x-show="radarOpen" x-transition.opacity.duration.200ms @click="radarOpen = false"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[60]" x-cloak></div>

    <div x-show="radarOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         @keydown.escape.window="radarOpen = false"
         class="fixed inset-4 sm:inset-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 sm:w-full sm:max-w-3xl sm:max-h-[85vh] bg-surface-overlay border border-border rounded-2xl shadow-2xl z-[61] flex flex-col overflow-hidden"
         x-cloak>

        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-border/50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <circle cx="12" cy="12" r="10"/>
                        <circle cx="12" cy="12" r="6"/>
                        <circle cx="12" cy="12" r="2"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-white">Radar PY</h2>
                    <p class="text-[11px] text-dg-400">Menores preços no Paraguai &middot; Compras Paraguai</p>
                </div>
            </div>
            <button @click="radarOpen = false" class="p-1.5 rounded-lg text-dg-400 hover:text-white hover:bg-surface-elevated transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Modal Content --}}
        <div class="flex-1 overflow-y-auto" style="scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.1) transparent;">
            @foreach($radarData as $productId => $data)
                <div class="{{ !$loop->first ? 'border-t border-border/30' : '' }}">
                    {{-- Product header --}}
                    <div class="px-5 py-3 bg-surface-elevated/20 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-white">{{ $data['product_name'] }}</span>
                            <span class="text-[10px] text-dg-500 bg-surface-overlay px-2 py-0.5 rounded-full">{{ count($data['offers']) }} ofertas</span>
                        </div>
                        <a href="{{ $data['url'] }}" target="_blank" rel="noopener noreferrer"
                           class="text-[10px] text-blue-400 hover:text-white transition font-medium">
                            Ver no site →
                        </a>
                    </div>

                    {{-- Offers table --}}
                    <div class="px-5">
                        <table class="w-full">
                            <thead>
                                <tr class="text-[10px] text-dg-500 uppercase tracking-wider">
                                    <th class="py-2 text-left w-8">#</th>
                                    <th class="py-2 text-left">Anúncio</th>
                                    <th class="py-2 text-right">US$</th>
                                    <th class="py-2 text-right">R$</th>
                                    <th class="py-2 text-right">Loja</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border/10">
                                @foreach($data['offers'] as $index => $offer)
                                    <tr class="hover:bg-surface-elevated/30 transition-colors">
                                        <td class="py-2.5 text-[11px] text-dg-500 font-mono">{{ $index + 1 }}</td>
                                        <td class="py-2.5 text-[12px] text-dg-200 pr-3 max-w-[250px]">
                                            <span class="line-clamp-1">{{ $offer['product_name'] ?? '-' }}</span>
                                        </td>
                                        <td class="py-2.5 text-right">
                                            <span class="text-[13px] font-bold {{ $index === 0 ? 'text-emerald-400' : 'text-white' }}">
                                                {{ number_format($offer['price_usd'], 2, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="py-2.5 text-right">
                                            <span class="text-[12px] text-dg-400">
                                                {{ number_format($offer['price_brl'], 2, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="py-2.5 text-right">
                                            <span class="text-[11px] font-medium {{ $index === 0 ? 'text-blue-300' : 'text-dg-400' }}">
                                                {{ $offer['store_name'] ?? '-' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Modal Footer --}}
        <div class="px-5 py-3 border-t border-border/50 flex items-center justify-between bg-surface-elevated/20">
            <span class="text-[10px] text-dg-500">
                Fonte: <a href="https://www.comprasparaguai.com.br" target="_blank" rel="noopener noreferrer" class="text-dg-400 hover:text-white transition">comprasparaguai.com.br</a>
                &middot; Atualizado via IA (Gemini)
            </span>
            @if($firstProduct)
                <span class="text-[10px] text-dg-500">{{ $firstProduct['fetched_at'] }}</span>
            @endif
        </div>
    </div>
</div>
@endif
@endif
