<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Detalle de Liquidación #{{ $liquidation->id }}</h1>
            <a href="{{ route('liquidations.index') }}" class="text-cj-turquesa hover:underline">
                ← Volver a lista
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header con monto -->
            <div class="bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white p-6">
                <div class="text-center">
                    <p class="text-sm opacity-90 mb-1">Monto Liquidado</p>
                    <p class="text-4xl font-bold">S/. {{ number_format($liquidation->amount, 2) }}</p>
                </div>
            </div>

            <!-- Detalles -->
            <div class="p-6 space-y-4">
                <!-- Vendedor -->
                <div class="border-b pb-4">
                    <p class="text-xs text-cj-texto-claro uppercase tracking-wider mb-1">Vendedor</p>
                    <p class="text-lg font-semibold text-cj-texto">{{ $liquidation->seller->name }}</p>
                    <p class="text-sm text-gray-600 font-mono">{{ $liquidation->seller->code }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Fecha de pago -->
                    <div>
                        <p class="text-xs text-cj-texto-claro uppercase tracking-wider mb-1">Fecha de Pago</p>
                        <p class="font-semibold">{{ $liquidation->payment_date->format('d/m/Y') }}</p>
                    </div>

                    <!-- Método de pago -->
                    <div>
                        <p class="text-xs text-cj-texto-claro uppercase tracking-wider mb-1">Método de Pago</p>
                        <span class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded font-semibold">
                            {{ $liquidation->paymentMethodLabel() }}
                        </span>
                    </div>
                </div>

                @if($liquidation->reference)
                    <div>
                        <p class="text-xs text-cj-texto-claro uppercase tracking-wider mb-1">Referencia / Comprobante</p>
                        <p class="font-mono text-cj-morado-profundo font-semibold">{{ $liquidation->reference }}</p>
                    </div>
                @endif

                @if($liquidation->notes)
                    <div>
                        <p class="text-xs text-cj-texto-claro uppercase tracking-wider mb-1">Notas</p>
                        <p class="text-gray-700">{{ $liquidation->notes }}</p>
                    </div>
                @endif

                <!-- Transacción de monedero -->
                @if($walletTransaction)
                    <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-cj-morado-profundo">
                        <p class="text-xs text-cj-texto-claro uppercase tracking-wider mb-2">Transacción de Monedero</p>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <span class="text-gray-600">Saldo anterior:</span>
                                <span class="font-semibold ml-1">S/. {{ number_format($walletTransaction->balance_after + $liquidation->amount, 2) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Saldo después:</span>
                                <span class="font-semibold text-cj-morado-profundo ml-1">S/. {{ number_format($walletTransaction->balance_after, 2) }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Info del registro -->
                <div class="border-t pt-4 text-sm text-gray-500">
                    <p>Registrado por: <span class="font-semibold">{{ $liquidation->creator->name ?? 'Sistema' }}</span></p>
                    <p>Fecha de registro: {{ $liquidation->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
