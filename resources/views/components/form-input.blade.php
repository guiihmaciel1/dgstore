@props([
    'label' => null,
    'name',
    'type' => 'text',
    'value' => null,
    'required' => false,
    'disabled' => false,
    'placeholder' => null,
    'help' => null,
])

<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-dg-300">
            {{ $label }}
            @if($required)
                <span class="text-red-400">*</span>
            @endif
        </label>
    @endif
    
    <div class="mt-1">
        <input 
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name, $value) }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            placeholder="{{ $placeholder }}"
            {{ $attributes->except('class')->merge(['class' => 'w-full px-4 py-3 bg-surface-overlay border border-border rounded-xl text-sm text-dg-100 placeholder-dg-500 focus:ring-2 focus:ring-dg-400/20 focus:border-dg-400 transition-all duration-150' . ($disabled ? ' opacity-50 cursor-not-allowed' : '')]) }}
        >
    </div>
    
    @if($help)
        <p class="mt-1 text-sm text-dg-500">{{ $help }}</p>
    @endif
    
    @error($name)
        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
    @enderror
</div>
