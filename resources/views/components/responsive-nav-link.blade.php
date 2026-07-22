@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-dg-200 text-start text-base font-medium text-dg-100 bg-surface-elevated focus:outline-none transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-dg-400 hover:text-dg-200 hover:bg-surface-elevated hover:border-dg-500 focus:outline-none focus:text-dg-200 focus:bg-surface-elevated focus:border-dg-500 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
