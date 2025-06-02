@props(['user', 'account', 'transaction' => null])

<form @if ($transaction) action="{{ route('profile.transaction.update') }}" data-id="{{ $transaction->code }}" @else action="{{ route('profile.transaction.create') }}" @endif method="POST" class="relative md:px-0 px-6 bg-main2 rounded-lg py-6">
    @csrf

    @if ($transaction)
        @method('patch')
        <input type="hidden" name="transaction_id" value="{{ $transaction->code }}" />
    @endif

    <input type="hidden" name="account_id" value="{{ $account->code }}" />

    <!-- Show the bank logo and name -->
    <h2 class="flex gap-4 items-center text-lg font-medium text-primary w-full sm:px-6 lg:px-8 pb-3">
        <img src="{{ $user->bank->institution->logo }}" alt="{{ $user->bank->institution->name }}" width="32" height="32" class="h-8 w-8 mr-2">
        {{ $account->institution?->name }} - {{ $account->iban }} <span class="md:block hidden">({{ $account->type }})</span>
    </h2>

    <!-- Show the account buttons -->
    <div class="grid grid-cols-3 gap-4 py-6 sm:px-6 lg:px-8 w-full">
        <x-inputs.input :errorName="$transaction ? 'Transaction.'.$transaction->code.'.entryReference' : 'newTransaction.entryReference'" :value="$transaction ? $transaction->entryReference : null" type="text" name="{{ $transaction ? 'Transaction['.$transaction->code.'][entryReference]' : 'newTransaction[entryReference]' }}" :label="__('Entry Reference')"/>
        <x-inputs.input :errorName="$transaction ? 'Transaction.'.$transaction->code.'.checkId' : 'newTransaction.checkId'" :value="$transaction ? $transaction->checkId : null" type="text" name="{{ $transaction ? 'Transaction['.$transaction->code.'][checkId]' : 'newTransaction[checkId]' }}" :label="__('Check ID')"/>
        <x-inputs.input :errorName="$transaction ? 'Transaction.'.$transaction->code.'.bookingDate' : 'newTransaction.bookingDate'" :value="$transaction ? $transaction->bookingDate->format('Y-m-d') : null" type="date" required="required" name="{{ $transaction ? 'Transaction['.$transaction->code.'][bookingDate]' : 'newTransaction[bookingDate]' }}" :label="__('Booking Date')"/>
        <x-inputs.input :errorName="$transaction ? 'Transaction.'.$transaction->code.'.valueDate' : 'newTransaction.valueDate'" :value="$transaction ? $transaction->valueDate->format('Y-m-d') : null" type="date" name="{{ $transaction ? 'Transaction['.$transaction->code.'][valueDate]' : 'newTransaction[valueDate]' }}" :label="__('Value Date')"/>
        <x-inputs.input :errorName="$transaction ? 'Transaction.'.$transaction->code.'.transactionAmount_amount' : 'newTransaction.transactionAmount_amount'" :value="$transaction ? $transaction->transactionAmount_amount : null" type="number" step="0.01" required="required" name="{{ $transaction ? 'Transaction['.$transaction->code.'][transactionAmount_amount]' : 'newTransaction[transactionAmount_amount]' }}" :label="__('Amount')"/>
        <x-inputs.input :errorName="$transaction ? 'Transaction.'.$transaction->code.'.transactionAmount_currency' : 'newTransaction.transactionAmount_currency'" :value="$transaction ? $transaction->transactionAmount_currency : null" minlength="3" maxlength="3" required="required" type="text" name="{{ $transaction ? 'Transaction['.$transaction->code.'][transactionAmount_currency]' : 'newTransaction[transactionAmount_currency]' }}" :label="__('Currency')"/>
        <x-inputs.input :errorName="$transaction ? 'Transaction.'.$transaction->code.'.remittanceInformationUnstructured' : 'newTransaction.remittanceInformationUnstructured'" :value="$transaction ? $transaction->remittanceInformationUnstructuredFormat : null" type="text" name="{{ $transaction ? 'Transaction['.$transaction->code.'][remittanceInformationUnstructured]' : 'newTransaction[remittanceInformationUnstructured]' }}" :label="__('Remittance Information')"/>
        <x-inputs.input :errorName="$transaction ? 'Transaction.'.$transaction->code.'.bankTransactionCode' : 'newTransaction.bankTransactionCode'" :value="$transaction ? $transaction->bankTransactionCode : null" type="text" name="{{ $transaction ? 'Transaction['.$transaction->code.'][bankTransactionCode]' : 'newTransaction[bankTransactionCode]' }}" :label="__('Bank Transaction Code')"/>
        <x-inputs.input :errorName="$transaction ? 'Transaction.'.$transaction->code.'.proprietaryBankTransactionCode' : 'newTransaction.proprietaryBankTransactionCode'" :value="$transaction ? $transaction->proprietaryBankTransactionCode : null" type="text" name="{{ $transaction ? 'Transaction['.$transaction->code.'][proprietaryBankTransactionCode]' : 'newTransaction[proprietaryBankTransactionCode]' }}" :label="__('Proprietary Bank Transaction Code')"/>
        <x-inputs.input :errorName="$transaction ? 'Transaction.'.$transaction->code.'.internalTransactionId' : 'newTransaction.internalTransactionId'" :value="$transaction ? $transaction->internalTransactionId : null" type="text" name="{{ $transaction ? 'Transaction['.$transaction->code.'][internalTransactionId]' : 'newTransaction[internalTransactionId]' }}" :label="__('Internal Transaction ID')"/>
        <x-inputs.input :errorName="$transaction ? 'Transaction.'.$transaction->code.'.debtorName' : 'newTransaction.debtorName'" :value="$transaction ? $transaction->debtorName : null" type="text" name="{{ $transaction ? 'Transaction['.$transaction->code.'][debtorName]' : 'newTransaction[debtorName]' }}" :label="__('Debtor Name')"/>
        <x-inputs.input :errorName="$transaction ? 'Transaction.'.$transaction->code.'.debtorAccount' : 'newTransaction.debtorAccount'" :value="$transaction ? $transaction->debtorAccount : null" type="text" name="{{ $transaction ? 'Transaction['.$transaction->code.'][debtorAccount]' : 'newTransaction[debtorAccount]' }}" :label="__('Debtor Account')"/>
    </div>

    @if (isset($transaction))
        <div class="sm:px-6 lg:px-8 flex md:flex-row flex-col justify-between">
            <div class="mt-2 flex justify-center md:justify-start gap-4">
                <x-buttons.secondary-button type="submit">
                    {{ __('Update Transaction') }}
                </x-buttons.secondary-button>
            </div>
        </div>
    @else
        <div class="sm:px-6 lg:px-8">
            <x-buttons.secondary-button type="submit">
                {{ __('Create Transaction') }}
            </x-buttons.secondary-button>
        </div>
    @endif
</form>
