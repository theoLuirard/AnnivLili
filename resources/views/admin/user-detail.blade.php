<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-4xl mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">My App - Admin Panel</h1>
            <div>
                <span class="text-gray-600 mr-4">{{ auth()->user()->name }}</span>
                <a href="{{ route('dashboard') }}" class="text-blue-500 hover:underline mr-4">Dashboard</a>
                <a href="{{ route('profile.show') }}" class="text-blue-500 hover:underline mr-4">My Profile</a>
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

        <h2 class="text-3xl font-bold text-gray-800 mb-6">Edit User</h2>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.user.update', $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-6">
                <label for="name" class="block text-gray-700 font-bold mb-2">Full Name</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', $user->name) }}" 
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <div class="mb-6">
                <label for="email" class="block text-gray-700 font-bold mb-2">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email', $user->email) }}" 
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <div class="mb-6">
                <label for="nickname" class="block text-gray-700 font-bold mb-2">Nickname</label>
                <input 
                    type="text" 
                    id="nickname" 
                    name="nickname" 
                    value="{{ old('nickname', $user->nickname) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <div class="mb-6">
                <label for="profile_picture" class="block text-gray-700 font-bold mb-2">Profile Picture</label>
                @if($user->profile_picture)
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile Picture" class="w-32 h-32 rounded-full object-cover">
                    </div>
                @endif
                <input 
                    type="file" 
                    id="profile_picture" 
                    name="profile_picture" 
                    accept="image/*"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                <p class="text-gray-500 text-sm mt-2">Max file size: 2MB. Allowed formats: JPEG, PNG, JPG, GIF</p>
            </div>

            <div class="flex gap-4">
                <button 
                    type="submit" 
                    class="bg-blue-500 text-white font-bold py-2 px-6 rounded-lg hover:bg-blue-600 transition"
                >
                    Save Changes
                </button>
                <a href="{{ route('admin.users') }}" class="bg-gray-500 text-white font-bold py-2 px-6 rounded-lg hover:bg-gray-600 transition">
                    Back to Users
                </a>
                <form action="{{ route('admin.user.delete', $user->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white font-bold py-2 px-6 rounded-lg hover:bg-red-600 transition">
                        Delete User
                    </button>
                </form>
            </div>
        </form>
    </div>
</body>
</html>
