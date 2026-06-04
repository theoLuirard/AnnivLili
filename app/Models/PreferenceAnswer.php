<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreferenceAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'user_id',
        'answer',
        'is_correct',
        'points_earned',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(PreferenceQuestion::class, 'question_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
