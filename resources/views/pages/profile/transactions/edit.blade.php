<x-app-layout>
    @include('partials.profile.navigation')

    <x-site.top-bar :text="__('Account') . ': ' . $account->iban" />

    <div class="py-6 md:px-0 px-4 sm:px-6 lg:px-8">
        <div class="pb-6 flex justify-center md:justify-start gap-4">
            <x-links.nav-link :href="route('profile.transaction.edit', ['id' => $account->code])" id="profile-accounts-transactions-table" :shepherd-text="trans('shepherd.profile-accounts-transactions-table')">
                {{ __('View Transactions') }}
            </x-links.nav-link>

            <x-links.nav-link :href="route('profile.balance.edit', ['id' => $account->code])" id="profile-accounts-balances-table" :shepherd-text="trans('shepherd.profile-accounts-balances-table')">
                {{ __('View Balances') }}
            </x-links.nav-link>
        </div>

        @if ($account->is_manual)
            <x-buttons.primary-button
                class="py-2 mb-4"
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-transaction-create')">
                {{ __('Create Transaction') }}
            </x-buttons.primary-button>

            <x-ui.modal name="confirm-transaction-create" :show="$errors->transactionCreate->isNotEmpty()" maxWidth="full" margin="sm:px-[50px]" focusable>
                @include('partials.profile.transaction', ['account' => $account, 'user' => $user, 'errorBag' => 'transactionCreate'])
            </x-ui.modal>
        @endif

        <div class="space-y-6 text-white">
            <x-tables.transaction-table
                :transactions="$transactions"
                :account="$account"
                :user="$user"
                type="request"
                data-url="{{ route('api.datatable.transactions', ['id' => $account->code]) }}"
                :actions="$account->is_manual" />
        </div>
    </div>
</x-app-layout>
