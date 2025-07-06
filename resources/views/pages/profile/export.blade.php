<x-app-layout>
    @include('partials.profile.navigation')

    <div class="py-6 md:px-0 px-4">
        <div class="pb-6 md:px-0 px-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="flex items-center justify-start gap-4">
                    <x-links.nav-link class="uppercase px-4 py-2" :href="route('profile.accounts.edit')" :active="request()->routeIs(['profile.accounts.edit'])">
                        {{ __('Accounts') }}
                    </x-links.nav-link>

                    <x-links.nav-link class="uppercase px-4 py-2" :href="route('profile.import.edit')" :active="request()->routeIs(['profile.import.edit'])">
                        {{ __('Import') }}
                    </x-links.nav-link>

                    <x-links.nav-link class="uppercase px-4 py-2" :href="route('profile.export.edit')" :active="request()->routeIs(['profile.export.edit'])">
                        {{ __('Export') }}
                    </x-links.nav-link>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-4 grid-cols-1 lg:grid-cols-3">
            <section class="relative md:px-0 px-6 bg-main2 rounded-lg py-6">
                <!-- Show the bank logo and name -->
                <h2 class="flex gap-4 items-center text-lg font-medium text-primary w-full sm:px-6 lg:px-8 pb-3">
                    {{ __('Export') }} {{ __('Accounts') }}
                </h2>

                <div class="text-white p-6">
                    <x-links.nav-link href="{{ route('profile.export.accounts', ['type' => 'csv']) }}">
                        {{ __('DOWNLOAD') }} CSV
                    </x-links.nav-link>
                    <x-links.nav-link href="{{ route('profile.export.accounts', ['type' => 'xlsx']) }}">
                        {{ __('DOWNLOAD') }} XLSX
                    </x-links.nav-link>
                </div>
            </section>

            <section class="relative md:px-0 px-6 bg-main2 rounded-lg py-6">
                <!-- Show the bank logo and name -->
                <h2 class="flex gap-4 items-center text-lg font-medium text-primary w-full sm:px-6 lg:px-8 pb-3">
                    {{ __('Export') }} {{ __('Transactions') }}
                </h2>

                <div class="text-white p-6">
                    <x-links.nav-link href="{{ route('profile.export.transactions', ['type' => 'csv']) }}">
                        {{ __('DOWNLOAD') }} CSV
                    </x-links.nav-link>
                    <x-links.nav-link href="{{ route('profile.export.transactions', ['type' => 'xlsx']) }}">
                        {{ __('DOWNLOAD') }} XLSX
                    </x-links.nav-link>
                </div>
            </section>

            <section class="relative md:px-0 px-6 bg-main2 rounded-lg py-6">
                <!-- Show the bank logo and name -->
                <h2 class="flex gap-4 items-center text-lg font-medium text-primary w-full sm:px-6 lg:px-8 pb-3">
                    {{ __('Export') }} {{ __('Balances') }}
                </h2>

                <div class="text-white p-6">
                    <x-links.nav-link href="{{ route('profile.export.balances', ['type' => 'csv']) }}">
                        {{ __('DOWNLOAD') }} CSV
                    </x-links.nav-link>
                    <x-links.nav-link href="{{ route('profile.export.balances', ['type' => 'xlsx']) }}">
                        {{ __('DOWNLOAD') }} XLSX
                    </x-links.nav-link>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
