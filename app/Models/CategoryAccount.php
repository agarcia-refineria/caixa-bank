<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 *
 * @property int $id
 * @property int $category_id
 * @property string $account_id
 * @property bool $paysheet_disabled
 * @property-read Account $account
 * @property-read Category $category
 * @method static Builder|CategoryAccount isPaysheetDisabled()
 * @method static Builder|CategoryAccount newModelQuery()
 * @method static Builder|CategoryAccount newQuery()
 * @method static Builder|CategoryAccount query()
 * @method static Builder|CategoryAccount whereAccountId($value)
 * @method static Builder|CategoryAccount whereCategoryId($value)
 * @method static Builder|CategoryAccount whereId($value)
 * @method static Builder|CategoryAccount wherePaysheetDisabled($value)
 * @mixin Eloquent
 */
class CategoryAccount extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'categories_accounts';

    protected $fillable = [
        'category_id',
        'account_id',
        'paysheet_disabled'
    ];

    protected $casts = [
        'paysheet_disabled' => 'boolean',
    ];


    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    /**
     * Scope a query to only include accounts with paysheet disabled.
     *
     * @noinspection PhpUnused
     * @param Builder $query
     * @return Builder
     */
    public function scopeIsPaysheetDisabled(Builder $query): Builder
    {
        return $query->where('paysheet_disabled', true);
    }
}
