<x-app-layout>
    <x-site.top-bar :text="__('History')" />

    <!-- Show the currents accounts code -->
    <div class="w-full  text-white p-6">
        <h2 class="text-lg font-semibold mb-4">{{ __('Accounts') }}</h2>
        <x-tables.balance-table :accounts="$user->accounts" />
    </div>

    <div class="w-full  text-white p-6">
        <h2 class="text-lg font-semibold mb-4">{{ __('Transactions') }}</h2>
        <x-tables.transaction-table :transactions="$transactions" />
    </div>
</x-app-layout>
