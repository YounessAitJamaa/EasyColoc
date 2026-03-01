<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Depenses - ') }} {{ $colocation->nom }}
            </h2>
            <a href="{{ route('colocations.show', $colocation->id) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                &larr; {{ __('Retour au tableau de bord') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="max-w-xl">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Ajouter une depense') }}</h3>
                    <form action="{{ route('depenses.store', $colocation->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="titre" :value="__('Titre / Description')" />
                            <x-text-input id="titre" name="titre" type="text" class="block mt-1 w-full" placeholder="Ex: Courses Carrefour" required />
                            <x-input-error :messages="$errors->get('titre')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="montant" :value="__('Montant (€)')" />
                            <x-text-input id="montant" name="montant" type="number" step="0.01" class="block mt-1 w-full" placeholder="0.00" required />
                            <x-input-error :messages="$errors->get('montant')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="date_depense" :value="__('Date')" />
                            <x-text-input id="date_depense" name="date_depense" type="date" class="block mt-1 w-full" value="{{ date('Y-m-d') }}" required />
                            <x-input-error :messages="$errors->get('date_depense')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="categorie_id" :value="__('Categorie')" />
                            <select id="categorie_id" name="categorie_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">{{ __('Aucune categorie') }}</option>
                                @foreach($categories as $categorie)
                                    <option value="{{ $categorie->id }}">{{ $categorie->nom }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('categorie_id')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500">
                                <a href="{{ route('categories.index', $colocation->id) }}" class="text-indigo-600 hover:underline">
                                    {{ __('Gerer les categories') }}
                                </a>
                            </p>
                        </div>

                        <div class="pt-4">
                            <x-primary-button class="w-full justify-center">
                                {{ __('Enregistrer la depense') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
