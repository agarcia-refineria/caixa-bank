<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        'bookingDate' => 'datetime',
        'valueDate' => 'datetime',
    ];

    protected $fillable = [
        'id',
        'entryReference',
        'checkId',
        'bookingDate',
        'valueDate',
        'transactionAmount_amount',
        'transactionAmount_currency',
        'remittanceInformationUnstructured',
        'bankTransactionCode',
        'proprietaryBankTransactionCode',
        'internalTransactionId',
        'debtorName',
        'debtorAccount',
        'account_id',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    /**
     * Scope a query to only include transactions of a given account.
     * @param $query
     * @return mixed
     */
    public function scopeOrderDate($query)
    {
        return $query->orderBy('bookingDate', 'desc');
    }

    /**
     * Get the debtor name in a formatted way.
     * @return string
     */
    public function getDebtorNameFormatAttribute()
    {
        return $this->attributes['debtorName'] ? str_replace(';', ' ', $this->attributes['debtorName']) : '--';
    }

    public static function getExampleModel()
    {
        return new self([
            'entryReference' => '1234567890',
            'checkId' => '1234567890',
            'bookingDate' => now(),
            'valueDate' => now(),
            'transactionAmount_amount' => 100.00,
            'transactionAmount_currency' => 'EUR',
            'remittanceInformationUnstructured' => 'Payment for services',
            'bankTransactionCode' => '1234',
            'proprietaryBankTransactionCode' => '5678',
            'internalTransactionId' => 'abcd1234',
            'debtorName' => 'John Doe',
            'debtorAccount' => 'DE89370400440532013000',
        ]);
    }
}
