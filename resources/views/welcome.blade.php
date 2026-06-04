<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-500 to-purple-600 min-h-screen">
    <nav class="bg-white shadow-lg">
        <div class="max-w-4xl mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">{{ env('APP_NAME') }}</h1>
            <div class="space-x-4">
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-800">Login</a>
                <a href="{{ route('register') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">Register</a>
            </div>
        </div>
    </nav>

    <div class="min-h-screen flex items-center justify-center">
        <div class="text-center text-white">
            <h1 class="text-6xl font-bold mb-4">Welcome!</h1>
            <p class="text-2xl mb-8">Please login or register to continue</p>
            <div class="space-x-4">
                <a href="{{ route('login') }}" class="bg-white text-blue-500 px-8 py-3 rounded-lg font-bold hover:bg-gray-100 transition inline-block">
                    Login
                </a>
                <a href="{{ route('register') }}" class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg font-bold hover:bg-white hover:text-blue-500 transition inline-block">
                    Register
                </a>
            </div>
        </div>
    </div>
</body>
</html>
