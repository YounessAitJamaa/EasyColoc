<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-indigo-800 leading-tight">
            {{ __('Administration Globale') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
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
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Liste des utilisateurs</h3>

                    @if (session('success'))
                        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
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
                                            <td class="px-4 py-3 text-right">
                                                @if($user->est_banni)
                                                    <form action="{{ route('admin.users.unban', $user) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-sm text-green-600 hover:text-green-800">Debannir</button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.users.ban', $user) }}" method="POST" class="inline">
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