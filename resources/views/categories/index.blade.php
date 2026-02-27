<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gerer les categories - ') }} {{ $colocation->nom }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                        role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Ajouter une categorie') }}</h3>
                    <form action="{{ route('categories.store', $colocation->id) }}" method="POST"
                        class="flex items-center space-x-4">
                        @csrf
                        <div class="flex-grow">
                            <x-text-input id="nom" name="nom" type="text" class="block w-full"
                                placeholder="Ex: Courses, Loyer, Factures..." required />
                            <x-input-error :messages="$errors->get('nom')" class="mt-2" />
                        </div>
                        <x-primary-button>
                            {{ __('Ajouter') }}
                        </x-primary-button>
                    </form>
                </div>

                <hr class="my-8">

                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Categories existantes') }}</h3>

                @if($categories->isEmpty())
                    <p class="text-gray-500 italic">{{ __('Pas encore de categorie.') }}</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($categories as $categorie)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <span class="text-gray-900 font-medium">{{ $categorie->nom }}</span>

                                <form action="{{ route('categories.destroy', $categorie->id) }}" method="POST"
                                    onsubmit="return confirm('Tu veux supprimer ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-semibold">
                                        {{ __('Supprimer') }}
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mt-8">
                    <a href="{{ route('colocations.show', $colocation->id) }}"
                        class="text-indigo-600 hover:text-indigo-900">
                        &larr; {{ __('Retour a la colocation') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>