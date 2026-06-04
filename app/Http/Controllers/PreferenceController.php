<?php

namespace App\Http\Controllers;

use App\Models\PreferenceGame;
use App\Models\PreferenceAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreferenceController extends Controller
{
    public function show()
    {
        $activeGame = PreferenceGame::where('status', 'active')->first();
        $userId = Auth::id();

        $initialState = $this->buildState($activeGame, $userId);

        return view('preference.show', compact('activeGame', 'initialState'));
    }

    public function state()
    {
        $activeGame = PreferenceGame::where('status', 'active')->first();
        $userId = Auth::id();

        return response()->json($this->buildState($activeGame, $userId));
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'answer' => 'required|in:a,b',
        ]);

        $activeGame = PreferenceGame::where('status', 'active')->first();

        if (!$activeGame) {
            return response()->json(['error' => 'Aucun jeu actif'], 422);
        }

        $userId = Auth::id();

        // Check if user is eliminated
        if ($activeGame->isUserEliminated($userId)) {
            return response()->json(['error' => 'Vous êtes éliminé pour cette phase'], 422);
        }

        $activeQuestion = $activeGame->activeQuestion();

        if (!$activeQuestion || !$activeQuestion->isActive()) {
            return response()->json(['error' => 'Aucune question active'], 422);
        }

        // Check if already answered
        $existing = PreferenceAnswer::where('question_id', $activeQuestion->id)
            ->where('user_id', $userId)
            ->exists();

        if ($existing) {
            return response()->json(['error' => 'Vous avez déjà répondu'], 422);
        }

        PreferenceAnswer::create([
            'question_id'  => $activeQuestion->id,
            'user_id'      => $userId,
            'answer'       => $validated['answer'],
            'is_correct'   => null,
            'points_earned'=> 0,
        ]);

        return response()->json(['success' => true]);
    }

    private function buildState(?PreferenceGame $game, int $userId): array
    {
        if (!$game) {
            return ['status' => 'waiting'];
        }

        $isEliminated = $game->isUserEliminated($userId);
        $question = $game->activeQuestion();
        $leaderboard = $game->getGameLeaderboard();

        // Compute this user's total points in this game
        $questionIds = $game->questions()->pluck('id');
        $myPoints = PreferenceAnswer::whereIn('question_id', $questionIds)
            ->where('user_id', $userId)
            ->sum('points_earned');

        $base = [
            'status'               => 'active',
            'game_id'              => $game->id,
            'game_title'           => $game->title,
            'is_eliminatory_phase' => $game->is_eliminatory_phase,
            'is_eliminated'        => $isEliminated,
            'my_points'            => (int) $myPoints,
            'leaderboard'          => $leaderboard,
        ];

        if (!$question) {
            return array_merge($base, ['question_status' => 'waiting']);
        }

        $myAnswer = PreferenceAnswer::where('question_id', $question->id)
            ->where('user_id', $userId)
            ->first();

        $questionData = [
            'question_id'     => $question->id,
            'question_text'   => $question->question_text,
            'option_a'        => $question->option_a,
            'option_b'        => $question->option_b,
            'is_eliminatory'  => $question->is_eliminatory,
            'question_status' => $question->status,
            'has_answered'    => $myAnswer !== null,
            'my_answer'       => $myAnswer?->answer,
        ];

        if ($question->isRevealing()) {
            $answers = $question->answers()->with('user')->get();
            $correctCount = $answers->where('is_correct', true)->count();
            $wrongCount   = $answers->where('is_correct', false)->count();

            $answerList = $answers->map(fn($a) => [
                'name'          => $a->user->name,
                'initials'      => $a->user->initials ?? strtoupper(substr($a->user->name, 0, 2)),
                'answer'        => $a->answer,
                'is_correct'    => $a->is_correct,
                'points_earned' => $a->points_earned,
                'is_me'         => $a->user_id === $userId,
            ])->values()->all();

            $questionData = array_merge($questionData, [
                'correct_answer'  => $question->correct_answer,
                'correct_label'   => $question->getCorrectOptionLabel(),
                'correct_count'   => $correctCount,
                'wrong_count'     => $wrongCount,
                'my_is_correct'   => $myAnswer?->is_correct,
                'my_points_earned'=> (int) ($myAnswer?->points_earned ?? 0),
                'answers'         => $answerList,
            ]);
        }

        return array_merge($base, $questionData);
    }
}
