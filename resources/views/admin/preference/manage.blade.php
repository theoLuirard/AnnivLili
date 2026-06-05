@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
<div class="max-w-5xl mx-auto px-4">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">💝 {{ $game->title }}</h1>
            <div class="flex items-center gap-3 mt-1">
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    {{ $game->status === 'draft'  ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $game->status === 'active' ? 'bg-green-100 text-green-800'  : '' }}
                    {{ $game->status === 'closed' ? 'bg-red-100 text-red-800'     : '' }}
                ">{{ ucfirst($game->status) }}</span>
                @if($game->is_eliminatory_phase)
                    <span class="px-3 py-1 rounded-full text-sm font-bold bg-red-100 text-red-700">⚡ Phase éliminatoire active</span>
                @endif
            </div>
        </div>
        <div class="flex gap-2">
            @if($game->isDraft())
                <form action="{{ route('admin.preference.activate', $game->id) }}" method="POST">
                    @csrf
                    <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">▶ Lancer le jeu</button>
                </form>
            @endif
            @if($game->isActive())
                <form action="{{ route('admin.preference.close', $game->id) }}" method="POST"
                      onsubmit="return confirm('Terminer définitivement le jeu ?')">
                    @csrf
                    <button class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">■ Terminer le jeu</button>
                </form>
            @endif
            @if($game->show_podium)
                <form action="{{ route('admin.preference.dismiss-podium', $game->id) }}" method="POST"
                      onsubmit="return confirm('Masquer le podium pour les joueurs ?')">
                    @csrf
                    <button class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">🏆 Masquer le podium</button>
                </form>
            @endif
            @if($game->is_eliminatory_phase)
                <form action="{{ route('admin.preference.end-eliminatory', $game->id) }}" method="POST"
                      onsubmit="return confirm('Mettre fin à la phase éliminatoire ? Tous les joueurs éliminés reviendront.')">
                    @csrf
                    <button class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600">🔓 Fin phase éliminatoire</button>
                </form>
            @endif
            <a href="{{ route('admin.preference.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">← Retour</a>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">{{ $message }}</div>
    @endif
    @if ($message = Session::get('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">{{ $message }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Questions panel --}}
        <div class="lg:col-span-2">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Questions</h2>

            @forelse($game->questions as $question)
                <div class="bg-white rounded-xl shadow mb-4 overflow-hidden border-l-4
                    {{ $question->status === 'active'    ? 'border-green-500' : '' }}
                    {{ $question->status === 'revealing' ? 'border-purple-500' : '' }}
                    {{ $question->status === 'closed'    ? 'border-gray-400'  : '' }}
                    {{ $question->status === 'pending'   ? 'border-yellow-400' : '' }}
                ">
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <span class="text-xs text-gray-400 uppercase font-medium">Question {{ $question->order }}</span>
                                @if($question->is_eliminatory)
                                    <span class="ml-2 px-2 py-0.5 bg-red-100 text-red-700 text-xs rounded-full font-bold">⚡ Éliminatoire</span>
                                @endif
                            </div>
                            <span class="text-xs px-2 py-1 rounded-full font-medium
                                {{ $question->status === 'pending'   ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $question->status === 'active'    ? 'bg-green-100 text-green-700'  : '' }}
                                {{ $question->status === 'revealing' ? 'bg-purple-100 text-purple-700' : '' }}
                                {{ $question->status === 'closed'    ? 'bg-gray-100 text-gray-600'   : '' }}
                            ">{{ ucfirst($question->status) }}</span>
                        </div>

                        <p class="text-gray-800 font-semibold text-lg mb-3">{{ $question->question_text }}</p>

                        <div class="flex gap-3 mb-4">
                            <div class="flex-1 bg-blue-50 rounded-lg px-3 py-2 text-center">
                                <span class="text-xs text-blue-500 font-medium block">Option A 💙</span>
                                <span class="font-semibold text-blue-700">{{ $question->option_a }}</span>
                                @if($question->correct_answer === 'a')
                                    <span class="block text-xs text-green-600 font-bold">✓ Réponse du MJ</span>
                                @endif
                            </div>
                            <div class="flex-1 bg-pink-50 rounded-lg px-3 py-2 text-center">
                                <span class="text-xs text-pink-500 font-medium block">Option B 💗</span>
                                <span class="font-semibold text-pink-700">{{ $question->option_b }}</span>
                                @if($question->correct_answer === 'b')
                                    <span class="block text-xs text-green-600 font-bold">✓ Réponse du MJ</span>
                                @endif
                            </div>
                        </div>

                        {{-- Answers count --}}
                        <p class="text-sm text-gray-500 mb-4">
                            <span @if($question->isActive()) id="answer-count-{{ $question->id }}" @endif>{{ $question->answers->count() }}</span>
                            réponse(s)
                            @if($question->isActive())
                                <span class="ml-1 inline-block w-2 h-2 rounded-full bg-green-400 animate-pulse align-middle" title="En direct"></span>
                            @endif
                            @if($question->correct_answer)
                                — ✅ {{ $question->answers->where('is_correct', true)->count() }} correct(s)
                                / ❌ {{ $question->answers->where('is_correct', false)->count() }} faux
                            @endif
                        </p>

                        {{-- Action buttons per status --}}
                        @if($game->isActive())
                            @if($question->isPending())
                                <form action="{{ route('admin.preference.question.activate', [$game->id, $question->id]) }}" method="POST">
                                    @csrf
                                    <button class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-medium">
                                        ▶ Activer cette question
                                    </button>
                                </form>

                            @elseif($question->isActive())
                                {{-- Reveal form --}}
                                <form action="{{ route('admin.preference.question.reveal', [$game->id, $question->id]) }}" method="POST">
                                    @csrf
                                    <p class="text-sm font-medium text-gray-700 mb-2">Ma réponse (maître du jeu) :</p>
                                    <div class="grid grid-cols-2 gap-3 mb-3">
                                        <label class="flex items-center gap-2 border-2 border-blue-300 rounded-xl p-3 cursor-pointer hover:bg-blue-50">
                                            <input type="radio" name="correct_answer" value="a" required class="accent-blue-500">
                                            <span class="font-semibold text-blue-700">💙 {{ $question->option_a }}</span>
                                        </label>
                                        <label class="flex items-center gap-2 border-2 border-pink-300 rounded-xl p-3 cursor-pointer hover:bg-pink-50">
                                            <input type="radio" name="correct_answer" value="b" required class="accent-pink-500">
                                            <span class="font-semibold text-pink-700">💗 {{ $question->option_b }}</span>
                                        </label>
                                    </div>
                                    <button type="submit" class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 font-medium">
                                        🔍 Révéler ma réponse
                                    </button>
                                </form>

                            @elseif($question->isRevealing())
                                <form action="{{ route('admin.preference.question.close', [$game->id, $question->id]) }}" method="POST">
                                    @csrf
                                    <button class="w-full bg-gray-700 text-white py-2 rounded-lg hover:bg-gray-800 font-medium">
                                        ⏭ Fermer et passer à la suite
                                    </button>
                                </form>
                            @endif
                        @endif

                        {{-- Answers detail (when revealing or closed) --}}
                        @if(in_array($question->status, ['revealing', 'closed']) && $question->answers->count() > 0)
                            <div class="mt-4 space-y-1">
                                @foreach($question->answers->sortByDesc('points_earned') as $ans)
                                    <div class="flex items-center justify-between text-sm rounded-lg px-3 py-2
                                        {{ $ans->is_correct ? 'bg-green-50' : 'bg-red-50' }}">
                                        <span class="{{ $ans->is_correct ? 'text-green-700' : 'text-red-600' }} font-medium">
                                            {{ $ans->user->name }}
                                        </span>
                                        <span class="text-gray-500">
                                            {{ $ans->answer === 'a' ? $question->option_a : $question->option_b }}
                                            {{ $ans->is_correct ? '✅' : '❌' }}
                                            @if($ans->points_earned > 0)
                                                <strong class="text-yellow-600">+{{ $ans->points_earned }}pts</strong>
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl p-6 text-center text-gray-400">Aucune question dans ce jeu.</div>
            @endforelse
        </div>

        {{-- Right panel: Leaderboard + Eliminations --}}
        <div>
            {{-- Current eliminations --}}
            @if($game->eliminations->count() > 0)
                <div class="bg-red-50 border border-red-200 rounded-xl p-5 mb-5">
                    <h3 class="text-lg font-bold text-red-700 mb-3">❌ Joueurs éliminés</h3>
                    <div class="space-y-2">
                        @foreach($game->eliminations as $elim)
                            <div class="flex items-center gap-2 text-sm text-red-700">
                                <span class="w-7 h-7 rounded-full bg-red-200 flex items-center justify-center font-bold text-xs">
                                    {{ strtoupper(substr($elim->user->name, 0, 2)) }}
                                </span>
                                {{ $elim->user->name }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Leaderboard --}}
            <div class="bg-white rounded-xl shadow p-5">
                <h3 class="text-lg font-bold text-gray-800 mb-4">🏆 Classement</h3>
                @if(count($leaderboard) > 0)
                    <div class="space-y-2">
                        @foreach($leaderboard as $i => $entry)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span>{{ $i === 0 ? '🥇' : ($i === 1 ? '🥈' : ($i === 2 ? '🥉' : '#'.($i+1))) }}</span>
                                    <span class="w-8 h-8 rounded-full bg-gradient-to-br from-pink-400 to-purple-500 text-white flex items-center justify-center text-xs font-bold">
                                        {{ $entry['initials'] }}
                                    </span>
                                    <span class="text-sm font-medium text-gray-800">{{ $entry['name'] }}</span>
                                </div>
                                <span class="font-bold text-purple-700">{{ $entry['points'] }} pts</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400 text-sm text-center">Aucun point encore</p>
                @endif
            </div>
        </div>

    </div>
</div>
</div>
@endsection

@php $activeQuestion = $game->questions->firstWhere('status', 'active'); @endphp
@if($game->isActive() && $activeQuestion)
@push('scripts')
<script>
(function () {
    const gameId = {{ $game->id }};
    let trackedQuestionId = {{ $activeQuestion->id }};

    setInterval(async () => {
        try {
            const res = await fetch('/admin/preference/' + gameId + '/live-count');
            if (!res.ok) return;
            const data = await res.json();
            if (!data.question_id) return;
            const el = document.getElementById('answer-count-' + data.question_id);
            if (el) el.textContent = data.count;
        } catch (e) {}
    }, 3000);
})();
</script>
@endpush
@endif
