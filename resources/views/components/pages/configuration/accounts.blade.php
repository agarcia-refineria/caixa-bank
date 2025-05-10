@if (count($accounts) > 0)
    @foreach ($accounts as $account)
        <div data-id="{{ $account->code }}" class="relative max-w-7xl md:px-0 px-4 mx-auto dark:bg-[#1c1d20] rounded-lg border-t-[#2d43b0] border-b-[#2d43b0] border-l-[#2d43b0] border-r-[#2d43b0] border-2 py-4 mt-4">
            <!-- Show the bank logo and name -->
            <h2 class="flex gap-4 items-center text-lg font-medium text-gray-900 dark:text-gray-100 w-full sm:px-6 lg:px-8 pb-3 border-b-[#2d43b0] border-t-0 border-l-0 border-r-0 border-2">
                <img src="{{ $user->bank->institution->logo }}" alt="{{ $user->bank->institution->name }}" width="32" height="32" class="h-8 w-8 mr-2">
                {{ $account->institution?->name }} - {{ $account->iban }} <span class="md:block hidden">({{ $account->status }})</span>
            </h2>

            <!-- Show the account buttons -->
            <div class="flex md:flex-row flex-col gap-4 py-6 sm:px-6 lg:px-8">
                <!-- Show the update all button -->
                @if ($showUpdateAccounts)
                    <x-pages.configuration.item
                        :title="__('Update all')"
                        :description="__('This will add all the transactions and balances at the same time')"
                        :button="__('UPDATE INFORMATION')"
                        :link="route('nordigen.all', ['accountId' => $account->code])"
                        class="bg-white dark:bg-[#1c1d20] overflow-hidden shadow-sm rounded-lg"/>
                @endif

                <!-- Show the transactions buttons -->
                <div class="bg-white dark:bg-[#1c1d20] overflow-hidden shadow-sm rounded-lg">
                    @if ($account->transactionsDisabled)
                        <x-pages.configuration.rate-info
                            text="[{{ __('Rate limit exceeded') }}: {{ $account->transactions_disabled_date->format('d-m-Y H:i:s') }}]" />
                    @endif

                    <x-pages.configuration.request
                        :title="__('Transactions update')"
                        :description="__('This will add all the transactions')"
                        :button="__('UPDATE INFORMATION')"

                        :account="$account"
                        :dataSyncCount="$account->bankDataSyncTransactionsCount"
                        :disabled="$account->transactionsDisabled"
                        :last="$account->bankDataSync()->dataTypeTransaction()->latest()->first() ? $account->bankDataSync()->dataTypeTransaction()->latest()->first()->created_at->format('d-m-Y H:i:s') : __('No transactions found')"
                        :link="route('nordigen.transactions', ['accountId' => $account->code])"/>
                </div>

                <!-- Show the balances buttons -->
                <div class="bg-white dark:bg-[#1c1d20] overflow-hidden shadow-sm rounded-lg">
                    @if ($account->balanceDisabled)
                        <x-pages.configuration.rate-info
                            text="[{{ __('Rate limit exceeded') }}: {{ $account->balance_disabled_date->format('d-m-Y H:i:s') }}]" />
                    @endif

                    <x-pages.configuration.request
                        :title="__('Balances update')"
                        :description="__('This will add all the balances')"
                        :button="__('UPDATE INFORMATION')"

                        :account="$account"
                        :dataSyncCount="$account->bankDataSyncBalancesCount"
                        :disabled="$account->balanceDisabled"
                        :last="$account->bankDataSync()->dataTypeBalance()->latest()->first() ? $account->bankDataSync()->dataTypeBalance()->latest()->first()->created_at->format('d-m-Y H:i:s') : __('No balances found')"
                        :link="route('nordigen.balances', ['accountId' => $account->code])"/>
                </div>
            </div>
        </div>
    @endforeach
@else
    <x-ui.empty
        :title="__('No accounts found')"
        :description="__('Please add an account from update accounts.')" />
@endif
