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

        <!-- Tasa activa destacada + Referencias BCV -->
        <div class="bg-gradient-to-r from-cj-morado-profundo to-cj-turquesa text-white rounded-lg p-6 mb-6 shadow-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tasa principal activa -->
                <div>
                    <h2 class="text-lg font-semibold mb-3">Tasa Activa Principal</h2>
                    @if($activeRate && $activeRate->currencyPair)
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm opacity-90">Par:</span>
                                <span class="text-xl font-bold">{{ $activeRate->currencyPair->display_name }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm opacity-90">Tasa Base:</span>
                                <span class="text-lg">{{ number_format($activeRate->base_rate, 5) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm opacity-90">Margen:</span>
                                <span class="text-lg">{{ $activeRate->margin_display }}</span>
                            </div>
                            <div class="flex justify-between items-center border-t border-white/20 pt-2">
                                <span class="text-sm opacity-90">Tasa Final:</span>
                                <span class="text-2xl font-bold">{{ number_format($activeRate->final_rate, 5) }}</span>
                            </div>
                        </div>
                    @else
                        <p class="text-sm opacity-75">No hay tasa activa con par asignado</p>
                    @endif
                </div>

                <!-- Referencias BCV (compatibilidad) -->
                <div>
                    <h2 class="text-lg font-semibold mb-3">Referencias BCV (Mercado)</h2>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="text-center">
                            <div class="text-xs opacity-75 mb-1">USD → VES</div>
                            <div class="text-lg font-bold">{{ number_format($activeRate->usd_rate ?? 0, 2) }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xs opacity-75 mb-1">EUR → VES</div>
                            <div class="text-lg font-bold">{{ number_format($activeRate->eur_rate ?? 0, 2) }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xs opacity-75 mb-1">PEN → VES</div>
                            <div class="text-lg font-bold">{{ number_format($activeRate->ves_rate ?? $activeRate->base_rate ?? 0, 5) }}</div>
                        </div>
                    </div>
                    <p class="text-xs opacity-75 mt-2 text-center">Referencia informativa del mercado</p>
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
                            <option value="">Todos</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activos</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactivos</option>
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
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tasa Base</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Margen</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tasa Final</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ganancia %</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Última Act.</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($rates as $rate)
                            <tr class="{{ $rate->is_active ? 'bg-cj-morado-claro/30' : '' }}"
                                @mouseenter="showProfit({{ $rate->id }}, {{ $rate->base_rate }}, {{ $rate->final_rate }})"
                                @mouseleave="hideProfit()">
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
                                            @else
                                                <div class="text-xs text-yellow-600">Legacy</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Tasa Base -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-cj-texto font-mono">
                                        {{ number_format($rate->base_rate ?? $rate->ves_rate ?? 0, 5) }}
                                    </div>
                                </td>

                                <!-- Margen -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ $rate->margin_type === 'percentage' ? 'bg-blue-100 text-blue-800' :
                                           ($rate->margin_type === 'fixed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600') }}">
                                        {{ $rate->margin_display }}
                                    </span>
                                </td>

                                <!-- Tasa Final -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-cj-morado-profundo font-mono">
                                        {{ number_format($rate->final_rate ?? $rate->base_rate ?? 0, 5) }}
                                    </div>
                                </td>

                                <!-- Ganancia % -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if($rate->margin_type === 'percentage')
                                        <div class="text-sm text-cj-turquesa font-semibold">
                                            +{{ number_format($rate->margin_value, 2) }}%
                                        </div>
                                    @else
                                        <div class="text-xs text-gray-400">-</div>
                                    @endif
                                </td>

                                <!-- Última Actualización -->
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-cj-texto-claro">
                                    <div>{{ $rate->updated_at->format('d/m/Y') }}</div>
                                    <div class="text-xs">{{ $rate->updated_at->format('H:i') }}</div>
                                    @if($rate->updatedBy)
                                        <div class="text-xs text-gray-400">{{ $rate->updatedBy->name }}</div>
                                    @endif
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
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                    No hay tasas registradas. <a href="{{ route('exchange_rates.create') }}" class="text-cj-turquesa hover:underline">Crear la primera</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Calculadora de ganancia (hover) -->
        <div x-show="profitCalculator.visible"
             x-transition
             class="fixed bottom-6 right-6 bg-white rounded-lg shadow-xl p-4 border-2 border-cj-morado-profundo max-w-sm">
            <h3 class="font-semibold text-cj-texto mb-3">Calculadora de Ganancia</h3>
            <div class="space-y-2">
                <div>
                    <label class="text-xs text-cj-texto-claro">Monto a enviar (origen):</label>
                    <input type="number"
                           x-model="profitCalculator.amount"
                           @input="calculateProfit()"
                           class="w-full rounded border-gray-300 text-sm mt-1"
                           placeholder="1000">
                </div>
                <div class="bg-cj-fondo p-3 rounded space-y-1">
                    <div class="flex justify-between text-sm">
                        <span class="text-cj-texto-claro">A tasa base:</span>
                        <span class="font-mono" x-text="profitCalculator.baseAmount"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-cj-texto-claro">A tasa final:</span>
                        <span class="font-mono" x-text="profitCalculator.finalAmount"></span>
                    </div>
                    <div class="flex justify-between text-sm font-semibold text-cj-turquesa border-t pt-1">
                        <span>Ganancia:</span>
                        <span class="font-mono" x-text="profitCalculator.profit"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function ratesManager() {
        return {
            profitCalculator: {
                visible: false,
                rateId: null,
                baseRate: 0,
                finalRate: 0,
                amount: 1000,
                baseAmount: '0.00',
                finalAmount: '0.00',
                profit: '0.00'
            },

            showProfit(rateId, baseRate, finalRate) {
                this.profitCalculator.visible = true;
                this.profitCalculator.rateId = rateId;
                this.profitCalculator.baseRate = baseRate;
                this.profitCalculator.finalRate = finalRate;
                this.calculateProfit();
            },

            hideProfit() {
                setTimeout(() => {
                    this.profitCalculator.visible = false;
                }, 300);
            },

            calculateProfit() {
                const amount = parseFloat(this.profitCalculator.amount) || 0;
                const base = amount * this.profitCalculator.baseRate;
                const final = amount * this.profitCalculator.finalRate;
                const profit = final - base;

                this.profitCalculator.baseAmount = base.toFixed(2);
                this.profitCalculator.finalAmount = final.toFixed(2);
                this.profitCalculator.profit = profit.toFixed(2);
            }
        }
    }
    </script>
</x-app-layout>
