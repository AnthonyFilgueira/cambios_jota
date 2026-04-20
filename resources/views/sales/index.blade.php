<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- Encabezado con paleta Cambio J -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Ventas Registradas</h1>
            <p class="text-gray-600 mt-2">Historial completo de ventas del sistema</p>
        </div>

        <!-- Tabla de ventas -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-purple-700 to-purple-600 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Fecha</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Vendedor</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Monto</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Estado</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Comisión Vendedor</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Comisión Jefe</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($sales as $sale)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-4 text-sm text-gray-700">{{ $sale->sale_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-4 text-sm font-medium text-gray-900">{{ $sale->seller->name }}</td>
                                <td class="px-4 py-4 text-sm font-semibold text-gray-900">S/. {{ number_format($sale->amount, 2) }}</td>
                                <td class="px-4 py-4">
                                    @if($sale->isApproved())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Aprobada
                                        </span>
                                    @elseif($sale->isRejected())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Rechazada
                                        </span>
                                    @elseif($sale->isPendingAdmin())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            Pendiente Admin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pendiente Vendedor
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm text-teal-600 font-medium">S/. {{ number_format($sale->sellerCommissionAmount(), 2) }}</td>
                                <td class="px-4 py-4 text-sm text-purple-600 font-medium">S/. {{ number_format($sale->bossCommissionAmount(), 2) }}</td>
                                <td class="px-4 py-4 text-center">
                                    <form action="{{ route('sales.destroy', $sale) }}" method="POST" onsubmit="return confirm('¿Eliminar esta venta?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-pink-500 hover:bg-pink-600 rounded-md transition-colors">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-500">No hay ventas registradas</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Paginación -->
        @if($sales->hasPages())
            <div class="mt-6">
                {{ $sales->links() }}
            </div>
        @endif
    </div>
</x-app-layout>