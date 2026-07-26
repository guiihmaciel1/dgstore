{{-- Dropdown de notícias Apple no navbar --}}
@php
    $newsItems = \Illuminate\Support\Facades\Cache::get('apple_news_items', []);
    $newsCount = count($newsItems);
    $hasUnread = $newsCount > 0;
@endphp

<div x-data="{ newsOpen: false }" class="relative">
    <button @click="newsOpen = !newsOpen"
            class="relative flex items-center text-dg-300 hover:text-white transition p-1 rounded-lg"
            :class="newsOpen ? 'text-white bg-surface-overlay' : ''"
            title="Novidades Apple">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
        </svg>
        @if($hasUnread)
            <span class="absolute -top-0.5 -right-0.5 flex h-3.5 w-3.5 items-center justify-center rounded-full bg-dg-500 text-[9px] font-bold text-white ring-2 ring-surface-raised">
                {{ min($newsCount, 9) }}{{ $newsCount > 9 ? '+' : '' }}
            </span>
        @endif
    </button>

    <div x-show="newsOpen"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="transform opacity-0 scale-95 -translate-y-1"
         x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="transform opacity-0 scale-95 -translate-y-1"
         @click.away="newsOpen = false"
         class="absolute right-0 mt-2 w-96 max-h-[32rem] rounded-xl bg-surface-overlay border border-border z-50 shadow-2xl overflow-hidden flex flex-col"
         x-cloak>

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-border/50 bg-surface-elevated/30">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-dg-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                </svg>
                <span class="text-sm font-semibold text-white">Novidades Apple</span>
            </div>
            <span class="text-[10px] text-dg-400 font-medium tracking-wide uppercase">RSS ao vivo</span>
        </div>

        {{-- News List --}}
        <div class="overflow-y-auto flex-1" style="scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.1) transparent;">
            @if($newsCount > 0)
                @foreach(array_slice($newsItems, 0, 10) as $item)
                    <a href="{{ $item['link'] }}" target="_blank" rel="noopener noreferrer"
                       class="flex gap-3 px-4 py-3 hover:bg-surface-elevated/50 transition-colors border-b border-border/20 last:border-0 group">
                        @if(!empty($item['image_url']))
                            <div class="flex-shrink-0 w-14 h-14 rounded-lg overflow-hidden bg-surface-elevated">
                                <img src="{{ $item['image_url'] }}" alt=""
                                     class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity"
                                     loading="lazy"
                                     onerror="this.parentElement.style.display='none'">
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-[13px] font-medium text-gray-200 group-hover:text-white transition-colors line-clamp-2 leading-snug">
                                {{ $item['title'] }}
                            </p>
                            <div class="flex items-center gap-2 mt-1.5">
                                @php
                                    $sourceColors = [
                                        'MacRumors' => 'bg-purple-500/20 text-purple-300',
                                        '9to5Mac' => 'bg-blue-500/20 text-blue-300',
                                        'MacMagazine' => 'bg-green-500/20 text-green-300',
                                    ];
                                    $colorClass = $sourceColors[$item['source']] ?? 'bg-gray-500/20 text-gray-300';
                                @endphp
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium {{ $colorClass }}">
                                    {{ $item['source'] }}
                                </span>
                                <span class="text-[10px] text-dg-400">
                                    {{ \Carbon\Carbon::parse($item['date'])->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            @else
                <div class="flex flex-col items-center justify-center py-10 px-4">
                    <svg class="w-8 h-8 text-dg-500/50 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                    <p class="text-xs text-dg-400">Nenhuma notícia ainda.</p>
                    <p class="text-[10px] text-dg-500 mt-1">Atualizações aparecem automaticamente.</p>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        @if($newsCount > 0)
            <div class="px-4 py-2.5 border-t border-border/50 bg-surface-elevated/20">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] text-dg-500">
                        {{ $newsCount }} notícia{{ $newsCount !== 1 ? 's' : '' }} disponíve{{ $newsCount !== 1 ? 'is' : 'l' }}
                    </span>
                    <a href="https://www.macrumors.com" target="_blank" rel="noopener noreferrer"
                       class="text-[10px] text-dg-400 hover:text-white transition-colors font-medium">
                        Ver todas →
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
