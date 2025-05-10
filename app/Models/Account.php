<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        'created' => 'datetime',
        'last_accessed' => 'datetime',
        'transactions_disabled_date' => 'datetime',
        'balance_disabled_date' => 'datetime',
    ];

    protected $fillable = [
        'id',
        'name',
        'iban',
        'bban',
        'status',
        'owner_name',
        'created',
        'last_accessed',
        'institution_id',
        'user_id'
    ];

    /**
     * Get the account code.
     * @return mixed
     */
    public function getCodeAttribute()
    {
        return $this->attributes['id'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'account_id', 'code');
    }

    public function balances()
    {
        return $this->hasMany(Balance::class, 'account_id', 'code');
    }

    public function bankDataSync()
    {
        return $this->hasMany(BankDataSync::class, 'account_id', 'code');
    }

    /**
     * Order the accounts by their order attribute.
     * @param $query
     * @return mixed
     */
    public function scopeSortOrder($query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * Generate a random color for the chart but with a palet of colors.
     * @param $usedColors
     * @return string
     */
    public function getUsedColors(&$usedColors)
    {
        do {
            $r = mt_rand(0, 100);
            $g = mt_rand(0, 255);
            $b = mt_rand(200, 255);
            $color = sprintf("#%02X%02X%02X", $r, $g, $b);
        } while (in_array($color, $usedColors));
        $usedColors[] = $color;
        return $color;
    }

    /**
     * Get the number of bank data syncs for today.
     * @return int
     */
    public function getBankDataSyncCountAttribute()
    {
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        return $this->bankDataSync()
            ->where('created_at', '>=', $todayStart)
            ->where('created_at', '<=', $todayEnd)
            ->count();
    }

    /**
     * Get the number of bank data syncs for today for type transactions.
     * @return int
     */
    public function getBankDataSyncTransactionsCountAttribute()
    {
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        return $this->bankDataSync()->dataTypeTransaction()
            ->where('created_at', '>=', $todayStart)
            ->where('created_at', '<=', $todayEnd)
            ->count();
    }

    /**
     * Get the number of bank data syncs for today for type balances.
     * @return int
     */
    public function getBankDataSyncBalancesCountAttribute()
    {
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        return $this->bankDataSync()->dataTypeBalance()
            ->where('created_at', '>=', $todayStart)
            ->where('created_at', '<=', $todayEnd)
            ->count();
    }

    /**
     * Check if the transactions is disabled.
     * @return bool
     */
    public function getTransactionsDisabledAttribute(): bool
    {
        return $this->transactions_disabled_date !== null && $this->transactions_disabled_date->isFuture();
    }

    /**
     * Check if the balance is disabled.
     * @return bool
     */
    public function getBalanceDisabledAttribute(): bool
    {
        return $this->balance_disabled_date !== null && $this->balance_disabled_date->isFuture();
    }

    /**
     * Get the transactions for the current month.
     * @return mixed
     */
    public function getTransactionsCurrentMonthAttribute()
    {
        $date = session('month') ?? now()->format('m-Y');
        $parsedDate = \Carbon\Carbon::createFromFormat('m-Y', $date);

        $startOfMonth = $parsedDate->copy()->startOfMonth()->startOfDay();
        $endOfMonth = $parsedDate->copy()->endOfMonth()->endOfDay();

        return $this->transactions()
            ->whereBetween('bookingDate', [$startOfMonth, $endOfMonth])
            ->orderDate()
            ->get();
    }

    /**
     * Get the transactions for the current month that are expenses.
     * @return mixed
     */
    public function getTransactionsExpensesCurrentMonthAttribute()
    {
        $date = session('month') ?? now()->format('m-Y');
        $parsedDate = \Carbon\Carbon::createFromFormat('m-Y', $date);

        $startOfMonth = $parsedDate->copy()->startOfMonth()->startOfDay();
        $endOfMonth = $parsedDate->copy()->endOfMonth()->endOfDay();

        return $this->transactions()
            ->whereBetween('bookingDate', [$startOfMonth, $endOfMonth])
            ->where('transactionAmount_amount', '<', 0)
            ->orderDate()
            ->get();
    }

    /**
     * Get the transactions for the current month that are income.
     * @return mixed
     */
    public function getTransactionsIncomeCurrentMonthAttribute()
    {
        $date = session('month') ?? now()->format('m-Y');
        $parsedDate = \Carbon\Carbon::createFromFormat('m-Y', $date);

        $startOfMonth = $parsedDate->copy()->startOfMonth()->startOfDay();
        $endOfMonth = $parsedDate->copy()->endOfMonth()->endOfDay();

        return $this->transactions()
            ->whereBetween('bookingDate', [$startOfMonth, $endOfMonth])
            ->where('transactionAmount_amount', '>', 0)
            ->orderDate()
            ->get();
    }

    /**
     * Get the total expenses for the current month.
     * @return int|mixed
     */
    public function getExpensesAttribute()
    {
        $date = session('month') ?? now()->format('m-Y');
        $parsedDate = \Carbon\Carbon::createFromFormat('m-Y', $date);

        $startOfMonth = $parsedDate->copy()->startOfMonth()->startOfDay();
        $endOfMonth = $parsedDate->copy()->endOfMonth()->endOfDay();

        return $this->transactions()
            ->whereBetween('bookingDate', [$startOfMonth, $endOfMonth])
            ->where('transactionAmount_amount', '<', 0)
            ->sum('transactionAmount_amount');
    }

    /**
     * Get the total income for the current month.
     * @return int|mixed
     */
    public function getIncomeAttribute()
    {
        $date = session('month') ?? now()->format('m-Y');
        $parsedDate = \Carbon\Carbon::createFromFormat('m-Y', $date);

        $startOfMonth = $parsedDate->copy()->startOfMonth()->startOfDay();
        $endOfMonth = $parsedDate->copy()->endOfMonth()->endOfDay();

        return $this->transactions()
            ->whereBetween('bookingDate', [$startOfMonth, $endOfMonth])
            ->where('transactionAmount_amount', '>', 0)
            ->sum('transactionAmount_amount');
    }

    public function getChartTransactionsValuesAttribute()
    {
        return $this->transactionsExpensesCurrentMonth
            ->groupBy('remittanceInformationUnstructured')
            ->map(fn($group) => $group->sum('transactionAmount_amount'))
            ->implode(',');
    }

    public function getChartTransactionsLabelsAttribute()
    {
        return $this->transactionsExpensesCurrentMonth
            ->groupBy('remittanceInformationUnstructured')
            ->keys()
            ->map(fn($key) => trim((string) $key, '[]"'))
            ->implode(',');
    }

    public function getChartTransactionsColorsAttribute()
    {
        $usedColors = [];
        return $this->transactionsExpensesCurrentMonth
            ->groupBy('remittanceInformationUnstructured')
            ->map(function ($group, $key) use (&$usedColors) {
                return $this->getUsedColors($usedColors);
            })->implode(',');
    }

    public function getChartBalancesValuesAttribute()
    {
        return $this->balances()
            ->balanceTypeForward()
            ->currentMonth()
            ->pluck('amount')
            ->implode(',');
    }

    public function getChartBalancesLabelsAttribute()
    {
        return $this->balances()
            ->balanceTypeForward()
            ->currentMonth()
            ->pluck('reference_date')
            ->map(fn($date) => trim($date->format('d-m-Y'), '[]"'))
            ->implode(',');
    }
}
