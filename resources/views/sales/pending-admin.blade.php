<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- Encabezado con paleta Cambio J -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Cola de Aprobación Administrativa</h1>
            <p class="text-gray-600 mt-2">Ventas escaladas por vendedores esperando aprobación final</p>
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

        <!-- Contador de ventas pendientes -->
        @if($sales->total() > 0)
            <div class="mb-4 p-4 bg-purple-50 border-l-4 border-purple-600 rounded">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-purple-800 font-medium">
                        {{ $sales->total() }} {{ $sales->total() === 1 ? 'venta pendiente' : 'ventas pendientes' }} de aprobación
                    </span>
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
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Comisiones</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Estado</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($sales as $sale)
                            <tr class="hover:bg-gray-50 transition-colors" x-data="{ showConfirm: false, action: '', showObserve: false, observation: '' }">
                                <td class="px-4 py-4 text-sm font-medium text-gray-900">#{{ $sale->id }}</td>
                                <td class="px-4 py-4 text-sm text-gray-700">{{ $sale->sale_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $sale->seller->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $sale->seller->email }}</div>
                                </td>
                                <td class="px-4 py-4 text-sm font-semibold text-gray-900">S/. {{ number_format($sale->amount, 2) }}</td>
                                <td class="px-4 py-4 text-sm">
                                    <div class="text-teal-600 font-medium">Vendedor: S/. {{ number_format($sale->sellerCommissionAmount(), 2) }}</div>
                                    <div class="text-purple-600 font-medium">Jefe: S/. {{ number_format($sale->bossCommissionAmount(), 2) }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                        Escalada
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Botón Aprobar Final -->
                                        <button
                                            @click="showConfirm = true; action = 'approve'"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-white bg-teal-500 hover:bg-teal-600 transition-colors shadow-sm"
                                        >
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Aprobar
                                        </button>

                                        <!-- Botón Observar -->
                                        <button
                                            @click="showObserve = true"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-white bg-orange-500 hover:bg-orange-600 transition-colors shadow-sm"
                                        >
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                            Solicitar Info
                                        </button>

                                        <!-- Botón Rechazar -->
                                        <button
                                            @click="showConfirm = true; action = 'reject'"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-white bg-pink-500 hover:bg-pink-600 transition-colors shadow-sm"
                                        >
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
                                                <h3 class="text-lg font-semibold mb-4" x-text="action === 'approve' ? '¿Aprobar definitivamente esta venta?' : '¿Rechazar esta venta?'"></h3>
                                                <p class="text-gray-600 mb-2" x-text="action === 'approve' ? 'Esta será la aprobación FINAL. La venta quedará completada.' : 'Esta acción no se puede deshacer. La venta será rechazada definitivamente.'"></p>
                                                <div x-show="action === 'approve'" class="mb-6 p-3 bg-gray-50 rounded text-sm">
                                                    <div class="font-medium text-gray-700 mb-1">Detalles de la venta:</div>
                                                    <div class="text-gray-600">Monto: <span class="font-semibold">S/. {{ number_format($sale->amount, 2) }}</span></div>
                                                    <div class="text-gray-600">Vendedor: <span class="font-semibold">{{ $sale->seller->name }}</span></div>
                                                </div>
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

                                    <!-- Modal de observación -->
                                    <div
                                        x-show="showObserve"
                                        x-cloak
                                        @click.away="showObserve = false"
                                        class="fixed inset-0 z-50 overflow-y-auto"
                                        style="display: none;"
                                    >
                                        <div class="flex items-center justify-center min-h-screen px-4">
                                            <div class="fixed inset-0 bg-gray-900 opacity-75"></div>
                                            <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
                                                <h3 class="text-lg font-semibold mb-4 text-orange-600">Solicitar Información Adicional</h3>
                                                <p class="text-gray-600 mb-4">Especifica qué información necesitas o qué debe corregir el vendedor:</p>

                                                <form action="{{ route('sales.observe', $sale) }}" method="POST">
                                                    @csrf
                                                    <div class="mb-4">
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">Observación</label>
                                                        <textarea
                                                            name="observation"
                                                            x-model="observation"
                                                            rows="4"
                                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                                            placeholder="Ej: Falta el DNI del cliente, la fecha de venta no coincide, etc."
                                                            required
                                                            minlength="10"
                                                            maxlength="1000"
                                                        ></textarea>
                                                        <p class="mt-1 text-xs text-gray-500">Mínimo 10 caracteres, máximo 1000</p>
                                                    </div>

                                                    <div class="bg-orange-50 border-l-4 border-orange-400 p-3 mb-4">
                                                        <div class="flex">
                                                            <svg class="w-5 h-5 text-orange-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                            </svg>
                                                            <p class="text-sm text-orange-700">La venta volverá al vendedor para que realice las correcciones necesarias.</p>
                                                        </div>
                                                    </div>

                                                    <div class="flex gap-3 justify-end">
                                                        <button
                                                            type="button"
                                                            @click="showObserve = false; observation = ''"
                                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors"
                                                        >
                                                            Cancelar
                                                        </button>
                                                        <button
                                                            type="submit"
                                                            class="px-4 py-2 text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 rounded-md transition-colors"
                                                            :disabled="observation.length < 10"
                                                            :class="observation.length < 10 ? 'opacity-50 cursor-not-allowed' : ''"
                                                        >
                                                            Enviar Observación
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-500">No hay ventas pendientes de aprobación</p>
                                        <p class="text-sm text-gray-400 mt-1">Todas las ventas escaladas han sido procesadas</p>
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
