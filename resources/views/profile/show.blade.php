<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-4xl mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">My App</h1>
            <div>
                <span class="text-gray-600 mr-4">{{ auth()->user()->name }}</span>
                <a href="{{ route('dashboard') }}" class="text-blue-500 hover:underline mr-4">Dashboard</a>
                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.users') }}" class="text-purple-500 hover:underline mr-4">Admin Panel</a>
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

    <div class="max-w-2xl mx-auto mt-8 bg-white p-8 rounded-lg shadow-lg">
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <h2 class="text-3xl font-bold text-gray-800 mb-6">My Profile</h2>

        <div class="mb-8">
            <div class="flex items-center">
                @if($user->profile_picture)
                    <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile Picture" class="w-32 h-32 rounded-full mr-6 object-cover">
                @else
                    <div class="w-32 h-32 rounded-full mr-6 flex items-center justify-center text-white text-3xl font-bold" style="background-color: {{ $user->avatar_color ?? '#6366f1' }}">
                        {{ $user->initials }}
                    </div>
                @endif
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h3>
                    <p class="text-gray-600">{{ $user->email }}</p>
                    @if($user->nickname)
                        <p class="text-gray-500">Nickname: {{ $user->nickname }}</p>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="mt-4 inline-block bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 transition">
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 p-6 rounded-lg">
            <h4 class="text-lg font-bold text-gray-800 mb-4">Account Information</h4>
            <p class="text-gray-600 mb-2"><strong>Name:</strong> {{ $user->name }}</p>
            <p class="text-gray-600 mb-2"><strong>Email:</strong> {{ $user->email }}</p>
            <p class="text-gray-600 mb-2"><strong>Nickname:</strong> {{ $user->nickname ?? 'Not set' }}</p>
            <p class="text-gray-600"><strong>Member since:</strong> {{ $user->created_at->format('F d, Y') }}</p>
        </div>
    </div>
</body>
</html>
