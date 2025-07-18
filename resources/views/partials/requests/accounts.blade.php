@if (count($accounts) > 0)
    @foreach ($accounts as $account)
        <div data-id="{{ $account->code }}" class="relative max-w-7xl md:px-0 px-4 mx-auto bg-main2 rounded-lg border-t-main3 border-b-main3 border-l-main3 border-r-main3 border-2 py-4 mt-4">
            <!-- Show the bank logo and name -->
            <div class="border-b-main3 border-t-0 border-l-0 border-r-0 border-2 w-full sm:px-6 lg:px-8 pb-3">
                <h2 class="flex gap-4 items-center text-lg font-medium text-primary ">
                    <img src="{{ $account->institution->logo }}" alt="{{ $account->institution->name }}" width="32" height="32" class="h-8 w-8 mr-2">
                    {{ $account->institution?->name }} - {{ $account->iban }} <span class="md:block hidden">({{ $account->type }})</span>
                    <br/>
                </h2>
            </div>


            @if ($account->is_api)
                <p class="mt-2 text-sm  @if ($account->IsAuthorized) text-secondary @else text-error @endif py-2 sm:px-6 lg:px-8">
                    {{ __('Authorization needed on :date', [
                            'date' => $account->authorized_at->addDays($account->institution->max_access_valid_for_days)->format('d-m-Y'),
                        ])
                     }}
                </p>

                @if ($account->IsAuthorized)
                    <!-- Show the account buttons -->
                    <div class="flex md:flex-row flex-col gap-4 pt-2 pb-4 sm:px-6 lg:px-8">
                        <!-- Show the update all button -->
                        @if ($account->show_update_all)
                            <x-box.item
                                :title="__('Update all')"
                                :description="__('This will add all the transactions and balances at the same time')"
                                :button="__('UPDATE INFORMATION')"
                                :link="route('nordigen.all', ['accountId' => $account->code])"
                                class="bg-main2 overflow-hidden shadow-sm rounded-lg"/>
                        @endif

                        <!-- Show the transactions buttons -->
                        <div class="bg-main2 overflow-hidden shadow-sm rounded-lg">
                            @if ($account->transactions_disabled)
                                <x-box.danger
                                    text="[{{ __('Rate limit exceeded') }}: {{ $account->transactions_disabled_date->format('d-m-Y H:i:s') }}]" />
                            @endif

                            @include('partials.requests.request', [
                                'title' => __('Transactions update'),
                                'description' => __('This will add all the transactions'),
                                'button' => __('UPDATE INFORMATION'),

                                'account' => $account,
                                'dataSyncCount' => $account->bank_data_sync_transactions_count,
                                'disabled' => $account->transactions_disabled,
                                'last' => $account->bankDataSync()->dataTypeTransaction()->latest()->first() ? $account->bankDataSync()->dataTypeTransaction()->latest()->first()->created_at->format('d-m-Y H:i:s') : __('No transactions found'),
                                'link' => route('nordigen.transactions', ['accountId' => $account->code]),
                            ])
                        </div>

                        <!-- Show the balances buttons -->
                        <div class="bg-main2 overflow-hidden shadow-sm rounded-lg">
                            @if ($account->balance_disabled)
                                <x-box.danger
                                    text="[{{ __('Rate limit exceeded') }}: {{ $account->balance_disabled_date->format('d-m-Y H:i:s') }}]" />
                            @endif

                            @include('partials.requests.request', [
                                'title' => __('Balances update'),
                                'description' => __('This will add all the balances'),
                                'button' => __('UPDATE INFORMATION'),

                                'account' => $account,
                                'dataSyncCount' => $account->bank_data_sync_balances_count,
                                'disabled' => $account->balance_disabled,
                                'last' => $account->bankDataSync()->dataTypeBalance()->latest()->first() ? $account->bankDataSync()->dataTypeBalance()->latest()->first()->created_at->format('d-m-Y H:i:s') : __('No balances found'),
                                'link' => route('nordigen.balances', ['accountId' => $account->code]),
                            ])
                        </div>
                    </div>
                @else
                    <div class="flex md:flex-row flex-col gap-4 py-2 pb-6 sm:px-6 lg:px-8">
                        <x-box.item
                            :title="__('Authorization needed')"
                            :description="__('You need to update this account on update accounts button')"
                            class="bg-main2 overflow-hidden shadow-sm rounded-lg"/>
                    </div>
                @endif
            @else
                <div class="flex md:flex-row flex-col gap-4 py-6 sm:px-6 lg:px-8">
                    <x-box.item
                        :title="__('Manual creation')"
                        :description="__('You can`t update this account couse it is not connected to the bank account id')"
                        class="bg-main2 overflow-hidden shadow-sm rounded-lg"/>
                </div>
            @endif
        </div>
    @endforeach
@else
    <x-ui.empty
        :title="__('No accounts found')"
        :description="__('Please add an account from update accounts.')" />
@endif
