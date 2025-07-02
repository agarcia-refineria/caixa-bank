<?php

namespace App\Imports;

use App\Models\Transaction;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Str;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class TransactionImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Model|Transaction|null
     */
    public function model(array $row): Model|Transaction|null
    {
        // Check if the account exists for the authenticated user
        $account = Auth::user()->accounts()
            ->where('id', $row['account_id'])
            ->first();

        if ($account) {
            // Check if the transaction id exists for the given account
            $existingTransaction = $account->transactions()
                ->where('id', $row['id'])
                ->first();

            if ($existingTransaction) {
                // If the transaction already exists, update it
                $existingTransaction->update([
                    'entryReference' => $row['entry_reference'] ?? null,
                    'checkId' => $row['check_id'] ?? null,
                    'bookingDate' => Date::excelToDateTimeObject($row['booking_date'])->format('d-m-Y H:i:s'),
                    'valueDate' => Date::excelToDateTimeObject($row['value_date'])->format('d-m-Y H:i:s'),
                    'transactionAmount_amount' => $row['transaction_amount'],
                    'transactionAmount_currency' => $row['currency'] ?? null,
                    'remittanceInformationUnstructured' => $row['remittance_information'] ?? null,
                    'bankTransactionCode' => $row['bank_transaction_code'] ?? null,
                    'proprietaryBankTransactionCode' => $row['proprietary_bank_transaction_code'] ?? null,
                    'internalTransactionId' => $row['internal_transaction_id'] ?? null,
                    'debtorName' => $row['debtor_name'] ?? null,
                    'debtorAccount' => $row['debtor_account'] ?? null,
                    'category_id' => Transaction::getCategoryId($row['remittance_information'] ?? null),
                ]);

                return $existingTransaction;
            }

            // If the transaction does not exist, create a new one
            return new Transaction([
                'id' => Str::uuid(),
                'entryReference' => $row['entry_reference'] ?? null,
                'checkId' => $row['check_id'] ?? null,
                'bookingDate' => Date::excelToDateTimeObject($row['booking_date'])->format('d-m-Y H:i:s'),
                'valueDate' => Date::excelToDateTimeObject($row['value_date'])->format('d-m-Y H:i:s'),
                'transactionAmount_amount' => $row['transaction_amount'],
                'transactionAmount_currency' => $row['currency'] ?? null,
                'remittanceInformationUnstructured' => $row['remittance_information'] ?? null,
                'bankTransactionCode' => $row['bank_transaction_code'] ?? null,
                'proprietaryBankTransactionCode' => $row['proprietary_bank_transaction_code'] ?? null,
                'internalTransactionId' => $row['internal_transaction_id'] ?? null,
                'debtorName' => $row['debtor_name'] ?? null,
                'debtorAccount' => $row['debtor_account'] ?? null,
                'account_id' => $account->id,
                'category_id' => Transaction::getCategoryId($row['remittance_information'] ?? null),
            ]);
        }

        return null;
    }
}
