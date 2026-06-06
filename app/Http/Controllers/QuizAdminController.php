<?php

namespace App\Http\Controllers;

use App\Models\NumericQuiz;
use App\Models\QuizResponse;
use App\Models\ScoreboardEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class QuizAdminController extends Controller
{
    public function index()
    {
        $quizzes = NumericQuiz::with('creator', 'responses.user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        return view('admin.quizzes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'correct_answer' => 'required|numeric',
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'draft';

        NumericQuiz::create($validated);

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Question créée avec succès');
    }

    public function edit(NumericQuiz $quiz)
    {
        return view('admin.quizzes.edit', compact('quiz'));
    }

    public function update(Request $request, NumericQuiz $quiz)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'correct_answer' => 'required|numeric',
            'description' => 'nullable|string|max:1000',
        ]);

        $quiz->update($validated);

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Question mise à jour');
    }

    public function activate(NumericQuiz $quiz)
    {
        // Close any other active quiz
        NumericQuiz::where('status', 'active')->update(['status' => 'closed']);

        $quiz->update(['status' => 'active']);

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Question activée');
    }

    public function close(NumericQuiz $quiz)
    {
        $quiz->update(['status' => 'closed']);

        // Load all responses once with quiz to avoid N+1 in calculateScore
        $responses = QuizResponse::where('quiz_id', $quiz->id)->with('quiz')->get();
        foreach ($responses as $response) {
            $score = $response->calculateScore($responses);
            $response->update(['score' => $score]);
        }

        // Update total scores
        $userIds = $responses->pluck('user_id')->unique();
        foreach ($userIds as $userId) {
            \App\Models\QuizScore::updateScore($userId);
        }

        // Award 1 auto scoreboard point per quiz participation
        foreach ($userIds as $userId) {
            ScoreboardEntry::create([
                'user_id'    => $userId,
                'points'     => 1,
                'category'   => 'Auto',
                'note'       => 'Participation au quiz #' . $quiz->id,
                'awarded_by' => null,
            ]);
        }

        return redirect()->route('admin.quizzes.results', $quiz->id)
            ->with('success', 'Question fermée et scores recalculés');
    }

    public function pushScores(NumericQuiz $quiz)
    {
        if (!$quiz->isClosed()) {
            return redirect()->route('admin.quizzes.index')
                ->with('error', 'Le quiz doit être fermé avant de transférer les scores.');
        }

        $noteKey = 'Score quiz #' . $quiz->id;

        if (ScoreboardEntry::where('note', $noteKey)->exists()) {
            return redirect()->back()
                ->with('error', 'Les scores de ce quiz ont déjà été ajoutés au classement général.');
        }

        $responses = QuizResponse::where('quiz_id', $quiz->id)
            ->where('score', '>', 0)
            ->get();

        foreach ($responses as $response) {
            ScoreboardEntry::create([
                'user_id'    => $response->user_id,
                'points'     => $response->score,
                'category'   => 'Games',
                'origin'     => 'Quiz',
                'note'       => $noteKey,
                'awarded_by' => null,
            ]);
        }

        return redirect()->back()
            ->with('success', "Scores du quiz #{$quiz->id} ajoutés au classement général ({$responses->count()} joueurs).");
    }

    public function reset(NumericQuiz $quiz)
    {
        if ($quiz->isDraft()) {
            return redirect()->route('admin.quizzes.index')
                ->with('error', 'Ce quiz est déjà en brouillon.');
        }

        // Get user IDs before deleting responses
        $userIds = QuizResponse::where('quiz_id', $quiz->id)
            ->pluck('user_id')
            ->unique();

        // Delete all responses for this quiz
        QuizResponse::where('quiz_id', $quiz->id)->delete();

        // Remove scoreboard entries awarded when the quiz was closed
        ScoreboardEntry::where('note', 'Participation au quiz #' . $quiz->id)->delete();
        ScoreboardEntry::where('note', 'Score quiz #' . $quiz->id)->delete();

        // Reset quiz status back to 'draft'
        $quiz->update(['status' => 'draft']);

        // Recalculate quiz scores for affected users
        foreach ($userIds as $userId) {
            \App\Models\QuizScore::updateScore($userId);
        }

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Quiz réinitialisé avec succès.');
    }

    public function showFinale()
    {
        Cache::forever('quiz_finale_active', true);

        return redirect()->route('admin.quizzes.index')
            ->with('success', '🏆 Podium final activé ! Tous les joueurs voient maintenant le podium.');
    }

    public function hideFinale()
    {
        Cache::forget('quiz_finale_active');

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Podium final désactivé.');
    }

    public function destroy(NumericQuiz $quiz)
    {
        $quiz->delete();

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Question supprimée');
    }

    public function showResults(NumericQuiz $quiz)
    {
        $responses = QuizResponse::where('quiz_id', $quiz->id)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $leaderboard = $responses->sortBy([
            fn($r) => abs((float) $r->numeric_answer - (float) $quiz->correct_answer),
            fn($r) => $r->created_at->timestamp,
        ]);

        return view('admin.quizzes.results', compact('quiz', 'responses', 'leaderboard'));
    }

    public function liveCount(NumericQuiz $quiz)
    {
        return response()->json([
            'count' => QuizResponse::where('quiz_id', $quiz->id)->count(),
        ]);
    }

    public function downloadResults(NumericQuiz $quiz)
    {
        $responses = QuizResponse::where('quiz_id', $quiz->id)
            ->with('user')
            ->orderBy('score', 'desc')
            ->get();

        $filename = 'quiz-results-' . $quiz->id . '-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($quiz, $responses) {
            $handle = fopen('php://output', 'w');

            // Meta info
            fputcsv($handle, ['Question', $quiz->question]);
            fputcsv($handle, ['Réponse correcte', $quiz->correct_answer]);
            fputcsv($handle, ['Statut', ucfirst($quiz->status)]);
            fputcsv($handle, ['Nombre de réponses', $responses->count()]);
            fputcsv($handle, []);

            // Column headers
            fputcsv($handle, ['Position', 'Joueur', 'Réponse', 'Différence', 'Différence %', 'Score', 'Date']);

            foreach ($responses as $index => $response) {
                $diff = abs((float) $response->numeric_answer - (float) $quiz->correct_answer);
                $percentage = $quiz->correct_answer != 0
                    ? round(($diff / (float) $quiz->correct_answer) * 100, 2)
                    : 0;

                fputcsv($handle, [
                    $index + 1,
                    $response->user->name,
                    $response->numeric_answer,
                    $response->numeric_answer == $quiz->correct_answer ? 'Exacte' : $diff,
                    $response->numeric_answer == $quiz->correct_answer ? '0%' : $percentage . '%',
                    $response->score,
                    $response->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
