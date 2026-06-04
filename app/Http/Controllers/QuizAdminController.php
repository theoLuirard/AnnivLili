<?php

namespace App\Http\Controllers;

use App\Models\NumericQuiz;
use App\Models\QuizResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            ->with('success', 'Question cre avec succs');
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
            ->with('success', 'Question mise  jour');
    }

    public function activate(NumericQuiz $quiz)
    {
        // Close any other active quiz
        NumericQuiz::where('status', 'active')->update(['status' => 'closed']);

        $quiz->update(['status' => 'active']);

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Question active');
    }

    public function close(NumericQuiz $quiz)
    {
        $quiz->update(['status' => 'closed']);

        // Recalculate all scores for this quiz
        $responses = QuizResponse::where('quiz_id', $quiz->id)->get();
        foreach ($responses as $response) {
            $score = $response->calculateScore();
            $response->update(['score' => $score]);
        }

        // Update total scores
        $userIds = $responses->pluck('user_id')->unique();
        foreach ($userIds as $userId) {
            \App\Models\QuizScore::updateScore($userId);
        }

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Question ferme et scores recalculs');
    }

    public function reset(NumericQuiz $quiz)
    {
        // Validation: Only allow reset if quiz is 'closed'
        if ($quiz->status !== 'closed') {
            return redirect()->route('admin.quizzes.index')
                ->with('error', 'Only closed quizzes can be reset');
        }

        // Get user IDs before deleting responses
        $userIds = QuizResponse::where('quiz_id', $quiz->id)
            ->pluck('user_id')
            ->unique();

        // Delete all responses for this quiz
        QuizResponse::where('quiz_id', $quiz->id)->delete();

        // Update quiz status back to 'draft'
        $quiz->update(['status' => 'draft']);

        // Reset scores for users who had responses on this quiz
        foreach ($userIds as $userId) {
            \App\Models\QuizScore::updateScore($userId);
        }

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Quiz réinitialisé');
    }

    public function destroy(NumericQuiz $quiz)
    {
        $quiz->delete();

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Question supprime');
    }

    public function showResults(NumericQuiz $quiz)
    {
        $responses = QuizResponse::where('quiz_id', $quiz->id)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $leaderboard = $responses->sortByDesc('score');

        return view('admin.quizzes.results', compact('quiz', 'responses', 'leaderboard'));
    }
}
