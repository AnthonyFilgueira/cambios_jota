<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Tasas de Cambio</h1>
            <a href="{{ route('exchange_rates.create') }}" class="inline-block bg-cj-morado-profundo text-white px-4 py-2 rounded-lg hover:bg-cj-morado-medio transition-colors">
                Nueva Tasa
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Tasa activa destacada -->
        <div class="bg-gradient-to-r from-cj-morado-profundo to-cj-turquesa text-white rounded-lg p-6 mb-6 shadow-lg">
            <h2 class="text-lg font-semibold mb-4">Tasa Activa Actual</h2>
            <div class="grid grid-cols-3 gap-4">
                <div class="text-center">
                    <div class="text-sm opacity-90 mb-1">USD (BCV)</div>
                    <div class="text-2xl font-bold">{{ number_format($activeRate->usd_rate, 5) }}</div>
                </div>
                <div class="text-center">
                    <div class="text-sm opacity-90 mb-1">EUR (BCV)</div>
                    <div class="text-2xl font-bold">{{ number_format($activeRate->eur_rate, 5) }}</div>
                </div>
                <div class="text-center">
                    <div class="text-sm opacity-90 mb-1">VES/PEN</div>
                    <div class="text-2xl font-bold">{{ number_format($activeRate->ves_rate, 5) }}</div>
                </div>
            </div>
        </div>

        <!-- Historial de tasas -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">USD (BCV)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">EUR (BCV)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">VES/PEN</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($rates as $rate)
                            <tr class="{{ $rate->is_active ? 'bg-cj-morado-claro' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($rate->usd_rate, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($rate->eur_rate, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($rate->ves_rate, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $rate->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($rate->is_active)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-cj-turquesa text-white">
                                            Activa
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-600">
                                            Inactiva
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-3">
                                    @if(!$rate->is_active)
                                        <form action="{{ route('exchange_rates.activate', $rate) }}" method="POST" class="inline">
                                            @csrf
                                            <button class="text-cj-turquesa hover:underline font-medium">Activar</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('exchange_rates.edit', $rate) }}" class="text-cj-morado-profundo hover:underline font-medium">
                                        Editar
                                    </a>
                                    @if(!$rate->is_active)
                                        <form action="{{ route('exchange_rates.destroy', $rate) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:underline font-medium" onclick="return confirm('¿Eliminar esta tasa?')">
                                                Eliminar
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    No hay tasas registradas. <a href="{{ route('exchange_rates.create') }}" class="text-cj-turquesa hover:underline">Crear la primera</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
