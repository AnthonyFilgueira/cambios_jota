<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📊 Reporte de Rendimiento: {{ $seller->name }}
            </h2>
            <a href="{{ route('reports.rankings') }}" class="text-sm text-cj-morado-medio hover:text-cj-morado-profundo">
                Ver todos los vendedores →
            </a>
        </div>
    </x-slot>

    <div class="py-6" x-data="{ period: '{{ $period }}' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- FILTROS DE PERÍODO -->
            <div class="mb-6 bg-white rounded-lg shadow p-4">
                <form method="GET" action="{{ route('reports.performance', $seller) }}" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Período</label>
                        <select name="period" x-model="period" @change="$el.form.submit()"
                                class="rounded-md border-gray-300 shadow-sm focus:border-cj-morado-medio focus:ring focus:ring-cj-morado-claro">
                            <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Hoy</option>
                            <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Esta semana</option>
                            <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Este mes</option>
                            <option value="quarter" {{ $period === 'quarter' ? 'selected' : '' }}>Este trimestre</option>
                            <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Este año</option>
                            <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Todo el tiempo</option>
                            <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Personalizado</option>
                        </select>
                    </div>

                    <template x-show="period === 'custom'">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Desde</label>
                            <input type="date" name="start_date" value="{{ $startDate }}"
                                   class="rounded-md border-gray-300 shadow-sm focus:border-cj-morado-medio focus:ring focus:ring-cj-morado-claro">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hasta</label>
                            <input type="date" name="end_date" value="{{ $endDate }}"
                                   class="rounded-md border-gray-300 shadow-sm focus:border-cj-morado-medio focus:ring focus:ring-cj-morado-claro">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white rounded-md hover:opacity-90">
                            Aplicar
                        </button>
                    </template>

                    <div class="ml-auto text-sm text-gray-500">
                        <strong>Período:</strong> {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                    </div>
                </form>

                <!-- BOTÓN DE EXPORTACIÓN -->
                <div class="flex gap-2 mt-4">
                    <a href="{{ route('export.seller.pdf', array_merge(['seller' => $seller], request()->all())) }}"
                       class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Exportar PDF
                    </a>
                </div>
            </div>

            <!-- INFO DEL VENDEDOR -->
            <div class="mb-6 bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold">{{ $seller->name }}</h3>
                        <p class="text-sm opacity-90 mt-1">Código: {{ $seller->code }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm opacity-90">Comisiones configuradas</p>
                        <p class="text-lg font-semibold">Vendedor: {{ $seller->seller_commission }}% | Dueño: {{ $seller->boss_commission }}%</p>
                    </div>
                </div>
            </div>

            <!-- MÉTRICAS PRINCIPALES -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Total Vendido -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Total Vendido</h3>
                    <p class="text-3xl font-bold text-cj-morado-profundo">S/. {{ number_format($metrics['total_amount'], 2) }}</p>
                    @if(isset($comparison['sales_change']))
                        <p class="text-sm mt-2">
                            <span class="{{ $comparison['sales_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $comparison['sales_change'] >= 0 ? '↑' : '↓' }} {{ abs($comparison['sales_change']) }}%
                            </span>
                            vs período anterior
                        </p>
                    @endif
                </div>

                <!-- Cantidad de Ventas -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Cantidad de Ventas</h3>
                    <p class="text-3xl font-bold text-cj-turquesa">{{ $metrics['total_sales'] }}</p>
                    <p class="text-xs text-gray-500 mt-2">
                        ✓ {{ $metrics['approved_sales'] }} aprobadas |
                        ⏳ {{ $metrics['pending_sales'] }} pendientes
                    </p>
                </div>

                <!-- Ticket Promedio -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Ticket Promedio</h3>
                    <p class="text-3xl font-bold text-cj-rosa">S/. {{ number_format($metrics['average_ticket'], 2) }}</p>
                    <p class="text-xs text-gray-500 mt-2">
                        Sistema: S/. {{ number_format($systemAverage['average_ticket'], 2) }}
                    </p>
                </div>

                <!-- Tasa de Conversión -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Tasa de Conversión</h3>
                    <p class="text-3xl font-bold text-purple-600">{{ number_format($metrics['conversion_rate'], 1) }}%</p>
                    <p class="text-xs text-gray-500 mt-2">
                        Aprobadas / Total
                    </p>
                </div>
            </div>

            <!-- COMISIONES Y MONEDERO -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- Comisión Vendedor -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Comisión Generada</h3>
                    <p class="text-2xl font-bold text-cj-morado-profundo">S/. {{ number_format($metrics['seller_commission'], 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $seller->seller_commission }}% del total vendido</p>
                </div>

                <!-- Saldo Monedero -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Saldo en Monedero</h3>
                    <p class="text-2xl font-bold text-cj-turquesa">S/. {{ number_format($wallet['balance'], 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Disponible para liquidar</p>
                </div>

                <!-- Total Liquidado -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Total Liquidado</h3>
                    <p class="text-2xl font-bold text-cj-rosa">S/. {{ number_format($liquidations['total'], 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $liquidations['count'] }} liquidaciones</p>
                </div>
            </div>

            <!-- RANKINGS Y DESGLOSE -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Posición en Rankings -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">🏆 Posición en Rankings</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Por Monto Total</span>
                                <span class="text-xl font-bold text-cj-morado-profundo">
                                    #{{ $rankings['by_amount'] ?? '-' }}
                                    <span class="text-sm text-gray-500">/ {{ $rankings['total_sellers'] }}</span>
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Por Cantidad de Ventas</span>
                                <span class="text-xl font-bold text-cj-turquesa">
                                    #{{ $rankings['by_count'] ?? '-' }}
                                    <span class="text-sm text-gray-500">/ {{ $rankings['total_sellers'] }}</span>
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Por Comisiones</span>
                                <span class="text-xl font-bold text-cj-rosa">
                                    #{{ $rankings['by_commission'] ?? '-' }}
                                    <span class="text-sm text-gray-500">/ {{ $rankings['total_sellers'] }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Desglose por Estado -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">📋 Desglose por Estado</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">✓ Aprobadas</span>
                                <span class="font-semibold text-green-600">{{ $metrics['approved_sales'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">✓ Completadas</span>
                                <span class="font-semibold text-green-700">{{ $metrics['completed_sales'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">⏳ Pendientes</span>
                                <span class="font-semibold text-yellow-600">{{ $metrics['pending_sales'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">👁 Observadas</span>
                                <span class="font-semibold text-orange-600">{{ $metrics['observed_sales'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">✗ Rechazadas</span>
                                <span class="font-semibold text-red-600">{{ $metrics['rejected_sales'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ÚLTIMAS LIQUIDACIONES -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">💰 Últimas Liquidaciones</h3>
                </div>
                <div class="p-6">
                    @forelse($liquidations['recent'] as $liq)
                        <div class="flex justify-between items-center py-3 border-b border-gray-100 last:border-0">
                            <div>
                                <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($liq->payment_date)->format('d/m/Y') }}</p>
                                <p class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $liq->payment_method) }}</p>
                            </div>
                            <p class="font-semibold text-cj-morado-profundo">S/. {{ number_format($liq->amount, 2) }}</p>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No hay liquidaciones registradas</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
