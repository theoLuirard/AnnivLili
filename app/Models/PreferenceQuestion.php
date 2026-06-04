<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PreferenceQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'question_text',
        'option_a',
        'option_b',
        'correct_answer',
        'is_eliminatory',
        'status',
        'order',
    ];

    protected $casts = [
        'is_eliminatory' => 'boolean',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(PreferenceGame::class, 'game_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(PreferenceAnswer::class, 'question_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isRevealing(): bool
    {
        return $this->status === 'revealing';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function getCorrectOptionLabel(): ?string
    {
        return match($this->correct_answer) {
            'a' => $this->option_a,
            'b' => $this->option_b,
            default => null,
        };
    }
}
