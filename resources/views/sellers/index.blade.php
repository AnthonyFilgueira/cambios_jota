<x-app-layout>
    <!-- Fondo animado con gradiente -->
    <div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
    <div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">👥 Gestión de Vendedores</h1>
                <p class="text-gray-600">Métricas, monedero y rendimiento de tu equipo de ventas</p>
            </div>
            <a href="{{ route('sellers.create') }}"
               class="bg-gradient-to-r from-purple-600 to-purple-700 text-white px-6 py-3 rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all">
                ➕ Nuevo Vendedor
            </a>
        </div>

        <!-- Cards de Vendedores -->
        <div class="space-y-6">
            @forelse ($sellers as $seller)
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/50 p-6 hover:shadow-3xl transition-all">

                    <!-- Header del Vendedor -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 pb-4 border-b border-gray-200">
                        <div class="mb-4 md:mb-0">
                            <h3 class="text-2xl font-bold text-gray-800 mb-2">{{ $seller->name }}</h3>
                            <span class="inline-block bg-gradient-to-r from-purple-600 to-purple-700 text-white font-mono text-sm px-4 py-2 rounded-lg shadow">
                                {{ $seller->code }}
                            </span>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('wallet.index') }}"
                               class="bg-gradient-to-r from-teal-500 to-teal-600 text-white px-4 py-2 rounded-lg shadow hover:shadow-lg transform hover:-translate-y-0.5 transition-all text-sm font-semibold">
                                💰 Ver Monedero
                            </a>
                            <a href="{{ route('sellers.commissions', $seller) }}"
                               class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-2 rounded-lg shadow hover:shadow-lg transform hover:-translate-y-0.5 transition-all text-sm font-semibold">
                                💸 Comisiones
                            </a>
                            <a href="{{ route('reports.performance', $seller) }}"
                               class="bg-gradient-to-r from-pink-500 to-pink-600 text-white px-4 py-2 rounded-lg shadow hover:shadow-lg transform hover:-translate-y-0.5 transition-all text-sm font-semibold">
                                📊 Ver Reportes
                            </a>
                            <a href="{{ route('sellers.edit', $seller) }}"
                               class="bg-gradient-to-r from-purple-500 to-purple-600 text-white px-4 py-2 rounded-lg shadow hover:shadow-lg transform hover:-translate-y-0.5 transition-all text-sm font-semibold">
                                ✏️ Editar
                            </a>
                            <form action="{{ route('sellers.destroy', $seller) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('¿Estás seguro de eliminar este vendedor? Esta acción no se puede deshacer.')"
                                        class="bg-gradient-to-r from-red-500 to-red-600 text-white px-4 py-2 rounded-lg shadow hover:shadow-lg transform hover:-translate-y-0.5 transition-all text-sm font-semibold">
                                    🗑️ Eliminar
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Métricas del Vendedor -->
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-4">
                        <!-- Saldo Monedero -->
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-xl border border-purple-200 shadow-sm hover:shadow-md transition-shadow">
                            <p class="text-xs font-semibold text-purple-600 mb-1 uppercase tracking-wide">💰 Saldo Monedero</p>
                            <p class="text-2xl font-bold text-purple-700">
                                S/. {{ number_format($seller->walletBalance(), 2) }}
                            </p>
                        </div>

                        <!-- Total Vendido -->
                        <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-xl border border-green-200 shadow-sm hover:shadow-md transition-shadow">
                            <p class="text-xs font-semibold text-green-600 mb-1 uppercase tracking-wide">💵 Total Vendido</p>
                            <p class="text-2xl font-bold text-green-700">
                                S/. {{ number_format($seller->totalSales(), 2) }}
                            </p>
                        </div>

                        <!-- Cantidad Ventas -->
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-xl border border-blue-200 shadow-sm hover:shadow-md transition-shadow">
                            <p class="text-xs font-semibold text-blue-600 mb-1 uppercase tracking-wide">📦 Cantidad Ventas</p>
                            <p class="text-2xl font-bold text-blue-700">
                                {{ $seller->salesCount() }}
                            </p>
                        </div>

                        <!-- Comisiones Ganadas -->
                        <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-xl border border-orange-200 shadow-sm hover:shadow-md transition-shadow">
                            <p class="text-xs font-semibold text-orange-600 mb-1 uppercase tracking-wide">💸 Comisiones</p>
                            <p class="text-2xl font-bold text-orange-700">
                                S/. {{ number_format($seller->totalCommissionsEarned(), 2) }}
                            </p>
                        </div>

                        <!-- Ticket Promedio -->
                        <div class="bg-gradient-to-br from-teal-50 to-teal-100 p-4 rounded-xl border border-teal-200 shadow-sm hover:shadow-md transition-shadow">
                            <p class="text-xs font-semibold text-teal-600 mb-1 uppercase tracking-wide">🎯 Ticket Promedio</p>
                            <p class="text-2xl font-bold text-teal-700">
                                S/. {{ number_format($seller->averageTicket(), 2) }}
                            </p>
                        </div>
                    </div>

                    <!-- Footer - Configuración de Comisiones -->
                    <div class="flex items-center justify-between text-sm text-gray-600 bg-gray-50 rounded-lg p-3">
                        <div class="flex items-center gap-4">
                            <span class="flex items-center">
                                <span class="font-semibold text-gray-700 mr-1">Comisión Vendedor:</span>
                                <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded font-bold">{{ $seller->seller_commission }}%</span>
                            </span>
                            <span class="text-gray-300">|</span>
                            <span class="flex items-center">
                                <span class="font-semibold text-gray-700 mr-1">Comisión Jefe:</span>
                                <span class="bg-pink-100 text-pink-700 px-2 py-1 rounded font-bold">{{ $seller->boss_commission }}%</span>
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/50 p-12 text-center">
                    <p class="text-gray-500 text-lg mb-4">👤 No hay vendedores registrados</p>
                    <a href="{{ route('sellers.create') }}"
                       class="inline-block bg-gradient-to-r from-purple-600 to-purple-700 text-white px-6 py-3 rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all">
                        ➕ Crear Primer Vendedor
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Paginación (si existe) -->
        @if(method_exists($sellers, 'links'))
            <div class="mt-8">
                {{ $sellers->links() }}
            </div>
        @endif
    </div>
</x-app-layout>