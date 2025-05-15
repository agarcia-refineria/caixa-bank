<h2 class="text-lg font-medium text-gray-100 flex items-center gap-2">
    {{ $title  }}
    <x-ui.times
        :count="$dataSyncCount"
        :disabled="$disabled"
        :maxTimes="\App\Models\ScheduledTasks::$MAX_TIMES"
        :warningTimes="\App\Models\ScheduledTasks::$WARNING_TIMES" />
</h2>

<p class="mt-1 text-sm text-gray-400">
    {{ $description }}
</p>

<!-- Show the date of the last transaction -->
<p class="mt-1 text-sm text-gray-400">
    {{ __('Last update') }}: {{ $last }}
</p>

@if ($dataSyncCount <= \App\Models\ScheduledTasks::$MAX_TIMES || $disabled)
    <div class="pt-4">
        <div x-data="{ showTip: false }" class="relative inline-block">
            <x-links.nav-link
                @mouseenter="showTip = true"
                @mouseleave="showTip = false"
                class="inline-flex items-center px-4 pt-1 border-b-2 leading-5 !text-white hover:!text-gray-300  hover:border-gray-300  focus:outline-none focus:border-gray-300  py-1 bg-[#2d43b0] border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                href="{{ $link }}"
            >
                {{ $button }}
            </x-links.nav-link>

            <div
                x-show="showTip"
                x-transition
                class="absolute z-10 bottom-full mb-2 w-64 p-2 text-sm bg-gray-800 text-white rounded shadow-lg"
                style="display: none;"
            >
                {{ __('Remember: You can update a maximum of :times times per day.', ['times' => \App\Models\ScheduledTasks::$MAX_TIMES]) }}
            </div>
        </div>
    </div>
@endif
