<?php

namespace App\Imports;

use App\Models\Account;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AccountImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Model|Account|null
     */
    public function model(array $row): Model|Account|null
    {
        $user = Auth::user();

        // Check if the account already exists for the authenticated user
        $existingAccount = Account::where('user_id', $user->id)
            ->where('id', $row['id'])
            ->first();

        if (!$existingAccount) {
            // Check if the account does not exist
            $existingAccount = Account::find($row['id']);

            if ($existingAccount) {
                // If the account exists but not for the current user, return null or user dosntt have a bank
                return null;
            }

            return new Account([
                'id' => $row['id'],
                'name' => $row['name'],
                'iban' => $row['iban'],
                'bban' => $row['bban'] ?? '',
                'status' => $row['status'] ?? '',
                'owner_name' => $row['owner_name'],
                'created' => Date::excelToDateTimeObject($row['created'])->format('d-m-Y H:i:s'),
                'last_accessed' => Date::excelToDateTimeObject($row['last_accessed'])->format('d-m-Y H:i:s'),
                'institution_id' => $row['institution_id'],
                'user_id' => $user->id,
                'type' => Account::$accountTypes['manual'],
            ]);
        } else {
            if ($existingAccount->type !== Account::$accountTypes['manual']) {
                // If the account exists but is not a manual account, return null
                return null;
            }

            // If the account already exists for the user, update it
            $existingAccount->update([
                'name' => $row['name'],
                'iban' => $row['iban'],
                'bban' => $row['bban'] ?? '',
                'status' => $row['status'] ?? '',
                'owner_name' => $row['owner_name'],
                'created' => Date::excelToDateTimeObject($row['created'])->format('d-m-Y H:i:s'),
                'last_accessed' => Date::excelToDateTimeObject($row['last_accessed'])->format('d-m-Y H:i:s'),
                'institution_id' => $row['institution_id'],
            ]);

            return $existingAccount;
        }
    }
}
