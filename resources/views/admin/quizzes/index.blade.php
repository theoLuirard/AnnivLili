@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Gestion des Questions</h1>
            <a href="{{ route('admin.quizzes.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Nouvelle Question
            </a>
        </div>

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

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">Question</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">Réponse</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">Réponses</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($quizzes as $quiz)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ substr($quiz->question, 0, 50) }}...</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">{{ $quiz->correct_answer }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    {{ $quiz->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $quiz->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $quiz->status === 'closed' ? 'bg-red-100 text-red-800' : '' }}
                                ">
                                    {{ ucfirst($quiz->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                @if($quiz->isActive()) id="response-count-{{ $quiz->id }}" @endif>
                                {{ $quiz->responses->count() }}
                                @if($quiz->isActive())
                                    <span class="ml-1 inline-block w-2 h-2 rounded-full bg-green-400 animate-pulse" title="En direct"></span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                @if ($quiz->isDraft())
                                    <form action="{{ route('admin.quizzes.activate', $quiz->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900">Activer</button>
                                    </form>
                                @endif

                                @if ($quiz->isActive())
                                    <form action="{{ route('admin.quizzes.close', $quiz->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900">Fermer</button>
                                    </form>
                                @endif

                                @if ($quiz->isClosed())
                                    <form action="{{ route('admin.quizzes.reset', $quiz->id) }}" method="POST" class="inline" onclick="return confirm('Êtes-vous sûr? Cela supprimera toutes les réponses.')">
                                        @csrf
                                        <button type="submit" class="text-yellow-600 hover:text-yellow-900">Réinitialiser</button>
                                    </form>
                                @endif

                                <a href="{{ route('admin.quizzes.edit', $quiz->id) }}" class="text-blue-600 hover:text-blue-900">Éditer</a>

                                @if (!$quiz->responses->isEmpty())
                                    <a href="{{ route('admin.quizzes.results', $quiz->id) }}" class="text-purple-600 hover:text-purple-900">Résultats</a>
                                @endif

                                <form action="{{ route('admin.quizzes.destroy', $quiz->id) }}" method="POST" class="inline" onclick="return confirm('Êtes-vous sûr ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Aucune question</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@php $activeQuiz = $quizzes->firstWhere('status', 'active'); @endphp
@if($activeQuiz)
@push('scripts')
<script>
(function () {
    const quizId = {{ $activeQuiz->id }};
    const el = document.getElementById('response-count-' + quizId);
    if (!el) return;
    setInterval(async () => {
        try {
            const res = await fetch('/admin/quizzes/' + quizId + '/live-count');
            if (!res.ok) return;
            const data = await res.json();
            const dot = el.querySelector('span');
            el.textContent = data.count + ' ';
            if (dot) el.appendChild(dot);
        } catch (e) {}
    }, 3000);
})();
</script>
@endpush
@endif
