@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-dg-200 text-sm font-medium leading-5 text-dg-100 focus:outline-none focus:border-dg-100 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-dg-400 hover:text-dg-200 hover:border-dg-500 focus:outline-none focus:text-dg-200 focus:border-dg-500 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
