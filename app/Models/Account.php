<?php

namespace App\Models;

use Auth;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Helpers\ColorHelper;

/**
 * @property int $id
 * @property string|null $name
 * @property string|null $iban
 * @property string|null $bban
 * @property string|null $status
 * @property string|null $owner_name
 * @property Carbon $created
 * @property Carbon $last_accessed
 * @property Carbon|null $transactions_disabled_date
 * @property Carbon|null $balance_disabled_date
 * @property int $institution_id
 * @property int $user_id
 * @property int $order
 * @property string $type
 * @property boolean $apply_expenses_monthly
 * @property string $paysheet_id
 * @property-read Collection<int, Balance> $balances
 * @property-read int|null $balances_count
 * @property-read Collection<int, BankDataSync> $bankDataSync
 * @property-read int $bank_data_sync_count
 * @property-read bool $balance_disabled
 * @property-read int $bank_data_sync_balances_count
 * @property-read int $bank_data_sync_transactions_count
 * @property-read mixed $chart_balances_labels
 * @property-read mixed $chart_balances_values
 * @property-read mixed $chart_transactions_colors
 * @property-read mixed $chart_transactions_labels
 * @property-read mixed $chart_transactions_values
 * @property-read mixed|null $code
 * @property-read int|mixed $expenses
 * @property-read int|mixed $income
 * @property-read mixed $is_api
 * @property-read mixed $is_manual
 * @property-read mixed $transactions_current_month
 * @property-read bool $transactions_disabled
 * @property-read mixed $transactions_expenses_current_month
 * @property-read mixed $transactions_income_current_month
 * @property-read mixed $average_month_expenses
 * @property-read mixed $average_month_expenses_excluding_categories
 * @property-read array $month_expenses_excluding_categories
 * @property-read Institution $institution
 * @property-read Collection<int, Transaction> $transactions
 * @property-read <int, Transaction> $paysheet
 * @property-read int|null $transactions_count
 * @property-read User $user
 * @method static Builder|Account newModelQuery()
 * @method static Builder|Account newQuery()
 * @method static Builder|Account onlyApi()
 * @method static Builder|Account onlyManual()
 * @method static Builder|Account onlyType($type)
 * @method static Builder|Account query()
 * @method static Builder|Account sortOrder()
 * @method static Builder|Account whereBalanceDisabledDate($value)
 * @method static Builder|Account whereBban($value)
 * @method static Builder|Account whereCreated($value)
 * @method static Builder|Account whereIban($value)
 * @method static Builder|Account whereId($value)
 * @method static Builder|Account whereInstitutionId($value)
 * @method static Builder|Account whereLastAccessed($value)
 * @method static Builder|Account whereName($value)
 * @method static Builder|Account whereOrder($value)
 * @method static Builder|Account whereOwnerName($value)
 * @method static Builder|Account whereStatus($value)
 * @method static Builder|Account whereTransactionsDisabledDate($value)
 * @method static Builder|Account whereType($value)
 * @method static Builder|Account whereUserId($value)
 * @property-read mixed $chart_transactions_colors_category
 * @property-read mixed $chart_transactions_labels_category
 * @property-read mixed $chart_transactions_values_category
 * @property-read bool $show_update_all
 * @mixin Eloquent
 */
class Account extends Model
{
    use HasFactory;

    public $timestamps = false;

