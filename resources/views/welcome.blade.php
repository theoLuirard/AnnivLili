@extends('layouts.app')

@section('title', 'Accueil')
@section('body-class', 'bg-gradient-to-br from-blue-500 to-purple-600 min-h-screen')

@section('content')
    <div class="min-h-screen flex items-center justify-center">
        <div class="text-center text-white">
            <h1 class="text-6xl font-bold mb-4">Bienvenue !</h1>
            <p class="text-2xl mb-8">Connectez-vous ou inscrivez-vous pour continuer</p>
            <div class="space-x-4">
                <a href="{{ route('login') }}" class="bg-white text-blue-500 px-8 py-3 rounded-lg font-bold hover:bg-gray-100 transition inline-block">
                    Connexion
                </a>
                <a href="{{ route('register') }}" class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg font-bold hover:bg-white hover:text-blue-500 transition inline-block">
                    S'inscrire
                </a>
            </div>
        </div>
    </div>
@endsection
