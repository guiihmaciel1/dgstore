@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-dg-300']) }}>
    {{ $value ?? $slot }}
</label>
