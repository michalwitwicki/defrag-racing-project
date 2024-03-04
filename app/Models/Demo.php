<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demo extends Model {
    use HasFactory;

    protected $fillable = [
        'round_id',
        'user_id',
        'file',
        'filename',
        'time',
        'rank',
        'physics',
        'points',
        'approved',
        'rejected',
        'counted',
        'reason',
    ];
}