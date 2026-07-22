<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2.5 bg-surface-elevated border border-border rounded-xl font-medium text-sm text-dg-200 hover:bg-dg-800 hover:border-border-strong focus:outline-none focus:ring-2 focus:ring-dg-400/20 disabled:opacity-25 active:scale-[0.97] transition-all duration-150']) }}>
    {{ $slot }}
</button>
