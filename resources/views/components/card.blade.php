@props([
    'title' => null,
    'subtitle' => null,
    'actions' => null,
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'bg-surface-raised rounded-2xl border border-border']) }}>
    @if($title || $actions)
        <div class="px-4 py-5 sm:px-6 border-b border-border flex items-center justify-between">
            <div>
                @if($title)
                    <h3 class="text-lg font-medium text-dg-50">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="mt-1 text-sm text-dg-400">{{ $subtitle }}</p>
                @endif
            </div>
            @if($actions)
                <div class="flex items-center gap-2">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif
    
    <div class="{{ $padding ? 'p-6' : '' }}">
        {{ $slot }}
    </div>
</div>
