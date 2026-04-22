<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📊 Dashboard del Dueño
        </h2>
    </x-slot>

    <div class="py-6" x-data="ownerDashboard()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- FILTROS DE PERÍODO -->
            <div class="mb-6 bg-white rounded-lg shadow p-4">
                <form method="GET" action="{{ route('owner.dashboard') }}" class="flex flex-wrap gap-4 items-end">
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

                <!-- BOTONES DE EXPORTACIÓN -->
                <div class="flex gap-2 mt-4">
                    <a href="{{ route('export.dashboard.csv', request()->all()) }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Exportar CSV
                    </a>
                    <a href="{{ route('export.dashboard.pdf', request()->all()) }}"
                       class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Exportar PDF
                    </a>
                </div>
            </div>

            <!-- MÉTRICAS GLOBALES -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Total Vendido -->
                <div class="bg-gradient-to-br from-cj-morado-profundo to-cj-morado-medio text-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium opacity-90">Total Vendido</h3>
                        <svg class="w-6 h-6 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold">S/. {{ number_format($metrics['total_sales'], 2) }}</p>
                    @if(isset($comparison['sales_change']))
                        <p class="text-sm mt-2 opacity-90">
                            <span class="{{ $comparison['sales_change'] >= 0 ? 'text-green-300' : 'text-red-300' }}">
                                {{ $comparison['sales_change'] >= 0 ? '↑' : '↓' }} {{ abs($comparison['sales_change']) }}%
                            </span>
                            vs período anterior
                        </p>
                    @endif
                </div>

                <!-- Comisiones Vendedores -->
                <div class="bg-gradient-to-br from-cj-turquesa to-teal-500 text-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium opacity-90">Comisiones Vendedores</h3>
                        <svg class="w-6 h-6 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold">S/. {{ number_format($metrics['seller_commissions'], 2) }}</p>
                    <p class="text-sm mt-2 opacity-90">
                        {{ $metrics['sales_count'] > 0 ? number_format(($metrics['seller_commissions'] / $metrics['total_sales']) * 100, 1) : 0 }}% del total
                    </p>
                </div>

                <!-- Comisiones Dueño -->
                <div class="bg-gradient-to-br from-cj-rosa to-pink-500 text-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium opacity-90">Mis Comisiones</h3>
                        <svg class="w-6 h-6 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold">S/. {{ number_format($metrics['boss_commissions'], 2) }}</p>
                    <p class="text-sm mt-2 opacity-90">
                        {{ $metrics['sales_count'] > 0 ? number_format(($metrics['boss_commissions'] / $metrics['total_sales']) * 100, 1) : 0 }}% del total
                    </p>
                </div>

                <!-- Cantidad de Ventas -->
                <div class="bg-gradient-to-br from-purple-500 to-indigo-500 text-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium opacity-90">Cantidad de Ventas</h3>
                        <svg class="w-6 h-6 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold">{{ $metrics['sales_count'] }}</p>
                    <p class="text-sm mt-2 opacity-90">
                        ✓ {{ $metrics['approved_count'] }} aprobadas |
                        ⏳ {{ $metrics['pending_count'] }} pendientes
                    </p>
                </div>
            </div>

            <!-- SEGUNDA FILA DE MÉTRICAS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- Ticket Promedio -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Ticket Promedio</h3>
                    <p class="text-2xl font-bold text-cj-morado-profundo">S/. {{ number_format($metrics['average_ticket'], 2) }}</p>
                </div>

                <!-- Total Liquidado -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Total Liquidado</h3>
                    <p class="text-2xl font-bold text-cj-turquesa">S/. {{ number_format($liquidations['total_liquidated'], 2) }}</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $liquidations['count'] }} liquidaciones</p>
                </div>

                <!-- Saldo en Monederos -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Saldo en Monederos</h3>
                    <p class="text-2xl font-bold text-cj-rosa">S/. {{ number_format($wallets['total_balance'], 2) }}</p>
                    <p class="text-sm text-gray-500 mt-1">Pendiente de liquidar</p>
                </div>
            </div>

            <!-- RANKING VENDEDORES (Tabla Única con Ordenamiento) -->
            <div class="mb-6">
                <div class="bg-white rounded-lg shadow" x-data="rankingTable()">
                    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">🏆 Top Vendedores</h3>
                        <div class="flex gap-2">
                            <button @click="sortBy = 'sales'; sortRankings()"
                                    :class="sortBy === 'sales' ? 'bg-cj-morado-profundo text-white' : 'bg-gray-100 text-gray-700'"
                                    class="px-4 py-2 text-sm rounded-md hover:opacity-90 transition">
                                💰 Por Monto
                            </button>
                            <button @click="sortBy = 'count'; sortRankings()"
                                    :class="sortBy === 'count' ? 'bg-cj-turquesa text-white' : 'bg-gray-100 text-gray-700'"
                                    class="px-4 py-2 text-sm rounded-md hover:opacity-90 transition">
                                📊 Por Cantidad
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <table class="w-full">
                            <thead class="text-xs text-gray-500 uppercase">
                                <tr>
                                    <th class="text-left pb-3">#</th>
                                    <th class="text-left pb-3">Vendedor</th>
                                    <th class="text-right pb-3 cursor-pointer hover:text-cj-morado-profundo" @click="sortBy = 'sales'; sortRankings()">
                                        Monto <span x-show="sortBy === 'sales'">▼</span>
                                    </th>
                                    <th class="text-right pb-3 cursor-pointer hover:text-cj-turquesa" @click="sortBy = 'count'; sortRankings()">
                                        Ventas <span x-show="sortBy === 'count'">▼</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <template x-for="(rank, index) in sortedRankings" :key="index">
                                    <tr class="border-t border-gray-100">
                                        <td class="py-3">
                                            <span x-text="index + 1"
                                                  :class="{
                                                      'bg-yellow-100 text-yellow-600': index === 0,
                                                      'bg-gray-100 text-gray-600': index === 1,
                                                      'bg-orange-100 text-orange-600': index === 2
                                                  }"
                                                  class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold">
                                            </span>
                                        </td>
                                        <td class="py-3 font-medium text-gray-900" x-text="rank.seller_name"></td>
                                        <td class="py-3 text-right font-semibold text-cj-morado-profundo">
                                            S/. <span x-text="formatNumber(rank.total_sales)"></span>
                                        </td>
                                        <td class="py-3 text-right text-gray-600" x-text="rank.sales_count"></td>
                                    </tr>
                                </template>
                                <template x-if="sortedRankings.length === 0">
                                    <tr>
                                        <td colspan="4" class="py-4 text-center text-gray-500">No hay datos</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- LIQUIDACIONES RECIENTES Y SALDOS PENDIENTES -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Liquidaciones Recientes -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">💰 Liquidaciones Recientes</h3>
                    </div>
                    <div class="p-6">
                        @forelse($liquidations['recent'] as $liq)
                            <div class="flex justify-between items-center py-3 border-b border-gray-100 last:border-0">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $liq->seller->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($liq->payment_date)->format('d/m/Y') }} •
                                        <span class="capitalize">{{ str_replace('_', ' ', $liq->payment_method) }}</span>
                                    </p>
                                </div>
                                <p class="font-semibold text-cj-morado-profundo">S/. {{ number_format($liq->amount, 2) }}</p>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No hay liquidaciones en este período</p>
                        @endforelse
                    </div>
                </div>

                <!-- Saldos Pendientes -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">💳 Saldos Pendientes de Liquidar</h3>
                    </div>
                    <div class="p-6">
                        @forelse($wallets['pending_liquidations'] as $pending)
                            <div class="flex justify-between items-center py-3 border-b border-gray-100 last:border-0">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $pending['seller']->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $pending['seller']->code }}</p>
                                </div>
                                <p class="font-semibold text-cj-rosa">S/. {{ number_format($pending['balance'], 2) }}</p>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">Todos los vendedores tienen saldo S/. 0.00</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
    function ownerDashboard() {
        return {
            period: '{{ $period }}',
        }
    }

    function rankingTable() {
        return {
            sortBy: 'sales', // 'sales' o 'count'
            rankings: @json($rankings['by_sales']->map(function($rank) {
                return [
                    'seller_name' => $rank['seller']->name,
                    'total_sales' => $rank['total_sales'],
                    'sales_count' => $rank['sales_count']
                ];
            })),
            sortedRankings: [],

            init() {
                this.sortRankings();
            },

            sortRankings() {
                if (this.sortBy === 'sales') {
                    this.sortedRankings = [...this.rankings].sort((a, b) => b.total_sales - a.total_sales);
                } else {
                    this.sortedRankings = [...this.rankings].sort((a, b) => b.sales_count - a.sales_count);
                }
            },

            formatNumber(num) {
                return new Intl.NumberFormat('es-PE', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(num);
            }
        }
    }
    </script>
</x-app-layout>
