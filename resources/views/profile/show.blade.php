@extends('layouts.app')

@section('title', 'Mon Profil')
@section('body-class', 'bg-gray-100')

@section('content')
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
@endsection
