<nav class="bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <a href="{{ auth()->check() ? route('dashboard') : route('welcome') }}" class="text-2xl font-bold text-blue-600">
                    {{ config('app.name', 'Télé Lili') }}
                </a>
            </div>
            <div class="flex items-center space-x-4">
                @auth
                    <span class="text-gray-700">{{ auth()->user()->name }}</span>
                    <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-900 font-bold">🏠 Menu</a>
                    <a href="{{ route('quiz.show') }}" class="text-green-600 hover:text-green-900 font-medium">Quiz</a>
                    <a href="{{ route('preference.show') }}" class="text-pink-600 hover:text-pink-900 font-medium">Tu préfères ?</a>
                    <a href="{{ route('scoreboard.index') }}" class="text-yellow-600 hover:text-yellow-900 font-medium">🏆 Scores</a>
                    <a href="{{ route('profile.show') }}" class="text-blue-600 hover:text-blue-900 font-medium">Mon Profil</a>
                    @if(auth()->user()->hasRole('admin'))
                        <a href="{{ route('admin.users') }}" class="text-purple-600 hover:text-purple-900 font-medium">Admin</a>
                        <a href="{{ route('admin.quizzes.index') }}" class="text-orange-600 hover:text-orange-900 font-medium">Gérer Quiz</a>
                        <a href="{{ route('admin.preference.index') }}" class="text-pink-600 hover:text-pink-900 font-medium">Gérer Préférences</a>
                        <a href="{{ route('admin.scoreboard.index') }}" class="text-teal-600 hover:text-teal-900 font-medium">Gérer Scores</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                            Déconnexion
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 font-medium">Connexion</a>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">S'inscrire</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
