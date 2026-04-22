<x-app-layout>
    <!-- Fondo gradiente animado -->
    <div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
    <div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>
    <div class="fixed top-20 left-10 w-72 h-72 bg-purple-400/30 rounded-full blur-3xl animate-float"></div>
    <div class="fixed bottom-20 right-10 w-96 h-96 bg-teal-400/30 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            👤 Mi Dashboard
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Bienvenida -->
            <div class="mb-6 bg-white/90 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/50 p-6">
                <h3 class="text-2xl font-bold text-cj-morado-profundo mb-2">Bienvenido, {{ $user->name }}!</h3>
                <p class="text-gray-600">Aquí puedes ver tu historial de transacciones.</p>
            </div>

            <!-- Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-gradient-to-br from-cj-morado-profundo to-cj-morado-medio text-white rounded-2xl shadow-2xl p-6">
                    <h3 class="text-sm font-medium opacity-90 mb-2">Total de Transacciones</h3>
                    <p class="text-3xl font-bold">{{ $stats['total_transactions'] }}</p>
                </div>

                <div class="bg-gradient-to-br from-cj-turquesa to-teal-500 text-white rounded-2xl shadow-2xl p-6">
                    <h3 class="text-sm font-medium opacity-90 mb-2">Monto Total</h3>
                    <p class="text-3xl font-bold">S/. {{ number_format($stats['total_amount'], 2) }}</p>
                </div>

                <div class="bg-gradient-to-br from-cj-rosa to-pink-500 text-white rounded-2xl shadow-2xl p-6">
                    <h3 class="text-sm font-medium opacity-90 mb-2">Últimos 30 días</h3>
                    <p class="text-3xl font-bold">{{ $stats['recent_count'] }}</p>
                </div>
            </div>

            <!-- Transacciones -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/50">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">📋 Mis Transacciones</h3>
                </div>
                <div class="p-6">
                    @if($transactions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="text-xs text-gray-500 uppercase">
                                    <tr>
                                        <th class="text-left pb-3">Fecha</th>
                                        <th class="text-left pb-3">Descripción</th>
                                        <th class="text-right pb-3">Monto</th>
                                        <th class="text-center pb-3">Estado</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm">
                                    @foreach($transactions as $transaction)
                                        <tr class="border-t border-gray-100">
                                            <td class="py-3">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="py-3">{{ $transaction->description ?? 'Transacción' }}</td>
                                            <td class="py-3 text-right font-semibold text-cj-morado-profundo">
                                                S/. {{ number_format($transaction->amount, 2) }}
                                            </td>
                                            <td class="py-3 text-center">
                                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Completada</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="mt-4">
                            {{ $transactions->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <p class="text-gray-500">No tienes transacciones registradas aún.</p>
                            <a href="/" class="mt-4 inline-block px-6 py-3 bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white rounded-lg hover:opacity-90">
                                Iniciar mi primer envío
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
