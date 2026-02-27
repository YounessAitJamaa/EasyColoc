<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Administration - ') }} {{ $colocation->nom }}
            </h2>
            <a href="{{ route('colocations.show', $colocation->id) }}"
                class="text-sm text-indigo-600 hover:text-indigo-900">
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Parametres de la Colocation') }}</h3>

                    <form action="{{ route('colocations.admin.update', $colocation->id) }}" method="POST"
                        class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <div>
                            <x-input-label for="nom" :value="__('Nom de la colocation')" />
                            <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full" :value="old('nom', $colocation->nom)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('nom')" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                rows="3">{{ old('description', $colocation->description) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Enregistrer les modifications') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Zone de danger') }}</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        {{ __('Attention, si tu supprime c\'est fini.') }}
                    </p>

                    <form action="{{ route('colocations.admin.destroy', $colocation->id) }}" method="POST"
                        onsubmit="return confirm('Tu es sur de vouloir supprimer ?');">
                        @csrf
                        @method('DELETE')
                        <x-danger-button type="submit">
                            {{ __('Supprimer la colocation') }}
                        </x-danger-button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>