@extends('layouts.app')

@section('title', 'Menu Principal')
@section('body-class', 'bg-gradient-to-br from-blue-500 to-purple-600 min-h-screen')

@section('content')
    <div class="min-h-screen flex flex-col items-center justify-center py-12 px-4">
        <h2 class="text-5xl font-bold text-white mb-2">Bienvenue, {{ auth()->user()->name }} ! 👋</h2>
        <p class="text-xl text-blue-100 mb-12">Choisis un jeu pour commencer !</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl w-full">

            {{-- Quiz --}}
            <a href="{{ route('quiz.show') }}" class="bg-white rounded-2xl shadow-2xl p-8 text-center hover:scale-105 transition-transform duration-200 group">
                <div class="text-6xl mb-4">🎯</div>
                <h3 class="text-3xl font-bold text-green-600 group-hover:text-green-700 mb-3">Quiz</h3>
                <p class="text-gray-500">Teste tes connaissances avec des questions à choix multiples !</p>
            </a>

            {{-- Tu préfères ? --}}
            <a href="{{ route('preference.show') }}" class="bg-white rounded-2xl shadow-2xl p-8 text-center hover:scale-105 transition-transform duration-200 group">
                <div class="text-6xl mb-4">💖</div>
                <h3 class="text-3xl font-bold text-pink-600 group-hover:text-pink-700 mb-3">Tu préfères ?</h3>
                <p class="text-gray-500">Réponds aux dilemmes et découvre tes préférences !</p>
            </a>

            {{-- Scores --}}
            <a href="{{ route('scoreboard.index') }}" class="bg-white rounded-2xl shadow-2xl p-8 text-center hover:scale-105 transition-transform duration-200 group">
                <div class="text-6xl mb-4">🏆</div>
                <h3 class="text-3xl font-bold text-yellow-500 group-hover:text-yellow-600 mb-3">Classement</h3>
                <p class="text-gray-500">Consulte le tableau des scores et les meilleurs joueurs !</p>
            </a>

            {{-- Mon Profil --}}
            <a href="{{ route('profile.show') }}" class="bg-white rounded-2xl shadow-2xl p-8 text-center hover:scale-105 transition-transform duration-200 group">
                <div class="text-6xl mb-4">👤</div>
                <h3 class="text-3xl font-bold text-blue-600 group-hover:text-blue-700 mb-3">Mon Profil</h3>
                <p class="text-gray-500">Consulte et modifie ton profil et tes informations.</p>
            </a>

        </div>

        @if(auth()->user()->hasRole('admin'))
        <div class="mt-10 bg-white bg-opacity-20 rounded-2xl p-6 max-w-4xl w-full">
            <h4 class="text-white text-xl font-bold mb-4 text-center">⚙️ Administration</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.users') }}" class="bg-purple-600 text-white rounded-xl px-4 py-3 text-center font-medium hover:bg-purple-700 transition">👥 Utilisateurs</a>
                <a href="{{ route('admin.quizzes.index') }}" class="bg-orange-500 text-white rounded-xl px-4 py-3 text-center font-medium hover:bg-orange-600 transition">📋 Quiz</a>
                <a href="{{ route('admin.preference.index') }}" class="bg-pink-500 text-white rounded-xl px-4 py-3 text-center font-medium hover:bg-pink-600 transition">💝 Préférences</a>
                <a href="{{ route('admin.scoreboard.index') }}" class="bg-teal-500 text-white rounded-xl px-4 py-3 text-center font-medium hover:bg-teal-600 transition">🏅 Scores</a>
            </div>
        </div>
        @endif
    </div>
@endsection
