<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class QuizResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'user_id',
        'numeric_answer',
        'score',
    ];

    protected $casts = [
        'numeric_answer' => 'decimal:2',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(NumericQuiz::class, 'quiz_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function calculateScore(?Collection $allResponses = null): int
    {
        $correct = (float) $this->quiz->correct_answer;

        // Use provided collection or fetch (fallback for standalone calls)
        if ($allResponses === null) {
            $allResponses = QuizResponse::where('quiz_id', $this->quiz_id)->get();
        }

        // Rank by proximity, speed (created_at) as tiebreaker
        $sorted = $allResponses->sortBy([
            fn($r) => abs((float) $r->numeric_answer - $correct),
            fn($r) => $r->created_at->timestamp,
        ]);
        $rank = $sorted->search(fn($r) => $r->id === $this->id);

        if ($rank === 0) return 10; // 1st
        if ($rank === 1) return 5;  // 2nd
        if ($rank === 2) return 2;  // 3rd

        return 0;
    }
}
