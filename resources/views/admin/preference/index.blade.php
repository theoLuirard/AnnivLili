@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">💝 Jeux « Tu préfères »</h1>
            <a href="{{ route('admin.preference.create') }}" class="bg-pink-600 text-white px-4 py-2 rounded-lg hover:bg-pink-700">
                + Nouveau jeu
            </a>
        </div>

        @if ($message = Session::get('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">{{ $message }}</div>
        @endif
        @if ($message = Session::get('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">{{ $message }}</div>
        @endif

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase">Titre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase">Questions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase">Créé par</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($games as $game)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $game->title }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    {{ $game->status === 'draft'  ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $game->status === 'active' ? 'bg-green-100 text-green-800'  : '' }}
                                    {{ $game->status === 'closed' ? 'bg-red-100 text-red-800'     : '' }}
                                ">{{ ucfirst($game->status) }}</span>
                                @if($game->is_eliminatory_phase)
                                    <span class="ml-1 px-2 py-0.5 bg-red-200 text-red-700 text-xs rounded-full">⚡ Éliminatoire</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $game->questions->count() }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $game->creator?->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <a href="{{ route('admin.preference.manage', $game->id) }}" class="text-blue-600 hover:text-blue-900">Gérer</a>

                                @if($game->isDraft())
                                    <form action="{{ route('admin.preference.activate', $game->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900">Activer</button>
                                    </form>
                                @endif

                                @if($game->isActive())
                                    <form action="{{ route('admin.preference.close', $game->id) }}" method="POST" class="inline" onclick="return confirm('Terminer le jeu ?')">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900">Terminer</button>
                                    </form>
                                @endif

                                @if($game->isClosed() || $game->isDraft())
                                    <form action="{{ route('admin.preference.destroy', $game->id) }}" method="POST" class="inline" onclick="return confirm('Supprimer ce jeu ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Aucun jeu créé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
