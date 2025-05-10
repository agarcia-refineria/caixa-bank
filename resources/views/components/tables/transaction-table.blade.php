<div class="bg-[#1c1d20] p-4 rounded-xl shadow">
    <table class="datatable min-w-full table-auto nowrap">
        <thead>
            <tr>
                <th class="py-2 dt-low-priority">{{ __('IBAN') }}</th>
                <th class="py-2">{{ __('Date') }}</th>
                <th class="py-2 dt-low-priority">{{ __('Deptor Name') }}</th>
                <th class="py-2 dt-low-priority">{{ __('Transaction') }}</th>
                <th class="py-2">{{ __('Amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                <tr class="border-t border-gray-700" data-amount="{{ number_format($transaction->transactionAmount_amount, 2, ',', '.') }}">
                    <td class="py-2">{{ $transaction->account->iban }}</td>
                    <td class="py-2 dt-date" data-order="{{ $transaction->bookingDate->format('Y-m-d') }}">{{ $transaction->bookingDate->format('d-m-Y') }}</td>
                    <td class="py-2">{{ $transaction->debtorNameFormat }}</td>
                    <td class="py-2">{{ json_decode($transaction->remittanceInformationUnstructured)[0] }}</td>
                    <td class="py-2 @if (number_format($transaction->transactionAmount_amount, 2, ',', '.') < 0) !text-red-600 @else !text-green-600 @endif">{{ number_format($transaction->transactionAmount_amount, 2, ',', '.') }} €</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="border-t border-gray-700 w-full font-bold">
                <td class="py-2"><span class="md:block hidden">Total:</span></td>
                <td class="py-2"><span class="md:hidden">Total:</span></td>
                <td class="py-2"></td>
                <td class="py-2"></td>
                <td class="py-2"></td>
            </tr>
        </tfoot>
    </table>
</div>
