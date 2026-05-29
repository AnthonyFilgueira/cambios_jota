<x-app-layout>
    <div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
    <div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-cj-texto leading-tight">Confirmación de envío</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-lg mx-auto px-4 sm:px-6 space-y-6">

            <!-- Hero de éxito -->
            <div class="bg-white/90 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/50 p-8 text-center">
                <!-- Círculo con check animado -->
                <div class="w-24 h-24 bg-gradient-to-br from-cj-turquesa to-teal-400 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-teal-400/30">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <h1 class="text-2xl font-bold text-cj-texto mb-2">¡Solicitud enviada!</h1>
                <p class="text-cj-texto-claro text-sm mb-6">Tu comprobante fue recibido. El vendedor lo revisará pronto.</p>

                <!-- Número de seguimiento -->
                <div class="bg-cj-morado-claro border border-cj-morado-profundo/20 rounded-2xl px-6 py-4 mb-6">
                    <p class="text-xs font-semibold text-cj-texto-claro uppercase tracking-widest mb-1">Número de seguimiento</p>
                    <p class="text-3xl font-bold font-mono text-cj-morado-profundo">#TRX-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</p>
                </div>

                <!-- Resumen de montos -->
                @php
                    $fromCurrency = $transaction->exchangeRate?->currencyPair?->fromCurrency;
                    $toCurrency   = $transaction->exchangeRate?->currencyPair?->toCurrency;
                    $fromSymbol   = $fromCurrency?->symbol ?? 'S/';
                    $toSymbol     = $toCurrency?->symbol   ?? 'Bs.';
                    $fromFlag     = $fromCurrency?->flag_emoji ?? '';
                    $toFlag       = $toCurrency?->flag_emoji   ?? '';
                    $toCountry    = $toCurrency?->country ?? 'destino';
                @endphp
                <div class="grid grid-cols-2 gap-3 mb-6">
                    <div class="bg-gradient-to-br from-cj-morado-profundo to-cj-morado-medio rounded-2xl p-4 text-white text-left">
                        <p class="text-xs opacity-80 font-medium mb-1">{{ $fromFlag }} Tú enviaste</p>
                        <p class="text-xl font-bold font-mono">{{ $fromSymbol }} {{ number_format($transaction->amount_pen, 2) }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-cj-turquesa to-teal-400 rounded-2xl p-4 text-white text-left">
                        <p class="text-xs opacity-80 font-medium mb-1">{{ $toFlag }} Tu familiar recibe</p>
                        <p class="text-xl font-bold font-mono">{{ $toSymbol }} {{ number_format($transaction->amount_ves, 2) }}</p>
                    </div>
                </div>

                <!-- Número de operación -->
                @if($transaction->operation_number)
                <div class="bg-gray-50 rounded-xl px-4 py-3 mb-4 text-left">
                    <p class="text-xs text-cj-texto-claro font-medium">Nº de operación bancaria</p>
                    <p class="font-bold text-cj-texto font-mono">{{ $transaction->operation_number }}</p>
                </div>
                @endif

                <!-- Vendedor asignado -->
                @if($transaction->seller)
                <div class="flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-3 mb-6 text-left">
                    <div class="w-10 h-10 bg-gradient-to-br from-cj-morado-profundo to-cj-morado-medio rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                        {{ strtoupper(substr($transaction->seller->name, 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-xs text-cj-texto-claro font-medium">Vendedor asignado</p>
                        <p class="font-bold text-cj-texto">{{ $transaction->seller->name }}</p>
                    </div>
                    <div class="ml-auto">
                        <span class="bg-cj-turquesa/10 text-cj-turquesa border border-cj-turquesa/20 rounded-full px-3 py-1 text-xs font-bold font-mono">
                            {{ $transaction->seller->code }}
                        </span>
                    </div>
                </div>
                @endif

                <!-- CTAs -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('transactions.index') }}"
                       class="flex-1 px-4 py-3 bg-white border-2 border-cj-morado-profundo/20 text-cj-morado-profundo rounded-xl font-semibold text-sm text-center hover:bg-cj-morado-claro transition-all">
                        Ver mis envíos
                    </a>
                    <a href="{{ route('transactions.create') }}"
                       class="flex-1 px-4 py-3 bg-gradient-to-r from-cj-rosa to-pink-600 text-white rounded-xl font-semibold text-sm text-center hover:opacity-90 transition-all shadow-lg shadow-pink-400/30">
                        Nuevo envío
                    </a>
                </div>
            </div>

            <!-- Timeline del proceso -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-6">
                <h3 class="font-bold text-cj-texto mb-5">¿Qué pasa ahora?</h3>

                @php
                $steps = [
                    ['icon' => '📤', 'title' => 'Solicitud enviada',       'desc' => 'Tu comprobante fue recibido.',                              'done' => true],
                    ['icon' => '🔍', 'title' => 'Revisión del operador',   'desc' => 'El operador verifica tu comprobante.',                      'done' => false],
                    ['icon' => '✅', 'title' => 'Aprobación',              'desc' => 'Se aprueba la operación.',                                  'done' => false],
                    ['icon' => '💸', 'title' => 'Transferencia realizada', 'desc' => 'Se ejecuta el envío a ' . ucfirst($toCountry) . '.',        'done' => false],
                    ['icon' => '🎉', 'title' => 'Completado',              'desc' => 'Tu familiar recibe el dinero ' . ($toFlag ? $toFlag : '') . '.', 'done' => false],
                ];
                @endphp

                <div class="space-y-4">
                    @foreach($steps as $i => $step)
                    <div class="flex items-start gap-4">
                        <!-- Indicador -->
                        <div class="flex flex-col items-center flex-shrink-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg
                                {{ $step['done'] ? 'bg-cj-turquesa shadow-lg shadow-teal-400/30' : 'bg-gray-100' }}">
                                {{ $step['icon'] }}
                            </div>
                            @if(!$loop->last)
                            <div class="w-0.5 h-6 mt-1 {{ $step['done'] ? 'bg-cj-turquesa' : 'bg-gray-200' }}"></div>
                            @endif
                        </div>
                        <!-- Texto -->
                        <div class="pt-1.5">
                            <p class="font-semibold text-sm {{ $step['done'] ? 'text-cj-texto' : 'text-cj-texto-claro' }}">
                                {{ $step['title'] }}
                                @if($step['done'])
                                <span class="ml-2 text-xs font-normal text-cj-turquesa">— Ahora mismo</span>
                                @endif
                            </p>
                            <p class="text-xs text-cj-texto-claro">{{ $step['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
