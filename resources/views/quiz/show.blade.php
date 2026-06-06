@extends('layouts.app')

@push('styles')
    <style>
        /* ── Confetti ──────────────────────────────────────────── */
        .confetti-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1100;
            overflow: hidden;
        }

        .confetti-piece {
            position: absolute;
            width: 10px;
            height: 14px;
            top: -20px;
            opacity: 0;
            animation: confettiFall linear forwards;
        }

        @keyframes confettiFall {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
            }

            80% {
                opacity: 1;
            }

            100% {
                transform: translateY(110vh) rotate(720deg);
                opacity: 0;
            }
        }

        /* ── Podium overlay (global leaderboard) ───────────────── */
        #podium-overlay {
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #4c1d95 100%);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        #podium-overlay.hidden {
            display: none;
        }

        .stars {
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(1px 1px at 20% 30%, rgba(255, 255, 255, 0.6) 0%, transparent 100%),
                radial-gradient(1px 1px at 80% 10%, rgba(255, 255, 255, 0.5) 0%, transparent 100%),
                radial-gradient(1px 1px at 50% 60%, rgba(255, 255, 255, 0.4) 0%, transparent 100%),
                radial-gradient(1px 1px at 10% 70%, rgba(255, 255, 255, 0.6) 0%, transparent 100%),
                radial-gradient(1px 1px at 90% 80%, rgba(255, 255, 255, 0.5) 0%, transparent 100%),
                radial-gradient(1px 1px at 35% 15%, rgba(255, 255, 255, 0.7) 0%, transparent 100%),
                radial-gradient(1px 1px at 65% 45%, rgba(255, 255, 255, 0.6) 0%, transparent 100%),
                radial-gradient(1.5px 1.5px at 75% 25%, rgba(255, 255, 255, 0.8) 0%, transparent 100%),
                radial-gradient(1.5px 1.5px at 25% 85%, rgba(255, 255, 255, 0.7) 0%, transparent 100%);
            pointer-events: none;
        }

        .podium-title {
            animation: titleDrop 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) 0.3s both;
        }

        @keyframes titleDrop {
            from {
                transform: translateY(-80px) scale(0.5);
                opacity: 0;
            }

            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }

        .podium-stage {
            display: flex;
            align-items: flex-end;
            justify-content: center;
            gap: 8px;
            height: 260px;
            position: relative;
        }

        .podium-block {
            width: 120px;
            border-radius: 12px 12px 0 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            padding-bottom: 12px;
            transform: translateY(100%);
            opacity: 0;
            transition: transform 0.9s cubic-bezier(0.34, 1.2, 0.64, 1), opacity 0.6s ease;
            position: relative;
            overflow: visible;
        }

        .podium-block.rise {
            transform: translateY(0);
            opacity: 1;
        }

        .podium-1st {
            height: 220px;
            background: linear-gradient(180deg, #f59e0b, #d97706);
            order: 2;
            transition-delay: 0.2s;
        }

        .podium-2nd {
            height: 160px;
            background: linear-gradient(180deg, #9ca3af, #6b7280);
            order: 1;
            transition-delay: 0.6s;
        }

        .podium-3rd {
            height: 120px;
            background: linear-gradient(180deg, #cd7c3a, #92400e);
            order: 3;
            transition-delay: 0.9s;
        }

        .podium-player {
            position: absolute;
            top: -90px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        .podium-player.show {
            opacity: 1;
            transform: translateY(0);
        }

        .podium-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 800;
            color: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            border: 3px solid white;
        }

        .podium-1st .podium-avatar {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            animation: pulse-winner 1.5s ease-in-out infinite 1.5s;
        }

        .podium-2nd .podium-avatar {
            background: linear-gradient(135deg, #d1d5db, #9ca3af);
        }

        .podium-3rd .podium-avatar {
            background: linear-gradient(135deg, #d97706, #92400e);
        }

        @keyframes pulse-winner {

            0%,
            100% {
                box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4);
                transform: scale(1);
            }

            50% {
                box-shadow: 0 4px 30px rgba(251, 191, 36, 0.8);
                transform: scale(1.08);
            }
        }

        .podium-name {
            font-size: 0.75rem;
            font-weight: 700;
            color: white;
            text-align: center;
            max-width: 110px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.5);
        }

        .podium-score-badge {
            font-size: 0.7rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.85);
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
        }

        .podium-rank-number {
            font-size: 1.5rem;
            font-weight: 900;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1;
        }

        .podium-medal {
            font-size: 2rem;
            line-height: 1;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }

        .podium-1st::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 20px;
            background: radial-gradient(ellipse, rgba(251, 191, 36, 0.6) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(6px);
        }

        #podium-close-btn {
            animation: fadeInUp 0.5s ease 2s both;
        }

        /* ── Results Overlay (per-question reveal) ─────────────── */
        #results-overlay {
            position: fixed;
            inset: 0;
            background: linear-gradient(160deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
            z-index: 900;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            overflow-y: auto;
            padding: 2rem 1rem 3rem;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.5s ease;
        }

        #results-overlay.visible {
            opacity: 1;
            pointer-events: all;
        }

        #results-overlay.hidden {
            display: none;
        }

        /* Starfield in results overlay */
        .results-stars {
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image:
                radial-gradient(1px 1px at 15% 20%, rgba(255, 255, 255, 0.5) 0%, transparent 100%),
                radial-gradient(1px 1px at 75% 15%, rgba(255, 255, 255, 0.4) 0%, transparent 100%),
                radial-gradient(1px 1px at 45% 65%, rgba(255, 255, 255, 0.3) 0%, transparent 100%),
                radial-gradient(1.5px 1.5px at 85% 45%, rgba(255, 255, 255, 0.6) 0%, transparent 100%),
                radial-gradient(1px 1px at 30% 85%, rgba(255, 255, 255, 0.4) 0%, transparent 100%),
                radial-gradient(1px 1px at 60% 35%, rgba(255, 255, 255, 0.5) 0%, transparent 100%),
                radial-gradient(1.5px 1.5px at 10% 55%, rgba(255, 255, 255, 0.6) 0%, transparent 100%);
        }

        /* Phase animations */
        .phase {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .phase.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* Correct answer big reveal */
        #correct-answer-number {
            display: inline-block;
            font-size: 5rem;
            font-weight: 900;
            color: #fbbf24;
            text-shadow: 0 0 40px rgba(251, 191, 36, 0.8), 0 0 80px rgba(251, 191, 36, 0.4);
            transform: scale(0);
            transition: transform 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        #correct-answer-number.pop {
            transform: scale(1);
        }

        /* Player result card */
        #my-result-card {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            max-width: 480px;
            width: 100%;
        }

        .rank-badge {
            font-size: 3rem;
            line-height: 1;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.4));
        }

        /* Leaderboard rows */
        .result-row {
            opacity: 0;
            transform: translateX(-30px);
            transition: opacity 0.4s ease, transform 0.4s ease;
        }

        .result-row.show {
            opacity: 1;
            transform: translateX(0);
        }

        .result-row.is-me {
            background: rgba(99, 102, 241, 0.25);
            border-color: rgba(99, 102, 241, 0.5) !important;
        }

        /* Waiting pulse */
        @keyframes waitPulse {

            0%,
            100% {
                opacity: 0.6;
            }

            50% {
                opacity: 1;
            }
        }

        .wait-pulse {
            animation: waitPulse 2s ease-in-out infinite;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

@section('content')

    {{-- ──────────────────────────────────────────────────────────── --}}
    {{-- Results Overlay (per-question animated reveal)               --}}
    {{-- ──────────────────────────────────────────────────────────── --}}
    <div id="results-overlay" class="{{ $initialState['status'] === 'closed' ? '' : 'hidden' }}">
        <div class="results-stars"></div>
        <div class="confetti-container" id="results-confetti"></div>

        <div class="relative z-10 w-full max-w-2xl mx-auto flex flex-col items-center gap-6">

            {{-- Phase 1: Header --}}
            <div id="phase-header" class="phase text-center pt-4">
                <p class="text-indigo-300 text-sm font-semibold tracking-widest uppercase mb-1">🔒 Question fermée !</p>
                <h2 class="text-3xl font-black text-white" id="results-question-text"></h2>
            </div>

            {{-- Phase 2: Correct answer --}}
            <div id="phase-answer" class="phase text-center">
                <p class="text-gray-400 text-sm uppercase tracking-widest mb-2">La bonne réponse était</p>
                <div>
                    <span id="correct-answer-number">0</span>
                </div>
            </div>

            {{-- Phase 3: My result --}}
            <div id="phase-myresult" class="phase w-full">
                <div id="my-result-card" class="p-5 mx-auto">
                    <div class="flex items-center gap-4">
                        <div class="rank-badge" id="my-rank-emoji">🎯</div>
                        <div class="flex-1">
                            <p class="text-white font-bold text-lg">Ma performance</p>
                            <p class="text-gray-300 text-sm" id="my-answer-text"></p>
                            <p class="text-gray-300 text-sm" id="my-diff-text"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-yellow-300 text-3xl font-black" id="my-score-text"></p>
                            <p class="text-gray-400 text-xs">points gagnés</p>
                        </div>
                    </div>
                </div>
                <div id="not-answered-card" class="hidden text-center py-4">
                    <p class="text-gray-400 text-sm">😶 Vous n'avez pas répondu à cette question</p>
                </div>
            </div>

            {{-- Phase 4: Leaderboard --}}
            <div id="phase-leaderboard" class="phase w-full">
                <h3 class="text-white font-bold text-center mb-3 text-lg">🏆 Classement de la question</h3>
                <div id="leaderboard-rows" class="space-y-2"></div>
            </div>

            {{-- Phase 5: Waiting --}}
            <div id="phase-waiting" class="phase text-center pb-4">
                <p class="text-indigo-300 text-sm wait-pulse">⏳ En attente de la prochaine question par le maître du jeu...
                </p>
            </div>
        </div>
    </div>

    {{-- ──────────────────────────────────────────────────────────── --}}
    {{-- Podium Overlay (global leaderboard end screen)              --}}
    {{-- Always in DOM; shown dynamically via JS or on page load     --}}
    {{-- ──────────────────────────────────────────────────────────── --}}
    <div id="podium-overlay" class="hidden">
        <div class="stars"></div>
        <div class="confetti-container" id="confetti-container"></div>

        <div class="relative z-10 flex flex-col items-center gap-8 px-4">
            <div class="podium-title text-center">
                <p class="text-yellow-300 text-lg font-semibold tracking-widest uppercase mb-1">🎉 Quiz Terminé !</p>
                <h1 class="text-5xl font-black text-white" style="text-shadow: 0 0 30px rgba(251,191,36,0.5);">
                    🏆 Podium Final
                </h1>
            </div>

                <div class="podium-stage">
                    <div class="podium-block podium-2nd" id="block-2nd">
                        <div class="podium-player" id="player-2nd">
                            @if ($topWinners->count() >= 2)
                                <div class="podium-medal">🥈</div>
                                <div class="podium-avatar">
                                    @if($topWinners[1]->user->profile_picture)
                                        <img src="{{ asset('storage/' . $topWinners[1]->user->profile_picture) }}" class="w-full h-full rounded-full object-cover" />
                                    @else
                                        {{ $topWinners[1]->user->initials }}
                                    @endif
                                </div>
                                <div class="podium-name">{{ $topWinners[1]->user->name }}</div>
                                <div class="podium-score-badge">{{ $topWinners[1]->total_score }} pts</div>
                            @endif
                        </div>
                        <span class="podium-rank-number">2</span>
                    </div>

                    <div class="podium-block podium-1st" id="block-1st">
                        <div class="podium-player" id="player-1st">
                            @if ($topWinners->count() >= 1)
                                <div class="podium-medal">🥇</div>
                                <div class="podium-avatar">
                                    @if($topWinners[0]->user->profile_picture)
                                        <img src="{{ asset('storage/' . $topWinners[0]->user->profile_picture) }}" class="w-full h-full rounded-full object-cover" />
                                    @else
                                        {{ $topWinners[0]->user->initials }}
                                    @endif
                                </div>
                                <div class="podium-name">{{ $topWinners[0]->user->name }}</div>
                                <div class="podium-score-badge">{{ $topWinners[0]->total_score }} pts</div>
                            @endif
                        </div>
                        <span class="podium-rank-number">1</span>
                    </div>

                    <div class="podium-block podium-3rd" id="block-3rd">
                        <div class="podium-player" id="player-3rd">
                            @if ($topWinners->count() >= 3)
                                <div class="podium-medal">🥉</div>
                                <div class="podium-avatar">
                                    @if($topWinners[2]->user->profile_picture)
                                        <img src="{{ asset('storage/' . $topWinners[2]->user->profile_picture) }}" class="w-full h-full rounded-full object-cover" />
                                    @else
                                        {{ $topWinners[2]->user->initials }}
                                    @endif
                                </div>
                                <div class="podium-name">{{ $topWinners[2]->user->name }}</div>
                                <div class="podium-score-badge">{{ $topWinners[2]->total_score }} pts</div>
                            @endif
                        </div>
                        <span class="podium-rank-number">3</span>
                    </div>
                </div>

            <div class="flex flex-col sm:flex-row gap-3 mt-2">
                <a href="{{ route('scoreboard.index') }}"
                    class="px-8 py-3 bg-white text-purple-900 font-bold text-lg rounded-full shadow-lg hover:bg-yellow-300 hover:text-purple-900 transition-all duration-300 hover:scale-105 text-center">
                    Voir le classement complet →
                </a>
                <a href="{{ route('dashboard') }}"
                    class="px-8 py-3 bg-purple-700 border-2 border-white text-white font-bold text-lg rounded-full shadow-lg hover:bg-purple-500 transition-all duration-300 hover:scale-105 text-center">
                    ← Retour au menu
                </a>
            </div>
        </div>
    </div>

    {{-- ──────────────────────────────────────────────────────────── --}}
    {{-- Main Quiz Page                                               --}}
    {{-- ──────────────────────────────────────────────────────────── --}}
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
                                        <input type="number" id="numeric_answer" name="numeric_answer" step="0.01"
                                            required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg"
                                            placeholder="Entrez votre réponse...">
                                    </div>
                                    <button type="submit"
                                        class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold text-lg">
                                        Soumettre ma réponse
                                    </button>
                                </form>
                            @else
                                <div class="p-6 bg-green-50 border border-green-200 rounded-lg">
                                    <p class="text-lg text-green-800 font-semibold">✓ Vous avez répondu</p>
                                    <p class="text-green-700 mt-2">Votre réponse :
                                        <strong>{{ $userResponse->numeric_answer }}</strong>
                                    </p>
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
                                    <div
                                        class="flex items-center justify-between p-3 rounded-lg
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

    {{-- ──────────────────────────────────────────────────────────── --}}
    {{-- Results Overlay JS                                           --}}
    {{-- ──────────────────────────────────────────────────────────── --}}
    <script>
        (function() {
            const RANK_EMOJIS = {
                1: '🥇',
                2: '🥈',
                3: '🥉'
            };
            const CONFETTI_COLORS = ['#fbbf24', '#f59e0b', '#a78bfa', '#60a5fa', '#34d399', '#f472b6', '#fff',
                '#fb923c'
            ];

            // Initial state from PHP
            let currentState = @json($initialState);
            let resultsShownForQuizId = null;

            // ── Helpers ────────────────────────────────────────────────
            function fmt(n) {
                return Number.isInteger(n) ? n : parseFloat(n).toFixed(2).replace(/\.?0+$/, '');
            }

            function spawnConfetti() {
                const container = document.getElementById('results-confetti');
                if (!container) return;
                container.innerHTML = '';
                for (let i = 0; i < 100; i++) {
                    const el = document.createElement('div');
                    el.className = 'confetti-piece';
                    const color = CONFETTI_COLORS[Math.floor(Math.random() * CONFETTI_COLORS.length)];
                    el.style.cssText =
                        `left:${Math.random()*100}%;width:${6+Math.random()*8}px;height:${(6+Math.random()*8)*1.4}px;background:${color};border-radius:${Math.random()>.5?'50%':'0%'};animation-duration:${2+Math.random()*2.5}s;animation-delay:${Math.random()*3}s;`;
                    container.appendChild(el);
                }
            }

            function showPhase(id, delay) {
                return new Promise(resolve => {
                    setTimeout(() => {
                        const el = document.getElementById(id);
                        if (el) el.classList.add('show');
                        resolve();
                    }, delay);
                });
            }

            // ── Build leaderboard HTML ─────────────────────────────────
            function buildLeaderboard(rows) {
                const container = document.getElementById('leaderboard-rows');
                if (!container) return;
                container.innerHTML = '';
                rows.forEach((r, i) => {
                    const div = document.createElement('div');
                    div.className =
                        `result-row flex items-center gap-3 p-3 rounded-xl border ${r.is_me ? 'is-me border-indigo-400' : 'border-white/10 bg-white/5'}`;
                    const rankEmoji = RANK_EMOJIS[r.rank] || '';
                    const diffText = r.is_exact ?
                        '<span class="text-green-400 font-semibold">✓ Exacte</span>' :
                        `<span class="text-gray-400">${fmt(r.difference)} d'écart</span>`;
                    const avatarHtml = r.avatar
                        ? `<img src="${r.avatar}" class="w-9 h-9 rounded-full object-cover border-2 border-white/30" />`
                        : `<div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white border-2 border-white/30" style="background: hsl(${(r.rank * 47) % 360}, 60%, 40%)">${r.initials}</div>`;
                    div.innerHTML = `
                <div class="w-8 text-center">
                    <span class="text-lg font-black ${r.rank <= 3 ? 'text-yellow-300' : 'text-gray-400'}">${rankEmoji || r.rank}</span>
                </div>
                ${avatarHtml}
                <div class="flex-1 min-w-0">
                    <p class="text-white font-semibold text-sm truncate">${r.name}${r.is_me ? ' <span class="text-indigo-300">(vous)</span>' : ''}</p>
                    <p class="text-xs">${diffText} · réponse : <span class="text-gray-300">${fmt(r.answer)}</span></p>
                </div>
                <div class="text-right shrink-0">
                    <span class="text-yellow-300 font-black text-lg">+${r.score}</span>
                    <p class="text-gray-400 text-xs">pts</p>
                </div>
            `;
                    container.appendChild(div);
                    // Stagger rows
                    setTimeout(() => div.classList.add('show'), i * 120);
                });
            }

            // ── Main reveal animation ──────────────────────────────────
            function revealResults(data) {
                if (resultsShownForQuizId === data.quiz_id) return;
                resultsShownForQuizId = data.quiz_id;

                const overlay = document.getElementById('results-overlay');
                if (!overlay) return;

                // Populate static fields before showing
                const qText = document.getElementById('results-question-text');
                if (qText) qText.textContent = data.question;

                const answerEl = document.getElementById('correct-answer-number');
                if (answerEl) answerEl.textContent = fmt(data.correct_answer);

                // Personal result
                if (data.has_answered) {
                    document.getElementById('my-result-card').style.display = '';
                    document.getElementById('not-answered-card').classList.add('hidden');

                    const rankEmoji = RANK_EMOJIS[data.my_rank] || `#${data.my_rank}`;
                    document.getElementById('my-rank-emoji').textContent = rankEmoji;
                    document.getElementById('my-answer-text').textContent =
                        `Votre réponse : ${fmt(data.my_answer)}` +
                        (data.my_is_exact ? '  ✓ Exacte !' : '');
                    document.getElementById('my-diff-text').textContent =
                        data.my_is_exact ? '' : `Différence : ${fmt(data.my_difference)}`;
                    document.getElementById('my-score-text').textContent = `+${data.my_score}`;
                } else {
                    document.getElementById('my-result-card').style.display = 'none';
                    document.getElementById('not-answered-card').classList.remove('hidden');
                }

                // Pre-load leaderboard rows (invisible yet)
                buildLeaderboard(data.leaderboard || []);

                // Show overlay
                overlay.classList.remove('hidden');
                requestAnimationFrame(() => {
                    overlay.classList.add('visible');
                });

                // Sequenced phase reveals
                showPhase('phase-header', 300);
                showPhase('phase-answer', 900).then(() => {
                    setTimeout(() => {
                        if (answerEl) answerEl.classList.add('pop');
                        // Confetti only for top 3 or exact answer
                        if (data.has_answered && (data.my_rank <= 3 || data.my_is_exact)) {
                            spawnConfetti();
                        }
                    }, 200);
                });
                showPhase('phase-myresult', 2000);
                showPhase('phase-leaderboard', 3200);
                showPhase('phase-waiting', 4500);
            }

            // ── Podium overlay (finale) ────────────────────────────────
            let podiumShown = false;
            const PODIUM_CONFETTI_COLORS = ['#fbbf24', '#f59e0b', '#a78bfa', '#60a5fa', '#34d399', '#f472b6', '#fff', '#fb923c'];

            function fillPlayerBlock(containerId, winner, medal) {
                const el = document.getElementById(containerId);
                if (!el || !winner) return;
                const avatarContent = winner.avatar
                    ? `<img src="${winner.avatar}" class="w-full h-full rounded-full object-cover" />`
                    : winner.initials;
                el.innerHTML = `
                    <div class="podium-medal">${medal}</div>
                    <div class="podium-avatar">${avatarContent}</div>
                    <div class="podium-name">${winner.name}</div>
                    <div class="podium-score-badge">${winner.total_score} pts</div>`;
            }

            function showPodiumOverlay(winners) {
                if (podiumShown) return;
                podiumShown = true;

                if (winners && winners.length >= 1) fillPlayerBlock('player-1st', winners[0], '🥇');
                if (winners && winners.length >= 2) fillPlayerBlock('player-2nd', winners[1], '🥈');
                if (winners && winners.length >= 3) fillPlayerBlock('player-3rd', winners[2], '🥉');

                const overlay = document.getElementById('podium-overlay');
                if (overlay) overlay.classList.remove('hidden');

                function createPodiumConfetti() {
                    const container = document.getElementById('confetti-container');
                    if (!container) return;
                    for (let i = 0; i < 120; i++) {
                        const el = document.createElement('div');
                        el.className = 'confetti-piece';
                        const color = PODIUM_CONFETTI_COLORS[Math.floor(Math.random() * PODIUM_CONFETTI_COLORS.length)];
                        el.style.cssText = `left:${Math.random()*100}%;width:${6+Math.random()*8}px;height:${(6+Math.random()*8)*1.4}px;background:${color};border-radius:${Math.random()>.5?'50%':'0%'};animation-duration:${2.5+Math.random()*3}s;animation-delay:${Math.random()*4}s;`;
                        container.appendChild(el);
                    }
                }

                setTimeout(() => document.getElementById('block-1st')?.classList.add('rise'), 400);
                setTimeout(() => document.getElementById('player-1st')?.classList.add('show'), 900);
                setTimeout(() => document.getElementById('block-2nd')?.classList.add('rise'), 800);
                setTimeout(() => document.getElementById('player-2nd')?.classList.add('show'), 1300);
                setTimeout(() => document.getElementById('block-3rd')?.classList.add('rise'), 1100);
                setTimeout(() => document.getElementById('player-3rd')?.classList.add('show'), 1600);
                setTimeout(createPodiumConfetti, 500);
            }

            // ── Polling ────────────────────────────────────────────────
            let pollTimer = null;

            async function poll() {
                try {
                    const res = await fetch('{{ route('quiz.state') }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (!res.ok) return;
                    const data = await res.json();

                    if (data.status === 'finale') {
                        showPodiumOverlay(data.top_winners);
                        return; // Finale is the terminal state — stop polling
                    } else if (data.status === 'closed' && data.quiz_id !== resultsShownForQuizId) {
                        revealResults(data);
                    } else if (data.status === 'active') {
                        // If a new active quiz appeared (different from what we know), reload
                        if (currentState.status !== 'active' || currentState.quiz_id !== data.quiz_id) {
                            location.reload();
                            return;
                        }
                    } else if (data.status === 'waiting' && currentState.status !== 'waiting') {
                        // Transitioned to waiting (no quiz at all)
                        location.reload();
                        return;
                    }

                    currentState = data;
                } catch (e) {
                    // Network error — keep polling
                }
                pollTimer = setTimeout(poll, 2500);
            }

            // ── Init ───────────────────────────────────────────────────
            document.addEventListener('DOMContentLoaded', function() {
                if (currentState.status === 'closed') {
                    revealResults(currentState);
                } else if (currentState.status === 'finale') {
                    showPodiumOverlay(currentState.top_winners);
                    return; // No need to poll — finale is already shown
                }

                // Poll to detect state changes
                pollTimer = setTimeout(poll, 2500);
            });
        })();
    </script>
@endsection
