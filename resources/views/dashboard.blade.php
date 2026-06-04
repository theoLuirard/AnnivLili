<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-500 to-purple-600 min-h-screen">
    <nav class="bg-white shadow-lg">
        <div class="max-w-4xl mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">{{ env('APP_NAME') }}</h1>
            <div>
                <span class="text-gray-600 mr-4">Welcome, {{ auth()->user()->name }}!</span>
                <a href="{{ route('quiz.show') }}" class="text-green-500 hover:underline mr-4">Quiz</a>
                <a href="{{ route('profile.show') }}" class="text-blue-500 hover:underline mr-4">My Profile</a>
                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.users') }}" class="text-purple-500 hover:underline mr-4">Admin Panel</a>
                    <a href="{{ route('admin.quizzes.index') }}" class="text-orange-500 hover:underline mr-4">Manage Quiz</a>
                @endif
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-12 rounded-lg shadow-2xl text-center max-w-2xl">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Welcome, {{ auth()->user()->name }}! 👋</h2>
            <p class="text-xl text-gray-600 mb-8">You have successfully logged in to the application.</p>
            
            <div class="bg-blue-100 border-l-4 border-blue-500 p-6 text-left mb-8 rounded">
                <p class="text-gray-700"><strong>Email:</strong> {{ auth()->user()->email }}</p>
                <p class="text-gray-700 mt-2"><strong>Account Created:</strong> {{ auth()->user()->created_at->format('F d, Y') }}</p>
                @if(auth()->user()->hasRole('admin'))
                    <p class="text-gray-700 mt-2"><strong>Role:</strong> <span class="bg-purple-200 text-purple-800 px-2 py-1 rounded">Admin</span></p>
                @endif
            </div>

            <p class="text-gray-500 text-sm">This is your protected dashboard. Only authenticated users can see this page.</p>
        </div>
    </div>
</body>
</html>
