<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\SoftDeletes;

class PlayerRating extends Model
{
    // use HasFactory;
    // use SoftDeletes;

    protected $fillable = [
        'name',
        'mdd_id',
        'user_id',
        'physics',
        'mode',
        'player_rating',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'mdd_id', 'mdd_id');
    }
}
