<?php

namespace App\Imports;

use App\Models\Balance;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class BalanceImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Model|Balance|null
     */
    public function model(array $row): Model|Balance|null
    {
        // Check if the account exists for the authenticated user
        $account = Auth::user()->accounts()
            ->where('id', $row['account_id'])
            ->first();

        if ($account) {
            // Check if the balance id exists for the given account
            $existingBalance = $account->balances()
                ->where('id', $row['id'])
                ->first();

            if ($existingBalance) {
                // If the balance already exists update it
                $existingBalance->update([
                    'amount' => $row['amount'],
                    'currency' => $row['currency'],
                    'balance_type' => $row['balance_type'],
                    'reference_date' => Date::excelToDateTimeObject($row['reference_date'])->format('d-m-Y H:i:s'),
                    'account_id' => $account->id,
                ]);

                return $existingBalance;
            }

            // If the balance does not exist, create a new one
            return new Balance([
                'id' => $row['id'],
                'amount' => $row['amount'],
                'currency' => $row['currency'],
                'balance_type' => $row['balance_type'],
                'reference_date' => Date::excelToDateTimeObject($row['reference_date'])->format('d-m-Y H:i:s'),
                'account_id' => $account->id,
            ]);
        }

        // If the account does not exist for the authenticated user, return null
        return null;
    }
}
