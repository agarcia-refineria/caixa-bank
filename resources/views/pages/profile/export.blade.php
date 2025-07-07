<x-app-layout>
    @include('partials.profile.navigation')

    <div class="py-6 md:px-0 px-4">
        <div class="pb-6 md:px-0 px-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="flex items-center justify-center md:justify-start gap-4">
                    <x-links.nav-link class="uppercase px-4 py-2" :href="route('profile.accounts.edit')" :active="request()->routeIs(['profile.accounts.edit'])" id="profile-accounts-create-account" shepherd-text="{{ trans('shepherd.profile-accounts-create-account') }}">
                        {{ __('Accounts') }}
                    </x-links.nav-link>

                    <x-links.nav-link class="uppercase px-4 py-2" :href="route('profile.import.edit')" :active="request()->routeIs(['profile.import.edit'])" id="profile-accounts-import" shepherd-text="{{ trans('shepherd.profile-accounts-import') }}">
                        {{ __('Import') }}
                    </x-links.nav-link>

                    <x-links.nav-link class="uppercase px-4 py-2" :href="route('profile.export.edit')" :active="request()->routeIs(['profile.export.edit'])" id="profile-accounts-export" shepherd-text="{{ trans('shepherd.profile-accounts-export') }}">
                        {{ __('Export') }}
                    </x-links.nav-link>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pb-4">
            <div class="bg-[#664d03] w-full rounded-2xl relative group inline-block px-4 py-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                (I). {{ __('You can export your transactions, accounts and balances to CSV or XLSX files.') }}<br/>
                (II). {{ __('Please note that the export process may take some time, depending on the volume of information.') }}<br/>
                (III). {{ __('The files exported represent the data from the database.') }}<br/>
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-4 grid-cols-1 lg:grid-cols-3">
            <section class="relative md:px-0 px-6 bg-main2 rounded-lg py-6">
                <!-- Show the bank logo and name -->
                <h2 class="flex gap-4 items-center text-lg font-medium text-primary w-full sm:px-6 lg:px-8 pb-3">
                    {{ __('Export') }} {{ __('Accounts') }}
                </h2>

                <div class="text-white p-6">
                    <x-links.nav-link x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-download')" onclick="setFormActionDownload()" data-href="{{ route('profile.export.accounts', ['type' => 'csv']) }}">
                        {{ __('DOWNLOAD') }} CSV
                    </x-links.nav-link>
                    <x-links.nav-link x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-download')" onclick="setFormActionDownload()" data-href="{{ route('profile.export.accounts', ['type' => 'xlsx']) }}">
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
                    <x-links.nav-link x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-download')" onclick="setFormActionDownload()" data-href="{{ route('profile.export.transactions', ['type' => 'csv']) }}">
                        {{ __('DOWNLOAD') }} CSV
                    </x-links.nav-link>
                    <x-links.nav-link x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-download')" onclick="setFormActionDownload()" data-href="{{ route('profile.export.transactions', ['type' => 'xlsx']) }}">
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
                    <x-links.nav-link x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-download')" onclick="setFormActionDownload()" data-href="{{ route('profile.export.balances', ['type' => 'csv']) }}">
                        {{ __('DOWNLOAD') }} CSV
                    </x-links.nav-link>
                    <x-links.nav-link x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-download')" onclick="setFormActionDownload()" data-href="{{ route('profile.export.balances', ['type' => 'xlsx']) }}">
                        {{ __('DOWNLOAD') }} XLSX
                    </x-links.nav-link>
                </div>
            </section>
        </div>
    </div>

    <x-ui.modal name="confirm-download" :show="$errors->downloadBag->isNotEmpty()" focusable>
        <form id="confirm-download" method="POST" action="" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-primary">
                {{ __('Download file') }}
            </h2>

            <p class="mt-1 text-sm text-secondary">
                {{ __('To ensure protected data you need to insert your user password.') }}
            </p>

            <div class="mt-6">
                <x-inputs.input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-inputs.text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Password') }}"
                />

                <x-inputs.input-error :messages="$errors->downloadBag->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-buttons.secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-buttons.secondary-button>

                <x-buttons.primary-button class="ms-3">
                    {{ __('DOWNLOAD') }}
                </x-buttons.primary-button>
            </div>
        </form>
    </x-ui.modal>

    <script>
        window.setFormActionDownload = setFormActionDownload;
        function setFormActionDownload() {
            const form = document.getElementById('confirm-download');
            const link = event.currentTarget.getAttribute('data-href');
            form.setAttribute('action', link);
        }
    </script>
</x-app-layout>
