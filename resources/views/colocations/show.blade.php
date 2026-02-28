<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $colocation->nom }}
            </h2>
            <div class="space-x-2 flex items-center">
                @php
                    $isOwner = $colocation->membres->firstWhere('id', Auth::id())?->pivot->role_dans_colocation === 'owner';
                @endphp

                @if($isOwner)
                    <a href="{{ route('colocations.admin', $colocation->id) }}"
                        class="text-xs text-indigo-600 hover:text-indigo-800 transition font-semibold">
                        {{ __('Administration') }}
                    </a>
                    <span class="text-gray-300">|</span>
                @endif

                <a href="{{ route('categories.index', $colocation->id) }}"
                    class="text-xs text-gray-500 hover:text-indigo-600 transition">
                    {{ __('Gerer Categories') }}
                </a>
                <span
                    class="ml-4 px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full uppercase tracking-wider">
                    {{ $colocation->statut }}
                </span>
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
                <div class="p-6 text-gray-900 border-b border-gray-100 italic">
                    <p class="text-gray-600">
                        {{ $colocation->description ?: __('Pas de description.') }}
                    </p>
                </div>
                <!-- Resume Financier -->
                <div class="grid grid-cols-2 divide-x divide-gray-100 bg-gray-50/50">
                    <div class="p-4 text-center">
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">{{ __('Total des depenses') }}
                        </p>
                        <p class="text-xl font-bold text-gray-900">{{ number_format($totalDepenses, 2) }} €</p>
                    </div>
                    <div class="p-4 text-center">
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">{{ __('Part par personne') }}</p>
                        <p class="text-xl font-bold text-indigo-600">{{ number_format($partIndividuelle, 2) }} €</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Membres') }}</h3>
                            <a href="{{ route('invitations.create', $colocation->id) }}"
                                class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                {{ __('Inviter') }}
                            </a>
                        </div>
                        <ul class="divide-y divide-gray-100">
                            @foreach($colocation->membres as $membre)
                                <li class="py-3 flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div
                                            class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs">
                                            {{ strtoupper(substr($membre->name, 0, 1)) }}
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <div class="flex items-center">
                                                <p class="font-medium text-gray-900">{{ $membre->name }}</p>
                                                @if($membre->id === Auth::id())
                                                    <span
                                                        class="ml-2 text-[10px] bg-gray-100 px-1 rounded text-gray-500">Moi</span>
                                                @endif
                                            </div>
                                            <p class="text-gray-500 capitalize text-[10px]">
                                                {{ $membre->pivot->role_dans_colocation }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Solde du membre -->
                                    @if(isset($balances[$membre->id]))
                                        <div class="text-right">
                                            <p
                                                class="text-sm font-bold {{ $balances[$membre->id]['solde'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $balances[$membre->id]['solde'] >= 0 ? '+' : '' }}{{ number_format($balances[$membre->id]['solde'], 2) }}
                                                €
                                            </p>
                                            <p class="text-[10px] text-gray-400">Paye:
                                                {{ number_format($balances[$membre->id]['paye'], 2) }} €
                                            </p>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Suggestions de remboursements -->
                @if(!empty($suggestions))
                    <div class="md:col-span-1 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Qui doit payer qui ?') }}</h3>
                            <ul class="space-y-3">
                                @foreach($suggestions as $suggestion)
                                    <li class="p-3 bg-gray-50 rounded-lg border border-gray-100 text-sm">
                                        <div class="flex flex-col">
                                            <span class="text-gray-600">
                                                <strong class="text-red-600">{{ $suggestion['payeur_nom'] }}</strong>
                                                doit donner
                                            </span>
                                            <span class="text-lg font-bold text-gray-900 my-1">
                                                {{ number_format($suggestion['montant'], 2) }} €
                                            </span>
                                            <span class="text-gray-600">
                                                à <strong class="text-green-600">{{ $suggestion['receveur_nom'] }}</strong>
                                            </span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Activite Recente') }}</h3>
                            @if($colocation->depenses->isEmpty())
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-semibold text-gray-900">{{ __('Pas de depense') }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ __('Ajoute une depense pour commencer.') }}
                                    </p>

                                </div>
                            @else
                                <ul class="divide-y divide-gray-100">
                                    @foreach($colocation->depenses as $depense)
                                        <li class="py-4">
                                            <div class="flex items-center justify-between">
                                                <span class="font-medium">{{ $depense->titre }}</span>
                                                <span
                                                    class="font-bold text-indigo-600">{{ number_format($depense->montant, 2) }}
                                                    €</span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            <div class="mt-6">
                                <a href="{{ route('depenses.index', $colocation->id) }}"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Ajouter une depense') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>