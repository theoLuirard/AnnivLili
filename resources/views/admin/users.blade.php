<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
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

    <div class="max-w-7xl mx-auto mt-8 bg-white p-8 rounded-lg shadow-lg">
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <h2 class="text-3xl font-bold text-gray-800 mb-6">Manage Users</h2>

        <div class="mb-6">
            <form method="GET" action="{{ route('admin.users') }}" class="flex gap-4">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search by name, email, or nickname..." 
                    value="{{ request('search') }}"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.users') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-300 px-6 py-3 text-left">Profile</th>
                        <th class="border border-gray-300 px-6 py-3 text-left">Name</th>
                        <th class="border border-gray-300 px-6 py-3 text-left">Email</th>
                        <th class="border border-gray-300 px-6 py-3 text-left">Nickname</th>
                        <th class="border border-gray-300 px-6 py-3 text-left">Role</th>
                        <th class="border border-gray-300 px-6 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-300 px-6 py-3">
                                @if($user->profile_picture)
                                    <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile" class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 text-xs">
                                        N/A
                                    </div>
                                @endif
                            </td>
                            <td class="border border-gray-300 px-6 py-3">{{ $user->name }}</td>
                            <td class="border border-gray-300 px-6 py-3">{{ $user->email }}</td>
                            <td class="border border-gray-300 px-6 py-3">{{ $user->nickname ?? '-' }}</td>
                            <td class="border border-gray-300 px-6 py-3">
                                @if($user->hasRole('admin'))
                                    <span class="inline-block bg-purple-100 text-purple-800 font-semibold px-2 py-1 rounded-full text-xs">Admin</span>
                                @else
                                    <span class="inline-block bg-gray-100 text-gray-700 font-semibold px-2 py-1 rounded-full text-xs">User</span>
                                @endif
                            </td>
                            <td class="border border-gray-300 px-6 py-3">
                                <a href="{{ route('admin.user.show', $user->id) }}" class="text-blue-500 hover:underline mr-4">
                                    View/Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="border border-gray-300 px-6 py-3 text-center text-gray-500">
                                No users found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>
</body>
</html>
