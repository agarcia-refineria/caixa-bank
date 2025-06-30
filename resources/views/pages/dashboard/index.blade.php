<x-app-layout>
    <x-site.top-bar id="index-total-amount" :shepherd-text="trans('shepherd.index-total-amount')" text="{{ __('The total of the accounts balances is') }} {{ $user->total_account_sum }} €" />

    <div class="block md:flex min-h-screen bg-main1 text-primary">
        @include('partials.dashboard.sidebar', [
            'accounts' => $accounts,
            'currentAccount' => $currentAccount,
        ])

        <div class="min-h-screen w-full p-6">
            @if ($currentAccount)
                @if ($balance)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        <x-ui.stat-box id="index-stat-current" :shepherd-text="trans('shepherd.index-stat-current')" icon="💳" :title="__('Current Balance')" value="{{ $balance ? number_format($balance->amount, 2, ',', '.') : 0 }} €" />
                        <x-ui.stat-box id="index-stat-expenses" :shepherd-text="trans('shepherd.index-stat-expenses')" icon="💸" :title="__('Expenses')" value="{{ number_format($currentAccount->expenses, 2, ',', '.') }} €" />
                        <x-ui.stat-box id="index-stat-income" :shepherd-text="trans('shepherd.index-stat-income')" icon="📈" :title="__('Income')" value="{{ number_format($currentAccount->income, 2, ',', '.') }} €" />
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                        @if ($user->chars == "all")
                            <x-charts.chart-card
                                id="categoryChart"
                                :shepherd-text="trans('shepherd.index-category-chart-all')"
                                :title="__('All Expenses')"
                                :data-values="$currentAccount->chart_transactions_values"
                                :data-labels="$currentAccount->chart_transactions_labels"
                                :data-colors="$currentAccount->chart_transactions_colors"
                                container-class="col-span-1" />
                        @else
                            <x-charts.chart-card
                                id="categoryChart"
                                :shepherd-text="trans('shepherd.index-category-chart-category')"
                                :data-default-label="__('Sin Categoria')"
                                :title="__('Expenses by Category')"
                                :data-values="$currentAccount->chart_transactions_values_category"
                                :data-labels="$currentAccount->chart_transactions_labels_category"
                                :data-colors="$currentAccount->chart_transactions_colors_category"
                                container-class="col-span-1" />
                        @endif

                        <x-charts.chart-card
                            id="balanceChart"
                            :shepherd-text="trans('shepherd.index-balance-chart')"
                            :title="__('Balance History')"
                            :data-values="$currentAccount->chart_balances_values"
                            :data-labels="$currentAccount->chart_balances_labels"
                            :data-color="$user->theme_main3"
                            container-class="col-span-1 lg:col-span-2"
                        />
                    </div>

                    <x-tables.transaction-table id="index-transactions-table" :shepherd-text="trans('shepherd.index-transactions-table')" :transactions="$currentAccount->transactions_current_month" />
                @else
                    <x-ui.empty-state
                        :title="__('No Balance Data Available')"
                        :message="__('It seems like there is no balance data available for this account. Go to configuration to import the balance and transfers.')"
                    />
                @endif
            @else
                <x-ui.empty-state
                    :title="__('No Account Selected')"
                    :message="__('Please select an account from the sidebar to view your dashboard. If there is no account on the sidebar go to configuration to import the accounts.')"
                />
            @endif
        </div>
    </div>
</x-app-layout>
