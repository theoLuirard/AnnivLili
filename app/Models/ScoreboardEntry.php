<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScoreboardEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'points',
        'category',
        'origin',
        'note',
        'awarded_by',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function awardedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'awarded_by');
    }

    public static function getLeaderboard()
    {
        return User::select('users.id', 'users.name', 'users.nickname', 'users.profile_picture', 'users.avatar_color')
            ->selectRaw('COALESCE(SUM(scoreboard_entries.points), 0) as total_points')
            ->leftJoin('scoreboard_entries', 'users.id', '=', 'scoreboard_entries.user_id')
            ->whereDoesntHave('roles', fn ($q) => $q->where('name', 'admin'))
            ->groupBy('users.id', 'users.name', 'users.nickname', 'users.profile_picture', 'users.avatar_color')
            ->orderByDesc('total_points')
            ->get();
    }
}
