@props([
    'color' => 'gray',
])

@php
    $colorClasses = [
        'gray' => 'bg-dg-800 text-dg-300 border border-border',
        'green' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
        'yellow' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
        'red' => 'bg-red-500/10 text-red-400 border border-red-500/20',
        'blue' => 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
        'indigo' => 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20',
        'purple' => 'bg-purple-500/10 text-purple-400 border border-purple-500/20',
        'orange' => 'bg-orange-500/10 text-orange-400 border border-orange-500/20',
        'emerald' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
        'amber' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . ($colorClasses[$color] ?? $colorClasses['gray'])]) }}>
    {{ $slot }}
</span>
