<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inviter un Colocataire') }} - {{ $colocation->nom }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('invitations.store', $colocation->id) }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="email" :value="__('Adresse Email de l\'invite')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                :value="old('email')" required autofocus />
                            <p class="mt-2 text-sm text-gray-500">
                                {{ __("On va envoyer un mail avec un lien.") }}
                            </p>
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Generer l\'invitation') }}</x-primary-button>
                            <a href="{{ route('colocations.show', $colocation->id) }}"
                                class="text-sm text-gray-600 hover:text-gray-900 underline">{{ __('Annuler') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>