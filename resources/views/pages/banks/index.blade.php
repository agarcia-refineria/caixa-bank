<x-app-layout>
    <x-site.top-bar text="{{ __('The total of the accounts balances is') }} {{ auth()->user()->totalAccountSum }} €" />

    <div class="block md:flex min-h-screen bg-[#0e0f11] text-white">
        <x-pages.dashboard.sidebar :accounts="$accounts" :current-account="$currentAccount" />

        <div class="min-h-screen w-full  text-white p-6">
            @if ($currentAccount)
                @if ($balance)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        <x-ui.stat-box icon="💳" :title="__('Current Balance')" value="{{ number_format($balance->amount, 2, ',', '.') }} €" />
                        <x-ui.stat-box icon="💸" :title="__('Expenses')" value="{{ number_format($currentAccount->expenses, 2, ',', '.') }} €" />
                        <x-ui.stat-box icon="📈" :title="__('Income')" value="{{ number_format($currentAccount->income, 2, ',', '.') }} €" />
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                        <x-charts.chart-card
                            id="categoryChart"
                            :title="__('Expenses by Category')"
                            :data-values="$currentAccount->chartTransactionsValues"
                            :data-labels="$currentAccount->chartTransactionsLabels"
                            :data-colors="$currentAccount->chartTransactionsColors"
                            container-class="col-span-2 md:col-span-1" />

                        <x-charts.chart-card
                            id="balanceChart"
                            :title="__('Balance History')"
                            :data-values="$currentAccount->chartBalancesValues"
                            :data-labels="$currentAccount->chartBalancesLabels"
                            container-class="col-span-2"
                            class="!w-full"
                        />
                    </div>

                    <x-tables.transaction-table :transactions="$currentAccount->transactionsCurrentMonth" />
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
