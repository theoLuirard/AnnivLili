<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Quiz App')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-blue-600">🎮 Quiz</a>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <span class="text-gray-700">{{ auth()->user()->name }}</span>
                        <a href="{{ route('quiz.show') }}" class="text-green-600 hover:text-green-900 font-medium">Quiz</a>
                        <a href="{{ route('profile.show') }}" class="text-blue-600 hover:text-blue-900 font-medium">Mon Profil</a>
                        @if(auth()->user()->hasRole('admin'))
                            <a href="{{ route('admin.users') }}" class="text-purple-600 hover:text-purple-900 font-medium">Admin</a>
                            <a href="{{ route('admin.quizzes.index') }}" class="text-orange-600 hover:text-orange-900 font-medium">Gérer Quiz</a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                                Déconnexion
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="bg-gray-800 text-white py-8 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; 2026 Quiz App. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>
