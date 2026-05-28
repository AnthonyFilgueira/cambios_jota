<x-app-layout>
    <!-- Fondo gradiente animado -->
    <div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
    <div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-cj-texto leading-tight">
            👋 Bienvenido, {{ Auth::user()->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Estadísticas rápidas -->
            <div class="grid md:grid-cols-4 gap-6 mb-6">
                <!-- Total Transacciones -->
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/50 p-6">
                    <div class="flex items-center justify-between mb-2">
                        <svg class="w-10 h-10 text-cj-morado-profundo" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-cj-morado-profundo">{{ $stats['total_transactions'] }}</p>
                    <p class="text-sm text-cj-texto-claro mt-1">Envíos totales</p>
                </div>

                <!-- Total Enviado PEN -->
                <div class="bg-gradient-to-br from-cj-morado-profundo to-cj-morado-medio text-white rounded-2xl shadow-2xl p-6">
                    <div class="flex items-center justify-between mb-2">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold">S/. {{ number_format($stats['total_amount_pen'], 2) }}</p>
                    <p class="text-sm opacity-90 mt-1">Total enviado</p>
                </div>

                <!-- Total Recibido VES -->
                <div class="bg-gradient-to-br from-cj-turquesa to-cj-rosa text-white rounded-2xl shadow-2xl p-6">
                    <div class="flex items-center justify-between mb-2">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold">Bs. {{ number_format($stats['total_amount_ves'], 2) }}</p>
                    <p class="text-sm opacity-90 mt-1">Total recibido</p>
                </div>

                <!-- Este mes -->
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/50 p-6">
                    <div class="flex items-center justify-between mb-2">
                        <svg class="w-10 h-10 text-cj-turquesa" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-cj-turquesa">{{ $stats['recent_count'] }}</p>
                    <p class="text-sm text-cj-texto-claro mt-1">Últimos 30 días</p>
                </div>
            </div>

            <!-- Acciones rápidas -->
            <div class="grid md:grid-cols-3 gap-6 mb-6">
                <!-- Iniciar Envío -->
                <a href="{{ route('transactions.create') }}" class="bg-gradient-to-br from-cj-rosa to-pink-600 text-white rounded-2xl p-6 shadow-xl hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold mb-2">Iniciar Envío</h4>
                    <p class="text-sm opacity-90">Cotiza y envía dinero ahora</p>
                </a>

                <!-- Mis Transacciones -->
                <a href="{{ route('transactions.index') }}" class="bg-white/90 backdrop-blur-lg rounded-2xl p-6 shadow-xl hover:shadow-2xl border border-white/50 transform hover:-translate-y-2 transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-4">
                        <svg class="w-12 h-12 text-cj-morado-profundo" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <svg class="w-6 h-6 text-cj-turquesa" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-cj-morado-profundo mb-2">Mis Transacciones</h4>
                    <p class="text-sm text-cj-texto-claro">Revisa el historial completo</p>
                </a>

                <!-- Mi Perfil -->
                <a href="{{ route('profile.edit') }}" class="bg-white/90 backdrop-blur-lg rounded-2xl p-6 shadow-xl hover:shadow-2xl border border-white/50 transform hover:-translate-y-2 transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-4">
                        <svg class="w-12 h-12 text-cj-turquesa" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <svg class="w-6 h-6 text-cj-morado-profundo" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-cj-morado-profundo mb-2">Mi Perfil</h4>
                    <p class="text-sm text-cj-texto-claro">Actualiza tu información</p>
                </a>
            </div>

            <!-- Últimas transacciones -->
            @if($transactions->count() > 0)
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/50 overflow-hidden">
                <div class="p-6 bg-gradient-to-r from-cj-morado-profundo/5 to-cj-turquesa/5 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-cj-morado-profundo">Últimas Transacciones</h3>
                            <p class="text-sm text-cj-texto-claro mt-1">Tus envíos más recientes</p>
                        </div>
                        <a href="{{ route('transactions.index') }}" class="text-sm font-semibold text-cj-morado-profundo hover:text-cj-turquesa transition-all">
                            Ver todas →
                        </a>
                    </div>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach($transactions->take(5) as $transaction)
                    <div class="p-4 hover:bg-gradient-to-r hover:from-cj-morado-profundo/5 hover:to-cj-turquesa/5 transition-all">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="bg-gradient-to-br from-cj-morado-profundo to-cj-turquesa rounded-xl p-3 shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-cj-texto-claro">{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
                                    @php
                                        $fromSymbol = $transaction->exchangeRate?->currencyPair?->fromCurrency?->symbol ?? 'S/';
                                        $toSymbol   = $transaction->exchangeRate?->currencyPair?->toCurrency?->symbol ?? 'Bs.';
                                    @endphp
                                    <p class="text-lg font-bold text-cj-morado-profundo">{{ $fromSymbol }} {{ number_format($transaction->amount_pen, 2) }}</p>
                                    <p class="text-sm text-cj-turquesa font-semibold">→ {{ $toSymbol }} {{ number_format($transaction->amount_ves, 2) }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                @php
                                    $statusConfig = [
                                        'pending' => ['label' => 'Pendiente', 'class' => 'bg-yellow-100 text-yellow-800 border border-yellow-200'],
                                        'processing' => ['label' => 'En proceso', 'class' => 'bg-blue-100 text-blue-800 border border-blue-200'],
                                        'completed' => ['label' => 'Completada', 'class' => 'bg-cj-turquesa/10 text-cj-turquesa border border-cj-turquesa/20'],
                                        'cancelled' => ['label' => 'Cancelada', 'class' => 'bg-gray-100 text-gray-600 border border-gray-200'],
                                    ];
                                    $config = $statusConfig[$transaction->status] ?? ['label' => $transaction->status, 'class' => 'bg-gray-100 text-gray-600'];
                                @endphp
                                <span class="inline-block px-4 py-2 rounded-xl text-xs font-bold {{ $config['class'] }}">
                                    {{ $config['label'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($transactions->total() > 5)
                <div class="p-4 bg-gray-50 text-center">
                    <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white rounded-xl font-semibold shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all">
                        Ver todas las transacciones
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
                @endif
            </div>
            @else
            <!-- Estado vacío -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/50 p-12 text-center">
                <div class="bg-gradient-to-br from-cj-morado-claro/30 to-cj-turquesa/20 rounded-full w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-12 h-12 text-cj-morado-medio" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <p class="text-cj-texto text-lg font-bold mb-2">No tienes transacciones aún</p>
                <p class="text-cj-texto-claro text-sm mb-6">Inicia tu primer envío ahora</p>
                <a href="{{ route('transactions.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-cj-rosa to-pink-600 text-white rounded-xl font-bold shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Iniciar Envío
                </a>
            </div>
            @endif
        </div>
    </div>

    <script>
        // Verificar si hay una transacción pendiente del simulador
        document.addEventListener('DOMContentLoaded', function() {
            const pendingTransaction = sessionStorage.getItem('pendingTransaction');

            if (pendingTransaction) {
                // Redirigir automáticamente a crear transacción
                window.location.href = '{{ route('transactions.create') }}';
            }
        });
    </script>
</x-app-layout>
