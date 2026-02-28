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
                <div class="p-6 text-gray-900 border-t border-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Statistiques de la plateforme') }}</h3>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>