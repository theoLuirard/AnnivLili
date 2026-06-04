@extends('layouts.app')

@push('styles')
<style>
    .option-btn {
        transition: all 0.2s ease;
        cursor: pointer;
        user-select: none;
    }
    .option-btn:hover:not(.disabled) {
        transform: scale(1.03);
    }
    .option-btn.selected-a { background: #3b82f6; color: white; border-color: #3b82f6; }
    .option-btn.selected-b { background: #ec4899; color: white; border-color: #ec4899; }
    .option-btn.correct   { background: #22c55e; color: white; border-color: #22c55e; }
    .option-btn.wrong     { background: #ef4444; color: white; border-color: #ef4444; }
    .option-btn.disabled  { opacity: 0.6; cursor: not-allowed; }

    .pulse-ring {
        animation: pulseRing 2s infinite;
    }
    @keyframes pulseRing {
        0%, 100% { box-shadow: 0 0 0 0 rgba(236, 72, 153, 0.4); }
        50% { box-shadow: 0 0 0 12px rgba(236, 72, 153, 0); }
    }

    .slide-in { animation: slideIn 0.5s ease; }
    @keyframes slideIn {
        from { transform: translateY(30px); opacity: 0; }
        to   { transform: translateY(0);    opacity: 1; }
    }

    .eliminated-banner {
        background: linear-gradient(135deg, #dc2626, #991b1b);
        animation: shake 0.5s ease;
    }
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-8px); }
        75% { transform: translateX(8px); }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-pink-50 to-purple-50 py-8">
    <div class="max-w-2xl mx-auto px-4">

        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800">💝 Tu préfères ?</h1>
            <p class="text-gray-500 mt-2">Découvrez les préférences du maître du jeu !</p>
        </div>

        {{-- Main card --}}
        <div id="game-card" class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div id="game-content">
                @if($activeGame)
                    <div class="p-8 text-center text-gray-500">
                        <div class="text-5xl mb-4">⏳</div>
                        <p class="text-xl">Chargement...</p>
                    </div>
                @else
                    <div class="p-8 text-center">
                        <div class="text-6xl mb-4">😴</div>
                        <h2 class="text-2xl font-bold text-gray-700 mb-2">En attente...</h2>
                        <p class="text-gray-500">Le jeu n'a pas encore commencé. Revenez bientôt !</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Leaderboard --}}
        <div id="leaderboard-section" class="mt-6 hidden">
            <h3 class="text-xl font-bold text-gray-700 mb-3 text-center">🏆 Classement du jeu</h3>
            <div id="leaderboard-list" class="bg-white rounded-2xl shadow overflow-hidden"></div>
        </div>

    </div>
</div>

<script>
const POLL_INTERVAL = 2500;
let lastQuestionId = null;
let lastQuestionStatus = null;
let lastGameStatus = null;
let hasAnswered = false;

const csrfToken = '{{ csrf_token() }}';
const stateUrl  = '{{ route("preference.state") }}';
const submitUrl = '{{ route("preference.submit") }}';

async function fetchState() {
    try {
        const res = await fetch(stateUrl, { headers: { 'Accept': 'application/json' } });
        return await res.json();
    } catch(e) { return null; }
}

async function submitAnswer(answer) {
    const res = await fetch(submitUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ answer }),
    });
    return await res.json();
}

function initials(name) {
    return name.split(' ').map(p => p[0]).join('').toUpperCase().slice(0, 2);
}

function renderWaiting(state) {
    return `
        <div class="p-8 text-center">
            <div class="text-6xl mb-4">😴</div>
            <h2 class="text-2xl font-bold text-gray-700 mb-2">En attente...</h2>
            <p class="text-gray-500">Le jeu n'a pas encore commencé.</p>
        </div>`;
}

function renderGameWaiting(state) {
    const badge = state.is_eliminatory_phase
        ? `<span class="inline-block bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full mb-4">⚡ Phase éliminatoire en cours</span>`
        : '';
    const eliminated = state.is_eliminated
        ? `<div class="eliminated-banner text-white rounded-xl p-4 mb-4">
               <p class="font-bold text-lg">❌ Vous êtes éliminé</p>
               <p class="text-sm opacity-90">Vous ne pouvez plus répondre jusqu'à la fin de la phase éliminatoire.</p>
           </div>`
        : '';
    return `
        <div class="bg-gradient-to-r from-pink-500 to-purple-600 p-4 text-white text-center">
            <h2 class="text-xl font-bold">${state.game_title}</h2>
            <p class="text-sm opacity-80">Mes points : <strong>${state.my_points}</strong></p>
        </div>
        <div class="p-8 text-center">
            ${badge}
            ${eliminated}
            <div class="text-5xl mb-4">⏳</div>
            <p class="text-xl text-gray-600">En attente de la prochaine question...</p>
        </div>`;
}

