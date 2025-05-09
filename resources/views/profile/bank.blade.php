<x-app-layout>
    <x-pages.profile.navigation />

    <div class="py-6 md:px-0 px-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-[#1a1b1e] shadow rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-bank')
                </div>
            </div>

            @if ($bank)
                <!-- Fields to update the accounts data -> transactions and balances with how many times (max 4 on each) and set the time of execute each one -->
                <div class="p-4 sm:p-8 bg-white dark:bg-[#1a1b1e] shadow rounded-lg">
                    @include('profile.partials.update-profile-accounts')
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
