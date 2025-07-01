<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function scopeIsPaysheetDisabled($query)
    {
        return $query->where('paysheet_disabled', true);
    }
}
