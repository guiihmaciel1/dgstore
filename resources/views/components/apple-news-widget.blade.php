@props(['news' => []])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
            </svg>
            <h3 class="text-base sm:text-lg font-semibold text-gray-900">Notícias Apple</h3>
        </div>
        <a href="https://www.macrumors.com" target="_blank" rel="noopener noreferrer"
           class="text-xs text-gray-400 hover:text-gray-600 transition-colors">
            Ver mais →
        </a>
    </div>

    @if(count($news) > 0)
        <div class="space-y-3 max-h-[420px] overflow-y-auto pr-1" style="scrollbar-width: thin;">
            @foreach(array_slice($news, 0, 8) as $item)
                <a href="{{ $item['link'] }}" target="_blank" rel="noopener noreferrer"
                   class="block p-3 rounded-lg hover:bg-gray-50 transition-colors group border border-transparent hover:border-gray-100">
                    <div class="flex gap-3">
                        @if(!empty($item['image_url']))
                            <div class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden bg-gray-100">
                                <img src="{{ $item['image_url'] }}" alt=""
                                     class="w-full h-full object-cover"
                                     loading="lazy"
                                     onerror="this.parentElement.style.display='none'">
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-2 leading-snug">
                                {{ $item['title'] }}
                            </p>
                            <div class="flex items-center gap-2 mt-1.5">
                                @php
                                    $sourceColors = [
                                        'MacRumors' => 'bg-purple-100 text-purple-700',
                                        '9to5Mac' => 'bg-blue-100 text-blue-700',
                                        'MacMagazine' => 'bg-green-100 text-green-700',
                                    ];
                                    $colorClass = $sourceColors[$item['source']] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium {{ $colorClass }}">
                                    {{ $item['source'] }}
                                </span>
                                <span class="text-[11px] text-gray-400">
                                    {{ \Carbon\Carbon::parse($item['date'])->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="text-center py-8">
            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
            </svg>
            <p class="text-sm text-gray-400">Nenhuma notícia disponível.</p>
            <p class="text-xs text-gray-300 mt-1">As notícias serão carregadas automaticamente.</p>
        </div>
    @endif
</div>
