<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $institution_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Institution $institution
 * @property-read User $user
 * @method static Builder|UserInstitution newModelQuery()
 * @method static Builder|UserInstitution newQuery()
 * @method static Builder|UserInstitution query()
 * @method static Builder|UserInstitution whereCreatedAt($value)
 * @method static Builder|UserInstitution whereId($value)
 * @method static Builder|UserInstitution whereInstitutionId($value)
 * @method static Builder|UserInstitution whereUpdatedAt($value)
 * @method static Builder|UserInstitution whereUserId($value)
 * @mixin Eloquent
 */
class UserInstitution extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'institution_id',
    ];

    protected $table = 'users_institutions';

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
