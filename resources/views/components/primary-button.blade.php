<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2.5 bg-white border border-transparent rounded-xl font-medium text-sm text-dg-950 hover:bg-dg-100 focus:outline-none focus:ring-2 focus:ring-dg-300/20 active:scale-[0.97] transition-all duration-150']) }}>
    {{ $slot }}
</button>
