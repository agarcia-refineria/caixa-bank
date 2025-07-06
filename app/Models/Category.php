<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $name
 * @property-read Collection<int, CategoryFilter> $filters
 * @property-read int|null $filters_count
 * @property-read Collection<int, Transaction> $transactions
 * @property-read int|null $transactions_count
 * @property-read User|null $user
 * @method static Builder|Category newModelQuery()
 * @method static Builder|Category newQuery()
 * @method static Builder|Category query()
 * @method static Builder|Category whereId($value)
 * @method static Builder|Category whereName($value)
 * @method static Builder|Category whereUserId($value)
 * @property-read Collection<int, CategoryAccount> $categories
 * @property-read int|null $categories_count
 * @mixin Eloquent
 */
class Category extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'categories';

    protected $fillable = [
        'name',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'category_id');
    }

    public function filters(): HasMany
    {
        return $this->hasMany(CategoryFilter::class, 'category_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(CategoryAccount::class, 'category_id');
    }
}
