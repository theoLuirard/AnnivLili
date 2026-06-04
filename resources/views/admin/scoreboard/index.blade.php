@extends('layouts.app')

@section('title', 'Gérer le tableau des scores')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10">

    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">🏆 Gestion du tableau des scores</h1>
            <p class="text-gray-500 mt-1">Attribuez des points manuellement aux participants</p>
        </div>
        <a href="{{ route('scoreboard.index') }}" class="text-blue-600 hover:underline text-sm">← Voir le classement public</a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Add points form --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Ajouter des points</h2>
                <form action="{{ route('admin.scoreboard.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Joueur</label>
                        <select name="user_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">-- Choisir un joueur --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->nickname ?: $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Points</label>
                        <input type="number" name="points" value="{{ old('points') }}" placeholder="Ex: 5 ou -3"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                        <p class="text-xs text-gray-400 mt-1">Valeur négative pour retirer des points</p>
                        @error('points')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                        <select name="category" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                        @error('category')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Note (optionnelle)</label>
                        <input type="text" name="note" value="{{ old('note') }}" placeholder="Raison ou description"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('note')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition font-medium">
                        Attribuer les points
                    </button>
                </form>
            </div>

            {{-- Current standings --}}
            <div class="bg-white rounded-xl shadow p-6 mt-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Classement actuel</h2>
                @if($leaderboard->isEmpty())
                    <p class="text-gray-400 text-sm">Aucun score pour le moment.</p>
                @else
                    <ul class="space-y-2">
                        @foreach($leaderboard->take(10) as $i => $player)
                        <li class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-400 text-sm w-5">#{{ $i+1 }}</span>
                                <span class="font-medium text-gray-700 text-sm">{{ $player->nickname ?: $player->name }}</span>
                            </div>
                            <span class="font-bold text-sm {{ $player->total_points > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                {{ $player->total_points }} pts
                            </span>
                        </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- Entries log --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-semibold text-gray-700">Historique des attributions</h2>
                </div>
                @if($entries->isEmpty())
                    <div class="p-10 text-center text-gray-400">
                        Aucune entrée pour le moment.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                                <tr>
                                    <th class="py-3 px-4 text-left">Joueur</th>
                                    <th class="py-3 px-4 text-left">Points</th>
                                    <th class="py-3 px-4 text-left">Catégorie</th>
                                    <th class="py-3 px-4 text-left">Note</th>
                                    <th class="py-3 px-4 text-left">Par</th>
                                    <th class="py-3 px-4 text-left">Date</th>
                                    <th class="py-3 px-4 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($entries as $entry)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 font-medium text-gray-800">{{ $entry->user->nickname ?: $entry->user->name }}</td>
                                    <td class="py-3 px-4 font-bold {{ $entry->points > 0 ? 'text-green-600' : 'text-red-500' }}">
                                        {{ $entry->points > 0 ? '+' : '' }}{{ $entry->points }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium
                                            @if($entry->category === 'Games') bg-blue-100 text-blue-700
                                            @elseif($entry->category === 'Challenges') bg-purple-100 text-purple-700
                                            @elseif($entry->category === 'Bonus') bg-yellow-100 text-yellow-700
                                            @else bg-gray-100 text-gray-600
                                            @endif">
                                            {{ $entry->category }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-gray-500">{{ $entry->note ?: '—' }}</td>
                                    <td class="py-3 px-4 text-gray-500">
                                        {{ $entry->awardedBy ? ($entry->awardedBy->nickname ?: $entry->awardedBy->name) : 'Auto' }}
                                    </td>
                                    <td class="py-3 px-4 text-gray-400">{{ $entry->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <form action="{{ route('admin.scoreboard.destroy', $entry) }}" method="POST"
                                              onsubmit="return confirm('Supprimer cette entrée ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">
                                                Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4">
                        {{ $entries->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
