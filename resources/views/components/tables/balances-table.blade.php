@props([
    'balances',
    'account',
    'user',
    'noFooter' => false,
    'actions' => false,
    'type' => 'static',
])

<div class="bg-[#1c1d20] p-4 rounded-xl shadow">
    <table class="datatable min-w-full table-auto nowrap text-left  @if (!$noFooter) u-footer @endif" data-type="{{ $type }}" {{ $attributes->merge() }}>
        <thead>
            <tr>
                <th class="py-2" data-column="iban">{{ __('IBAN') }}</th>
                <th class="py-2" data-column="reference_date">{{ __('Reference Date') }}</th>
                <th class="py-2" data-column="balance_type">{{ __('Balance Type') }}</th>
                <th class="py-2 dt-low-priority" data-column="amount">{{ __('Amount') }}</th>
                <th class="py-2" data-column="currency">{{ __('Currency') }}</th>

                @if ($actions)
                    <th class="py-2 dt-low-priority">{{ __('Actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($balances as $balance)
                <tr class="border-t border-gray-700">
                    <td class="py-2">{{ $balance->account->iban }}</td>
                    <td class="py-2">{{ $balance->reference_date->format('d-m-Y') }}</td>
                    <td class="py-2">{{ $balance->balance_type }}</td>
                    <td class="py-2 @if (number_format($balance->amount, 2, ',', '.') < 0) !text-red-600 @else !text-green-600 @endif">{{ $balance->amount }} €</td>
                    <td class="py-2">{{ $balance->currency }}</td>

                    @if ($actions)
                        <td class="py-2">
                            <x-buttons.primary-button class="py-2" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-transaction-update-{{ $balance->code }}')">
                                {{ __('UPDATE') }}
                            </x-buttons.primary-button>
                            <x-buttons.danger-button class="py-2" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-transaction-delete-{{ $balance->code }}')">
                                {{ __('DELETE') }}
                            </x-buttons.danger-button>

                            <x-ui.modal name="confirm-transaction-update-{{ $balance->code }}" maxWidth="full" margin="sm:px-[50px]" focusable>
                                @include('partials.profile.balance', [
                                    'balance' => $balance,
                                    'account' => $account,
                                    'user' => $user
                                ])
                            </x-ui.modal>

                            <x-ui.modal name="confirm-transaction-delete-{{ $balance->code }}" focusable>
                                <form method="post" action="{{ route('profile.balance.destroy') }}" class="p-6">
                                    @csrf
                                    @method('delete')

                                    <input type="hidden" name="account_id" value="{{ $account->code }}" />
                                    <input type="hidden" name="balance_id" value="{{ $balance->code }}" />

                                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        {{ __('Are you sure you want to delete your balance?') }}
                                    </h2>

                                    <div class="mt-6 flex justify-end">
                                        <x-buttons.secondary-button x-on:click="$dispatch('close')">
                                            {{ __('Cancel') }}
                                        </x-buttons.secondary-button>

                                        <x-buttons.danger-button class="ms-3">
                                            {{ __('Delete Balance') }}
                                        </x-buttons.danger-button>
                                    </div>
                                </form>
                            </x-ui.modal>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>

        @if (!$noFooter)
            <tfoot>
                <tr class="border-t border-gray-700 w-full font-bold">
                    <td class="py-2">Total:</td>
                    <td class="py-2"></td>
                    <td class="py-2"></td>
                    <td class="py-2"></td>
                    <td class="py-2"></td>

                    @if ($actions)
                        <td class="py-2"></td>
                    @endif
                </tr>
            </tfoot>
        @endif
    </table>
</div>
