<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- Encabezado con paleta Cambio J -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Ventas Aprobadas - Pendientes de Comprobante</h1>
            <p class="text-gray-600 mt-2">Cargar comprobante de transferencia para finalizar</p>
        </div>

        <!-- Mensajes flash -->
        <x-notifications />

        <!-- Contador de ventas -->
        @if($sales->total() > 0)
            <div class="mb-4 p-4 bg-teal-50 border-l-4 border-teal-500 rounded">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-teal-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-teal-800 font-medium">
                        {{ $sales->total() }} {{ $sales->total() === 1 ? 'venta aprobada' : 'ventas aprobadas' }} esperando comprobante
                    </span>
                </div>
            </div>
        @endif

        <!-- Tabla de ventas aprobadas -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-teal-600 to-teal-500 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Fecha</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Vendedor</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Monto</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Comisiones</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($sales as $sale)
                            <tr class="hover:bg-teal-50 transition-colors" x-data="{ showUpload: false }">
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
                                    <div class="flex items-center justify-center">
                                        <button
                                            @click="showUpload = true"
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 transition-colors shadow-sm"
                                        >
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                            Cargar Comprobante
                                        </button>
                                    </div>

                                    <!-- Modal de carga de comprobante -->
                                    <div
                                        x-show="showUpload"
                                        x-cloak
                                        @click.away="showUpload = false"
                                        class="fixed inset-0 z-50 overflow-y-auto"
                                        style="display: none;"
                                    >
                                        <div class="flex items-center justify-center min-h-screen px-4">
                                            <div class="fixed inset-0 bg-gray-900 opacity-75"></div>
                                            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                                                <h3 class="text-lg font-semibold mb-4 text-purple-600">Cargar Comprobante de Transferencia</h3>
                                                <p class="text-gray-600 mb-4">Sube el comprobante de la transferencia realizada (JPG, PNG o PDF, máx 5MB)</p>

                                                <form action="{{ route('sales.uploadVoucher', $sale) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="mb-4">
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">Archivo del comprobante</label>
                                                        <input
                                                            type="file"
                                                            name="voucher"
                                                            accept=".jpg,.jpeg,.png,.pdf"
                                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100"
                                                            required
                                                        >
                                                        <p class="mt-1 text-xs text-gray-500">Formatos: JPG, PNG, PDF (máx 5MB)</p>
                                                    </div>

                                                    <div class="bg-teal-50 border-l-4 border-teal-400 p-3 mb-4">
                                                        <div class="flex">
                                                            <svg class="w-5 h-5 text-teal-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                            </svg>
                                                            <p class="text-sm text-teal-700">Al cargar el comprobante, la venta se marcará como <strong>Completada</strong>.</p>
                                                        </div>
                                                    </div>

                                                    <div class="flex gap-3 justify-end">
                                                        <button
                                                            type="button"
                                                            @click="showUpload = false"
                                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors"
                                                        >
                                                            Cancelar
                                                        </button>
                                                        <button
                                                            type="submit"
                                                            class="px-4 py-2 text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 rounded-md transition-colors"
                                                        >
                                                            Cargar y Completar
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
                                <td colspan="6" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-500">No hay ventas aprobadas pendientes</p>
                                        <p class="text-sm text-gray-400 mt-1">Todas las ventas tienen comprobante cargado</p>
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
