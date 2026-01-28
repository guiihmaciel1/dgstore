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
        'indigo' => 'bg-indigo-500',
        'green' => 'bg-green-500',
        'yellow' => 'bg-yellow-500',
        'red' => 'bg-red-500',
        'blue' => 'bg-blue-500',
    ];
@endphp

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
    <div class="flex items-center">
        @if($icon)
            <div class="flex-shrink-0">
                <div class="{{ $colorClasses[$color] ?? 'bg-indigo-500' }} rounded-md p-3">
                    {!! $icon !!}
                </div>
            </div>
        @endif
        <div class="{{ $icon ? 'ml-5' : '' }} w-0 flex-1">
            <dl>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                    {{ $title }}
                </dt>
                <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ $value }}
                    </div>
                    @if($trend)
                        <span class="ml-2 text-sm font-medium {{ $trendUp ? 'text-green-600' : 'text-red-600' }}">
                            {{ $trendUp ? '+' : '' }}{{ $trend }}
                        </span>
                    @endif
                </dd>
            </dl>
        </div>
    </div>
</div>
