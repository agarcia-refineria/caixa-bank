<x-app-layout>
    @include('partials.profile.navigation')

    <div class="py-6 md:px-0 px-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-4 grid-cols-1 lg:grid-cols-3">
            <section class="relative md:px-0 px-6 bg-main2 rounded-lg py-6">
                <!-- Show the bank logo and name -->
                <h2 class="flex gap-4 items-center text-lg font-medium text-primary w-full sm:px-6 lg:px-8 pb-3">
                    <img src="{{ $user->bank->institution->logo }}" alt="{{ $user->bank->institution->name }}" width="32" height="32" class="h-8 w-8 mr-2">
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
                    <img src="{{ $user->bank->institution->logo }}" alt="{{ $user->bank->institution->name }}" width="32" height="32" class="h-8 w-8 mr-2">
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
                    <img src="{{ $user->bank->institution->logo }}" alt="{{ $user->bank->institution->name }}" width="32" height="32" class="h-8 w-8 mr-2">
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