function renderQuestion(state) {
    const badge = state.is_eliminatory
        ? `<div class="bg-red-50 border border-red-200 rounded-xl p-3 mb-5 text-center">
               <span class="text-red-700 font-bold text-sm">⚡ Question éliminatoire — les perdants sont éliminés !</span>
           </div>`
        : '';

    const eliminated = state.is_eliminated
        ? `<div class="eliminated-banner text-white rounded-xl p-4 mb-4 text-center">
               <p class="font-bold">❌ Vous êtes éliminé — vous ne pouvez pas répondre.</p>
           </div>`
        : '';

    const optionAClass = state.has_answered && state.my_answer === 'a' ? 'selected-a' : '';
    const optionBClass = state.has_answered && state.my_answer === 'b' ? 'selected-b' : '';
    const disabledAttr = (state.has_answered || state.is_eliminated) ? 'disabled' : '';
    const disabledClass = (state.has_answered || state.is_eliminated) ? 'disabled' : '';

    const answeredMsg = state.has_answered
        ? `<p class="text-center text-green-600 font-medium mt-4">✅ Réponse enregistrée ! En attente de la révélation...</p>`
        : '';

    return `
        <div class="bg-gradient-to-r from-pink-500 to-purple-600 p-4 text-white text-center">
            <h2 class="text-xl font-bold">${state.game_title}</h2>
            <p class="text-sm opacity-80">Mes points : <strong>${state.my_points}</strong></p>
        </div>
        <div class="p-8 slide-in">
            ${badge}
            ${eliminated}
            <h3 class="text-2xl font-bold text-gray-800 text-center mb-8">${state.question_text}</h3>
            <div class="grid grid-cols-2 gap-4">
                <button onclick="answer('a')" class="option-btn ${optionAClass} ${disabledClass} border-2 border-blue-300 rounded-2xl p-6 text-center text-lg font-semibold text-gray-800 hover:border-blue-500 pulse-ring" ${disabledAttr}>
                    <div class="text-3xl mb-2">💙</div>
                    ${state.option_a}
                </button>
                <button onclick="answer('b')" class="option-btn ${optionBClass} ${disabledClass} border-2 border-pink-300 rounded-2xl p-6 text-center text-lg font-semibold text-gray-800 hover:border-pink-500" ${disabledAttr}>
                    <div class="text-3xl mb-2">💗</div>
                    ${state.option_b}
                </button>
            </div>
            ${answeredMsg}
        </div>`;
}

