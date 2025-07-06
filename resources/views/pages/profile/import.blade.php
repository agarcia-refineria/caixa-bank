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

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pb-4">
            <div class="bg-[#664d03] w-full rounded-2xl relative group inline-block px-4 py-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                (I). {{ __('You can import your transactions, accounts and balances from CSV or XLSX files. You can also download example files to help you with the import process.') }}<br/>
                (II). {{ __('Please note that the import process may take some time, depending on the size of the files you are uploading.') }}<br/>
                (III). {{ __('The fields needs to be THE EXACT same name from examples showing you.') }}<br/>
            </div>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-4 grid-cols-1 lg:grid-cols-3">
            <x-form.import
                :user="$user"
                :action="route('profile.import.accounts')"
                :type="__('Accounts')"
                typeField="accounts">
                <table class="datatable table-auto w-full px-4" data-type="default">
                    <thead>
                        <tr>
                            <th class="!text-left">{{ __('ID') }}</th>
                            <th class="!text-left">{{ __('Name') }}</th>
                            <th class="!text-left dt-low-priority">{{ __('IBAN') }}</th>
                            <th class="!text-left dt-low-priority">{{ __('BBAN') }}</th>
                            <th class="!text-left dt-low-priority">{{ __('Status') }}</th>
                            <th class="!text-left dt-low-priority">{{ __('Owner Name') }}</th>
                            <th class="!text-left dt-low-priority">{{ __('Created') }}</th>
                            <th class="!text-left dt-low-priority">{{ __('Last Accessed') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $defaultAccount = \App\Models\Account::getExampleModel(); @endphp
                        <tr>
                            <td class="!text-left">{{ $defaultAccount->code }}</td>
                            <td class="!text-left">{{ $defaultAccount->name }}</td>
                            <td class="!text-left">{{ $defaultAccount->iban }}</td>
                            <td class="!text-left">{{ $defaultAccount->bban }}</td>
                            <td class="!text-left">{{ $defaultAccount->status }}</td>
                            <td class="!text-left">{{ $defaultAccount->owner_name }}</td>
                            <td class="!text-left">{{ $defaultAccount->created->format('d-m-Y H:i:s') }}</td>
                            <td class="!text-left">{{ $defaultAccount->last_accessed->format('d-m-Y H:i:s') }}</td>
                        </tr>
                    </tbody>
                </table>

                <x-links.nav-link href="/csv/import_accounts.csv" download="import_accounts.csv">
                    {{ __('DOWNLOAD') }} CSV
                </x-links.nav-link>
                <x-links.nav-link href="/xlsx/import_accounts.xlsx" download="import_accounts.xlsx">
                    {{ __('DOWNLOAD') }} XLSX
                </x-links.nav-link>
            </x-form.import>

            <x-form.import
                :user="$user"
                :action="route('profile.import.transactions')"
                :type="__('Transactions')"
                typeField="transactions">
                <table class="datatable table-auto w-full px-4" data-type="default">
                    <thead>
                        <tr>
                            <th>{{ __('ID') }}</th>
                            <th class="dt-low-priority">{{ __('Entry Reference') }}</th>
                            <th class="dt-low-priority">{{ __('Check ID') }}</th>
                            <th>{{ __('Booking Date') }}</th>
                            <th class="dt-low-priority">{{ __('Value Date') }}</th>
                            <th>{{ __('Transaction Amount') }}</th>
                            <th class="dt-low-priority">{{ __('Currency') }}</th>
                            <th class="dt-low-priority">{{ __('Remittance Information') }}</th>
                            <th class="dt-low-priority">{{ __('Bank Transaction Code') }}</th>
                            <th class="dt-low-priority">{{ __('Proprietary Bank Transaction Code') }}</th>
                            <th class="dt-low-priority">{{ __('Internal Transaction ID') }}</th>
                            <th class="dt-low-priority">{{ __('Debtor Name') }}</th>
                            <th class="dt-low-priority">{{ __('Debtor Account') }}</th>
                            <th>{{ __('Account ID') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $defaultTransaction = \App\Models\Transaction::getExampleModel(); @endphp
                        <tr>
                            <td>{{ $defaultTransaction->id }}</td>
                            <td>{{ $defaultTransaction->entryReference }}</td>
                            <td>{{ $defaultTransaction->checkId }}</td>
                            <td>{{ $defaultTransaction->bookingDate->format('d-m-Y H:i:s') }}</td>
                            <td>{{ $defaultTransaction->valueDate->format('d-m-Y H:i:s') }}</td>
                            <td>{{ $defaultTransaction->transactionAmount_amount }}</td>
                            <td>{{ $defaultTransaction->transactionAmount_currency }}</td>
                            <td>{{ $defaultTransaction->remittanceInformationUnstructured }}</td>
                            <td>{{ $defaultTransaction->bankTransactionCode }}</td>
                            <td>{{ $defaultTransaction->proprietaryBankTransactionCode }}</td>
                            <td>{{ $defaultTransaction->internalTransactionId }}</td>
                            <td>{{ $defaultTransaction->debtorName }}</td>
                            <td>{{ $defaultTransaction->debtorAccount }}</td>
                            <td>{{ $defaultAccount->id }}</td>
                        </tr>
                    </tbody>
                </table>

                <x-links.nav-link href="/csv/import_transactions.csv" download="import_transactions.csv">
                    {{ __('DOWNLOAD') }} CSV
                </x-links.nav-link>
                <x-links.nav-link href="/xlsx/import_transactions.xlsx" download="import_transactions.xlsx">
                    {{ __('DOWNLOAD') }} XLSX
                </x-links.nav-link>
            </x-form.import>

            <x-form.import
                :user="$user"
                :action="route('profile.import.balances')"
                :type="__('Balances')"
                typeField="balances">
                <table class="datatable table-auto w-full px-4" data-type="default">
                    <thead>
                        <tr>
                            <th>{{ __('ID') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th class="dt-low-priority">{{ __('Currency') }}</th>
                            <th>{{ __('Balance Type') }}</th>
                            <th>{{ __('Reference Date') }}</th>
                            <th>{{ __('Account ID') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $defaultBalance = \App\Models\Balance::getExampleModel(); @endphp
                        <tr>
                            <td>{{ $defaultBalance->id }}</td>
                            <td>{{ $defaultBalance->amount }}</td>
                            <td>{{ $defaultBalance->currency }}</td>
                            <td>{{ $defaultBalance->balance_type }}</td>
                            <td>{{ $defaultBalance->reference_date->format('d-m-Y H:i:s') }}</td>
                            <td>{{ $defaultAccount->id }}</td>
                        </tr>
                    </tbody>
                </table>

                <x-links.nav-link href="/csv/import_balances.csv" download="import_balances.csv">
                    {{ __('DOWNLOAD') }} CSV
                </x-links.nav-link>
                <x-links.nav-link href="/xlsx/import_balances.xlsx" download="import_balances.xlsx">
                    {{ __('DOWNLOAD') }} XLSX
                </x-links.nav-link>
            </x-form.import>
        </div>
    </div>
</x-app-layout>
