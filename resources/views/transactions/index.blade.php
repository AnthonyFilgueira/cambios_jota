<x-app-layout>
    <!-- Fondo gradiente animado -->
    <div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
    <div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-cj-texto leading-tight">
            💰 Mis Transacciones
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Widget de consumo acumulado -->
            <div class="mb-6">
                <div class="bg-gradient-to-br from-cj-morado-profundo to-cj-morado-medio rounded-2xl shadow-2xl p-6 text-white border border-white/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wider opacity-90 mb-1">Total enviado</p>
                            <p class="text-4xl font-bold">S/ {{ number_format($totalSpent, 2) }}</p>
                            <p class="text-sm opacity-75 mt-1">Soles peruanos</p>
                        </div>
                        <div class="bg-white/20 backdrop-blur-lg rounded-full p-4 shadow-lg">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historial de transacciones -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/50 overflow-hidden" x-data="{ expandedId: null }">
                <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-cj-morado-profundo/5 to-cj-turquesa/5">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-cj-morado-profundo">Historial de envíos</h3>
                            <p class="text-sm text-cj-texto-claro mt-1">{{ $transactions->count() }} transacciones registradas</p>
                        </div>
                        <a href="{{ route('transactions.create') }}" class="px-4 py-2 bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white rounded-xl text-sm font-semibold shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all">
                            + Nuevo Envío
                        </a>
                    </div>
                </div>

                @if($transactions->isEmpty())
                    <div class="p-12 text-center">
                        <div class="bg-gradient-to-br from-cj-morado-claro/30 to-cj-turquesa/20 rounded-full w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-12 h-12 text-cj-morado-medio" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <p class="text-cj-texto text-lg font-bold">No tienes transacciones aún</p>
                        <p class="text-cj-texto-claro text-sm mt-2 mb-4">Inicia tu primer envío ahora</p>
                        <a href="{{ route('transactions.create') }}" class="inline-block px-6 py-3 bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white rounded-xl font-semibold shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all">
                            Crear Envío
                        </a>
                    </div>
                @else
                    <!-- Tarjetas de transacciones -->
                    <div class="divide-y divide-gray-100">
                        @foreach($transactions as $transaction)
                        <div class="p-4 hover:bg-gradient-to-r hover:from-cj-morado-profundo/5 hover:to-cj-turquesa/5 transition-all">
                            <!-- Resumen -->
                            <div class="cursor-pointer" @click="expandedId = expandedId === {{ $transaction->id }} ? null : {{ $transaction->id }}">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-4">
                                        <div class="bg-gradient-to-br from-cj-morado-profundo to-cj-turquesa rounded-xl p-3 shadow-lg">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-cj-texto-claro">{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
                                            <p class="text-xl font-bold text-cj-morado-profundo">S/ {{ number_format($transaction->amount_pen, 2) }}</p>
                                            <p class="text-sm text-cj-turquesa font-semibold">→ Bs. {{ number_format($transaction->amount_ves, 2) }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @php
                                            $statusConfig = [
                                                'pending' => ['label' => 'Pendiente', 'class' => 'bg-cj-rosa/10 text-cj-rosa border border-cj-rosa/20'],
                                                'observed' => ['label' => '⚠ Con observaciones', 'class' => 'bg-orange-100 text-orange-800 border border-orange-300'],
                                                'processing' => ['label' => 'En proceso', 'class' => 'bg-yellow-100 text-yellow-800 border border-yellow-200'],
                                                'completed' => ['label' => '✓ Completado', 'class' => 'bg-cj-turquesa/10 text-cj-turquesa border border-cj-turquesa/20'],
                                                'cancelled' => ['label' => 'Cancelado', 'class' => 'bg-gray-100 text-gray-600 border border-gray-200'],
                                            ];
                                            $config = $statusConfig[$transaction->status] ?? ['label' => $transaction->status, 'class' => 'bg-gray-100 text-gray-600'];
                                        @endphp
                                        <span class="inline-block px-4 py-2 rounded-xl text-xs font-bold {{ $config['class'] }} mb-2">
                                            {{ $config['label'] }}
                                        </span>
                                        @if($transaction->status === 'observed')
                                        <p class="text-xs text-orange-600 font-semibold">Requiere atención</p>
                                        @endif
                                        <p class="text-xs text-cj-texto-claro">
                                            <span x-show="expandedId !== {{ $transaction->id }}">Ver detalles ▼</span>
                                            <span x-show="expandedId === {{ $transaction->id }}">Ocultar ▲</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Detalles expandibles -->
                            <div x-show="expandedId === {{ $transaction->id }}" x-collapse class="mt-4">
                                <div class="grid md:grid-cols-2 gap-4 p-4 bg-gray-50/50 rounded-xl">
                                    <!-- Datos del envío -->
                                    <div class="space-y-3">
                                        <h5 class="font-bold text-cj-morado-profundo text-sm flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                            </svg>
                                            Datos del Envío
                                        </h5>
                                        <div class="space-y-2 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-cj-texto-claro">Tasa aplicada:</span>
                                                <span class="font-mono font-semibold text-cj-texto">{{ number_format($transaction->exchangeRate->ves_rate, 2) }} Bs/PEN</span>
                                            </div>
                                            @if($transaction->usd_bcv_rate)
                                            <div class="flex justify-between">
                                                <span class="text-cj-texto-claro">Tasa BCV USD:</span>
                                                <span class="font-mono text-cj-texto">{{ number_format($transaction->usd_bcv_rate, 2) }}</span>
                                            </div>
                                            @endif
                                            @if($transaction->eur_bcv_rate)
                                            <div class="flex justify-between">
                                                <span class="text-cj-texto-claro">Tasa BCV EUR:</span>
                                                <span class="font-mono text-cj-texto">{{ number_format($transaction->eur_bcv_rate, 2) }}</span>
                                            </div>
                                            @endif
                                            @if($transaction->seller)
                                            <div class="flex justify-between">
                                                <span class="text-cj-texto-claro">Vendedor:</span>
                                                <span class="font-semibold text-cj-morado-profundo">{{ $transaction->seller->name }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Datos bancarios receptor (Venezuela) -->
                                    @if($transaction->recipient_bank)
                                    <div class="space-y-3">
                                        <h5 class="font-bold text-cj-morado-profundo text-sm flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                            </svg>
                                            Receptor (Venezuela)
                                        </h5>
                                        <div class="space-y-2 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-cj-texto-claro">Banco:</span>
                                                <span class="font-semibold text-cj-texto">{{ $transaction->recipient_bank }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-cj-texto-claro">Cuenta:</span>
                                                <span class="font-mono text-cj-texto">{{ $transaction->recipient_account_number }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-cj-texto-claro">Cédula:</span>
                                                <span class="font-mono text-cj-texto">{{ $transaction->recipient_dni }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-cj-texto-claro">Tipo:</span>
                                                <span class="capitalize text-cj-texto">{{ $transaction->recipient_account_type }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Datos bancarios origen (Perú) -->
                                    @if($transaction->sender_bank)
                                    <div class="space-y-3">
                                        <h5 class="font-bold text-cj-morado-profundo text-sm flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                            </svg>
                                            Origen (Perú)
                                        </h5>
                                        <div class="space-y-2 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-cj-texto-claro">Banco:</span>
                                                <span class="font-semibold text-cj-texto">{{ $transaction->sender_bank }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-cj-texto-claro">Cuenta:</span>
                                                <span class="font-mono text-cj-texto">{{ $transaction->sender_account_number }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Comprobante -->
                                    @if($transaction->voucher)
                                    <div class="space-y-3">
                                        <h5 class="font-bold text-cj-morado-profundo text-sm flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Comprobante
                                        </h5>
                                        <a href="{{ asset('storage/' . $transaction->voucher) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-cj-turquesa/10 hover:bg-cj-turquesa/20 text-cj-turquesa rounded-lg transition-all text-sm font-semibold">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Ver comprobante
                                        </a>
                                    </div>
                                    @endif

                                    <!-- Observaciones -->
                                    @if($transaction->observation)
                                    <div class="space-y-3 md:col-span-2">
                                        <h5 class="font-bold text-orange-600 text-sm flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                            </svg>
                                            ⚠️ Observaciones
                                        </h5>
                                        <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded-lg">
                                            <p class="text-sm text-orange-800 font-semibold">{{ $transaction->observation }}</p>
                                            <p class="text-xs text-orange-600 mt-2">Por favor, revisa esta observación y contacta al vendedor si es necesario.</p>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Notas -->
                                    @if($transaction->notes)
                                    <div class="space-y-3 md:col-span-2">
                                        <h5 class="font-bold text-cj-morado-profundo text-sm">Notas</h5>
                                        <p class="text-sm text-cj-texto bg-white p-3 rounded-lg italic">{{ $transaction->notes }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
