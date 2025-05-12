<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledTasks extends Model
{
    use HasFactory;

    public $timestamps = false;

    public static $MAX_TIMES = 3;

    public static $WARNING_TIMES = 2;

    protected $fillable = [
        'hour',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the hour in a simple format (HH:MM).
     * @return string
     */
    public function getHourSimpleAttribute()
    {
        return date('H:i', strtotime($this->hour));
    }
}