    public static array $accountTypes = [
        'api' => 'api',
        'manual' => 'manual',
    ];

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
        'type',
        'user_id',
        'order'
    ];

    /**
     * Get the account code.
     *
     * @noinspection PhpUnused
     * @return mixed
     */
    public function getCodeAttribute(): mixed
    {
        return $this->attributes['id'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function paysheet(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'account_id', 'code');
    }

    public function balances(): HasMany
    {
        return $this->hasMany(Balance::class, 'account_id', 'code');
    }

    public function bankDataSync(): HasMany
    {
        return $this->hasMany(BankDataSync::class, 'account_id', 'code');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(CategoryAccount::class, 'account_id', 'code');
    }

    /**
     * Order the accounts by their order attribute.
     *
     * @noinspection PhpUnused
     */
    public function scopeSortOrder($query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * Generate a random color for the chart but with a color from tailwind-colors.json.
     * @param $usedColors
     * @return string
     */
    public function getUsedColors(&$usedColors): string
    {
        //$colorsJson = file_get_contents(base_path('tailwind-colors.json'));
        //$colorsArray = json_decode($colorsJson, true);
        $mainColor = Auth::user()->theme_main3;
        $minDistance = 10;

        if (empty($usedColors)) {
            $usedColors[] = $mainColor;
            return $mainColor;
        }

        $rgb = ColorHelper::hexToRgb($mainColor);

        // If color is white or black, generate a random color
        if ($rgb[0] == 255 && $rgb[1] == 255 && $rgb[2] == 255) {
            $rgb = [rand(0, 255), rand(0, 255), rand(0, 255)];
        } elseif ($rgb[0] == 0 && $rgb[1] == 0 && $rgb[2] == 0) {
            $rgb = [rand(0, 255), rand(0, 255), rand(0, 255)];
        }

        do {
            list($r, $g, $b) = $rgb;

            // Switch the RGB values to see wich one is the most dominant
            // and set the other two to random values between 0 and 255

            switch (true) {
                case $r > $g && $r > $b:
                    // Red
                    $r = rand(200, 255);
                    $g = rand(0, 255);
                    $b = rand(0, 0);
                    break;
                case $g > $r && $g > $b:
                    // Green
                    $r = rand(0, 0);
                    $g = rand(200, 255);
                    $b = rand(0, 255);
                    break;
                case $b > $r && $b > $g:
                    // Blue
                    $r = mt_rand(0, 0);
                    $g = mt_rand(0, 255);
                    $b = mt_rand(200, 255);
                    break;
            }

            $isTooSimilar = false;
            foreach ($usedColors as $used) {
                if ($this->colorDistance([$r, $g, $b], ColorHelper::hexToRgb($used)) < $minDistance) {
                    $isTooSimilar = true;
                    break;
                }
            }

            $color = sprintf("#%02X%02X%02X", $r, $g, $b);
        } while ($isTooSimilar);
        $usedColors[] = $color;
        return $color;
    }

    public function colorDistance($c1, $c2): float
    {
        list($r1, $g1, $b1) = $c1;
        list($r2, $g2, $b2) = $c2;

        return sqrt(pow($r1 - $r2, 2) + pow($g1 - $g2, 2) + pow($b1 - $b2, 2));
    }

    /**
     * Scope only accounts created by the user.
     *
     * @noinspection PhpUnused
     */
    public function scopeOnlyManual($query)
    {
        return $query->where('type', Account::$accountTypes['manual']);
    }

    /**
     * Scope only accounts created by the API.
     *
     * @noinspection PhpUnused
     */
    public function scopeOnlyApi($query)
    {
        return $query->where('type', Account::$accountTypes['api']);
    }

    /**
     * Scope only accounts created by the type.
     *
     * @noinspection PhpUnused
     */
    public function scopeOnlyType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Bool is the account is created by the API.
     *
     * @noinspection PhpUnused
     */
    public function getIsApiAttribute(): bool
    {
        return $this->type == Account::$accountTypes['api'];
    }

    /**
     * Bool is the account is created by the user.
     *
     * @noinspection PhpUnused
     */
    public function getIsManualAttribute(): bool
    {
        return $this->type == Account::$accountTypes['manual'];
    }

    /**
     * Get the number of bank data syncs for today.
     *
     * @noinspection PhpUnused
     * @return int
     */
    public function getBankDataSyncCountAttribute(): int
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
     *
     * @noinspection PhpUnused
     * @return int
     */
    public function getBankDataSyncTransactionsCountAttribute(): int
    {
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        return tap($this->bankDataSync()->getQuery())
            ->dataTypeTransaction()
            ->where('created_at', '>=', $todayStart)
            ->where('created_at', '<=', $todayEnd)
            ->count();
    }

    /**
     * Get the number of bank data syncs for today for type balances.
     *
     * @noinspection PhpUnused
     * @return int
     */
    public function getBankDataSyncBalancesCountAttribute(): int
    {
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        return tap($this->bankDataSync()->getQuery())
            ->dataTypeBalance()
            ->where('created_at', '>=', $todayStart)
            ->where('created_at', '<=', $todayEnd)
            ->count();
    }

    /**
     * Check if the transactions is disabled.
     *
     * @noinspection PhpUnused
     * @return bool
     */
    public function getTransactionsDisabledAttribute(): bool
    {
        return $this->transactions_disabled_date !== null && $this->transactions_disabled_date->isFuture();
    }

    /**
     * Check if the balance is disabled.
     *
     * @noinspection PhpUnused
     * @return bool
     */
    public function getBalanceDisabledAttribute(): bool
    {
        return $this->balance_disabled_date !== null && $this->balance_disabled_date->isFuture();
    }

    /**
     * Get the transactions for the current month.
     *
     * @noinspection PhpUnused
     * @return \Illuminate\Support\Collection
     */
    public function getTransactionsCurrentMonthAttribute(): \Illuminate\Support\Collection
    {
        $date = session('month') ?? now()->format('m-Y');
        $parsedDate = \Carbon\Carbon::createFromFormat('m-Y', $date);

        $startOfMonth = $parsedDate->copy()->startOfMonth();
        $endOfMonth = $parsedDate->copy()->endOfMonth();

        return $this->transactions()
            ->whereBetween('bookingDate', [$startOfMonth, $endOfMonth])
            ->orderBy('bookingDate', 'desc')
            ->get();
    }

    /**
     * Get the transactions for the current month that are expenses.
     *
     * @noinspection PhpUnused
     * @return \Illuminate\Support\Collection
     */
    public function getTransactionsExpensesCurrentMonthAttribute(): \Illuminate\Support\Collection
    {
        $date = session('month') ?? now()->format('m-Y');
        $parsedDate = \Carbon\Carbon::createFromFormat('m-Y', $date);

        $startOfMonth = $parsedDate->copy()->startOfMonth();
        $endOfMonth = $parsedDate->copy()->endOfMonth();

        return $this->transactions()
            ->whereBetween('bookingDate', [$startOfMonth, $endOfMonth])
            ->where('transactionAmount_amount', '<', 0)
            ->orderBy('bookingDate', 'desc')
            ->get();
    }

    /**
     * Get the transactions for the current month that are income.
     *
     * @noinspection PhpUnused
     * @return \Illuminate\Support\Collection
     */
    public function getTransactionsIncomeCurrentMonthAttribute(): \Illuminate\Support\Collection
    {
        $date = session('month') ?? now()->format('m-Y');
        $parsedDate = \Carbon\Carbon::createFromFormat('m-Y', $date);

        $startOfMonth = $parsedDate->copy()->startOfMonth();
        $endOfMonth = $parsedDate->copy()->endOfMonth();

        return $this->transactions()
            ->whereBetween('bookingDate', [$startOfMonth, $endOfMonth])
            ->where('transactionAmount_amount', '>', 0)
            ->orderBy('bookingDate', 'desc')
            ->get();
    }

    /**
     * Get the total expenses for the current month.
     *
     * @noinspection PhpUnused
     * @return int|mixed
     */
    public function getExpensesAttribute(): mixed
    {
        $date = session('month') ?? now()->format('m-Y');
        $parsedDate = \Carbon\Carbon::createFromFormat('m-Y', $date);

        $startOfMonth = $parsedDate->copy()->startOfMonth();
        $endOfMonth = $parsedDate->copy()->endOfMonth();

        return $this->transactions()
            ->whereBetween('bookingDate', [$startOfMonth, $endOfMonth])
            ->where('transactionAmount_amount', '<', 0)
            ->sum('transactionAmount_amount');
    }

    /**
     * Get the total income for the current month.
     *
     * @noinspection PhpUnused
     * @return int|mixed
     */
    public function getIncomeAttribute(): mixed
    {
        $date = session('month') ?? now()->format('m-Y');
        $parsedDate = \Carbon\Carbon::createFromFormat('m-Y', $date);

        $startOfMonth = $parsedDate->copy()->startOfMonth();
        $endOfMonth = $parsedDate->copy()->endOfMonth();

        return $this->transactions()
            ->whereBetween('bookingDate', [$startOfMonth, $endOfMonth])
            ->where('transactionAmount_amount', '>', 0)
            ->sum('transactionAmount_amount');
    }

    /**
     * Get the chart transactions values for the current month.
     *
     * @noinspection PhpUnused
     * @return mixed
     */
    public function getChartTransactionsValuesAttribute(): mixed
    {
        return $this->transactions_expenses_current_month
            ->groupBy('remittanceInformationUnstructured')
            ->map(fn($group) => $group->sum('transactionAmount_amount'))
            ->implode(',');
    }

    /**
     * Get the chart transactions labels for the current month.
     *
     * @noinspection PhpUnused
     * @return mixed
     */
    public function getChartTransactionsLabelsAttribute(): mixed
    {
        return $this->transactions_expenses_current_month
            ->groupBy('remittanceInformationUnstructured')
            ->keys()
            ->map(fn($key) => trim((string) $key, '[]"'))
            ->implode(',');
    }

    /**
     * Get the chart transactions colors for the current month.
     *
     * @noinspection PhpUnused
     * @return mixed
     */
    public function getChartTransactionsColorsAttribute(): mixed
    {
        $usedColors = [];
        return $this->transactions_expenses_current_month
            ->groupBy('remittanceInformationUnstructured')
            ->map(function () use (&$usedColors) {
                return $this->getUsedColors($usedColors);
            })->implode(',');
    }

    /**
     * Get the chart transactions values for the current month group by category.
     *
     * @noinspection PhpUnused
     * @return mixed
     */
    public function getChartTransactionsValuesCategoryAttribute(): mixed
    {
        return $this->transactions_expenses_current_month
            ->groupBy('category.name')
            ->map(fn($group) => $group->sum('transactionAmount_amount'))
            ->implode(',');
    }

    /**
     * Get the chart transactions labels for the current month group by category.
     *
     * @noinspection PhpUnused
     * @return mixed
     */
    public function getChartTransactionsLabelsCategoryAttribute(): mixed
    {
        return $this->transactions_expenses_current_month
            ->groupBy('category.name')
            ->keys()
            ->map(fn($key) => trim((string) $key, '[]"'))
            ->implode(',');
    }

    /**
     * Get the chart transactions colors for the current month group by category.
     *
     * @noinspection PhpUnused
     * @return mixed
     */
    public function getChartTransactionsColorsCategoryAttribute(): mixed
    {
        $usedColors = [];
        return $this->transactions_expenses_current_month
            ->groupBy('category')
            ->map(function () use (&$usedColors) {
                return $this->getUsedColors($usedColors);
            })->implode(',');
    }

    /**
     * Get the chart balances values for the current month.
     *
     * @noinspection PhpUnused
     * @return mixed
     */
    public function getChartBalancesValuesAttribute(): mixed
    {
        return tap($this->balances()->getQuery())
            ->balanceTypeForward()
            ->currentMonth()
            ->pluck('amount')
            ->implode(',');
    }

    /**
     * Get the chart balances labels for the current month.
     *
     * @noinspection PhpUnused
     * @return mixed
     */
    public function getChartBalancesLabelsAttribute(): mixed
    {
        return tap($this->balances()->getQuery())
            ->balanceTypeForward()
            ->currentMonth()
            ->pluck('reference_date')
            ->map(fn($date) => trim($date->format('d-m-Y'), '[]"'))
            ->implode(',');
    }

    /**
     * Check if the account should show the update all button.
     *
     * @noinspection PhpUnused
     * @return bool
     */
    public function getShowUpdateAllAttribute(): bool
    {
        if ($this->is_api) {
            return !$this->transactions_disabled
                && !$this->balance_disabled
                && $this->bank_data_sync_transactions_count <= ScheduledTasks::$MAX_TIMES
                && $this->bank_data_sync_balances_count <= ScheduledTasks::$MAX_TIMES;
        }

        return false;
    }

    /**
     * Get the average monthly expenses for the currently selected month.
     *
     * @noinspection PhpUnused
     * @return float
     * @property-read float $average_month_expenses The calculated average monthly expense.
     *
     */
    public function getAverageMonthExpensesAttribute(): float
    {
        $allTransactions = $this->transactions()
            ->where('transactionAmount_amount', '<', 0)
            ->orderBy('bookingDate')
            ->get();

        if ($allTransactions->isEmpty()) {
            return 0.0;
        }

        $startDate = \Carbon\Carbon::parse($allTransactions->first()->bookingDate)->startOfMonth();
        $endDate = \Carbon\Carbon::parse($allTransactions->last()->bookingDate)->endOfMonth();

        $months = [];
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $months[$current->format('Y-m')] = 0.0;
            $current->addMonth();
        }

        $grouped = $allTransactions->groupBy(function ($transaction) {
            return \Carbon\Carbon::parse($transaction->bookingDate)->format('Y-m');
        });

        foreach ($grouped as $month => $transactions) {
            $months[$month] = $transactions->sum('transactionAmount_amount');
        }

        return round(collect($months)->avg(), 2);
    }

    /**
     * Get the monthly expenses excluding specific categories.
     *
     * This method calculates the total expenses for each month, excluding transactions that fall under
     * specific categories (e.g., categories marked as "paysheet_disabled") or are uncategorized.
     * It groups expenses by month and ensures all transactions are updated with categories based on remittance information beforehand.
     *
     * @return array Returns an associative array where the keys are months in 'Y-m' format
     *               and the values are the total expenses (negative amounts).
     *
     * @property-read array $monthExpensesExcludingCategories Monthly expenses with excluded categories considered.
     *
     * @noinspection PhpUnused
     * @uses \Carbon\Carbon Used to manipulate and handle dates for calculating date ranges and grouping transactions.
     * @method Collection transactions() Retrieve all transactions for the account.
     * @method Collection categories() Retrieve all categories linked to the account.
     */
    public function getMonthExpensesExcludingCategoriesAttribute(): array
    {
        // Get all categories that are excluded from the paysheet
        $excludedCategories = $this->categories()
            ->where('paysheet_disabled', true)
            ->get()
            ->pluck('id')
            ->toArray();

        // Get all transactions that are not in the excluded categories and are expenses
        $allTransactions = $this->transactions()
            ->where(function (Builder $query) use ($excludedCategories) {
                $query->orWhere('category_id', '=', null)
                    ->orWhereNotIn('category_id', $excludedCategories);
            })
            ->where('transactionAmount_amount', '<', 0)
            ->orderBy('bookingDate')
            ->get();

        if ($allTransactions->isEmpty()) {
            return [];
        }

        $startDate = \Carbon\Carbon::parse($allTransactions->first()->bookingDate)->startOfMonth();
        $endDate = \Carbon\Carbon::parse($allTransactions->last()->bookingDate)->endOfMonth();

        $months = [];
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $months[$current->format('Y-m')] = 0.0;
            $current->addMonth();
        }

        $grouped = $allTransactions->groupBy(function ($transaction) {
            return \Carbon\Carbon::parse($transaction->bookingDate)->format('Y-m');
        });

        foreach ($grouped as $month => $group) {
            $months[$month] = $group->sum('transactionAmount_amount');
        }

        return $months;
    }

    /**
     * @property-read string $average_month_expenses_excluding_categories
     * @noinspection PhpUnused
     */
    public function getAverageMonthExpensesExcludingCategoriesAttribute(): string
    {
        $months = $this->month_expenses_excluding_categories;
        return !empty($months) ? round(collect($months)->avg(), 2) : 0.0;
    }

    /**
     * @property-read string $chart_forecast_month_incomes_values
     * @noinspection PhpUnused
     */
    public function getChartForecastMonthIncomesValuesAttribute(): string
    {
        $incomesMonthly = $this->paysheet ? $this->paysheet->transactionAmount_amount : 0;

        if ($this->apply_expenses_monthly) {
            $expensesMonthly = $this->average_month_expenses_excluding_categories;
        } else {
            $expensesMonthly = 0;
        }

        $forecast = (float) $incomesMonthly + (float) $expensesMonthly;

        $currentBalance = tap($this->balances()->getQuery())
            ->balanceTypeForward()
            ->orderDate()
            ->first();
        $balance = $currentBalance ? $currentBalance->amount : 0;

        $result = [];

        for ($i = 0; $i < 12; $i++) {
            $result[] = $balance+($forecast * $i);
        }

        return implode(',', $result);
    }

    /**
     * @property-read string $chart_forecast_month_incomes_labels A comma-separated string of labels representing
     * the forecasted month incomes for the next 12 months, formatted as "Mon Y".
     * @noinspection PhpUnused
     */
    public function getChartForecastMonthIncomesLabelsAttribute(): string
    {
        $labels = [];
        $currentMonth = now()->startOfMonth();

        for ($i = 0; $i < 12; $i++) {
            $labels[] = $currentMonth->format('M Y');
            $currentMonth->addMonth();
        }

        return implode(',', $labels);
    }



    /**
     * Get the example model for testing purposes.
     *
     * @noinspection PhpUnused
     * @return self
     */
    public static function getExampleModel(): Account
    {
        return new self([
            'id' => Str::uuid()->toString(),
            'name' => 'Example Account',
            'iban' => 'DE89370400440532013000',
            'bban' => '12345678901234567890',
            'status' => 'active',
            'owner_name' => 'John Doe',
            'created' => now(),
            'last_accessed' => now(),
            'institution_id' => 1
        ]);
    }
}
