<?php

namespace App\Http\Controllers;

use App\Models\ScoreboardEntry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScoreboardController extends Controller
{
    public function index()
    {
        $leaderboard = ScoreboardEntry::getLeaderboard();

        return view('scoreboard.index', compact('leaderboard'));
    }

    public function history()
    {
        $entries = ScoreboardEntry::with('user', 'awardedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return view('scoreboard.history', compact('entries'));
    }
}
