<?php

namespace App\Models;

use App\Http\Controllers\NordigenController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Monolog\Logger;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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
        'execute_login'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function bank()
    {
        return $this->hasOne(Bank::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class, 'user_id');
    }

    public function schedule()
    {
        return $this->hasMany(ScheduledTasks::class, 'user_id');
    }

    public function getBankDataSyncCountAttribute()
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
     * @return mixed
     */
    public function getTransactionsAttribute()
    {
        return Transaction::whereHas('account', function ($query) {
            $query->where('user_id', $this->id);
        })->orderDate()->get();
    }

    /**
     * Get user's logger instance.
     * @return Logger
     */
    public function getLoggerAttribute()
    {
        $logger = new \Monolog\Logger("user_{$this->id}");
        $logPath = storage_path("logs/user_{$this->id}.log");
        $logger->pushHandler(new \Monolog\Handler\StreamHandler($logPath, \Monolog\Logger::INFO));

        return $logger;
    }

    /**
     * Calculate the total sum of all accounts for the user on the relationship with balances on each account
     * @return mixed
     */
    public function getTotalAccountSumAttribute()
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
    public function executeAccountTasks()
    {
        $nordigen = new NordigenController();

        foreach ($this->accounts as $account) {
            $nordigen->update(new Request(), $account->code);
        }
    }
}
