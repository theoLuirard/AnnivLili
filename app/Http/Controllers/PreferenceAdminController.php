<?php

namespace App\Http\Controllers;

use App\Models\PreferenceGame;
use App\Models\PreferenceQuestion;
use App\Models\PreferenceAnswer;
use App\Models\PreferenceElimination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PreferenceAdminController extends Controller
{
    public function index()
    {
        $games = PreferenceGame::with('creator', 'questions')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.preference.index', compact('games'));
    }

    public function create()
    {
        return view('admin.preference.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'                    => 'required|string|max:255',
            'questions'                => 'required|array|min:1',
            'questions.*.question_text'=> 'required|string|max:500',
            'questions.*.option_a'     => 'required|string|max:200',
            'questions.*.option_b'     => 'required|string|max:200',
            'questions.*.is_eliminatory' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($validated) {
            $game = PreferenceGame::create([
                'title'      => $validated['title'],
                'status'     => 'draft',
                'created_by' => Auth::id(),
            ]);

            foreach ($validated['questions'] as $index => $q) {
                PreferenceQuestion::create([
                    'game_id'       => $game->id,
                    'question_text' => $q['question_text'],
                    'option_a'      => $q['option_a'],
                    'option_b'      => $q['option_b'],
                    'is_eliminatory'=> !empty($q['is_eliminatory']),
                    'status'        => 'pending',
                    'order'         => $index + 1,
                ]);
            }
        });

        return redirect()->route('admin.preference.index')
            ->with('success', 'Jeu créé avec succès');
    }

    public function manage(PreferenceGame $game)
    {
        $game->load(['questions.answers.user', 'eliminations.user']);
        $leaderboard = $game->getGameLeaderboard();

        return view('admin.preference.manage', compact('game', 'leaderboard'));
    }

    public function activate(PreferenceGame $game)
    {
        // Only one game active at a time
        PreferenceGame::where('status', 'active')->update(['status' => 'closed']);
        $game->update(['status' => 'active']);

        return redirect()->route('admin.preference.manage', $game->id)
            ->with('success', 'Jeu activé');
    }

    public function activateQuestion(PreferenceGame $game, PreferenceQuestion $question)
    {
        // Close any already-active question first
        $game->questions()->whereIn('status', ['active', 'revealing'])->update(['status' => 'closed']);

        $question->update(['status' => 'active']);

        return redirect()->route('admin.preference.manage', $game->id)
            ->with('success', 'Question activée');
    }

    public function revealAnswer(Request $request, PreferenceGame $game, PreferenceQuestion $question)
    {
        $validated = $request->validate([
            'correct_answer' => 'required|in:a,b',
        ]);

        $correctAnswer = $validated['correct_answer'];

        DB::transaction(function () use ($game, $question, $correctAnswer) {
            // Save the game master's answer
            $question->update([
                'correct_answer' => $correctAnswer,
                'status'         => 'revealing',
            ]);

            // Load all answers for this question
            $answers = PreferenceAnswer::where('question_id', $question->id)->get();

            $wrongAnswerers = [];
            $correctAnswerers = [];

            foreach ($answers as $answer) {
                $isCorrect = $answer->answer === $correctAnswer;
                if ($isCorrect) {
                    $correctAnswerers[] = $answer->user_id;
                } else {
                    $wrongAnswerers[] = $answer->user_id;
                }
                $answer->update(['is_correct' => $isCorrect]);
            }

            // Calculate points
            if ($question->is_eliminatory) {
                // Mark the game as in eliminatory phase
                $game->update(['is_eliminatory_phase' => true]);

                // Points = number of people who failed
                $points = count($wrongAnswerers);

                foreach ($answers as $answer) {
                    if ($answer->is_correct) {
                        $answer->update(['points_earned' => $points]);
                    }
                }

                // Eliminate wrong answerers (only if not already eliminated)
                foreach ($wrongAnswerers as $userId) {
                    PreferenceElimination::firstOrCreate([
                        'game_id' => $game->id,
                        'user_id' => $userId,
                    ], [
                        'question_id' => $question->id,
                    ]);
                }
            } else {
                // Normal question: 1 point for correct answer
                foreach ($answers as $answer) {
                    if ($answer->is_correct) {
                        $answer->update(['points_earned' => 1]);
                    }
                }
            }
        });

        return redirect()->route('admin.preference.manage', $game->id)
            ->with('success', 'Réponse révélée');
    }

    public function closeQuestion(PreferenceGame $game, PreferenceQuestion $question)
    {
        $question->update(['status' => 'closed']);

        return redirect()->route('admin.preference.manage', $game->id)
            ->with('success', 'Question fermée');
    }

    public function endEliminatoryPhase(PreferenceGame $game)
    {
        $game->update(['is_eliminatory_phase' => false]);
        PreferenceElimination::where('game_id', $game->id)->delete();

        return redirect()->route('admin.preference.manage', $game->id)
            ->with('success', 'Phase éliminatoire terminée — tous les joueurs sont de retour');
    }

    public function closeGame(PreferenceGame $game)
    {
        // Close any open question
        $game->questions()->whereIn('status', ['active', 'revealing'])->update(['status' => 'closed']);
        $game->update(['status' => 'closed', 'is_eliminatory_phase' => false]);

        return redirect()->route('admin.preference.index')
            ->with('success', 'Jeu terminé');
    }

    public function liveCount(PreferenceGame $game)
    {
        $activeQuestion = $game->questions()->where('status', 'active')->first();

        if (!$activeQuestion) {
            return response()->json(['question_id' => null, 'count' => 0]);
        }

        return response()->json([
            'question_id' => $activeQuestion->id,
            'count'       => $activeQuestion->answers()->count(),
        ]);
    }

    public function destroy(PreferenceGame $game)
    {
        $game->delete();

        return redirect()->route('admin.preference.index')
            ->with('success', 'Jeu supprimé');
    }
}
