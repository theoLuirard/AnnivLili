@extends('layouts.app')

@push('styles')
<style>
    .question-block { border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; margin-bottom: 16px; background: #fafafa; }
    .question-block .block-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
    .remove-btn { color: #ef4444; cursor: pointer; font-size: 0.875rem; }
    .remove-btn:hover { color: #b91c1c; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Nouveau jeu « Tu préfères »</h1>
            <a href="{{ route('admin.preference.index') }}" class="text-gray-600 hover:text-gray-900">← Retour</a>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-300 rounded text-red-700">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.preference.store') }}" method="POST">
            @csrf

            <div class="bg-white rounded-xl shadow p-6 mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Titre du jeu *</label>
                <input type="text" name="title" value="{{ old('title') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-pink-400"
                    placeholder="Ex: Préférences de Lili" required>
            </div>

            <div id="questions-container">
                {{-- Question blocks injected by JS --}}
            </div>

            <button type="button" onclick="addQuestion()"
                class="w-full border-2 border-dashed border-pink-300 text-pink-600 rounded-xl py-3 font-medium hover:bg-pink-50 transition mb-6">
                + Ajouter une question
            </button>

            <button type="submit"
                class="w-full bg-pink-600 text-white py-3 rounded-xl font-bold text-lg hover:bg-pink-700 transition">
                Créer le jeu
            </button>
        </form>
    </div>
</div>

<script>
let questionCount = 0;

function addQuestion() {
    const index = questionCount++;
    const container = document.getElementById('questions-container');
    const div = document.createElement('div');
    div.className = 'question-block';
    div.id = `q-block-${index}`;
    div.innerHTML = `
        <div class="block-header">
            <span class="font-semibold text-gray-700">Question ${index + 1}</span>
            <span class="remove-btn" onclick="removeQuestion(${index})">✕ Supprimer</span>
        </div>
        <div class="mb-3">
            <label class="block text-sm text-gray-600 mb-1">Question *</label>
            <input type="text" name="questions[${index}][question_text]"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-300"
                placeholder="Ex: Plage ou montagne ?" required>
        </div>
        <div class="grid grid-cols-2 gap-4 mb-3">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Option A 💙 *</label>
                <input type="text" name="questions[${index}][option_a]"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300"
                    placeholder="Ex: Plage" required>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Option B 💗 *</label>
                <input type="text" name="questions[${index}][option_b]"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-300"
                    placeholder="Ex: Montagne" required>
            </div>
        </div>
        <label class="flex items-center gap-2 cursor-pointer select-none">
            <input type="checkbox" name="questions[${index}][is_eliminatory]" value="1"
                class="w-4 h-4 accent-red-500">
            <span class="text-sm text-gray-700">⚡ Question éliminatoire <span class="text-gray-400 text-xs">(les perdants sont éliminés pour la suite de la phase)</span></span>
        </label>
    `;
    container.appendChild(div);
}

function removeQuestion(index) {
    const el = document.getElementById(`q-block-${index}`);
    if (el) el.remove();
}

// Start with 1 question
addQuestion();
</script>
@endsection
