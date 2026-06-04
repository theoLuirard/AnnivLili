@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Quiz</h1>

        @if ($message = Session::get('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ $message }}
            </div>
        @endif

        @if ($message = Session::get('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ $message }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Quiz Section -->
            <div class="lg:col-span-2">
                @if ($activeQuiz)
                    <div class="bg-white shadow rounded-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $activeQuiz->question }}</h2>

                        @if ($activeQuiz->description)
                            <p class="text-gray-600 mb-6 text-lg">{{ $activeQuiz->description }}</p>
                        @endif

                        @if (!$userResponse)
                            <form action="{{ route('quiz.submit') }}" method="POST">
                                @csrf

                                <div class="mb-6">
                                    <label for="numeric_answer" class="block text-sm font-medium text-gray-700 mb-2">
                                        Votre réponse (nombre)
                                    </label>
                                    <input type="number" id="numeric_answer" name="numeric_answer" step="0.01" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg"
                                        placeholder="Entrez votre réponse...">
                                </div>

                                <button type="submit" class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold text-lg">
                                    Soumettre ma réponse
                                </button>
                            </form>
                        @else
                            <div class="p-6 bg-green-50 border border-green-200 rounded-lg">
                                <p class="text-lg text-green-800 font-semibold">✓ Vous avez répondu</p>
                                <p class="text-green-700 mt-2">Votre réponse : <strong>{{ $userResponse->numeric_answer }}</strong></p>
                                <p class="text-sm text-green-600 mt-2">En attente de clôture de la question...</p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="bg-white shadow rounded-lg p-8 text-center">
                        <p class="text-gray-500 text-lg">En attente de la prochaine question...</p>
                        <p class="text-gray-400 mt-2">Consultez le classement ci-contre en attendant</p>
                    </div>
                @endif
            </div>

            <!-- Leaderboard Section -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg p-6 sticky top-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">🏆 Classement Global</h3>

                    @if ($leaderboard->count() > 0)
                        <div class="space-y-3">
                            @foreach ($leaderboard as $index => $score)
                                <div class="flex items-center justify-between p-3 rounded-lg
                                    {{ $index === 0 ? 'bg-yellow-50 border border-yellow-200' : 'bg-gray-50' }}
                                    {{ auth()->id() === $score->user->id ? 'ring-2 ring-blue-500' : '' }}
                                ">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl font-bold text-gray-700 w-6 text-center">
                                            {{ $index + 1 }}{{ $index === 0 ? '🥇' : ($index === 1 ? '🥈' : ($index === 2 ? '🥉' : '')) }}
                                        </span>
                                        <div>
                                            <p class="font-semibold text-gray-900 text-sm">
                                                {{ $score->user->name }}
                                                @if (auth()->id() === $score->user->id)
                                                    <span class="text-blue-600 font-bold">(vous)</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <span class="font-bold text-lg text-gray-900">{{ $score->total_score }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">Aucun score pour l'instant</p>
                    @endif

                    <div class="mt-6 p-3 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-800">
                        <p class="font-semibold mb-2">Système de scoring :</p>
                        <ul class="space-y-1">
                            <li>🥇 1er : 5 pts</li>
                            <li>🥈 2e : 2 pts</li>
                            <li>🥉 3e : 1 pt</li>
                            <li>✨ +3 pts exacte</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
