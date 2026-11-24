{{-- Bloco de preço dos cards do catálogo: um preço principal (PIX) + parcelamento + estoque --}}
@php
    $mainPrice = $product->pix_price ?: $product->sale_price;
    $isPix = (bool) $product->pix_price;
    $discount = $product->catalog_discount_percent;
@endphp
@if($mainPrice)
    <div class="card-price {{ ($size ?? 'md') === 'sm' ? 'card-price-sm' : '' }}">
        @if($discount >= 5)
            <p class="card-price-from">R$ {{ number_format((float) $product->original_price, 0, ',', '.') }}</p>
        @endif
        <p class="card-price-main">
            <span class="card-price-value">R$ {{ number_format((float) $mainPrice, 0, ',', '.') }}</span>
            @if($isPix)
                <span class="card-price-pix">no PIX</span>
            @endif
        </p>
        @if($product->sale_price)
            <p class="card-price-installments">
                ou 10x de R$ {{ number_format((float) $product->sale_price / 10, 0, ',', '.') }} {{ ($size ?? 'md') === 'sm' ? 's/ juros' : 'sem juros' }}
            </p>
        @endif
        <p class="card-stock {{ $product->stock_quantity > 0 ? 'card-stock-ready' : 'card-stock-order' }}">
            {{ $product->stock_quantity > 0 ? 'Pronta entrega' : 'Sob encomenda · 3–5 dias' }}
        </p>
    </div>
@endif
