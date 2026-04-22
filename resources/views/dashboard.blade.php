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
            <!-- Card de bienvenida -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/50 p-8 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-cj-morado-profundo mb-2">¡Envía dinero a Venezuela fácil y rápido!</h3>
                        <p class="text-cj-texto-claro">Transferencias seguras con las mejores tasas del mercado</p>
                    </div>
                    <div class="hidden md:block">
                        <div class="w-24 h-24 bg-gradient-to-br from-cj-morado-profundo to-cj-turquesa rounded-full flex items-center justify-center shadow-xl">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
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
                    <p class="text-sm text-cj-texto-claro">Revisa el historial de envíos</p>
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

            <!-- Información útil -->
            <div class="grid md:grid-cols-2 gap-6">
                <!-- ¿Cómo funciona? -->
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/50 p-6">
                    <h4 class="text-lg font-bold text-cj-morado-profundo mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        ¿Cómo funciona?
                    </h4>
                    <ol class="space-y-3 text-sm text-cj-texto">
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-cj-turquesa text-white rounded-full flex items-center justify-center font-bold text-xs">1</span>
                            <span>Cotiza tu envío en USD, EUR o PEN</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-cj-turquesa text-white rounded-full flex items-center justify-center font-bold text-xs">2</span>
                            <span>Completa los datos bancarios del receptor en Venezuela</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-cj-turquesa text-white rounded-full flex items-center justify-center font-bold text-xs">3</span>
                            <span>Realiza la transferencia y sube el comprobante</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-cj-turquesa text-white rounded-full flex items-center justify-center font-bold text-xs">4</span>
                            <span>¡Listo! Tu familiar recibe el dinero en minutos</span>
                        </li>
                    </ol>
                </div>

                <!-- Beneficios -->
                <div class="bg-gradient-to-br from-cj-morado-profundo to-cj-morado-medio text-white rounded-2xl shadow-2xl p-6">
                    <h4 class="text-lg font-bold mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        ¿Por qué elegirnos?
                    </h4>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 flex-shrink-0 text-cj-turquesa" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Tasas competitivas actualizadas diariamente</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 flex-shrink-0 text-cj-turquesa" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Transferencias rápidas y seguras</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 flex-shrink-0 text-cj-turquesa" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Atención personalizada 24/7</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 flex-shrink-0 text-cj-turquesa" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Sin comisiones ocultas</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Verificar si hay una transacción pendiente del simulador
        document.addEventListener('DOMContentLoaded', function() {
            const pendingTransaction = sessionStorage.getItem('pendingTransaction');

            if (pendingTransaction) {
                // Redirigir automáticamente a crear transacción
                // El formulario leerá los datos del sessionStorage
                window.location.href = '{{ route('transactions.create') }}';
            }
        });
    </script>
</x-app-layout>
