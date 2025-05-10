<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        'reference_date' => 'date',
    ];

    private $balanceTypes = [
        'closingBooked',
        'forwardAvailable',
    ];

    protected $fillable = [
        'amount',
        'currency',
        'balance_type',
        'reference_date',
        'account_id',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'code');
    }

    /**
     * Get the balance for the current month.
     * @param $query
     * @return mixed
     */
    public function scopeCurrentMonth($query)
    {
        $date = session('month') ?? now()->format('m-Y');
        $date = \DateTime::createFromFormat('m-Y', $date);

        if ($date === false) {
            return null;
        }

        $startDate = $date->modify('first day of this month')->format('Y-m-d');
        $endDate = $date->modify('last day of this month')->format('Y-m-d');
        return $query->whereBetween('reference_date', [$startDate, $endDate]);
    }

    /**
     * Order by reference date.
     * @param $query
     * @return mixed
     */
    public function scopeLastInstance($query)
    {
        return $query->orderBy('reference_date', 'desc')->first();
    }

    /**
     * Filter by balance type 1.
     * @param $query
     * @return mixed
     */
    public function scopeBalanceTypeForward($query)
    {
        return $query->where('balance_type', $this->balanceTypes[1]);
    }

    /**
     * Filter by balance type 0.
     * @param $query
     * @return mixed
     */
    public function scopeBalanceTypeClosing($query)
    {
        return $query->where('balance_type', $this->balanceTypes[0]);
    }
}
