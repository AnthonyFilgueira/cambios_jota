<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🔄 Gestión de Pares de Divisas
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('currencies.index') }}"
                   class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                    Divisas
                </a>
                <a href="{{ route('corridors.index') }}"
                   class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                    Corredores
                </a>
                <a href="{{ route('corridor-matrix.index') }}"
                   class="px-4 py-2 bg-white border border-cj-turquesa text-cj-turquesa rounded-md hover:bg-cj-turquesa hover:bg-opacity-10">
                    🎯 Matriz
                </a>
                <a href="{{ route('currency-pairs.create') }}"
                   class="px-4 py-2 bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white rounded-md hover:opacity-90">
                    + Nuevo Par
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase">Par</th>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase">Origen</th>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase">Destino</th>
                            <th class="px-6 py-4 text-center text-xs font-medium uppercase">Corredores</th>
                            <th class="px-6 py-4 text-center text-xs font-medium uppercase">Estado</th>
                            <th class="px-6 py-4 text-center text-xs font-medium uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($pairs as $pair)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <span class="text-lg font-bold text-cj-morado-profundo">
                                        {{ $pair->full_display }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <span class="font-semibold">{{ $pair->fromCurrency->code }}</span>
                                        <span class="text-gray-500">- {{ $pair->fromCurrency->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <span class="font-semibold">{{ $pair->toCurrency->code }}</span>
                                        <span class="text-gray-500">- {{ $pair->toCurrency->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $enabledCount = $pair->corridors->where('pivot.is_enabled', true)->count();
                                        $totalCount = $pair->corridors->count();
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $enabledCount > 0 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $enabledCount }} / {{ $totalCount }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('currency-pairs.toggleStatus', $pair) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="px-3 py-1 rounded-full text-xs font-semibold {{ $pair->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                                            {{ $pair->is_active ? 'Activo' : 'Inactivo' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('currency-pairs.edit', $pair) }}"
                                           class="text-cj-morado-medio hover:text-cj-morado-profundo font-medium">
                                            Editar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    No hay pares de divisas registrados.
                                    <a href="{{ route('currency-pairs.create') }}" class="text-cj-morado-medio hover:underline">
                                        Crear el primer par
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($pairs->count() > 0)
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600">
                            Total de pares: <strong>{{ $pairs->count() }}</strong>
                            ({{ $pairs->where('is_active', true)->count() }} activos)
                        </p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
