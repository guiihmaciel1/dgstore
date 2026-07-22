@props([
    'label' => null,
    'name',
    'options' => [],
    'value' => null,
    'required' => false,
    'disabled' => false,
    'placeholder' => 'Selecione...',
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
        <select
            name="{{ $name }}"
            id="{{ $name }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->except('class')->merge(['class' => 'w-full px-4 py-3 bg-surface-overlay border border-border rounded-xl text-sm text-dg-100 focus:ring-2 focus:ring-dg-400/20 focus:border-dg-400 appearance-none cursor-pointer transition-all duration-150']) }}
        >
            @if($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            
            @foreach($options as $optValue => $optLabel)
                <option 
                    value="{{ $optValue }}" 
                    {{ old($name, $value) == $optValue ? 'selected' : '' }}
                >
                    {{ $optLabel }}
                </option>
            @endforeach
        </select>
    </div>
    
    @error($name)
        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
    @enderror
</div>
