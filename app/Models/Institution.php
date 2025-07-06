<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $bic
 * @property int $transaction_total_days
 * @property int $max_access_valid_for_days
 * @property string|null $countries
 * @property string|null $logo
 * @property-read Collection<int, Account> $accounts
 * @property-read int|null $accounts_count
 * @property-read Collection<int, UserInstitution> $institutions
 * @property-read int|null $institutions_count
 * @method static Builder|Institution newModelQuery()
 * @method static Builder|Institution newQuery()
 * @method static Builder|Institution query()
 * @method static Builder|Institution whereBic($value)
 * @method static Builder|Institution whereCode($value)
 * @method static Builder|Institution whereCountries($value)
 * @method static Builder|Institution whereId($value)
 * @method static Builder|Institution whereLogo($value)
 * @method static Builder|Institution whereMaxAccessValidForDays($value)
 * @method static Builder|Institution whereName($value)
 * @method static Builder|Institution whereTransactionTotalDays($value)
 * @property-read Collection<int, UserInstitution> $users
 * @property-read int|null $users_count
 * @mixin Eloquent
 */
class Institution extends Model
{
    use HasFactory;

    protected $table = 'institutions';

    public $timestamps = false;

    protected $fillable = [
        'code',
        'name',
        'bic',
        'transaction_total_days',
        'country',
        'logo',
        'max_access_valid_for_days',
    ];

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(UserInstitution::class, 'user_institution', 'institution_id', 'user_id');
    }
}
