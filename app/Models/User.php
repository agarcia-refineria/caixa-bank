<?php

namespace App\Models;

use App\Http\Controllers\NordigenController;
use Database\Factories\UserFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property mixed $password
 * @property int $schedule_times
 * @property string|null $remember_token
 * @property int $execute_login
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Account> $accounts
 * @property-read int|null $accounts_count
 * @property-read Bank|null $bank
 * @property-read mixed $bank_data_sync_count
 * @property-read Logger $logger
 * @property-read mixed $total_account_sum
 * @property-read mixed $transactions
 * @property-read mixed $balances
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, ScheduledTasks> $schedule
 * @property-read int|null $schedule_count
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static UserFactory factory($count = null, $state = [])
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereExecuteLogin($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereScheduleTimes($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @property string $chars
 * @property array|null $theme
 * @property string|null $NORDIGEN_SECRET_ID
 * @property string|null $NORDIGEN_SECRET_KEY
 * @property string $lang
 * @property-read Collection<int, Category> $categories
 * @property-read int|null $categories_count
 * @property-read string $theme_main3
 * @property-read string $theme_nav_active
 * @property-read string $theme_nav_active_bg
 * @method static Builder|User whereChars($value)
 * @method static Builder|User whereLang($value)
 * @method static Builder|User whereNORDIGENSECRETID($value)
 * @method static Builder|User whereNORDIGENSECRETKEY($value)
 * @method static Builder|User whereTheme($value)
 * @mixin Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public static array $langs = [
        'es',
        'en',
    ];

    public static array $charsTypes = [
        'all',
        'categories'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'schedule_times',
        'execute_login',
        'chars',
        'theme',
        'NORDIGEN_SECRET_ID',
        'NORDIGEN_SECRET_KEY',
        'lang'
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'NORDIGEN_SECRET_ID',
        'NORDIGEN_SECRET_KEY'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'theme' => 'json',
    ];

    public function bank(): HasOne
    {
        return $this->hasOne(Bank::class);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'user_id');
    }

    public function schedule(): HasMany
    {
        return $this->hasMany(ScheduledTasks::class, 'user_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'user_id');
    }

    /**
     * @noinspection PhpUnused
     * @property-read string $theme_main3
     */
    public function getThemeMain3Attribute(): string
    {
        return $this->theme['main3'] ?? '#364791';
    }

    /**
     * @noinspection PhpUnused
     * @property-read string $theme_nav_active
     */
    public function getThemeNavActiveAttribute(): string
    {
        return $this->theme['navActive'] ?? '#f0f0f0';
    }

    /**
     * @noinspection PhpUnused
     * @property-read string $theme_nav_active_bg
     */
    public function getThemeNavActiveBgAttribute(): string
    {
        return $this->theme['navActiveBg'] ?? '#3b3b3b';
    }

    /**
     * Get the count of bank data syncs for all accounts
     *
     * @noinspection PhpUnused
     * @return int
     */
    public function getBankDataSyncCountAttribute(): int
    {
        $count = 0;
        $accounts = $this->accounts()->get();

        foreach ($accounts as $account) {
            $count += $account->bankDataSyncCount;
        }

        return $count;
    }

    /**
     * Fetch all transactions for the user ordered by booking date
     *
     * @noinspection PhpUnused
     * @return Collection|array
     */
    public function getTransactionsAttribute(): Collection|array
    {
        return Transaction::whereHas('account', function ($query) {
            $query->where('user_id', $this->id);
        })->orderDate()->get();
    }

    /**
     * Fetch all balances for the user ordered by reference date
     *
     * @noinspection PhpUnused
     * @return Collection|array
     */
    public function getBalancesAttribute(): Collection|array
    {
        return Balance::whereHas('account', function ($query) {
            $query->where('user_id', $this->id);
        })->orderDate()->get();
    }

    /**
     * Get user's logger instance.
     *
     * @noinspection PhpUnused
     * @return Logger
     */
    public function getLoggerAttribute(): Logger
    {
        $logger = new Logger("user_$this->id");
        $logPath = storage_path("logs/user_$this->id.log");
        $logger->pushHandler(new StreamHandler($logPath, Logger::INFO));

        return $logger;
    }

    /**
     * Calculate the total sum of all accounts for the user on the relationship with balances on each account
     *
     * @noinspection PhpUnused
     * @return mixed
     */
    public function getTotalAccountSumAttribute(): mixed
    {
        return $this->accounts->sum(function ($account) {
            $latestBalance = $account->balances()
                ->balanceTypeForward()
                ->orderByDesc('reference_date')
                ->first();

            return $latestBalance?->amount ?? 0;
        });
    }

    /**
     * Execute account tasks for the user
     * @return void
     */
    public function executeAccountTasks(): void
    {
        $nordigen = new NordigenController();

        foreach ($this->accounts as $account) {
            $nordigen->update($account->code);
        }
    }
}
