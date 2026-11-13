@props([
    'title',
    'subtitle' => null,
])

{{-- Cabeçalho padrão de página: título, subtítulo e ações à direita (empilha no celular). --}}
<div {{ $attributes->merge(['class' => 'mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between']) }}>
    <div class="min-w-0">
        <h1 class="text-xl font-bold text-dg-50 sm:text-2xl">{{ $title }}</h1>
        @if($subtitle)
            <p class="mt-0.5 text-sm text-dg-400">{{ $subtitle }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
    @endisset
</div>
