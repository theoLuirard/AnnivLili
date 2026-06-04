@extends('layouts.app')

@section('title', 'Tableau des scores')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">

    <div class="text-center mb-10">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">🏆 Tableau des scores</h1>
        <p class="text-gray-500">Classement général de tous les participants</p>
        <a href="{{ route('scoreboard.history') }}" class="inline-block mt-3 text-sm text-blue-600 hover:text-blue-800 font-medium">
            📋 Voir l'historique des attributions →
        </a>
    </div>

    @if($leaderboard->isEmpty())
        <div class="bg-white rounded-xl shadow p-10 text-center text-gray-500">
            Aucun score pour le moment. Les points seront affichés ici dès qu'ils seront attribués.
        </div>
    @else
        {{-- Podium (top 3) --}}
        @if($leaderboard->count() >= 1)
        <div class="flex justify-center items-end gap-6 mb-10">
            {{-- 2nd place --}}
            @if($leaderboard->count() >= 2)
            <div class="flex flex-col items-center">
                <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center text-2xl font-bold text-gray-600 mb-2 shadow">
                    {{ mb_strtoupper(mb_substr($leaderboard[1]->nickname ?: $leaderboard[1]->name, 0, 2)) }}
                </div>
                <div class="text-sm font-semibold text-gray-700">{{ $leaderboard[1]->nickname ?: $leaderboard[1]->name }}</div>
                <div class="text-lg font-bold text-gray-500">{{ $leaderboard[1]->total_points }} pts</div>
                <div class="w-20 h-16 bg-gray-300 rounded-t-lg flex items-center justify-center text-2xl">🥈</div>
            </div>
            @endif

            {{-- 1st place --}}
            <div class="flex flex-col items-center">
                <div class="w-20 h-20 rounded-full bg-yellow-300 flex items-center justify-center text-3xl font-bold text-yellow-800 mb-2 shadow-lg">
                    {{ mb_strtoupper(mb_substr($leaderboard[0]->nickname ?: $leaderboard[0]->name, 0, 2)) }}
                </div>
                <div class="text-base font-bold text-gray-800">{{ $leaderboard[0]->nickname ?: $leaderboard[0]->name }}</div>
                <div class="text-xl font-extrabold text-yellow-600">{{ $leaderboard[0]->total_points }} pts</div>
                <div class="w-24 h-24 bg-yellow-300 rounded-t-lg flex items-center justify-center text-3xl">🥇</div>
            </div>

            {{-- 3rd place --}}
            @if($leaderboard->count() >= 3)
            <div class="flex flex-col items-center">
                <div class="w-14 h-14 rounded-full bg-orange-200 flex items-center justify-center text-xl font-bold text-orange-700 mb-2 shadow">
                    {{ mb_strtoupper(mb_substr($leaderboard[2]->nickname ?: $leaderboard[2]->name, 0, 2)) }}
                </div>
                <div class="text-sm font-semibold text-gray-700">{{ $leaderboard[2]->nickname ?: $leaderboard[2]->name }}</div>
                <div class="text-lg font-bold text-orange-500">{{ $leaderboard[2]->total_points }} pts</div>
                <div class="w-16 h-10 bg-orange-300 rounded-t-lg flex items-center justify-center text-xl">🥉</div>
            </div>
            @endif
        </div>
        @endif

        {{-- Full ranking table --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100 text-gray-600 text-sm uppercase">
                    <tr>
                        <th class="py-3 px-6 text-left">Rang</th>
                        <th class="py-3 px-6 text-left">Joueur</th>
                        <th class="py-3 px-6 text-right">Points</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($leaderboard as $i => $player)
                    <tr class="{{ auth()->id() == $player->id ? 'bg-blue-50' : 'hover:bg-gray-50' }} transition">
                        <td class="py-4 px-6 font-bold text-gray-500">
                            @if($i === 0) 🥇
                            @elseif($i === 1) 🥈
                            @elseif($i === 2) 🥉
                            @else #{{ $i + 1 }}
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-sm font-bold text-blue-700">
                                    {{ mb_strtoupper(mb_substr($player->nickname ?: $player->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-800">{{ $player->nickname ?: $player->name }}</div>
                                    @if(auth()->id() == $player->id)
                                        <div class="text-xs text-blue-500">Vous</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-right font-bold text-lg {{ $player->total_points > 0 ? 'text-green-600' : 'text-gray-400' }}">
                            {{ $player->total_points }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
