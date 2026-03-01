<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <div class="text-sm text-gray-600">
                {{ __('Score reputation') }}: <span class="font-bold text-indigo-600">{{ Auth::user()->score_reputation ?? 0 }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 shadow-sm" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 shadow-sm" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Mes Colocations') }}</h3>
                        <a href="{{ route('colocations.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('+ Nouvelle Colocation') }}
                        </a>
                    </div>

                    @if(Auth::user()->colocations->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-500">{{ __("Vous ne faites partie d'aucune colocation pour le moment.") }}
                            </p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach(Auth::user()->colocations as $colocation)
                                @if($colocation->statut === 'annulee')
                                    <div class="block p-4 border border-gray-200 rounded-lg bg-gray-50 opacity-75 cursor-not-allowed">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="font-bold text-gray-500 uppercase text-sm tracking-wider">
                                                {{ $colocation->nom }}
                                            </h4>
                                            <span class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded">Annulee</span>
                                        </div>
                                        <p class="text-gray-400 text-sm line-clamp-2 truncate">
                                            {{ $colocation->description ?: __('Aucune description') }}
                                        </p>
                                        <div class="mt-4 text-xs text-gray-400">
                                            {{ __('Colocation desactivee') }}
                                        </div>
                                    </div>
                                @else
                                <a href="{{ route('colocations.show', $colocation->id) }}"
                                    class="block p-4 border border-gray-200 rounded-lg hover:border-indigo-400 hover:shadow-sm transition-all">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-bold text-indigo-600 uppercase text-sm tracking-wider">
                                            {{ $colocation->nom }}
                                        </h4>
                                        <span
                                            class="text-xs px-2 py-1 bg-gray-100 rounded text-gray-600">{{ $colocation->pivot->role_dans_colocation }}</span>
                                    </div>
                                    <p class="text-gray-500 text-sm line-clamp-2 truncate">
                                        {{ $colocation->description ?: __('Aucune description') }}
                                    </p>
                                    <div class="mt-4 text-xs text-gray-400 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        {{ $colocation->membres()->count() }} {{ __('membres') }}
                                    </div>
                                </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>