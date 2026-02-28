<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Creer une Colocation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('error'))
                        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
                    @endif
                    <form method="POST" action="{{ route('colocations.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="nom" :value="__('Nom de la Colocation')" />
                            <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full" :value="old('nom')"
                                required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('nom')" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description (Optionnel)')" />
                            <textarea id="description" name="description"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                rows="4">{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Creer') }}</x-primary-button>
                            <a href="{{ route('dashboard') }}"
                                class="text-sm text-gray-600 hover:text-gray-900 underline">{{ __('Annuler') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>