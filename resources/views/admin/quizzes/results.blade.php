@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('admin.quizzes.index') }}" class="text-blue-600 hover:text-blue-900">← Retour</a>
            <h1 class="text-3xl font-bold text-gray-900 mt-2">Résultats - {{ substr($quiz->question, 0, 50) }}...</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Réponse Correcte</h3>
                <p class="text-3xl font-bold text-blue-600">{{ $quiz->correct_answer }}</p>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Nombre de Réponses</h3>
                <p class="text-3xl font-bold text-green-600">{{ $responses->count() }}</p>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Statut</h3>
                <p class="text-3xl font-bold
                    {{ $quiz->status === 'draft' ? 'text-yellow-600' : '' }}
                    {{ $quiz->status === 'active' ? 'text-green-600' : '' }}
                    {{ $quiz->status === 'closed' ? 'text-red-600' : '' }}
                ">
                    {{ ucfirst($quiz->status) }}
                </p>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Classement</h2>
            </div>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">Joueur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">Réponse</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">Différence</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">Score</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($leaderboard as $index => $response)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-lg font-bold
                                    {{ $index === 0 ? 'text-yellow-600' : '' }}
                                    {{ $index === 1 ? 'text-gray-400' : '' }}
                                    {{ $index === 2 ? 'text-orange-600' : '' }}
                                ">
                                    {{ $index + 1 }}{{ $index === 0 ? '🥇' : ($index === 1 ? '🥈' : ($index === 2 ? '🥉' : '')) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                {{ $response->user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $response->numeric_answer }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @php
                                    $diff = abs($response->numeric_answer - $quiz->correct_answer);
                                    $percentage = round(($diff / $quiz->correct_answer) * 100, 2);
                                @endphp
                                {{ $response->numeric_answer == $quiz->correct_answer ? '✓ Exacte' : $diff . ' (' . $percentage . '%)' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">
                                <span class="px-3 py-1 rounded-full
                                    {{ $response->score >= 5 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}
                                ">
                                    {{ $response->score }} pts
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Aucune réponse</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <h3 class="font-semibold text-blue-900 mb-2">Comment fonctionne le scoring :</h3>
            <ul class="text-sm text-blue-800 space-y-1">
                <li>🥇 1er plus proche : 5 points</li>
                <li>🥈 2e plus proche : 2 points</li>
                <li>🥉 3e plus proche : 1 point</li>
                <li>✨ Bonus : +3 points si réponse exacte</li>
            </ul>
        </div>
    </div>
</div>
@endsection
