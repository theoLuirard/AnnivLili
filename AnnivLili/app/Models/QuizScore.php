<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_score',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function updateScore(int $userId): void
    {
        $totalScore = QuizResponse::where('user_id', $userId)->sum('score');

        self::updateOrCreate(
            ['user_id' => $userId],
            ['total_score' => $totalScore]
        );
    }

    public static function getLeaderboard()
    {
        return self::with('user')
            ->orderBy('total_score', 'desc')
            ->get();
    }
}
