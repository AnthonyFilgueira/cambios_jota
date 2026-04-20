<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-cj-texto">Liquidaciones</h1>
            <a href="{{ route('liquidations.create') }}" class="bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white px-4 py-2 rounded-lg font-semibold hover:shadow-lg transition">
                Nueva Liquidación
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form method="GET" action="{{ route('liquidations.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Vendedor</label>
                    <select name="seller_id" class="w-full border-gray-300 rounded p-2">
                        <option value="">Todos</option>
                        @foreach($sellers as $seller)
                            <option value="{{ $seller->id }}" {{ request('seller_id') == $seller->id ? 'selected' : '' }}>
                                {{ $seller->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Método de pago</label>
                    <select name="payment_method" class="w-full border-gray-300 rounded p-2">
                        <option value="">Todos</option>
                        @foreach($paymentMethods as $key => $label)
                            <option value="{{ $key }}" {{ request('payment_method') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Desde</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full border-gray-300 rounded p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Hasta</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full border-gray-300 rounded p-2">
                </div>

                <div class="md:col-span-4">
                    <button type="submit" class="bg-cj-turquesa text-white px-4 py-2 rounded hover:bg-teal-600 transition">
                        Filtrar
                    </button>
                    <a href="{{ route('liquidations.index') }}" class="ml-2 text-cj-texto-claro hover:text-cj-morado-profundo">
                        Limpiar filtros
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-left">Vendedor</th>
                        <th class="px-4 py-3 text-right">Monto</th>
                        <th class="px-4 py-3 text-left">Método</th>
                        <th class="px-4 py-3 text-left">Referencia</th>
                        <th class="px-4 py-3 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($liquidations as $liquidation)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $liquidation->payment_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                <span class="font-semibold">{{ $liquidation->seller->name }}</span>
                                <span class="text-xs text-gray-500 block">{{ $liquidation->seller->code }}</span>
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-red-600">
                                -S/. {{ number_format($liquidation->amount, 2) }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs">
                                    {{ $liquidation->paymentMethodLabel() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $liquidation->reference ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('liquidations.show', $liquidation) }}" class="text-cj-turquesa hover:underline">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                No hay liquidaciones registradas
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($liquidations->hasPages())
                <div class="px-4 py-3 border-t">
                    {{ $liquidations->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
