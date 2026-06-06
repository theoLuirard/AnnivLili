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
        animation: pulseRingA 2s infinite;
    }
    @keyframes pulseRingA {
        0%, 100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); }
        50% { box-shadow: 0 0 0 12px rgba(59, 130, 246, 0); }
    }

    .pulse-ring-b {
        animation: pulseRingB 2s infinite 1s;
    }
    @keyframes pulseRingB {
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

    /* ===== PODIUM STYLES ===== */
    .podium-screen {
        background: linear-gradient(135deg, #1a0533 0%, #2d1060 40%, #4a1a8a 100%);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        padding: 2rem 1rem;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        z-index: 1000;
        overflow-y: auto;
    }

    .podium-title {
        font-size: 2.5rem;
        font-weight: 900;
        color: #ffd700;
        text-align: center;
        margin-bottom: 0.5rem;
        animation: titleDrop 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        opacity: 0;
        text-shadow: 0 0 20px rgba(255,215,0,0.6), 0 2px 4px rgba(0,0,0,0.5);
    }
    @keyframes titleDrop {
        from { transform: translateY(-60px) scale(0.5); opacity: 0; }
        to   { transform: translateY(0) scale(1); opacity: 1; }
    }

    .podium-subtitle {
        color: rgba(255,255,255,0.7);
        font-size: 1rem;
        margin-bottom: 2.5rem;
        animation: fadeInUp 0.6s 0.4s ease forwards;
        opacity: 0;
        text-align: center;
    }
    @keyframes fadeInUp {
        from { transform: translateY(20px); opacity: 0; }
        to   { transform: translateY(0); opacity: 1; }
    }

    .podium-stage {
        display: flex;
        align-items: flex-end;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        max-width: 400px;
        margin-bottom: 2rem;
    }

    .podium-column {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        opacity: 0;
    }

    .podium-column.rank-3 { animation: podiumRise 0.7s 0.8s cubic-bezier(0.34,1.56,0.64,1) forwards; }
    .podium-column.rank-1 { animation: podiumRise 0.7s 1.1s cubic-bezier(0.34,1.56,0.64,1) forwards; }
    .podium-column.rank-2 { animation: podiumRise 0.7s 1.4s cubic-bezier(0.34,1.56,0.64,1) forwards; }
    @keyframes podiumRise { from { transform: translateY(40px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

    .podium-avatar {
        width: 52px; height: 52px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 900; font-size: 1rem; color: white;
        margin-bottom: 0.4rem; position: relative;
    }
    .podium-column.rank-1 .podium-avatar { width: 68px; height: 68px; font-size: 1.2rem; }
    .podium-avatar-1 { background: linear-gradient(135deg,#ffd700,#ff8c00); box-shadow: 0 0 20px rgba(255,215,0,0.7); }
    .podium-avatar-2 { background: linear-gradient(135deg,#c0c0c0,#808080); box-shadow: 0 0 12px rgba(192,192,192,0.5); }
    .podium-avatar-3 { background: linear-gradient(135deg,#cd7f32,#8b4513); box-shadow: 0 0 10px rgba(205,127,50,0.5); }
    .podium-avatar-other { background: linear-gradient(135deg,#6366f1,#8b5cf6); }

    .podium-medal { font-size: 1.6rem; margin-bottom: 0.25rem; }
    .podium-column.rank-1 .podium-medal { font-size: 2.2rem; }

    .podium-name {
        font-size: 0.7rem; font-weight: 700; color: white;
        text-align: center; margin-bottom: 0.3rem;
        max-width: 80px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .podium-column.rank-1 .podium-name { font-size: 0.85rem; max-width: 100px; }

    .podium-points { font-size: 0.65rem; font-weight: 600; color: rgba(255,255,255,0.8); margin-bottom: 0.35rem; }

    .podium-bar {
        width: 100%; border-radius: 8px 8px 0 0;
        display: flex; align-items: center; justify-content: center;
        font-weight: 900; color: rgba(255,255,255,0.9); font-size: 1.1rem;
        transform-origin: bottom; transform: scaleY(0);
        animation: barGrow 0.7s ease forwards;
    }
    .podium-column.rank-1 .podium-bar { height: 110px; background: linear-gradient(180deg,#ffd700,#f59e0b); animation-delay: 1.1s; }
    .podium-column.rank-2 .podium-bar { height: 80px;  background: linear-gradient(180deg,#d1d5db,#9ca3af); animation-delay: 1.4s; }
    .podium-column.rank-3 .podium-bar { height: 56px;  background: linear-gradient(180deg,#d97706,#b45309); animation-delay: 0.8s; }
    @keyframes barGrow { from { transform: scaleY(0); } to { transform: scaleY(1); } }

    .podium-others {
        width: 100%; max-width: 380px;
        display: flex; flex-direction: column; gap: 0.5rem;
        opacity: 0;
        animation: fadeInUp 0.6s 2s ease forwards;
    }
    .podium-other-row {
        display: flex; align-items: center; justify-content: space-between;
        background: rgba(255,255,255,0.1);
        border-radius: 12px; padding: 0.6rem 1rem;
        backdrop-filter: blur(4px);
    }

    .confetti-container {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        pointer-events: none; overflow: hidden; z-index: 999;
    }
    .confetti-piece {
        position: absolute; width: 10px; height: 10px; top: -12px;
        animation: confettiFall linear forwards;
        border-radius: 2px;
    }
    @keyframes confettiFall {
        0%   { transform: translateY(0) rotateZ(0deg); opacity: 1; }
        100% { transform: translateY(105vh) rotateZ(720deg); opacity: 0.2; }
    }

    .winner-crown {
        position: absolute; top: -18px; left: 50%; transform: translateX(-50%);
        font-size: 1.3rem;
        animation: crownBounce 0.9s 2s ease infinite alternate;
    }
    @keyframes crownBounce {
        from { transform: translateX(-50%) translateY(0); }
        to   { transform: translateX(-50%) translateY(-5px); }
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
let lastHasAnswered = false;
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
                <button onclick="answer('b')" class="option-btn ${optionBClass} ${disabledClass} border-2 border-pink-300 rounded-2xl p-6 text-center text-lg font-semibold text-gray-800 hover:border-pink-500 pulse-ring-b" ${disabledAttr}>
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
                ${a.avatar
                    ? `<img src="${a.avatar}" class="w-7 h-7 rounded-full object-cover shrink-0" />`
                    : `<span class="font-bold w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center text-xs shrink-0">${a.initials}</span>`
                }
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
                ${p.avatar
                    ? `<img src="${p.avatar}" class="w-8 h-8 rounded-full object-cover shrink-0" />`
                    : `<span class="font-bold w-8 h-8 rounded-full bg-gradient-to-br from-pink-400 to-purple-500 text-white flex items-center justify-center text-xs shrink-0">${p.initials}</span>`
                }
                <span class="font-medium text-gray-800">${p.name}</span>
            </div>
            <span class="font-bold text-purple-700 text-lg">${p.points} pts</span>
        </div>`;
    }).join('');

    return rows;
}

function spawnConfetti() {
    const container = document.createElement('div');
    container.className = 'confetti-container';
    document.body.appendChild(container);

    const colors = ['#ffd700','#ff6b9d','#c084fc','#34d399','#60a5fa','#f97316','#fff'];
    const shapes = ['2px', '50%'];

    for (let i = 0; i < 80; i++) {
        const piece = document.createElement('div');
        piece.className = 'confetti-piece';
        const color = colors[Math.floor(Math.random() * colors.length)];
        const size  = 6 + Math.random() * 8;
        piece.style.cssText = `
            left: ${Math.random() * 100}%;
            width: ${size}px;
            height: ${size}px;
            background: ${color};
            border-radius: ${shapes[Math.floor(Math.random() * shapes.length)]};
            animation-duration: ${2 + Math.random() * 3}s;
            animation-delay: ${Math.random() * 2}s;
        `;
        container.appendChild(piece);
    }

    // Remove container after animation
    setTimeout(() => container.remove(), 6000);
}

function renderPodium(state) {
    const lb = state.leaderboard || [];
    const top3 = lb.slice(0, 3);
    const others = lb.slice(3);

    // Order for podium display: 3rd, 1st, 2nd
    const podiumOrder = [];
    if (top3[2]) podiumOrder.push({ ...top3[2], rank: 3, rankClass: 'rank-3', avatarClass: 'podium-avatar-3', medal: '🥉', barLabel: '3' });
    if (top3[0]) podiumOrder.push({ ...top3[0], rank: 1, rankClass: 'rank-1', avatarClass: 'podium-avatar-1', medal: '🥇', barLabel: '1' });
    if (top3[1]) podiumOrder.push({ ...top3[1], rank: 2, rankClass: 'rank-2', avatarClass: 'podium-avatar-2', medal: '🥈', barLabel: '2' });

    const podiumCols = podiumOrder.map(p => {
        const crown = p.rank === 1 ? `<span class="winner-crown">👑</span>` : '';
        return `
            <div class="podium-column ${p.rankClass}">
                <div class="podium-medal">${p.medal}</div>
                <div class="podium-avatar ${p.avatarClass}">
                    ${crown}
                    ${p.avatar
                        ? `<img src="${p.avatar}" class="w-full h-full rounded-full object-cover" />`
                        : p.initials
                    }
                </div>
                <div class="podium-name">${p.name}</div>
                <div class="podium-points">${p.points} pts</div>
                <div class="podium-bar">${p.barLabel}</div>
            </div>`;
    }).join('');

    const othersHtml = others.length > 0
        ? `<div class="podium-others">
            <p style="color:rgba(255,255,255,0.5);font-size:0.75rem;text-align:center;margin-bottom:0.5rem;">Autres joueurs</p>
            ${others.map((p, i) => `
                <div class="podium-other-row">
                    <div style="display:flex;align-items:center;gap:0.6rem;">
                        <span style="color:rgba(255,255,255,0.6);font-size:0.8rem;font-weight:700;width:1.5rem;">#${i+4}</span>
                        ${p.avatar
                            ? `<img src="${p.avatar}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;flex-shrink:0;" />`
                            : `<span style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.7rem;color:white;flex-shrink:0;">${p.initials}</span>`
                        }
                        <span style="color:white;font-weight:600;font-size:0.85rem;">${p.name}</span>
                    </div>
                    <span style="color:#c084fc;font-weight:700;">${p.points} pts</span>
                </div>`).join('')}
           </div>`
        : '';

    return `
        <div class="podium-screen">
            <h1 class="podium-title">🏆 Classement Final !</h1>
            <p class="podium-subtitle">${state.game_title} — merci d'avoir joué !</p>
            <div class="podium-stage">${podiumCols}</div>
            ${othersHtml}
        </div>
        <div class="confetti-trigger" style="display:none;"></div>`;
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

    // Finished game → show podium animation
    if (state.status === 'finished') {
        if (lastGameStatus !== 'finished') {
            content.innerHTML = renderPodium(state);
            lbSection.classList.add('hidden');
            // Trigger confetti after short delay
            setTimeout(spawnConfetti, 800);
            setTimeout(spawnConfetti, 1800);
        }
        lastGameStatus = 'finished';
        return;
    }

    if (state.status === 'waiting') {
        content.innerHTML = renderWaiting(state);
        lbSection.classList.add('hidden');
        lastQuestionId = null;
        lastQuestionStatus = null;
        lastGameStatus = 'waiting';
        return;
    }

    const qs = state.question_status;
    const questionChanged = state.question_id !== lastQuestionId;
    const statusChanged   = qs !== lastQuestionStatus;
    const answeredChanged = state.has_answered !== lastHasAnswered;

    // Detect new question → reset hasAnswered
    if (questionChanged && state.question_id) {
        hasAnswered = state.has_answered || false;
        lastQuestionId = state.question_id;
    }

    if (state.has_answered) hasAnswered = true;

    if (!qs || qs === 'waiting') {
        if (statusChanged || questionChanged) {
            content.innerHTML = renderGameWaiting(state);
        }
    } else if (qs === 'active') {
        if (questionChanged || statusChanged || answeredChanged) {
            content.innerHTML = renderQuestion(state);
        }
    } else if (qs === 'revealing') {
        if (questionChanged || statusChanged) {
            content.innerHTML = renderRevealing(state);
        }
    } else {
        if (statusChanged || questionChanged) {
            content.innerHTML = renderGameWaiting(state);
        }
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
    lastHasAnswered = state.has_answered || false;
}

// Initial render then start polling
poll();
setInterval(poll, POLL_INTERVAL);
</script>
@endsection
