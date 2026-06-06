<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PreferenceGame extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'status',
        'is_eliminatory_phase',
        'show_podium',
        'created_by',
    ];

    protected $casts = [
        'is_eliminatory_phase' => 'boolean',
        'show_podium'          => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(PreferenceQuestion::class, 'game_id')->orderBy('order');
    }

    public function eliminations(): HasMany
    {
        return $this->hasMany(PreferenceElimination::class, 'game_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function activeQuestion(): ?PreferenceQuestion
    {
        return $this->questions()->whereIn('status', ['active', 'revealing'])->first();
    }

    public function isUserEliminated(int $userId): bool
    {
        return $this->eliminations()->where('user_id', $userId)->exists();
    }

    public function getGameLeaderboard(): array
    {
        $questionIds = $this->questions()->pluck('id');

        return PreferenceAnswer::whereIn('question_id', $questionIds)
            ->with('user')
            ->get()
            ->groupBy('user_id')
            ->map(function ($answers, $userId) {
                $user = $answers->first()->user;
                return [
                    'user_id'  => $userId,
                    'name'     => $user->name,
                    'initials' => $user->initials ?? strtoupper(substr($user->name, 0, 2)),
                    'avatar'   => $user->avatar_url,
                    'points'   => $answers->sum('points_earned'),
                    'correct'  => $answers->where('is_correct', true)->count(),
                    'total'    => $answers->count(),
                ];
            })
            ->sortByDesc('points')
            ->values()
            ->all();
    }
}
