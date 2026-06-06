@extends('layouts.app')

@section('title', 'Modifier mon profil')
@section('body-class', 'bg-gray-100')

@section('content')
    <div class="max-w-2xl mx-auto mt-8 bg-white p-8 rounded-lg shadow-lg">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Edit Profile</h2>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
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
                <label class="block text-gray-700 font-bold mb-2">Avatar Background Color</label>
                @if(!$user->profile_picture)
                    <div class="mb-3 flex items-center gap-3">
                        <div class="w-14 h-14 rounded-full flex items-center justify-center text-white text-xl font-bold" id="avatar-preview" style="background-color: {{ old('avatar_color', $user->avatar_color ?? '#6366f1') }}">
                            {{ $user->initials }}
                        </div>
                        <span class="text-gray-500 text-sm">Preview</span>
                    </div>
                @endif
                <div class="flex flex-wrap gap-3">
                    @foreach(['#6366f1','#8b5cf6','#ec4899','#f43f5e','#f97316','#f59e0b','#22c55e','#14b8a6','#06b6d4','#3b82f6'] as $color)
                        <label class="cursor-pointer">
                            <input type="radio" name="avatar_color" value="{{ $color }}" class="sr-only avatar-color-radio"
                                {{ old('avatar_color', $user->avatar_color ?? '#6366f1') === $color ? 'checked' : '' }}>
                            <span class="block w-9 h-9 rounded-full border-4 transition-all"
                                style="background-color: {{ $color }}; border-color: {{ old('avatar_color', $user->avatar_color ?? '#6366f1') === $color ? '#1e293b' : 'transparent' }}"
                                data-color="{{ $color }}"></span>
                        </label>
                    @endforeach
                </div>
                <p class="text-gray-500 text-sm mt-2">Used when no profile picture is set.</p>
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

            <div class="mb-6">
                <label for="email" class="block text-gray-700 font-bold mb-2">Email (Read-only)</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ $user->email }}" 
                    disabled
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100"
                >
            </div>

            <div class="flex gap-4">
                <button 
                    type="submit" 
                    class="bg-blue-500 text-white font-bold py-2 px-6 rounded-lg hover:bg-blue-600 transition"
                >
                    Save Changes
                </button>
                <a href="{{ route('profile.show') }}" class="bg-gray-500 text-white font-bold py-2 px-6 rounded-lg hover:bg-gray-600 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.avatar-color-radio').forEach(function(radio) {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.avatar-color-radio + span').forEach(function(span) {
                span.style.borderColor = 'transparent';
            });
            var swatch = this.nextElementSibling;
            swatch.style.borderColor = '#1e293b';
            var preview = document.getElementById('avatar-preview');
            if (preview) preview.style.backgroundColor = this.value;
        });
    });
</script>
@endpush