function renderRevealing(state) {
    const correctLabel = state.correct_answer === 'a' ? state.option_a : state.option_b;
    const myResult = state.has_answered
        ? (state.my_is_correct
            ? `<div class="bg-green-50 border border-green-300 rounded-xl p-4 mb-6 text-center">
                   <p class="text-green-700 font-bold text-xl">🎉 Bonne réponse ! +${state.my_points_earned} point${state.my_points_earned > 1 ? 's' : ''}</p>
               </div>`
            : `<div class="bg-red-50 border border-red-300 rounded-xl p-4 mb-6 text-center">
                   <p class="text-red-700 font-bold text-xl">${state.is_eliminatory ? '❌ Mauvaise réponse — vous êtes éliminé !' : '❌ Mauvaise réponse'}</p>
               </div>`)
        : `<div class="bg-gray-50 border border-gray-200 rounded-xl p-3 mb-6 text-center text-gray-500">Vous n'avez pas répondu à cette question.</div>`;

    const eliminatoryResult = state.is_eliminatory && state.my_is_correct
        ? `<p class="text-center text-sm text-gray-500 mb-4">${state.wrong_count} joueur(s) éliminé(s) → vous gagnez ${state.my_points_earned} point(s) !</p>`
        : '';

    const answers = (state.answers || []).map(a => {
        const bgColor = a.is_correct ? 'bg-green-50' : 'bg-red-50';
        const textColor = a.is_correct ? 'text-green-700' : 'text-red-600';
        const icon = a.is_correct ? '✅' : '❌';
        const pts = a.points_earned > 0 ? `<span class="font-bold text-yellow-600">+${a.points_earned}pts</span>` : '';
        const meTag = a.is_me ? '<span class="text-xs bg-blue-100 text-blue-700 px-1 rounded">Moi</span>' : '';
        const optLabel = a.answer === 'a' ? state.option_a : state.option_b;
        return `<div class="flex items-center justify-between ${bgColor} rounded-lg px-4 py-2">
            <div class="flex items-center gap-2">
                <span class="font-bold w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center text-xs">${a.initials}</span>
                <span class="${textColor} font-medium">${a.name}</span>
                ${meTag}
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">${optLabel}</span>
                <span>${icon}</span>
                ${pts}
            </div>
        </div>`;
    }).join('');

    return `
        <div class="bg-gradient-to-r from-pink-500 to-purple-600 p-4 text-white text-center">
            <h2 class="text-xl font-bold">${state.game_title}</h2>
            <p class="text-sm opacity-80">Mes points : <strong>${state.my_points}</strong></p>
        </div>
        <div class="p-8 slide-in">
            <div class="text-center mb-6">
                <p class="text-gray-500 text-sm mb-1">La réponse du maître du jeu :</p>
                <div class="text-3xl font-bold text-purple-700">${correctLabel} ${state.correct_answer === 'a' ? '💙' : '💗'}</div>
            </div>
            ${eliminatoryResult}
            ${myResult}
            <div class="space-y-2">${answers}</div>
        </div>`;
}

function renderLeaderboard(leaderboard) {
    if (!leaderboard || leaderboard.length === 0) return '';

    const rows = leaderboard.map((p, i) => {
        const medal = i === 0 ? '🥇' : i === 1 ? '🥈' : i === 2 ? '🥉' : `#${i+1}`;
        return `<div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 last:border-0">
            <div class="flex items-center gap-3">
                <span class="text-xl">${medal}</span>
                <span class="font-bold w-8 h-8 rounded-full bg-gradient-to-br from-pink-400 to-purple-500 text-white flex items-center justify-center text-xs">${p.initials}</span>
                <span class="font-medium text-gray-800">${p.name}</span>
            </div>
            <span class="font-bold text-purple-700 text-lg">${p.points} pts</span>
        </div>`;
    }).join('');

    return rows;
}

async function answer(choice) {
    if (hasAnswered) return;
    hasAnswered = true;

    const result = await submitAnswer(choice);
    if (result.error) {
        hasAnswered = false;
        alert(result.error);
        return;
    }
    // Update UI immediately
    poll();
}

async function poll() {
    const state = await fetchState();
    if (!state) return;

    const content = document.getElementById('game-content');
    const lbSection = document.getElementById('leaderboard-section');
    const lbList    = document.getElementById('leaderboard-list');

    if (state.status === 'waiting') {
        content.innerHTML = renderWaiting(state);
        lbSection.classList.add('hidden');
        lastQuestionId = null;
        lastQuestionStatus = null;
        lastGameStatus = 'waiting';
        return;
    }

    // Detect new question → reset hasAnswered
    if (state.question_id && state.question_id !== lastQuestionId) {
        hasAnswered = state.has_answered || false;
        lastQuestionId = state.question_id;
    }

    if (state.has_answered) hasAnswered = true;

    const qs = state.question_status;

    if (!qs || qs === 'waiting') {
        content.innerHTML = renderGameWaiting(state);
    } else if (qs === 'active') {
        content.innerHTML = renderQuestion(state);
    } else if (qs === 'revealing') {
        content.innerHTML = renderRevealing(state);
    } else {
        content.innerHTML = renderGameWaiting(state);
    }

    // Leaderboard
    if (state.leaderboard && state.leaderboard.length > 0) {
        lbList.innerHTML = renderLeaderboard(state.leaderboard);
        lbSection.classList.remove('hidden');
    } else {
        lbSection.classList.add('hidden');
    }

    lastQuestionStatus = qs;
    lastGameStatus = state.status;
}

// Initial render then start polling
poll();
setInterval(poll, POLL_INTERVAL);
</script>
@endsection
