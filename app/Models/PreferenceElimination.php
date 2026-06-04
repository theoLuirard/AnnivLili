<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreferenceElimination extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'user_id',
        'question_id',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(PreferenceGame::class, 'game_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(PreferenceQuestion::class, 'question_id');
    }
}
