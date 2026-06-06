<?php

namespace App\Http\Controllers;

use App\Models\NumericQuiz;
use App\Models\QuizResponse;
use App\Models\QuizScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class QuizController extends Controller
{
    public function show()
    {
        $finaleActive = Cache::get('quiz_finale_active', false);

        $activeQuiz = NumericQuiz::where('status', 'active')->first();
        $userResponse = null;

        if ($activeQuiz) {
            $userResponse = QuizResponse::where('quiz_id', $activeQuiz->id)
                ->where('user_id', Auth::id())
                ->first();
        }

        $leaderboard = QuizScore::getLeaderboard();
        $hasClosedQuizzes = NumericQuiz::where('status', 'closed')->exists();
        $showPodium = $finaleActive || (!$activeQuiz && $hasClosedQuizzes && $leaderboard->count() > 0);
        $topWinners = $leaderboard->take(3)->values();

        $initialState = ['status' => 'waiting'];

        if ($finaleActive) {
            $initialState = [
                'status'      => 'finale',
                'top_winners' => $topWinners->map(fn($s, $i) => [
                    'rank'        => $i + 1,
                    'name'        => $s->user->name,
                    'initials'    => $s->user->initials,
                    'avatar'      => $s->user->avatar_url,
                    'total_score' => $s->total_score,
                ])->values()->all(),
            ];
        } elseif ($activeQuiz) {
            $initialState = [
                'status'       => 'active',
                'quiz_id'      => $activeQuiz->id,
                'has_answered' => $userResponse !== null,
            ];
        } else {
            $closedQuiz = NumericQuiz::where('status', 'closed')
                ->orderBy('updated_at', 'desc')
                ->first();
            if ($closedQuiz) {
                $initialState = $this->buildClosedState($closedQuiz);
            }
        }

        return view('quiz.show', compact(
            'activeQuiz', 'userResponse', 'leaderboard',
            'showPodium', 'topWinners', 'initialState'
        ));
    }

    public function state()
    {
        if (Cache::get('quiz_finale_active')) {
            $winners = QuizScore::getLeaderboard()->take(3)->values();
            return response()->json([
                'status'      => 'finale',
                'top_winners' => $winners->map(fn($s, $i) => [
                    'rank'        => $i + 1,
                    'name'        => $s->user->name,
                    'initials'    => $s->user->initials,
                    'avatar'      => $s->user->avatar_url,
                    'total_score' => $s->total_score,
                ])->values()->all(),
            ]);
        }

        $activeQuiz = NumericQuiz::where('status', 'active')->first();

        if ($activeQuiz) {
            $userResponse = QuizResponse::where('quiz_id', $activeQuiz->id)
                ->where('user_id', Auth::id())
                ->first();

            return response()->json([
                'status'       => 'active',
                'quiz_id'      => $activeQuiz->id,
                'question'     => $activeQuiz->question,
                'description'  => $activeQuiz->description,
                'has_answered' => $userResponse !== null,
            ]);
        }

        $closedQuiz = NumericQuiz::where('status', 'closed')
            ->orderBy('updated_at', 'desc')
            ->first();

        if ($closedQuiz) {
            return response()->json($this->buildClosedState($closedQuiz));
        }

        return response()->json(['status' => 'waiting']);
    }

    private function buildClosedState(NumericQuiz $quiz): array
    {
        $userId = Auth::id();

        $userResponse = QuizResponse::where('quiz_id', $quiz->id)
            ->where('user_id', $userId)
            ->first();

        $allResponses = QuizResponse::where('quiz_id', $quiz->id)
            ->with('user')
            ->get()
            ->sortBy([
                fn($r) => abs((float) $r->numeric_answer - (float) $quiz->correct_answer),
                fn($r) => $r->created_at->timestamp,
            ])
            ->values();

        $leaderboard = $allResponses->map(function ($r, $index) use ($quiz, $userId) {
            $diff = abs((float) $r->numeric_answer - (float) $quiz->correct_answer);
            return [
                'rank'       => $index + 1,
                'name'       => $r->user->name,
                'initials'   => $r->user->initials,
                'avatar'     => $r->user->avatar_url,
                'answer'     => (float) $r->numeric_answer,
                'difference' => $diff,
                'is_exact'   => (float) $r->numeric_answer == (float) $quiz->correct_answer,
                'score'      => $r->score,
                'is_me'      => $r->user_id === $userId,
            ];
        })->sortByDesc('score')->values()->all();

        $myRank       = null;
        $myScore      = null;
        $myAnswer     = null;
        $myDifference = null;
        $myIsExact    = false;

        if ($userResponse) {
            $myRank       = $allResponses->search(fn($r) => $r->user_id === $userId) + 1;
            $myScore      = $userResponse->score;
            $myAnswer     = (float) $userResponse->numeric_answer;
            $myDifference = abs($myAnswer - (float) $quiz->correct_answer);
            $myIsExact    = $myAnswer == (float) $quiz->correct_answer;
        }

        return [
            'status'        => 'closed',
            'quiz_id'       => $quiz->id,
            'question'      => $quiz->question,
            'correct_answer'=> (float) $quiz->correct_answer,
            'has_answered'  => $userResponse !== null,
            'my_answer'     => $myAnswer,
            'my_difference' => $myDifference,
            'my_is_exact'   => $myIsExact,
            'my_rank'       => $myRank,
            'my_score'      => $myScore,
            'leaderboard'   => $leaderboard,
        ];
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
                ->with('error', 'Vous avez déjà répondu à cette question');
        }

        // Create response
        $response = QuizResponse::create([
            'quiz_id' => $activeQuiz->id,
            'user_id' => Auth::id(),
            'numeric_answer' => $validated['numeric_answer'],
            'score' => 0, // Will be calculated when quiz is closed
        ]);

        return redirect()->route('quiz.show')
            ->with('success', 'Réponse enregistrée');
    }
}
