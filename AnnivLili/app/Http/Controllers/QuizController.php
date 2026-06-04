<?php

namespace App\Http\Controllers;

use App\Models\NumericQuiz;
use App\Models\QuizResponse;
use App\Models\QuizScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function show()
    {
        $activeQuiz = NumericQuiz::where('status', 'active')->first();
        $userResponse = null;

        if ($activeQuiz) {
            $userResponse = QuizResponse::where('quiz_id', $activeQuiz->id)
                ->where('user_id', Auth::id())
                ->first();
        }

        $leaderboard = QuizScore::getLeaderboard();

        return view('quiz.show', compact('activeQuiz', 'userResponse', 'leaderboard'));
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'numeric_answer' => 'required|numeric',
        ]);

        $activeQuiz = NumericQuiz::where('status', 'active')->first();

        if (!$activeQuiz) {
            return redirect()->route('quiz.show')
                ->with('error', 'Aucune question active');
        }

        // Check if user already answered this quiz
        $existingResponse = QuizResponse::where('quiz_id', $activeQuiz->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingResponse) {
            return redirect()->route('quiz.show')
                ->with('error', 'Vous avez dj rpondu  cette question');
        }

        // Create response
        $response = QuizResponse::create([
            'quiz_id' => $activeQuiz->id,
            'user_id' => Auth::id(),
            'numeric_answer' => $validated['numeric_answer'],
            'score' => 0, // Will be calculated when quiz is closed
        ]);

        return redirect()->route('quiz.show')
            ->with('success', 'Rponse enregistre');
    }
}
