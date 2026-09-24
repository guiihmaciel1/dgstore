{{-- Highlighted sections: Destaques + Mais Procurados + Mais Vendidos --}}
@if($featured->isNotEmpty() || $mostWanted->isNotEmpty() || $bestSellers->isNotEmpty())
<div class="space-y-12 mb-14">

    {{-- Destaques (em estoque) --}}
    @if($featured->isNotEmpty())
        <section class="animate-fade-up" style="animation-delay: 0.05s;">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg" style="background: rgba(16, 185, 129, 0.10);">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg sm:text-xl font-bold font-serif" style="background: linear-gradient(135deg, #34d399, #10b981); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Destaques</h2>
                    <span class="text-[10px] font-medium tracking-wider uppercase px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/15">Pronta entrega</span>
                </div>
                <div class="hidden sm:flex items-center gap-1.5">
                    <button onclick="scrollCarousel('featured', -1)" class="carousel-arrow" aria-label="Anterior">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button onclick="scrollCarousel('featured', 1)" class="carousel-arrow" aria-label="Próximo">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

            <div class="relative -mx-5 px-5 sm:mx-0 sm:px-0">
                <div id="featured" class="carousel-track flex gap-3.5 sm:gap-4 overflow-x-auto hide-scrollbar pb-2 snap-x snap-mandatory">
                    @foreach($featured as $product)
                        @include('catalogo.partials.highlight-card', ['product' => $product])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Mais Procurados --}}
    @if($mostWanted->isNotEmpty())
        <section class="animate-fade-up" style="animation-delay: 0.1s;">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg" style="background: rgba(46, 196, 182, 0.10);">
                        <svg class="w-4 h-4" style="color: var(--teal);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <h2 class="text-lg sm:text-xl font-bold font-serif" style="background: linear-gradient(135deg, var(--teal-light), var(--teal)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Mais Procurados</h2>
                </div>
                <div class="hidden sm:flex items-center gap-1.5">
                    <button onclick="scrollCarousel('most-wanted', -1)" class="carousel-arrow" aria-label="Anterior">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button onclick="scrollCarousel('most-wanted', 1)" class="carousel-arrow" aria-label="Próximo">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

            <div class="relative -mx-5 px-5 sm:mx-0 sm:px-0">
                <div id="most-wanted" class="carousel-track flex gap-3.5 sm:gap-4 overflow-x-auto hide-scrollbar pb-2 snap-x snap-mandatory">
                    @foreach($mostWanted as $product)
                        @include('catalogo.partials.highlight-card', ['product' => $product])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Mais Vendidos --}}
    @if($bestSellers->isNotEmpty())
        <section class="animate-fade-up" style="animation-delay: 0.15s;">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg" style="background: rgba(212, 165, 64, 0.10);">
                        <svg class="w-4 h-4" style="color: var(--gold);" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg sm:text-xl font-bold text-gold-gradient font-serif">Mais Vendidos</h2>
                </div>
                <div class="hidden sm:flex items-center gap-1.5">
                    <button onclick="scrollCarousel('best-sellers', -1)" class="carousel-arrow" aria-label="Anterior">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button onclick="scrollCarousel('best-sellers', 1)" class="carousel-arrow" aria-label="Próximo">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

            <div class="relative -mx-5 px-5 sm:mx-0 sm:px-0">
                <div id="best-sellers" class="carousel-track flex gap-3.5 sm:gap-4 overflow-x-auto hide-scrollbar pb-2 snap-x snap-mandatory">
                    @foreach($bestSellers as $product)
                        @include('catalogo.partials.highlight-card', ['product' => $product])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <div class="gold-line max-w-[200px] mx-auto"></div>
</div>

@push('scripts')
<script>
function scrollCarousel(id, direction) {
    const track = document.getElementById(id);
    if (!track) return;
    const card = track.querySelector('a');
    const step = card ? (card.offsetWidth + 16) * 2 : 340;
    track.scrollBy({ left: direction * step, behavior: 'smooth' });
}

document.querySelectorAll('.carousel-track').forEach(function(track) {
    let isDown = false, startX, scrollLeft, moved = false;

    track.addEventListener('mousedown', function(e) {
        isDown = true; moved = false;
        track.style.cursor = 'grabbing';
        track.style.scrollSnapType = 'none';
        startX = e.pageX - track.offsetLeft;
        scrollLeft = track.scrollLeft;
    });

    track.addEventListener('mouseleave', function() {
        isDown = false;
        track.style.cursor = '';
        track.style.scrollSnapType = '';
    });

    track.addEventListener('mouseup', function() {
        isDown = false;
        track.style.cursor = '';
        track.style.scrollSnapType = '';
    });

    track.addEventListener('mousemove', function(e) {
        if (!isDown) return;
        e.preventDefault();
        moved = true;
        var x = e.pageX - track.offsetLeft;
        track.scrollLeft = scrollLeft - (x - startX);
    });

    track.addEventListener('click', function(e) {
        if (moved) e.preventDefault();
    }, true);
});
</script>
@endpush
@endif
