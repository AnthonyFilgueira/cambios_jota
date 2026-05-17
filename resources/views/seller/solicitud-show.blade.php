<x-app-layout>
    <div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
    <div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('seller.bandeja') }}" class="text-cj-texto-claro hover:text-cj-morado-profundo transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-cj-texto">Solicitud #{{ $transaction->id }}</h2>
        </div>
    </x-slot>

    <div class="py-6" x-data="{ modalAccion: null, motivo: '' }">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            <x-notifications />

            <!-- Estado hero -->
            @php
                $stateMap = [
                    'pending'    => ['icon' => '⏳', 'title' => 'Pendiente de tu revisión', 'color' => 'from-yellow-400 to-orange-400'],
                    'observed'   => ['icon' => '⚠️', 'title' => 'Observada — cliente debe corregir', 'color' => 'from-orange-400 to-red-400'],
                    'processing' => ['icon' => '✅', 'title' => 'Aprobada — en proceso por el dueño', 'color' => 'from-blue-500 to-blue-700'],
                    'completed'  => ['icon' => '🎉', 'title' => 'Completada', 'color' => 'from-green-500 to-teal-500'],
                    'cancelled'  => ['icon' => '❌', 'title' => 'Denegada', 'color' => 'from-red-400 to-red-600'],
                ];
                $st = $stateMap[$transaction->status] ?? $stateMap['pending'];
            @endphp
            <div class="bg-gradient-to-r {{ $st['color'] }} rounded-2xl p-6 text-white text-center shadow-lg">
                <p class="text-4xl mb-2">{{ $st['icon'] }}</p>
                <p class="font-bold text-lg">{{ $st['title'] }}</p>
                @if($transaction->observation)
                <div class="mt-3 bg-white/20 rounded-xl px-4 py-2 text-sm text-left">
                    <span class="font-semibold">Motivo:</span> {{ $transaction->observation }}
                </div>
                @endif
            </div>

            <!-- Card montos -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 overflow-hidden">
                <div class="bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio px-6 py-4 flex justify-between items-center">
                    <div>
                        <p class="text-white/70 text-xs uppercase tracking-wider">Cliente envía</p>
                        <p class="text-white text-2xl font-bold">S/ {{ number_format($transaction->amount_pen, 2) }}</p>
                    </div>
                    <svg class="w-6 h-6 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    <div class="text-right">
                        <p class="text-white/70 text-xs uppercase tracking-wider">Familiar recibe</p>
                        <p class="text-white text-2xl font-bold">Bs. {{ number_format($transaction->amount_ves, 0) }}</p>
                    </div>
                </div>
                <div class="px-6 py-4 space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-cj-texto-claro">Tasa aplicada</span>
                        <span class="font-mono font-semibold text-cj-morado-profundo">1 PEN = {{ number_format($transaction->exchangeRate->ves_rate ?? 0, 2) }} VES</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-cj-texto-claro">Cliente</span>
                        <span class="font-semibold">{{ $transaction->user->name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-cj-texto-claro">Tipo de operación</span>
                        <span class="font-semibold">{{ $transaction->operation_type === 'pago_movil' ? 'Pago Móvil' : 'Transferencia Bancaria' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-cj-texto-claro">Fecha solicitud</span>
                        <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Receptor en Venezuela -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-5">
                <h3 class="font-bold text-cj-morado-profundo mb-4 flex items-center gap-2">
                    <span>🇻🇪</span> Receptor en Venezuela
                </h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-cj-texto-claro text-xs">Banco</p>
                        <p class="font-semibold">{{ $transaction->recipient_bank }}</p>
                    </div>
                    <div>
                        <p class="text-cj-texto-claro text-xs">Cédula</p>
                        <p class="font-mono font-semibold">{{ $transaction->recipient_dni }}</p>
                    </div>
                    <div>
                        <p class="text-cj-texto-claro text-xs">Teléfono</p>
                        <p class="font-semibold">{{ $transaction->recipient_phone ?? '—' }}</p>
                    </div>
                    @if($transaction->operation_type !== 'pago_movil')
                    <div>
                        <p class="text-cj-texto-claro text-xs">Nº de cuenta</p>
                        <p class="font-mono font-semibold text-xs">{{ $transaction->recipient_account_number ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-cj-texto-claro text-xs">Tipo de cuenta</p>
                        <p class="font-semibold">{{ ucfirst($transaction->recipient_account_type ?? '—') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Transferencia desde Perú -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-5">
                <h3 class="font-bold text-cj-morado-profundo mb-4 flex items-center gap-2">
                    <span>🇵🇪</span> Transferencia desde Perú
                </h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-cj-texto-claro text-xs">Banco origen</p>
                        <p class="font-semibold">{{ $transaction->sender_bank }}</p>
                    </div>
                    <div>
                        <p class="text-cj-texto-claro text-xs">DNI del titular</p>
                        <p class="font-mono font-semibold">{{ $transaction->sender_dni ?? '—' }}</p>
                    </div>
                    @if($transaction->sender_account_number)
                    <div class="col-span-2">
                        <p class="text-cj-texto-claro text-xs">Nº cuenta origen</p>
                        <p class="font-mono">{{ $transaction->sender_account_number }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Comprobante del cliente -->
            @if($transaction->voucher)
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-5">
                <h3 class="font-bold text-cj-morado-profundo mb-4">Comprobante del cliente</h3>
                @php $ext = pathinfo($transaction->voucher, PATHINFO_EXTENSION); @endphp
                @if(in_array(strtolower($ext), ['jpg','jpeg','png','webp']))
                <div class="relative rounded-xl overflow-hidden border border-gray-200 mb-3">
                    <img src="{{ Storage::url($transaction->voucher) }}" alt="Comprobante"
                         class="w-full max-h-96 object-contain bg-gray-50">
                </div>
                @else
                <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-4 mb-3">
                    <svg class="w-10 h-10 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <div>
                        <p class="font-semibold text-cj-texto">Comprobante PDF</p>
                        <p class="text-xs text-cj-texto-claro">{{ basename($transaction->voucher) }}</p>
                    </div>
                </div>
                @endif
                <div class="flex gap-3">
                    <a href="{{ Storage::url($transaction->voucher) }}" target="_blank"
                       class="flex-1 text-center py-2.5 border-2 border-cj-morado-profundo text-cj-morado-profundo font-semibold rounded-xl hover:bg-cj-morado-profundo hover:text-white transition-all text-sm">
                        Ver completo
                    </a>
                    <a href="{{ Storage::url($transaction->voucher) }}" download
                       class="flex-1 text-center py-2.5 bg-cj-morado-profundo text-white font-semibold rounded-xl hover:bg-cj-morado-medio transition-all text-sm">
                        Descargar
                    </a>
                </div>
            </div>
            @endif

            <!-- Notas del cliente -->
            @if($transaction->notes)
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4">
                <p class="text-xs font-semibold text-blue-700 mb-1">Notas del cliente</p>
                <p class="text-sm text-blue-800">{{ $transaction->notes }}</p>
            </div>
            @endif

            <!-- Timeline de estados -->
            @if($transaction->logs->isNotEmpty())
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-5">
                <h3 class="font-bold text-cj-morado-profundo mb-4">Historial de acciones</h3>
                <div class="space-y-3">
                    @foreach($transaction->logs->sortByDesc('created_at') as $log)
                    <div class="flex gap-3 text-sm">
                        <div class="w-2 h-2 rounded-full bg-cj-morado-profundo mt-1.5 flex-shrink-0"></div>
                        <div>
                            <p class="font-semibold text-cj-texto">{{ $log->user->name ?? 'Sistema' }}</p>
                            <p class="text-cj-texto-claro text-xs">{{ $log->comment }}</p>
                            <p class="text-gray-400 text-xs mt-0.5">{{ $log->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- ACCIONES (solo si está pendiente u observada) -->
            @if(in_array($transaction->status, ['pending', 'observed']))
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-6 space-y-3">
                <h3 class="font-bold text-cj-morado-profundo">Tu decisión</h3>

                <!-- Botón Aprobar -->
                <button @click="modalAccion = 'aprobar'"
                        class="w-full py-3 bg-gradient-to-r from-green-500 to-teal-500 text-white font-bold rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition-all">
                    Aprobar solicitud →
                </button>

                <!-- Botón Observar -->
                <button @click="modalAccion = 'observar'"
                        class="w-full py-3 border-2 border-orange-400 text-orange-600 font-bold rounded-xl hover:bg-orange-50 transition-all">
                    Observar / pedir corrección
                </button>

                <!-- Botón Denegar -->
                <button @click="modalAccion = 'denegar'"
                        class="w-full py-3 border-2 border-red-400 text-red-600 font-bold rounded-xl hover:bg-red-50 transition-all">
                    Denegar solicitud
                </button>
            </div>
            @endif

        </div>
    </div>

    <!-- MODAL APROBAR -->
    <div x-show="modalAccion === 'aprobar'" x-cloak
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="modalAccion = null"
         x-data="{ confirmed: false }">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-5">
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-cj-texto">Confirmar aprobación</h3>
                <p class="text-sm text-cj-texto-claro mt-1">Esta acción notificará al dueño para ejecutar la transferencia.</p>
            </div>

            <div class="bg-gray-50 rounded-xl p-4 text-sm space-y-2">
                <div class="flex justify-between"><span class="text-cj-texto-claro">Cliente envía</span><span class="font-bold">S/ {{ number_format($transaction->amount_pen, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-cj-texto-claro">Familiar recibe</span><span class="font-bold">Bs. {{ number_format($transaction->amount_ves, 0) }}</span></div>
            </div>

            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" x-model="confirmed" class="mt-0.5 h-4 w-4 rounded text-green-500 focus:ring-green-500">
                <span class="text-sm text-cj-texto">Confirmo que el <strong>depósito es correcto</strong> y que el monto coincide con el comprobante presentado.</span>
            </label>

            <div class="flex gap-3">
                <button @click="modalAccion = null"
                        class="flex-1 py-2.5 border-2 border-gray-200 text-cj-texto rounded-xl font-semibold hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <form method="POST" action="{{ route('seller.solicitud.approve', $transaction) }}" class="flex-1">
                    @csrf
                    <button type="submit" :disabled="!confirmed"
                            class="w-full py-2.5 bg-green-500 text-white font-bold rounded-xl transition-all disabled:opacity-40 disabled:cursor-not-allowed hover:bg-green-600">
                        Aprobar →
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL OBSERVAR -->
    <div x-show="modalAccion === 'observar'" x-cloak
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="modalAccion = null">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-5">
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-cj-texto">Observar solicitud</h3>
                <p class="text-sm text-cj-texto-claro mt-1">El cliente recibirá tu mensaje y deberá corregir su envío.</p>
            </div>

            <form method="POST" action="{{ route('seller.solicitud.observe', $transaction) }}" class="space-y-4">
                @csrf
                <!-- Motivos rápidos -->
                <div>
                    <p class="text-xs font-semibold text-cj-texto mb-2">Motivos frecuentes</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Comprobante no legible', 'Monto no coincide', 'Cuenta destino incorrecta', 'Fecha inválida'] as $chip)
                        <button type="button"
                                @click="motivo = '{{ $chip }}'"
                                class="px-3 py-1.5 bg-orange-50 border border-orange-200 rounded-full text-xs font-medium text-orange-700 hover:bg-orange-100 transition">
                            {{ $chip }}
                        </button>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-cj-texto mb-1">Mensaje al cliente *</label>
                    <textarea name="motivo" x-model="motivo" required minlength="10" maxlength="500" rows="3"
                              class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-orange-400 focus:ring-2 focus:ring-orange-400/20 transition-all text-sm resize-none"
                              placeholder="Explica qué debe corregir..."></textarea>
                    <p class="text-xs text-cj-texto-claro text-right mt-1" x-text="motivo.length + '/500'"></p>
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="modalAccion = null"
                            class="flex-1 py-2.5 border-2 border-gray-200 text-cj-texto rounded-xl font-semibold hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 bg-orange-500 text-white font-bold rounded-xl hover:bg-orange-600 transition">
                        Enviar observación
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL DENEGAR -->
    <div x-show="modalAccion === 'denegar'" x-cloak
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="modalAccion = null">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-5">
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <h3 class="text-xl font-bold text-cj-texto">Denegar solicitud</h3>
                <p class="text-sm text-cj-texto-claro mt-1">Esta acción es definitiva. El cliente será notificado.</p>
            </div>

            <form method="POST" action="{{ route('seller.solicitud.deny', $transaction) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-cj-texto mb-1">Motivo del rechazo * <span class="text-red-500">(obligatorio)</span></label>
                    <textarea name="motivo" required minlength="10" maxlength="500" rows="3"
                              class="w-full p-3 border-2 border-red-200 rounded-xl focus:border-red-400 focus:ring-2 focus:ring-red-400/20 transition-all text-sm resize-none"
                              placeholder="Explica el motivo del rechazo..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="modalAccion = null"
                            class="flex-1 py-2.5 border-2 border-gray-200 text-cj-texto rounded-xl font-semibold hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 bg-red-500 text-white font-bold rounded-xl hover:bg-red-600 transition">
                        Denegar definitivamente
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
