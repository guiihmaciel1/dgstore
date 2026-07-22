@props([
    'title',
    'value',
    'icon' => null,
    'trend' => null,
    'trendUp' => true,
    'color' => 'indigo',
])

@php
    $colorClasses = [
        'indigo' => 'bg-indigo-500/10 text-indigo-400',
        'green' => 'bg-emerald-500/10 text-emerald-400',
        'yellow' => 'bg-amber-500/10 text-amber-400',
        'red' => 'bg-red-500/10 text-red-400',
        'blue' => 'bg-blue-500/10 text-blue-400',
    ];
@endphp

<div class="bg-surface-raised rounded-2xl border border-border p-6">
    <div class="flex items-center">
        @if($icon)
            <div class="flex-shrink-0">
                <div class="{{ $colorClasses[$color] ?? 'bg-indigo-500/10 text-indigo-400' }} rounded-xl p-3">
                    {!! $icon !!}
                </div>
            </div>
        @endif
        <div class="{{ $icon ? 'ml-5' : '' }} w-0 flex-1">
            <dl>
                <dt class="text-sm font-medium text-dg-400 truncate">
                    {{ $title }}
                </dt>
                <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-dg-50">
                        {{ $value }}
                    </div>
                    @if($trend)
                        <span class="ml-2 text-sm font-medium {{ $trendUp ? 'text-emerald-400' : 'text-red-400' }}">
                            {{ $trendUp ? '+' : '' }}{{ $trend }}
                        </span>
                    @endif
                </dd>
            </dl>
        </div>
    </div>
</div>
