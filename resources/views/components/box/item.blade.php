<div>
    <div class="{{ $class ?? 'max-w-7xl mx-auto sm:px-6 lg:px-8 pt-4' }} ">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ $title }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ $description }}
        </p>
    </div>

    @if (isset($slot))
        {{ $slot }}
    @endif

    @if (isset($button) && isset($link))
        <div class="{{ $class ?? 'max-w-7xl mx-auto sm:px-6 lg:px-8 pb-4' }} pt-4">
            <x-links.nav-link class="inline-flex items-center px-4 py-1 bg-[#1c1d20] dark:bg-gray-200 dark:hover:text-white border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700  focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" :href="$link">
                {{ $button }}
            </x-links.nav-link>
        </div>
    @endif
</div>
