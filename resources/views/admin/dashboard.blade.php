<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-indigo-800 leading-tight">
            {{ __('Administration Globale') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Stat: Utilisateurs -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-b-4 border-indigo-500">
                    <div class="p-6">
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Total Utilisateurs') }}
                        </p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['total_utilisateurs'] }}</p>
                    </div>
                </div>

                <!-- Stat: Colocations -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-b-4 border-green-500">
                    <div class="p-6">
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Colocations Actives') }}
                        </p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['total_colocations'] }}</p>
                    </div>
                </div>

                <!-- Stat: Depenses -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-b-4 border-yellow-500">
                    <div class="p-6">
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Depenses Plateforme') }}
                        </p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">
                            {{ number_format($stats['total_depenses'], 2) }} €
                        </p>
                    </div>
                </div>

                <!-- Stat: Bannis -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-b-4 border-red-500">
                    <div class="p-6">
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Bannis') }}
                        </p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['total_bannis'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Liste des utilisateurs</h3>

                    <form action="{{ route('admin.dashboard') }}" method="GET" class="mb-4 flex gap-2 flex-wrap items-end">
                        <div class="flex-1 min-w-[200px]">
                            <label for="q" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                            <input type="text" id="q" name="q" value="{{ request('q') }}"
                                placeholder="Nom ou email..."
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                            Rechercher
                        </button>
                        @if(request('q'))
                            <a href="{{ route('admin.dashboard') }}"
                                class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300">
                                Tout afficher
                            </a>
                        @endif
                    </form>

                    @if (session('success'))
                        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
                    @endif

                    @if($users->isEmpty())
                        <p class="text-gray-500">Pas d'utilisateurs.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                                        <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                        <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                        <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Reputation</th>
                                        <th class="px-4 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($users as $user)
                                        <tr>
                                            <td class="px-4 py-3 text-sm text-gray-900">{{ $user->name }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-500">{{ $user->email }}</td>
                                            <td class="px-4 py-3">
                                                @if($user->est_banni)
                                                    <span class="px-2 py-1 text-xs font-medium rounded bg-red-100 text-red-800">Banni</span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-800">Actif</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700">{{ $user->score_reputation ?? 0 }}</td>
                                            <td class="px-4 py-3 text-right">
                                                @if($user->est_banni)
                                                    <form action="{{ route('admin.users.unban', $user) }}" method="POST" class="inline" onsubmit="return confirm('Debannir ce user ?');">
                                                        @csrf
                                                        <button type="submit" class="text-sm text-green-600 hover:text-green-800">Debannir</button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.users.ban', $user) }}" method="POST" class="inline" onsubmit="return confirm('Bannir ce user ?');">
                                                        @csrf
                                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Bannir</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>