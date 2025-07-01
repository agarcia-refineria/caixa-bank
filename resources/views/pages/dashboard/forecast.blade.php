<x-app-layout>
    <x-site.top-info :text="__('Forecast Saving')" />

    <div class="block md:flex min-h-screen bg-main1 text-primary">
        @include('partials.forecast.sidebar', [
            'accounts' => $accounts,
            'currentAccount' => $currentAccount,
        ])

        <section class="min-h-screen w-full p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <section class="bg-main2 px-[40px] py-[20px] rounded-xl shadow flex flex-col gap-2">
                    <x-inputs.input-label for="paysheet" :value="__('Paysheet')" />
                    <select onchange="setPaysheetAccount()" data-default="{{ __('-- Select an option --') }}" id="paysheet" name="paysheet" class="select2 form-control border-third bg-main3 text-primary rounded-md shadow-sm mt-1 block w-full">
                        <option value="" selected disabled>{{ __('-- Select an option --') }}</option>
                        @foreach($currentAccount->transactions()->where('transactionAmount_amount', '>', 0)->orderBy('bookingDate', 'desc')->get() as $transaction)
                            <option value="{{ $transaction->code }}" {{ $transaction->code == $currentAccount->paysheet_id ? 'selected' : '' }}>
                                {{ $transaction->remittance_information_unstructured_format  }} ({{ $transaction->transactionAmount_amount }} €)
                            </option>
                        @endforeach
                    </select>
                </section>

                <section class="bg-main2 px-[40px] py-[20px] rounded-xl shadow flex flex-col gap-2">
                    <x-inputs.input-label for="average_month_expenses" :value="__('Average Month Expenses')" />
                    <x-inputs.text-input
                        type="text"
                        step="0.01"
                        name="average_month_expenses"
                        class="w-full !border-none focus:ring-0 focus:border-none text-[24px]"
                        value="{{ $currentAccount->average_month_expenses_excluding_categories }} €"
                        readonly/>
                </section>

                <section class="bg-main2 px-[40px] py-[20px] rounded-xl shadow flex flex-col gap-2">
                    <x-inputs.input-label for="disable_transactions" :value="__('Disable Transfers')" />
                    <select onchange="setDisableTransactions()" data-default="{{ __('-- Select an option --') }}" id="disable_transactions" name="disable_transactions[]" multiple="multiple" class="select2 form-control border-third bg-main3 text-primary rounded-md shadow-sm mt-1 block w-full">
                        <option value="" disabled>{{ __('-- Select an option --') }}</option>
                        @foreach($user->categories as $category)
                            <option value="{{ $category->id }}" {{ $currentAccount->categories()->where('paysheet_disabled', true)->where('category_id', $category->id)->exists() ? 'selected' : '' }}>
                                {{ $category->name  }} ({{ $category->filters()->count() ?? '0' }} {{ __('filters') }})
                            </option>
                        @endforeach
                    </select>
                </section>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="px-4">
                    <x-inputs.input-label for="apply_expenses_monthly" :value="__('Apply Expenses Monthly')" />
                    <x-inputs.checkbox id="apply_expenses_monthly" :active="$currentAccount->apply_expenses_monthly" name="apply_expenses_monthly" class="mt-2" />
                    <x-inputs.input-error class="mt-2" :messages="$errors->get('apply_expenses_monthly')" />
                </div>
            </div>

            <div class="w-full">
                <x-charts.chart-card
                    id="futureIncomeChart"
                    :title="__('Incomes Future History')"
                    :data-values="$currentAccount->chart_forecast_month_incomes_values"
                    :data-labels="$currentAccount->chart_forecast_month_incomes_labels"
                    :data-color="$user->theme_main3"
                />
            </div>
        </section>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.getElementById('apply_expenses_monthly').addEventListener('change', setApplyExpensesMonthly);
            });

            function setApplyExpensesMonthly() {
                const applyExpensesMonthly = document.getElementById('apply_expenses_monthly').checked;

                fetch('{{ route('profile.account.apply-expenses-monthly') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ apply_expenses_monthly: applyExpensesMonthly, account_id: '{{ $currentAccount->code }}' })
                });
            }
            function setPaysheetAccount() {
                const paysheetSelect = document.getElementById('paysheet');
                const paysheetValue = paysheetSelect.value;

                fetch('{{ route('profile.account.paysheet') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ paysheet: paysheetValue })
                });
            }
            function setDisableTransactions() {
                const selectedOptions = Array.from(document.getElementById('disable_transactions').selectedOptions).map(option => option.value);
                fetch('{{ route('profile.account.disable-transactions') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ categories: selectedOptions, account_id: '{{ $currentAccount->code }}' })
                });
            }
        </script>
    </div>
</x-app-layout>
