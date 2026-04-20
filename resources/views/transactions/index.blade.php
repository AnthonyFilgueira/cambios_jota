<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-cj-texto leading-tight">
            Mis Transacciones
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Widget de consumo acumulado -->
            <div class="mb-6">
                <div class="bg-gradient-to-br from-cj-morado-profundo to-cj-morado-medio rounded-2xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wider opacity-90 mb-1">Total enviado</p>
                            <p class="text-4xl font-bold">S/ {{ number_format($totalSpent, 2) }}</p>
                            <p class="text-sm opacity-75 mt-1">Soles peruanos</p>
                        </div>
                        <div class="bg-white/20 rounded-full p-4">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historial de transacciones -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-cj-texto">Historial de envíos</h3>
                    <p class="text-sm text-cj-texto-claro mt-1">{{ $transactions->count() }} transacciones</p>
                </div>

                @if($transactions->isEmpty())
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-500 text-lg font-medium">No tienes transacciones aún</p>
                        <p class="text-gray-400 text-sm mt-2">Inicia tu primer envío desde el simulador</p>
                    </div>
                @else
                    <!-- Desktop -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-cj-texto-claro uppercase tracking-wider">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-cj-texto-claro uppercase tracking-wider">Enviado (PEN)</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-cj-texto-claro uppercase tracking-wider">Recibido (VES)</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-cj-texto-claro uppercase tracking-wider">Tasa</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-cj-texto-claro uppercase tracking-wider">Vendedor</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-cj-texto-claro uppercase tracking-wider">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($transactions as $transaction)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-cj-texto">
                                        {{ $transaction->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-cj-morado-profundo">
                                        S/ {{ number_format($transaction->amount_pen, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-cj-turquesa">
                                        Bs. {{ number_format($transaction->amount_ves, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-cj-texto-claro font-mono">
                                        {{ number_format($transaction->exchangeRate->ves_rate, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-cj-texto">
                                        {{ $transaction->seller ? $transaction->seller->name : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusConfig = [
                                                'pending' => ['label' => 'Pendiente', 'class' => 'bg-cj-rosa/10 text-cj-rosa'],
                                                'processing' => ['label' => 'En proceso', 'class' => 'bg-yellow-100 text-yellow-800'],
                                                'completed' => ['label' => 'Completado', 'class' => 'bg-cj-turquesa/10 text-cj-turquesa'],
                                                'cancelled' => ['label' => 'Cancelado', 'class' => 'bg-gray-100 text-gray-600'],
                                            ];
                                            $config = $statusConfig[$transaction->status] ?? ['label' => $transaction->status, 'class' => 'bg-gray-100 text-gray-600'];
                                        @endphp
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $config['class'] }}">
                                            {{ $config['label'] }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile -->
                    <div class="md:hidden divide-y divide-gray-100">
                        @foreach($transactions as $transaction)
                        <div class="p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <p class="text-xs text-cj-texto-claro">{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
                                    <p class="text-lg font-bold text-cj-morado-profundo mt-1">S/ {{ number_format($transaction->amount_pen, 2) }}</p>
                                </div>
                                @php
                                    $statusConfig = [
                                        'pending' => ['label' => 'Pendiente', 'class' => 'bg-cj-rosa/10 text-cj-rosa'],
                                        'processing' => ['label' => 'En proceso', 'class' => 'bg-yellow-100 text-yellow-800'],
                                        'completed' => ['label' => 'Completado', 'class' => 'bg-cj-turquesa/10 text-cj-turquesa'],
                                        'cancelled' => ['label' => 'Cancelado', 'class' => 'bg-gray-100 text-gray-600'],
                                    ];
                                    $config = $statusConfig[$transaction->status] ?? ['label' => $transaction->status, 'class' => 'bg-gray-100 text-gray-600'];
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $config['class'] }}">
                                    {{ $config['label'] }}
                                </span>
                            </div>
                            <div class="bg-cj-morado-claro/30 rounded-lg p-3">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-cj-texto-claro">Tu familiar recibe</span>
                                    <span class="font-semibold text-cj-turquesa">Bs. {{ number_format($transaction->amount_ves, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between text-xs mt-2">
                                    <span class="text-cj-texto-claro">Tasa aplicada</span>
                                    <span class="font-mono text-cj-texto-claro">{{ number_format($transaction->exchangeRate->ves_rate, 2) }} VES/PEN</span>
                                </div>
                                @if($transaction->seller)
                                <div class="flex items-center justify-between text-xs mt-1">
                                    <span class="text-cj-texto-claro">Vendedor</span>
                                    <span class="text-cj-texto">{{ $transaction->seller->name }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
