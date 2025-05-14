<div class="bg-[#1c1d20] p-4 rounded-xl shadow">
    <table class="datatable min-w-full table-auto nowrap text-left u-footer">
        <thead>
            <tr>
                <th class="py-2 dt-low-priority">{{ __('IBAN') }}</th>
                <th class="py-2">{{ __('Owner Name') }}</th>
                <th class="py-2">{{ __('Balance') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($accounts as $account)
                @php $lastInstance = $account->balances()->balanceTypeForward()->lastInstance()->first(); @endphp
                <tr class="border-t border-gray-700" data-amount="{{ number_format($lastInstance ? $lastInstance->amount : 0, 2, ',', '.') }}">
                    <td class="py-2" width="25%">{{ $account->iban }}</td>
                    <td class="py-2">{{ $account->owner_name }}</td>
                    <td class="py-2 @if (number_format($lastInstance ? $lastInstance->amount : 0, 2, ',', '.') < 0) !text-red-600 @else !text-green-600 @endif" width="25%">{{ number_format($lastInstance ? $lastInstance->amount : 0, 2, ',', '.') }} €</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="border-t border-gray-700 w-full font-bold">
                <td class="py-2"><span class="md:block hidden">Total:</span></td>
                <td class="py-2"><span class="md:hidden">Total:</span></td>
                <td class="py-2"></td>
            </tr>
        </tfoot>
    </table>
</div>
