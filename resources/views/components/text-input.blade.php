@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-surface-overlay border-border text-dg-100 placeholder-dg-500 focus:border-dg-400 focus:ring-dg-400/20 rounded-xl transition-all duration-150']) }}>
