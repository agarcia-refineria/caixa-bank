<div class="bg-[#1c1d20] p-4 rounded-xl shadow">
    <table class="datatable min-w-full table-auto nowrap">
        <thead>
            <tr>
                <th class="py-2 dt-low-priority">{{ __('IBAN') }}</th>
                <th class="py-2">{{ __('Date') }}</th>
                <th class="py-2 dt-low-priority">{{ __('Deptor Name') }}</th>
                <th class="py-2 dt-low-priority">{{ __('Transaction') }}</th>
                <th class="py-2">{{ __('Amount') }}</th>

                @if (isset($actions))
                    <th class="py-2 dt-low-priority">{{ __('Actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                <tr class="border-t border-gray-700" data-amount="{{ number_format($transaction->transactionAmount_amount, 2, ',', '.') }}">
                    <td class="py-2">{{ $transaction->account->iban }}</td>
                    <td class="py-2 dt-date" data-order="{{ $transaction->bookingDate->format('Y-m-d') }}">{{ $transaction->bookingDate->format('d-m-Y') }}</td>
                    <td class="py-2">{{ $transaction->debtorNameFormat }}</td>
                    <td class="py-2">{{ json_decode($transaction->remittanceInformationUnstructured) ? json_decode($transaction->remittanceInformationUnstructured)[0] : '--' }}</td>
                    <td class="py-2 @if (number_format($transaction->transactionAmount_amount, 2, ',', '.') < 0) !text-red-600 @else !text-green-600 @endif">{{ number_format($transaction->transactionAmount_amount, 2, ',', '.') }} €</td>

                    @if (isset($actions))
                        <td class="py-2">
                            <x-buttons.primary-button class="py-2" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-transaction-update-{{ $transaction->code }}')">
                                {{ __('UPDATE') }}
                            </x-buttons.primary-button>
                            <x-buttons.danger-button class="py-2" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-transaction-delete-{{ $transaction->code }}')">
                                {{ __('DELETE') }}
                            </x-buttons.danger-button>

                            <x-ui.modal name="confirm-transaction-update-{{ $transaction->code }}" maxWidth="full" margin="sm:px-[50px]" focusable>
                                <x-pages.profile.transaction
                                    :transaction="$transaction"
                                    :account="$account"
                                    :user="$user" />
                            </x-ui.modal>

                            <x-ui.modal name="confirm-transaction-delete-{{ $transaction->code }}" focusable>
                                <form method="post" action="{{ route('profile.transaction.destroy') }}" class="p-6">
                                    @csrf
                                    @method('delete')

                                    <input type="hidden" name="account_id" value="{{ $account->code }}" />
                                    <input type="hidden" name="transaction_id" value="{{ $transaction->code }}" />

                                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        {{ __('Are you sure you want to delete your transaction?') }}
                                    </h2>

                                    <div class="mt-6 flex justify-end">
                                        <x-buttons.secondary-button x-on:click="$dispatch('close')">
                                            {{ __('Cancel') }}
                                        </x-buttons.secondary-button>

                                        <x-buttons.danger-button class="ms-3">
                                            {{ __('Delete Transaction') }}
                                        </x-buttons.danger-button>
                                    </div>
                                </form>
                            </x-ui.modal>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
        @if (!isset($noFooter))
            <tfoot>
                <tr class="border-t border-gray-700 w-full font-bold">
                    <td class="py-2"><span class="md:block hidden">Total:</span></td>
                    <td class="py-2"><span class="md:hidden">Total:</span></td>
                    <td class="py-2"></td>
                    <td class="py-2"></td>
                    <td class="py-2"></td>
                </tr>
            </tfoot>
        @endif
    </table>
</div>
