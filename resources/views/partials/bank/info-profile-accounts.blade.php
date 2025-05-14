<div class="max-w-xl">
    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
        {{ __('Bank Sync info') }}
    </h2>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 pb-4">
        {{ __("Max schedule times is") . ' ' . (\App\Models\ScheduledTasks::$MAX_TIMES * 2) . ' ' . __('for each account, :times for each types (transactions and balances)',['times' => \App\Models\ScheduledTasks::$MAX_TIMES]) }}.
    </p>

    @foreach(\App\Models\Account::$accountTypes as $type)
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 flex gap-2 items-center">
            {{ __("Accounts total") }} ({{ $type }})
            <x-ui.times
                :count="$user->accounts()->onlyType($type)?->count()"
                :maxTimes="1000"
                :warningTimes="1000"
                defaultBg="bg-gray-800" />
        </p>
    @endforeach

    <br/>

    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 flex gap-2 items-center">
        {{ __("Syncs total") }}
        <x-ui.times
            :count="$user->bankDataSyncCount"
            :maxTimes="(\App\Models\ScheduledTasks::$MAX_TIMES * 2) * $user->accounts()->onlyApi()->count()"
            :warningTimes="(\App\Models\ScheduledTasks::$WARNING_TIMES * 2) * $user->accounts()->onlyApi()->count()" />
    </p>

    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 flex gap-2 items-center">
        {{ __('Max Syncs') }}
        <x-ui.times
            :count="(\App\Models\ScheduledTasks::$MAX_TIMES * 2) * $user->accounts()->onlyApi()->count()"
            defaultBg="bg-red-800" />
    </p>
</div>
