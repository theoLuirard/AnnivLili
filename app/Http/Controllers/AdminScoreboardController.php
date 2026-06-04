<?php

namespace App\Http\Controllers;

use App\Models\ScoreboardEntry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminScoreboardController extends Controller
{
    public function index()
    {
        $users = User::all();
        $entries = ScoreboardEntry::with('user', 'awardedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(30);
        $leaderboard = ScoreboardEntry::getLeaderboard();
        $categories = ['Games', 'Challenges', 'Bonus'];

        return view('admin.scoreboard.index', compact('users', 'entries', 'leaderboard', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'  => 'required|exists:users,id',
            'points'   => 'required|integer|not_in:0',
            'category' => 'required|in:Games,Challenges,Bonus',
            'note'     => 'nullable|string|max:255',
        ]);

        $validated['awarded_by'] = Auth::id();

        ScoreboardEntry::create($validated);

        return redirect()->route('admin.scoreboard.index')
            ->with('success', 'Points ajoutés avec succès');
    }

    public function destroy(ScoreboardEntry $entry)
    {
        $entry->delete();

        return redirect()->route('admin.scoreboard.index')
            ->with('success', 'Entrée supprimée');
    }
}
