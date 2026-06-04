@extends('layouts.app')

@section('title', 'Historique des points')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">

    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">📋 Historique des points</h1>
        <p class="text-gray-500">Toutes les attributions de points, les plus récentes en premier</p>
    </div>

    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('scoreboard.index') }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
            ← Retour au classement
        </a>
        <span class="text-sm text-gray-400">{{ $entries->total() }} entrée(s)</span>
    </div>

    @if($entries->isEmpty())
        <div class="bg-white rounded-xl shadow p-10 text-center text-gray-500">
            Aucune attribution de points pour le moment.
        </div>
    @else
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100 text-gray-600 text-sm uppercase">
                    <tr>
                        <th class="py-3 px-5 text-left">Date</th>
                        <th class="py-3 px-5 text-left">Joueur</th>
                        <th class="py-3 px-5 text-left">Catégorie</th>
                        <th class="py-3 px-5 text-left">Note</th>
                        <th class="py-3 px-5 text-left">Attribué par</th>
                        <th class="py-3 px-5 text-right">Points</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($entries as $entry)
                    <tr class="{{ auth()->id() == $entry->user_id ? 'bg-blue-50' : 'hover:bg-gray-50' }} transition">
                        <td class="py-3 px-5 text-sm text-gray-500 whitespace-nowrap">
                            {{ $entry->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="py-3 px-5">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-xs font-bold text-blue-700">
                                    {{ mb_strtoupper(mb_substr($entry->user->nickname ?: $entry->user->name, 0, 2)) }}
                                </div>
                                <span class="font-medium text-gray-800">
                                    {{ $entry->user->nickname ?: $entry->user->name }}
                                    @if(auth()->id() == $entry->user_id)
                                        <span class="text-xs text-blue-500 ml-1">(vous)</span>
                                    @endif
                                </span>
                            </div>
                        </td>
                        <td class="py-3 px-5">
                            @php
                                $badgeColors = [
                                    'Games'      => 'bg-purple-100 text-purple-700',
                                    'Challenges' => 'bg-orange-100 text-orange-700',
                                    'Bonus'      => 'bg-green-100 text-green-700',
                                    'Auto'       => 'bg-gray-100 text-gray-600',
                                ];
                                $color = $badgeColors[$entry->category] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{ $color }}">
                                {{ $entry->category }}
                            </span>
                        </td>
                        <td class="py-3 px-5 text-sm text-gray-600 max-w-xs truncate">
                            {{ $entry->note ?: '—' }}
                        </td>
                        <td class="py-3 px-5 text-sm text-gray-500">
                            {{ $entry->awardedBy ? ($entry->awardedBy->nickname ?: $entry->awardedBy->name) : '—' }}
                        </td>
                        <td class="py-3 px-5 text-right font-bold text-lg {{ $entry->points > 0 ? 'text-green-600' : 'text-red-500' }}">
                            {{ $entry->points > 0 ? '+' : '' }}{{ $entry->points }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($entries->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $entries->links() }}
        </div>
        @endif
    @endif
</div>
@endsection
