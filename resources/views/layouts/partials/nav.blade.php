<nav class="bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            {{-- Logo --}}
            <a href="{{ auth()->check() ? route('dashboard') : route('welcome') }}" class="text-2xl font-bold text-blue-600 shrink-0">
                {{ config('app.name', 'Télé Lili') }}
            </a>

            {{-- Desktop links --}}
            <div class="hidden md:flex items-center space-x-4">
                @auth
                    <span class="text-gray-700 text-sm">{{ auth()->user()->name }}</span>
                    <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-900 font-bold text-sm">🏠 Menu</a>
                    <a href="{{ route('quiz.show') }}" class="text-green-600 hover:text-green-900 font-medium text-sm">Quiz</a>
                    <a href="{{ route('preference.show') }}" class="text-pink-600 hover:text-pink-900 font-medium text-sm">Tu préfères ?</a>
                    <a href="{{ route('scoreboard.index') }}" class="text-yellow-600 hover:text-yellow-900 font-medium text-sm">🏆 Scores</a>
                    <a href="{{ route('profile.show') }}" class="text-blue-600 hover:text-blue-900 font-medium text-sm">Mon Profil</a>
                    @if(auth()->user()->hasRole('admin'))
                        <a href="{{ route('admin.users') }}" class="text-purple-600 hover:text-purple-900 font-medium text-sm">Admin</a>
                        <a href="{{ route('admin.quizzes.index') }}" class="text-orange-600 hover:text-orange-900 font-medium text-sm">Gérer Quiz</a>
                        <a href="{{ route('admin.preference.index') }}" class="text-pink-600 hover:text-pink-900 font-medium text-sm">Gérer Préf.</a>
                        <a href="{{ route('admin.scoreboard.index') }}" class="text-teal-600 hover:text-teal-900 font-medium text-sm">Gérer Scores</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 text-white px-3 py-1.5 rounded text-sm hover:bg-red-700 transition">
                            Déconnexion
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 font-medium text-sm">Connexion</a>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 transition">S'inscrire</a>
                @endauth
            </div>

            {{-- Hamburger button --}}
            <button id="nav-toggle" class="md:hidden flex flex-col justify-center items-center w-10 h-10 rounded focus:outline-none" aria-label="Menu">
                <span class="block w-6 h-0.5 bg-gray-700 mb-1 transition-all" id="bar1"></span>
                <span class="block w-6 h-0.5 bg-gray-700 mb-1 transition-all" id="bar2"></span>
                <span class="block w-6 h-0.5 bg-gray-700 transition-all" id="bar3"></span>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div id="mobile-menu" class="md:hidden hidden border-t border-gray-200 bg-white">
        <div class="flex flex-col px-4 py-3 space-y-2">
            @auth
                <span class="text-gray-500 text-sm font-medium pb-1 border-b border-gray-100">👤 {{ auth()->user()->name }}</span>
                <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-900 font-bold py-1">🏠 Menu</a>
                <a href="{{ route('quiz.show') }}" class="text-green-600 hover:text-green-900 font-medium py-1">Quiz</a>
                <a href="{{ route('preference.show') }}" class="text-pink-600 hover:text-pink-900 font-medium py-1">Tu préfères ?</a>
                <a href="{{ route('scoreboard.index') }}" class="text-yellow-600 hover:text-yellow-900 font-medium py-1">🏆 Scores</a>
                <a href="{{ route('profile.show') }}" class="text-blue-600 hover:text-blue-900 font-medium py-1">Mon Profil</a>
                @if(auth()->user()->hasRole('admin'))
                    <div class="pt-1 border-t border-gray-100">
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Administration</p>
                        <a href="{{ route('admin.users') }}" class="block text-purple-600 hover:text-purple-900 font-medium py-1">Admin</a>
                        <a href="{{ route('admin.quizzes.index') }}" class="block text-orange-600 hover:text-orange-900 font-medium py-1">Gérer Quiz</a>
                        <a href="{{ route('admin.preference.index') }}" class="block text-pink-600 hover:text-pink-900 font-medium py-1">Gérer Préférences</a>
                        <a href="{{ route('admin.scoreboard.index') }}" class="block text-teal-600 hover:text-teal-900 font-medium py-1">Gérer Scores</a>
                    </div>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="pt-1">
                    @csrf
                    <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                        Déconnexion
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 font-medium py-1">Connexion</a>
                <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded text-center hover:bg-blue-700 transition">S'inscrire</a>
            @endauth
        </div>
    </div>
</nav>

<script>
    const toggle = document.getElementById('nav-toggle');
    const menu = document.getElementById('mobile-menu');
    toggle.addEventListener('click', () => {
        menu.classList.toggle('hidden');
    });
</script>
