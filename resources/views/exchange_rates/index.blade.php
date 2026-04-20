<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 py-6" x-data="ratesManager()">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-cj-texto">Consola de Tasas de Cambio</h1>
                <p class="text-sm text-cj-texto-claro mt-1">Gestión de tasas base y márgenes por par de divisas</p>
            </div>
            <a href="{{ route('exchange_rates.create') }}" class="inline-block bg-cj-morado-profundo text-white px-4 py-2 rounded-lg hover:bg-cj-morado-medio transition-colors">
                Nueva Tasa
            </a>
        </div>

        <!-- Mensajes de sesión -->
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

        <!-- Tasas activas + Referencias BCV -->
        <div class="bg-gradient-to-r from-cj-morado-profundo to-cj-turquesa text-white rounded-lg p-6 mb-6 shadow-lg">
            <h2 class="text-lg font-semibold mb-4">Tasas de Referencia BCV y Activas</h2>
            <div class="grid grid-cols-3 gap-4">
                <div class="text-center">
                    <div class="text-sm opacity-90 mb-1">USD → VES (BCV)</div>
                    <div class="text-2xl font-bold">{{ number_format($activeRate->usd_rate ?? 0, 2) }}</div>
                    <div class="text-xs opacity-75 mt-1">Referencia del mercado</div>
                </div>
                <div class="text-center">
                    <div class="text-sm opacity-90 mb-1">EUR → VES (BCV)</div>
                    <div class="text-2xl font-bold">{{ number_format($activeRate->eur_rate ?? 0, 2) }}</div>
                    <div class="text-xs opacity-75 mt-1">Referencia del mercado</div>
                </div>
                <div class="text-center border-l border-white/30">
                    <div class="text-sm opacity-90 mb-1">
                        @if($activeRate && $activeRate->currencyPair)
                            {{ $activeRate->currencyPair->display_name }}
                        @else
                            PEN → VES
                        @endif
                    </div>
                    <div class="text-2xl font-bold">{{ number_format($activeRate->ves_rate ?? 0, 5) }}</div>
                    <div class="text-xs opacity-75 mt-1">
                        @if($activeRate && $activeRate->is_active)
                            ✓ Tasa activa
                        @else
                            Tasa de referencia
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form method="GET" action="{{ route('exchange_rates.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Divisa origen -->
                    <div>
                        <label class="block text-sm font-medium text-cj-texto mb-1">Divisa Origen</label>
                        <select name="from_currency" class="w-full rounded-lg border-gray-300">
                            <option value="">Todas</option>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}" {{ request('from_currency') == $currency->id ? 'selected' : '' }}>
                                    {{ $currency->code }} - {{ $currency->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Divisa destino -->
                    <div>
                        <label class="block text-sm font-medium text-cj-texto mb-1">Divisa Destino</label>
                        <select name="to_currency" class="w-full rounded-lg border-gray-300">
                            <option value="">Todas</option>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}" {{ request('to_currency') == $currency->id ? 'selected' : '' }}>
                                    {{ $currency->code }} - {{ $currency->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Estado -->
                    <div>
                        <label class="block text-sm font-medium text-cj-texto mb-1">Estado</label>
                        <select name="status" class="w-full rounded-lg border-gray-300">
                            <option value="active" {{ request('status', 'active') == 'active' ? 'selected' : '' }}>✓ Activas (actual)</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>✕ Inactivas (historial)</option>
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Todas (activas + historial)</option>
                        </select>
                    </div>

                    <!-- Botones -->
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 bg-cj-morado-profundo text-white px-4 py-2 rounded-lg hover:bg-cj-morado-medio transition-colors">
                            Filtrar
                        </button>
                        <a href="{{ route('exchange_rates.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabla de tasas -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Par</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tasa VES</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">USD (BCV)</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">EUR (BCV)</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Última Act.</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($rates as $rate)
                            <tr class="{{ $rate->is_active ? 'bg-cj-morado-claro/30' : '' }}">
                                <!-- Par -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-cj-texto">
                                                {{ $rate->pair_name }}
                                            </div>
                                            @if($rate->currencyPair)
                                                <div class="text-xs text-cj-texto-claro">
                                                    {{ $rate->currencyPair->fromCurrency->code }} → {{ $rate->currencyPair->toCurrency->code }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Tasa VES -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-cj-morado-profundo font-mono">
                                        {{ number_format($rate->ves_rate ?? 0, 5) }}
                                    </div>
                                </td>

                                <!-- USD BCV -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-cj-texto font-mono">
                                        {{ number_format($rate->usd_rate ?? 0, 2) }}
                                    </div>
                                </td>

                                <!-- EUR BCV -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-cj-texto font-mono">
                                        {{ number_format($rate->eur_rate ?? 0, 2) }}
                                    </div>
                                </td>

                                <!-- Última Actualización -->
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-cj-texto-claro">
                                    <div>{{ $rate->updated_at->format('d/m/Y') }}</div>
                                    <div class="text-xs">{{ $rate->updated_at->format('H:i') }}</div>
                                </td>

                                <!-- Estado -->
                                <td class="px-4 py-4 whitespace-nowrap">
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

                                <!-- Acciones -->
                                <td class="px-4 py-4 whitespace-nowrap text-sm space-x-2">
                                    <div class="flex gap-2">
                                        @if(!$rate->is_active)
                                            <form action="{{ route('exchange_rates.activate', $rate) }}" method="POST" class="inline">
                                                @csrf
                                                <button class="text-cj-turquesa hover:underline font-medium text-xs">
                                                    Activar
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('exchange_rates.edit', $rate) }}"
                                           class="text-cj-morado-profundo hover:underline font-medium text-xs">
                                            Editar
                                        </a>
                                        @if(!$rate->is_active && $rate->canBeModified())
                                            <form action="{{ route('exchange_rates.destroy', $rate) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-600 hover:underline font-medium text-xs"
                                                        onclick="return confirm('¿Eliminar esta tasa?')">
                                                    Eliminar
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
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
