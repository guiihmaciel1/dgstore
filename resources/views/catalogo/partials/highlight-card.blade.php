{{-- Single highlight card (used inside horizontal scroll) --}}
@php
    $hlOrigPrice = $product->original_price ?? ($product->sale_price ? (int)(ceil(((float)$product->sale_price * 1.2) / 10) * 10) : null);
    $hlDiscount = ($hlOrigPrice && $product->pix_price && $hlOrigPrice > 0)
        ? round(($hlOrigPrice - (float)$product->pix_price) / $hlOrigPrice * 100)
        : 0;
@endphp
<a href="{{ route('catalogo.show', $product->slug) }}"
   class="snap-start shrink-0 w-[160px] sm:w-[185px] group"
   style="scroll-snap-align: start;">
    <div class="cat-card h-full flex flex-col">
        {{-- Image --}}
        <div class="cat-card-img aspect-[4/5] flex items-center justify-center p-4 sm:p-5 relative">
            @if($hlDiscount >= 5)
                <div class="discount-pin discount-pin-sm">
                    <span class="discount-pin-value">{{ $hlDiscount }}%</span>
                    <span class="discount-pin-label">OFF</span>
                </div>
            @endif
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
        <div class="px-3.5 pt-3 pb-4 flex-1 flex flex-col">
            @if($product->brand)
                <p class="text-[10px] sm:text-[11px] uppercase tracking-[0.12em] font-medium mb-1 truncate" style="color: var(--gold);">
                    {{ $product->brand }}
                </p>
            @endif

            <h3 class="text-[13px] sm:text-sm font-semibold text-white leading-snug line-clamp-2">
                {{ $product->name }}
            </h3>

            @if($product->inspired_by)
                <p class="text-[10px] mt-1 truncate" style="color: var(--muted);">
                    Inspirado em {{ $product->inspired_by }}
                </p>
            @endif

            @if($product->rating)
                <div class="flex items-center gap-1 mt-2">
                    <svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    <span class="text-[11px] text-amber-400 font-semibold">{{ number_format($product->rating, 1) }}</span>
                </div>
            @endif

            {{-- Pricing --}}
            @if($product->sale_price && $product->pix_price)
                <div class="mt-auto pt-3" style="border-top: 1px solid rgba(212, 165, 64, 0.06); margin-top: auto;">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-[10px] line-through" style="color: var(--muted);">R$ {{ number_format($hlOrigPrice, 0, ',', '.') }}</span>
                        <span class="text-base font-bold text-white">R$ {{ number_format($product->sale_price, 0, ',', '.') }}</span>
                    </div>
                    <p class="text-[10px] mt-0.5" style="color: var(--muted);">
                        10x de R$ {{ number_format((float)$product->sale_price / 10, 0, ',', '.') }} s/ juros
                    </p>
                    <div class="mt-2 px-3 py-1.5 rounded-lg pix-badge">
                        <div class="flex items-baseline gap-0.5">
                            <span class="text-xs font-bold" style="color: var(--teal);">R$ {{ number_format($product->pix_price, 0, ',', '.') }}</span>
                            <span class="text-[9px] font-medium" style="color: var(--teal); opacity: 0.6;">PIX</span>
                        </div>
                        @if($hlDiscount >= 5)
                            <p class="text-[9px] font-bold mt-0.5" style="color: var(--teal-light);">-{{ $hlDiscount }}% economia</p>
                        @endif
                    </div>
                    @if($product->stock_quantity <= 0)
                        <p class="text-[9px] mt-1.5 font-medium tracking-wide" style="color: var(--gold);">
                            Sob encomenda · 3–5 dias
                        </p>
                    @else
                        <p class="text-[9px] mt-1.5 font-medium tracking-wide text-emerald-400">
                            ✅ Pronta entrega
                        </p>
                    @endif
                </div>
            @elseif($product->pix_price)
                <div class="mt-auto pt-3" style="border-top: 1px solid rgba(212, 165, 64, 0.06); margin-top: auto;">
                    <div class="px-3 py-1.5 rounded-lg pix-badge">
                        <span class="text-sm font-bold" style="color: var(--teal);">R$ {{ number_format($product->pix_price, 0, ',', '.') }}</span>
                        <span class="text-[9px] font-medium ml-0.5" style="color: var(--teal); opacity: 0.6;">PIX</span>
                    </div>
                    @if($product->stock_quantity <= 0)
                        <p class="text-[9px] mt-1.5 font-medium tracking-wide" style="color: var(--gold);">
                            Sob encomenda · 3–5 dias
                        </p>
                    @else
                        <p class="text-[9px] mt-1.5 font-medium tracking-wide text-emerald-400">
                            ✅ Pronta entrega
                        </p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</a>
