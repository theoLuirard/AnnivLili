@php
    $quiz = isset($quiz) ? $quiz : null;
@endphp
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('admin.quizzes.index') }}" class="text-blue-600 hover:text-blue- Retour</a>900">
            <h1 class="text-3xl font-bold text-gray-900 mt-2">diter la Question</h1>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow rounded-lg p-6">
            <form action="{{ route('admin.quizzes.update', $quiz->id) }}" method="POST">
                @csrf

                <div class="mb-6">
                    <label for="question" class="block text-sm font-medium text-gray-700 mb-2">Question</label>
                    <textarea id="question" name="question" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $quiz->question }}</textarea>
                </div>

                <div class="mb-6">
                    <label for="correct_answer" class="block text-sm font-medium text-gray-700 mb-2">Rponse Correcte (nombre)</label>
                    <input type="number" id="correct_answer" name="correct_answer" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $quiz->correct_answer }}">
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description (optionnel)</label>
                    <textarea id="description" name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $quiz->description }}</textarea>
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        Mettre  jour
                    </button>
                    <a href="{{ route('admin.quizzes.index') }}" class="bg-gray-300 text-gray-900 px-6 py-2 rounded-lg hover:bg-gray-400">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
