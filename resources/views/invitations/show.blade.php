<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Invitation a une colocation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 text-center">
                    <h1 class="text-2xl font-bold mb-4">Bonjour !</h1>
                    <p class="text-lg mb-8">
                        Tu es invite dans la colocation
                        <span class="font-semibold text-indigo-600">{{ $invitation->colocation->nom }}</span>.
                    </p>

                    <div class="flex justify-center space-x-4">
                        <form action="{{ route('invitations.accept', $invitation->token) }}" method="POST">
                            @csrf
                            <x-primary-button
                                class="bg-green-600 hover:bg-green-700 focus:bg-green-700 active:bg-green-800">
                                {{ __('Accepter l\'invitation') }}
                            </x-primary-button>
                        </form>

                        <form action="{{ route('invitations.refuse', $invitation->token) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Refuser') }}
                            </button>
                        </form>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ __("On va envoyer un mail avec un lien.") }}
                    </p>
                    <p class="mt-8 text-sm text-gray-500">
                        C'est bon jusqu'au {{ $invitation->expire_le->format('d/m/Y a H:i') }}.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>