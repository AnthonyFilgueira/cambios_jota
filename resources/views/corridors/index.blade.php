<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🏦 Gestión de Corredores
            </h2>
            <a href="{{ route('corridors.create') }}"
               class="px-4 py-2 bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white rounded-md hover:opacity-90">
                + Nuevo Corredor
            </a>
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
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase">Nombre</th>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase">Descripción</th>
                            <th class="px-6 py-4 text-center text-xs font-medium uppercase">Estado</th>
                            <th class="px-6 py-4 text-center text-xs font-medium uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($corridors as $corridor)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <span class="text-lg font-bold text-cj-morado-profundo">
                                        {{ $corridor->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-600">
                                        {{ $corridor->description ?? 'Sin descripción' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('corridors.toggleStatus', $corridor) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="px-3 py-1 rounded-full text-xs font-semibold {{ $corridor->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                                            {{ $corridor->is_active ? 'Activo' : 'Inactivo' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('corridors.edit', $corridor) }}"
                                           class="text-cj-morado-medio hover:text-cj-morado-profundo font-medium">
                                            Editar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    No hay corredores registrados.
                                    <a href="{{ route('corridors.create') }}" class="text-cj-morado-medio hover:underline">
                                        Crear el primer corredor
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($corridors->count() > 0)
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600">
                            Total de corredores: <strong>{{ $corridors->count() }}</strong>
                            ({{ $corridors->where('is_active', true)->count() }} activos)
                        </p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
