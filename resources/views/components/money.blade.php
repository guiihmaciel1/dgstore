{{-- Valor em reais que respeita o "Ocultar valores" (variável Alpine showValues). --}}
@props(['value' => 0, 'decimals' => 2, 'prefix' => 'R$ ', 'sign' => false])
@php
    $number = (float) $value;
    $formatted = number_format(abs($number), $decimals, ',', '.');
    $signChar = $number < 0 ? '− ' : ($sign && $number > 0 ? '+ ' : '');
@endphp
<span {{ $attributes->merge(['class' => 'tabular-nums']) }}><span x-show="showValues">{{ $signChar }}{{ $prefix }}{{ $formatted }}</span><span x-show="!showValues" x-cloak class="dg-hidden-value">{{ $prefix }}&bull;&bull;&bull;</span></span>