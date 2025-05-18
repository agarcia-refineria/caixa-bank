<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-main2 border border-secondary rounded-md font-semibold text-xs text-secondary hover:text-primary uppercase tracking-widest shadow-sm hover:bg-third disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
