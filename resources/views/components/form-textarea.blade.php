@props([
    'label' => null,
    'name',
    'value' => null,
    'required' => false,
    'disabled' => false,
    'placeholder' => null,
    'rows' => 3,
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
        <textarea 
            name="{{ $name }}"
            id="{{ $name }}"
            rows="{{ $rows }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            placeholder="{{ $placeholder }}"
            {{ $attributes->except('class')->merge(['class' => 'w-full px-4 py-3 bg-surface-overlay border border-border rounded-xl text-sm text-dg-100 placeholder-dg-500 focus:ring-2 focus:ring-dg-400/20 focus:border-dg-400 resize-y transition-all duration-150']) }}
        >{{ old($name, $value) }}</textarea>
    </div>
    
    @error($name)
        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
    @enderror
</div>
