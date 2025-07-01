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
 * @property string $value
 * @property string|null $field
 * @property string $type
 * @property bool $enabled
 * @property-read Category $category
 * @method static Builder|CategoryFilter isEnabled()
 * @method static Builder|CategoryFilter newModelQuery()
 * @method static Builder|CategoryFilter newQuery()
 * @method static Builder|CategoryFilter query()
 * @method static Builder|CategoryFilter whereCategoryId($value)
 * @method static Builder|CategoryFilter whereEnabled($value)
 * @method static Builder|CategoryFilter whereField($value)
 * @method static Builder|CategoryFilter whereId($value)
 * @method static Builder|CategoryFilter whereType($value)
 * @method static Builder|CategoryFilter whereValue($value)
 * @mixin Eloquent
 */
class CategoryFilter extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'category_id',
        'value',
        'field',
        'type',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * @noinspection PhpUnused
     */
    public function scopeIsEnabled($query)
    {
        return $query->where('enabled', true);
    }
}
