<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2.5 bg-red-500/10 border border-red-500/20 rounded-xl font-medium text-sm text-red-400 hover:bg-red-500/20 focus:outline-none focus:ring-2 focus:ring-red-500/20 active:scale-[0.97] transition-all duration-150']) }}>
    {{ $slot }}
</button>
