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
}
