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
            'user_ids'    => 'required|array|min:1',
            'user_ids.*'  => 'exists:users,id',
            'points'      => 'required|integer|not_in:0',
            'category'    => 'required|in:Games,Challenges,Bonus',
            'origin'      => 'nullable|string|max:255',
            'note'        => 'nullable|string|max:255',
        ]);

        $awardedBy = Auth::id();

        foreach ($validated['user_ids'] as $userId) {
            ScoreboardEntry::create([
                'user_id'    => $userId,
                'points'     => $validated['points'],
                'category'   => $validated['category'],
                'origin'     => $validated['origin'] ?? null,
                'note'       => $validated['note'] ?? null,
                'awarded_by' => $awardedBy,
            ]);
        }

        $count = count($validated['user_ids']);
        $message = $count > 1
            ? "Points ajoutés à {$count} joueurs avec succès"
            : 'Points ajoutés avec succès';

        return redirect()->route('admin.scoreboard.index')->with('success', $message);
    }

    public function destroy(ScoreboardEntry $entry)
    {
        $entry->delete();

        return redirect()->route('admin.scoreboard.index')
            ->with('success', 'Entrée supprimée');
    }
}
