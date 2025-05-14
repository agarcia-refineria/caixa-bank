<x-app-layout>
    <x-pages.profile.navigation />

    <x-site.top-bar :text="__('Account') . ': ' . $account->iban" />

    <div class="py-6 md:px-0 px-4 sm:px-6 lg:px-8">
        <div class="pb-6 flex justify-center md:justify-start gap-4">
            <x-links.nav-link :href="route('profile.accounts.edit')">
                {{ __('View Accounts') }}
            </x-links.nav-link>

            <x-links.nav-link :href="route('profile.transaction.edit', ['id' => $account->code])">
                {{ __('View Transactions') }}
            </x-links.nav-link>

            <x-links.nav-link :href="route('profile.balance.edit', ['id' => $account->code])">
                {{ __('View Balances') }}
            </x-links.nav-link>
        </div>

        @if ($account->isManual)
            <x-buttons.primary-button
                class="py-2 mb-4"
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-transaction-create')"
            >{{ __('Create Transaction') }}</x-buttons.primary-button>

            <x-ui.modal name="confirm-transaction-create" maxWidth="full" margin="sm:px-[50px]" focusable>
                <x-pages.profile.transaction
                    :account="$account"
                    :user="$user" />
            </x-ui.modal>
        @endif

        <div class="space-y-6 text-white">
            <x-tables.transaction-table :transactions="$transactions" :account="$account" :user="$user" :noFooter="true" :actions="$account->isManual" />
        </div>
    </div>
</x-app-layout>
