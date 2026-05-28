<x-app-layout>
    <div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
    <div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-cj-texto leading-tight">Mis envíos</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 space-y-5">

            <x-notifications />

            <!-- Widget total enviado -->
            <div class="bg-gradient-to-br from-cj-morado-profundo to-cj-morado-medio rounded-2xl shadow-2xl p-5 text-white flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-widest opacity-75 mb-1">Total enviado</p>
                    <p class="text-3xl font-bold font-mono">S/ {{ number_format($totalSpent, 2) }}</p>
                    <p class="text-xs opacity-60 mt-1">Soles peruanos</p>
                </div>
                <a href="{{ route('transactions.create') }}"
                   class="flex items-center gap-2 bg-white/20 hover:bg-white/30 backdrop-blur border border-white/30 text-white font-semibold px-4 py-2.5 rounded-xl text-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v14M5 12h14"/>
                    </svg>
                    Nuevo envío
                </a>
            </div>

            <!-- Filtros chips -->
            @php
                $filtros = [
                    'all'        => ['label' => 'Todos',        'color' => 'gray'],
                    'pending'    => ['label' => 'Pendiente',    'color' => 'yellow'],
                    'observed'   => ['label' => 'Observado',    'color' => 'orange'],
                    'processing' => ['label' => 'En proceso',   'color' => 'blue'],
                    'completed'  => ['label' => 'Completado',   'color' => 'green'],
                    'cancelled'  => ['label' => 'Cancelado',    'color' => 'red'],
                ];
                $chipBase = [
                    'gray'   => 'bg-gray-100 text-gray-700 border-gray-200',
                    'yellow' => 'bg-yellow-50 text-yellow-800 border-yellow-200',
                    'orange' => 'bg-orange-50 text-orange-800 border-orange-200',
                    'blue'   => 'bg-blue-50 text-blue-800 border-blue-200',
                    'green'  => 'bg-green-50 text-green-800 border-green-200',
                    'red'    => 'bg-red-50 text-red-800 border-red-200',
                ];
                $chipActive = [
                    'gray'   => 'bg-gray-600 text-white border-gray-600',
                    'yellow' => 'bg-yellow-500 text-white border-yellow-500',
                    'orange' => 'bg-orange-500 text-white border-orange-500',
                    'blue'   => 'bg-blue-600 text-white border-blue-600',
                    'green'  => 'bg-green-600 text-white border-green-600',
                    'red'    => 'bg-red-500 text-white border-red-500',
                ];
            @endphp

            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-4">
                <div class="flex gap-2 flex-wrap">
                    @foreach($filtros as $key => $f)
                    <a href="{{ route('transactions.index', ['status' => $key]) }}"
                       class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border text-xs font-semibold transition-all
                              {{ $statusFilter === $key ? $chipActive[$f['color']] : $chipBase[$f['color']] }}">
                        {{ $f['label'] }}
                        @if(isset($counts[$key]) && $counts[$key] > 0)
                        <span class="rounded-full px-1.5 py-0.5 text-xs font-bold
                                     {{ $statusFilter === $key ? 'bg-white/25' : 'bg-black/10' }}">
                            {{ $counts[$key] }}
                        </span>
                        @endif
                    </a>
                    @endforeach
                </div>
            </div>

            <!-- Lista de transacciones -->
            @if($transactions->isEmpty())
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-12 text-center">
                <div class="text-5xl mb-4">📭</div>
                <p class="font-semibold text-cj-texto">No hay transacciones en esta categoría</p>
                <p class="text-sm text-cj-texto-claro mt-1">Prueba otro filtro o crea un nuevo envío.</p>
            </div>
            @else
            <div class="space-y-3" x-data="{ openId: null }">
                @foreach($transactions as $tx)
                @php
                    $statusStyles = [
                        'pending'    => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'dot' => 'bg-yellow-400', 'label' => 'Pendiente'],
                        'observed'   => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'dot' => 'bg-orange-400', 'label' => 'Con observaciones'],
                        'processing' => ['bg' => 'bg-blue-100',   'text' => 'text-blue-800',   'dot' => 'bg-blue-400',   'label' => 'En proceso'],
                        'completed'  => ['bg' => 'bg-green-100',  'text' => 'text-green-800',  'dot' => 'bg-green-400',  'label' => 'Completado'],
                        'cancelled'  => ['bg' => 'bg-red-100',    'text' => 'text-red-800',    'dot' => 'bg-red-400',    'label' => 'Cancelado'],
                    ];
                    $s = $statusStyles[$tx->status] ?? $statusStyles['pending'];
                @endphp

                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 overflow-hidden">
                    <!-- Fila resumen — clic para expandir -->
                    <button type="button"
                            @click="openId = openId === {{ $tx->id }} ? null : {{ $tx->id }}"
                            class="w-full text-left p-5 hover:bg-gray-50/50 transition-all">
                        <div class="flex items-center justify-between gap-3">
                            <!-- Fecha + montos -->
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-11 h-11 bg-gradient-to-br from-cj-morado-profundo to-cj-morado-medio rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                    #{{ $tx->id }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-cj-texto">S/ {{ number_format($tx->amount_pen, 2) }}
                                        <span class="text-cj-texto-claro font-normal text-sm">→ Bs. {{ number_format($tx->amount_ves, 2) }}</span>
                                    </p>
                                    <p class="text-xs text-cj-texto-claro">{{ $tx->created_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                            <!-- Estado + flecha -->
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span class="flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $s['bg'] }} {{ $s['text'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $s['dot'] }}"></span>
                                    {{ $s['label'] }}
                                </span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform"
                                     :class="openId === {{ $tx->id }} ? 'rotate-90' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Alerta observación visible sin expandir + botón Corregir -->
                        @if($tx->status === 'observed' && $tx->observation)
                        <div class="mt-3 bg-orange-50 border-2 border-orange-300 rounded-xl px-4 py-3 text-left space-y-2">
                            <p class="text-xs font-bold text-orange-700">⚠️ El vendedor solicitó una corrección:</p>
                            <p class="text-sm text-orange-800">{{ $tx->observation }}</p>
                            <a href="{{ route('transactions.edit', $tx) }}"
                               class="inline-flex items-center gap-1.5 mt-1 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold rounded-xl transition-all shadow-sm">
                                ✏️ Corregir y reenviar
                            </a>
                        </div>
                        @endif
                    </button>

                    <!-- Detalle expandible -->
                    <div x-show="openId === {{ $tx->id }}" x-collapse>
                        <div class="border-t border-gray-100 p-5 space-y-5 bg-gray-50/30">

                            <!-- Timeline de estados -->
                            @php
                                $timelineSteps = [
                                    ['key' => 'pending',    'label' => 'Solicitud recibida',      'icon' => '📤'],
                                    ['key' => 'processing', 'label' => 'Aprobada por el vendedor', 'icon' => '✅'],
                                    ['key' => 'completed',  'label' => 'Dinero enviado',           'icon' => '💸'],
                                ];
                                $statusOrder = ['pending' => 0, 'observed' => 0, 'processing' => 1, 'completed' => 2, 'cancelled' => -1];
                                $currentStep = $statusOrder[$tx->status] ?? 0;
                            @endphp

                            @if($tx->status !== 'cancelled')
                            <div>
                                <p class="text-xs font-bold uppercase tracking-widest text-cj-texto-claro mb-3">Progreso</p>
                                <div class="flex items-center gap-0">
                                    @foreach($timelineSteps as $i => $step)
                                    <div class="flex items-center {{ $i < count($timelineSteps) - 1 ? 'flex-1' : '' }}">
                                        <div class="flex flex-col items-center">
                                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-base
                                                         {{ $i <= $currentStep ? 'bg-cj-turquesa shadow-md shadow-teal-400/30' : 'bg-gray-200' }}">
                                                {{ $step['icon'] }}
                                            </div>
                                            <p class="text-xs text-center mt-1 w-20
                                                       {{ $i <= $currentStep ? 'font-semibold text-cj-texto' : 'text-cj-texto-claro' }}">
                                                {{ $step['label'] }}
                                            </p>
                                        </div>
                                        @if($i < count($timelineSteps) - 1)
                                        <div class="flex-1 h-0.5 mb-6 {{ $i < $currentStep ? 'bg-cj-turquesa' : 'bg-gray-200' }}"></div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @else
                            <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700 font-semibold">
                                Esta solicitud fue cancelada/denegada.
                            </div>
                            @endif

                            <!-- Datos receptor Venezuela -->
                            @if($tx->recipient_bank)
                            <div>
                                <p class="text-xs font-bold uppercase tracking-widest text-cj-texto-claro mb-3">Receptor en Venezuela</p>
                                <div class="bg-white rounded-xl border border-gray-100 divide-y divide-gray-50">
                                    <div class="flex justify-between px-4 py-2.5 text-sm">
                                        <span class="text-cj-texto-claro">Tipo</span>
                                        <span class="font-semibold text-cj-texto capitalize">
                                            {{ $tx->operation_type === 'pago_movil' ? 'Pago Móvil' : 'Transferencia' }}
                                        </span>
                                    </div>
                                    @if($tx->operation_number)
                                    <div class="flex justify-between px-4 py-2.5 text-sm">
                                        <span class="text-cj-texto-claro">Nº operación</span>
                                        <span class="font-mono font-semibold text-cj-texto">{{ $tx->operation_number }}</span>
                                    </div>
                                    @endif
                                    <div class="flex justify-between px-4 py-2.5 text-sm">
                                        <span class="text-cj-texto-claro">Banco VE</span>
                                        <span class="font-semibold text-cj-texto">{{ $tx->recipient_bank }}</span>
                                    </div>
                                    <div class="flex justify-between px-4 py-2.5 text-sm">
                                        <span class="text-cj-texto-claro">Cédula</span>
                                        <span class="font-mono text-cj-texto">{{ $tx->recipient_dni }}</span>
                                    </div>
                                    <div class="flex justify-between px-4 py-2.5 text-sm">
                                        <span class="text-cj-texto-claro">Teléfono</span>
                                        <span class="font-mono text-cj-texto">{{ $tx->recipient_phone }}</span>
                                    </div>
                                    @if($tx->recipient_account_number)
                                    <div class="flex justify-between px-4 py-2.5 text-sm">
                                        <span class="text-cj-texto-claro">Cuenta</span>
                                        <span class="font-mono text-cj-texto">{{ $tx->recipient_account_number }}</span>
                                    </div>
                                    <div class="flex justify-between px-4 py-2.5 text-sm">
                                        <span class="text-cj-texto-claro">Tipo cuenta</span>
                                        <span class="font-semibold text-cj-texto capitalize">{{ $tx->recipient_account_type }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- Comprobante del cliente -->
                            @if($tx->voucher)
                            <div class="flex items-center justify-between bg-white rounded-xl border border-gray-100 px-4 py-3">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-widest text-cj-texto-claro">Tu comprobante</p>
                                    <p class="text-sm font-semibold text-cj-texto mt-0.5">Enviado al registro</p>
                                </div>
                                <a href="{{ asset('storage/' . $tx->voucher) }}" target="_blank"
                                   class="flex items-center gap-1.5 px-3 py-2 bg-cj-turquesa/10 hover:bg-cj-turquesa/20 text-cj-turquesa rounded-lg text-sm font-semibold transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Ver
                                </a>
                            </div>
                            @endif

                            <!-- Comprobante final del admin -->
                            @if($tx->final_voucher)
                            <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-xl px-4 py-3">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-widest text-green-600">Comprobante de envío</p>
                                    <p class="text-sm font-semibold text-green-800 mt-0.5">Confirmación del operador</p>
                                </div>
                                <a href="{{ asset('storage/' . $tx->final_voucher) }}" target="_blank"
                                   class="flex items-center gap-1.5 px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Descargar
                                </a>
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
</x-app-layout>
