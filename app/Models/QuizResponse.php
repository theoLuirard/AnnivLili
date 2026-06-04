<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function calculateScore(): int
    {
        $correct = (float) $this->quiz->correct_answer;
        $answer = (float) $this->numeric_answer;

        $score = 0;

        // Check if exact match
        if ($answer == $correct) {
            $score += 3;
        }

        // Get all responses for this quiz
        $allResponses = QuizResponse::where('quiz_id', $this->quiz_id)
            ->with('user')
            ->get()
            ->sortBy(fn($r) => abs((float)$r->numeric_answer - $correct));

        // Rank this response by proximity
        $rank = $allResponses->search(fn($r) => $r->id === $this->id);

        if ($rank === 0) {
            $score += 5; // 1st closest
        } elseif ($rank === 1) {
            $score += 2; // 2nd closest
        } elseif ($rank === 2) {
            $score += 1; // 3rd closest
        }

        return $score;
    }
}
