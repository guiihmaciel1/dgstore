{{-- Single highlight card (used inside horizontal scroll) --}}
<a href="{{ route('catalogo.show', $product->slug) }}"
   class="snap-start shrink-0 w-[140px] sm:w-[160px] group"
   style="scroll-snap-align: start;">
    <div class="cat-card h-full flex flex-col">
        {{-- Image --}}
        <div class="cat-card-img aspect-[3/4] flex items-center justify-center p-3 sm:p-4">
            @if($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                     class="w-full h-full object-contain drop-shadow-lg group-hover:scale-105 transition-transform duration-500"
                     loading="lazy">
            @else
                <svg class="w-8 h-8 opacity-15" style="color: var(--muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
            @endif
        </div>

        {{-- Info --}}
        <div class="px-3 pt-2.5 pb-3.5 flex-1 flex flex-col">
            @if($product->brand)
                <p class="text-[9px] sm:text-[10px] uppercase tracking-[0.12em] font-medium truncate" style="color: var(--gold); opacity: 0.7;">
                    {{ $product->brand }}
                </p>
            @endif
            <h3 class="text-xs sm:text-[13px] font-semibold text-white leading-snug line-clamp-2 mt-0.5">
                {{ $product->name }}
            </h3>

            @if($product->pix_price)
                <div class="mt-auto pt-2">
                    <span class="text-xs sm:text-sm font-bold" style="color: var(--teal);">R$ {{ number_format($product->pix_price, 0, ',', '.') }}</span>
                    <span class="text-[9px] font-medium ml-0.5" style="color: var(--teal); opacity: 0.5;">PIX</span>
                </div>
            @endif
        </div>
    </div>
</a>
