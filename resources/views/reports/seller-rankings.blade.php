<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🏆 Rankings de Vendedores
        </h2>
    </x-slot>

    <div class="py-6" x-data="{ period: new URLSearchParams(window.location.search).get('period') ?? 'month' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- FILTROS -->
            <div class="mb-6 bg-white rounded-lg shadow p-4">
                <form method="GET" action="{{ route('reports.rankings') }}" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Período</label>
                        <select name="period" x-model="period" @change="$el.form.submit()"
                                class="rounded-md border-gray-300 shadow-sm focus:border-cj-morado-medio focus:ring focus:ring-cj-morado-claro">
                            <option value="today" :selected="period === 'today'">Hoy</option>
                            <option value="week" :selected="period === 'week'">Esta semana</option>
                            <option value="month" :selected="period === 'month'">Este mes</option>
                            <option value="quarter" :selected="period === 'quarter'">Este trimestre</option>
                            <option value="year" :selected="period === 'year'">Este año</option>
                            <option value="all" :selected="period === 'all'">Todo el tiempo</option>
                            <option value="custom" :selected="period === 'custom'">Personalizado</option>
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
                    <a href="{{ route('export.rankings.csv', request()->all()) }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Exportar CSV
                    </a>
                    <a href="{{ route('export.rankings.pdf', request()->all()) }}"
                       class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Exportar PDF
                    </a>
                </div>
            </div>

            <!-- TABLA DE RANKINGS -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">#</th>
                                <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">
                                    <a href="{{ route('reports.rankings', array_merge(request()->all(), ['sort' => 'seller', 'direction' => $sortBy === 'seller' && $sortDirection === 'asc' ? 'desc' : 'asc'])) }}"
                                       class="hover:underline">
                                        Vendedor
                                        @if($sortBy === 'seller')
                                            <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-medium uppercase tracking-wider">
                                    <a href="{{ route('reports.rankings', array_merge(request()->all(), ['sort' => 'total_sales', 'direction' => $sortBy === 'total_sales' && $sortDirection === 'desc' ? 'asc' : 'desc'])) }}"
                                       class="hover:underline">
                                        Total Vendido
                                        @if($sortBy === 'total_sales')
                                            <span>{{ $sortDirection === 'desc' ? '↓' : '↑' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-medium uppercase tracking-wider">
                                    <a href="{{ route('reports.rankings', array_merge(request()->all(), ['sort' => 'sales_count', 'direction' => $sortBy === 'sales_count' && $sortDirection === 'desc' ? 'asc' : 'desc'])) }}"
                                       class="hover:underline">
                                        Cantidad
                                        @if($sortBy === 'sales_count')
                                            <span>{{ $sortDirection === 'desc' ? '↓' : '↑' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-medium uppercase tracking-wider">
                                    <a href="{{ route('reports.rankings', array_merge(request()->all(), ['sort' => 'average_ticket', 'direction' => $sortBy === 'average_ticket' && $sortDirection === 'desc' ? 'asc' : 'desc'])) }}"
                                       class="hover:underline">
                                        Ticket Prom.
                                        @if($sortBy === 'average_ticket')
                                            <span>{{ $sortDirection === 'desc' ? '↓' : '↑' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-medium uppercase tracking-wider">
                                    <a href="{{ route('reports.rankings', array_merge(request()->all(), ['sort' => 'conversion_rate', 'direction' => $sortBy === 'conversion_rate' && $sortDirection === 'desc' ? 'asc' : 'desc'])) }}"
                                       class="hover:underline">
                                        Conversión
                                        @if($sortBy === 'conversion_rate')
                                            <span>{{ $sortDirection === 'desc' ? '↓' : '↑' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-medium uppercase tracking-wider">
                                    <a href="{{ route('reports.rankings', array_merge(request()->all(), ['sort' => 'seller_commission', 'direction' => $sortBy === 'seller_commission' && $sortDirection === 'desc' ? 'asc' : 'desc'])) }}"
                                       class="hover:underline">
                                        Comisión
                                        @if($sortBy === 'seller_commission')
                                            <span>{{ $sortDirection === 'desc' ? '↓' : '↑' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-medium uppercase tracking-wider">
                                    <a href="{{ route('reports.rankings', array_merge(request()->all(), ['sort' => 'wallet_balance', 'direction' => $sortBy === 'wallet_balance' && $sortDirection === 'desc' ? 'asc' : 'desc'])) }}"
                                       class="hover:underline">
                                        Saldo
                                        @if($sortBy === 'wallet_balance')
                                            <span>{{ $sortDirection === 'desc' ? '↓' : '↑' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-medium uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($sellers as $index => $data)
                                <tr class="hover:bg-gray-50 {{ $index < 3 ? 'bg-yellow-50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($index < 3)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ $index === 0 ? 'bg-yellow-100 text-yellow-600' : ($index === 1 ? 'bg-gray-100 text-gray-600' : 'bg-orange-100 text-orange-600') }} text-sm font-bold">
                                                {{ $index + 1 }}
                                            </span>
                                        @else
                                            <span class="text-gray-500">{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $data['seller']->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $data['seller']->code }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <span class="font-semibold text-cj-morado-profundo">S/. {{ number_format($data['total_sales'], 2) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <span class="text-gray-900">{{ $data['sales_count'] }}</span>
                                        <div class="text-xs text-gray-500">{{ $data['approved_count'] }} aprobadas</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <span class="text-gray-900">S/. {{ number_format($data['average_ticket'], 2) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <span class="font-medium {{ $data['conversion_rate'] >= 80 ? 'text-green-600' : ($data['conversion_rate'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ number_format($data['conversion_rate'], 1) }}%
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <span class="font-semibold text-cj-turquesa">S/. {{ number_format($data['seller_commission'], 2) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <span class="font-medium text-cj-rosa">S/. {{ number_format($data['wallet_balance'], 2) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <a href="{{ route('reports.performance', $data['seller']) }}"
                                           class="text-cj-morado-medio hover:text-cj-morado-profundo text-sm font-medium">
                                            Ver detalle →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                        No hay vendedores registrados
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($sellers->count() > 0)
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                        <div class="flex justify-between items-center text-sm text-gray-600">
                            <span>Total de vendedores: <strong>{{ $sellers->count() }}</strong></span>
                            <span>
                                Total vendido (sistema):
                                <strong class="text-cj-morado-profundo">S/. {{ number_format($sellers->sum('total_sales'), 2) }}</strong>
                            </span>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
