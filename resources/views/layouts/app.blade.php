<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'Télé Lili'))</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
</head>
<body class="@yield('body-class', 'bg-gray-50')">
    @include('layouts.partials.nav')

    <main>
        @yield('content')
    </main>

    <footer class="bg-gray-800 text-white py-8 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Télé Lili') }}. Tous droits réservés.</p>
        </div>
    </footer>
    @stack('scripts')
</body>
</html>
