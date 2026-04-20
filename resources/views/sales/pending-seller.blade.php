<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- Encabezado con paleta Cambio J -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Ventas Pendientes de Validar</h1>
            <p class="text-gray-600 mt-2">Aprueba o rechaza las ventas asignadas a tu código de vendedor</p>
        </div>

        <!-- Mensajes flash -->
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Tabla de ventas pendientes -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-purple-700 to-purple-600 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Fecha</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Vendedor</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Monto</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Estado</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($sales as $sale)
                            <tr class="hover:bg-gray-50 transition-colors" x-data="{ showConfirm: false, action: '' }">
                                <td class="px-4 py-4 text-sm font-medium text-gray-900">#{{ $sale->id }}</td>
                                <td class="px-4 py-4 text-sm text-gray-700">{{ $sale->sale_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-4 text-sm text-gray-700">{{ $sale->seller->name }}</td>
                                <td class="px-4 py-4 text-sm font-semibold text-gray-900">S/. {{ number_format($sale->amount, 2) }}</td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        Pendiente
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Botón Aprobar -->
                                        <button
                                            @click="showConfirm = true; action = 'approve'"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-white bg-teal-500 hover:bg-teal-600 transition-colors shadow-sm"
                                        >
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Aprobar
                                        </button>

                                        <!-- Botón Rechazar -->
                                        <button
                                            @click="showConfirm = true; action = 'reject'"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-white bg-pink-500 hover:bg-pink-600 transition-colors shadow-sm"
                                        >
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Rechazar
                                        </button>
                                    </div>

                                    <!-- Modal de confirmación con Alpine.js -->
                                    <div
                                        x-show="showConfirm"
                                        x-cloak
                                        @click.away="showConfirm = false"
                                        class="fixed inset-0 z-50 overflow-y-auto"
                                        style="display: none;"
                                    >
                                        <div class="flex items-center justify-center min-h-screen px-4">
                                            <div class="fixed inset-0 bg-gray-900 opacity-75"></div>
                                            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                                                <h3 class="text-lg font-semibold mb-4" x-text="action === 'approve' ? '¿Aprobar esta venta?' : '¿Rechazar esta venta?'"></h3>
                                                <p class="text-gray-600 mb-6" x-text="action === 'approve' ? 'La venta será escalada al administrador para aprobación final.' : 'Esta acción no se puede deshacer.'"></p>
                                                <div class="flex gap-3 justify-end">
                                                    <button
                                                        @click="showConfirm = false"
                                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors"
                                                    >
                                                        Cancelar
                                                    </button>
                                                    <form :action="action === 'approve' ? '{{ route('sales.approve', $sale) }}' : '{{ route('sales.reject', $sale) }}'" method="POST" class="inline">
                                                        @csrf
                                                        <button
                                                            type="submit"
                                                            class="px-4 py-2 text-sm font-medium text-white rounded-md transition-colors"
                                                            :class="action === 'approve' ? 'bg-teal-500 hover:bg-teal-600' : 'bg-pink-500 hover:bg-pink-600'"
                                                            x-text="action === 'approve' ? 'Sí, aprobar' : 'Sí, rechazar'"
                                                        ></button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-500">No hay ventas pendientes de validar</p>
                                        <p class="text-sm text-gray-400 mt-1">Todas las ventas han sido procesadas</p>
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

    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>
