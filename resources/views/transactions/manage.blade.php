<x-app-layout>
    <div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
    <div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-cj-texto leading-tight">Gestión de Transacciones</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Estadísticas -->
            <div class="grid grid-cols-3 md:grid-cols-6 gap-3 mb-6">
                <div class="bg-white/90 backdrop-blur-lg rounded-xl shadow p-3 border border-white/50 text-center">
                    <p class="text-xs text-gray-500 uppercase mb-1">Total</p>
                    <p class="text-2xl font-bold text-cj-morado-profundo">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-pink-50 rounded-xl shadow p-3 border border-pink-200 text-center">
                    <p class="text-xs text-pink-600 uppercase mb-1">Pendientes</p>
                    <p class="text-2xl font-bold text-pink-700">{{ $stats['pending'] }}</p>
                </div>
                <div class="bg-orange-50 rounded-xl shadow p-3 border border-orange-200 text-center">
                    <p class="text-xs text-orange-600 uppercase mb-1">Observadas</p>
                    <p class="text-2xl font-bold text-orange-700">{{ $stats['observed'] }}</p>
                </div>
                <div class="bg-yellow-50 rounded-xl shadow p-3 border border-yellow-200 text-center">
                    <p class="text-xs text-yellow-600 uppercase mb-1">En proceso</p>
                    <p class="text-2xl font-bold text-yellow-700">{{ $stats['processing'] }}</p>
                </div>
                <div class="bg-teal-50 rounded-xl shadow p-3 border border-teal-200 text-center">
                    <p class="text-xs text-teal-600 uppercase mb-1">Completadas</p>
                    <p class="text-2xl font-bold text-teal-700">{{ $stats['completed'] }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl shadow p-3 border border-gray-200 text-center">
                    <p class="text-xs text-gray-600 uppercase mb-1">Canceladas</p>
                    <p class="text-2xl font-bold text-gray-700">{{ $stats['cancelled'] }}</p>
                </div>
            </div>

            <!-- Lista de transacciones -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/50 overflow-hidden"
                 x-data="{
                     expandedId: null,
                     observeModal: false,
                     cancelModal: false,
                     completeModal: false,
                     selectedTransaction: null,
                     observation: '',
                     cancelReason: '',
                     finalVoucherPreview: null,
                     handleFinalVoucher(e) {
                         const file = e.target.files[0];
                         if (!file) return;
                         if (file.type.startsWith('image/')) {
                             const reader = new FileReader();
                             reader.onload = ev => this.finalVoucherPreview = ev.target.result;
                             reader.readAsDataURL(file);
                         } else {
                             this.finalVoucherPreview = 'pdf';
                         }
                     }
                 }">

                <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-cj-morado-profundo/5 to-cj-turquesa/5">
                    <h3 class="text-lg font-bold text-cj-morado-profundo">Transacciones</h3>
                    <p class="text-sm text-cj-texto-claro mt-0.5">Gestiona las solicitudes de envío de divisas</p>
                </div>

                @if($transactions->isEmpty())
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <p class="text-cj-texto-claro font-medium">No hay transacciones registradas</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($transactions as $transaction)
                        @php
                            $fromCurrency = $transaction->exchangeRate?->currencyPair?->fromCurrency;
                            $toCurrency   = $transaction->exchangeRate?->currencyPair?->toCurrency;
                            $fromSymbol   = $fromCurrency?->symbol ?? 'S/';
                            $toSymbol     = $toCurrency?->symbol ?? 'Bs.';
                            $fromCode     = $fromCurrency?->code ?? '—';
                            $toCode       = $toCurrency?->code ?? '—';

                            $statusConfig = [
                                'pending'    => ['label' => 'Pendiente',   'class' => 'bg-pink-100 text-pink-800',   'dot' => 'bg-pink-400'],
                                'observed'   => ['label' => 'Observada',   'class' => 'bg-orange-100 text-orange-800','dot' => 'bg-orange-400'],
                                'processing' => ['label' => 'En proceso',  'class' => 'bg-yellow-100 text-yellow-800','dot' => 'bg-yellow-400'],
                                'completed'  => ['label' => 'Completada',  'class' => 'bg-teal-100 text-teal-800',   'dot' => 'bg-teal-400'],
                                'cancelled'  => ['label' => 'Cancelada',   'class' => 'bg-gray-100 text-gray-600',   'dot' => 'bg-gray-400'],
                            ];
                            $sc = $statusConfig[$transaction->status] ?? ['label' => $transaction->status, 'class' => 'bg-gray-100 text-gray-600', 'dot' => 'bg-gray-400'];

                            $opType = $transaction->sender_operation_type ?? $transaction->operation_type ?? null;
                        @endphp

                        <div class="hover:bg-gradient-to-r hover:from-cj-morado-profundo/3 hover:to-cj-turquesa/3 transition-all">
                            <!-- Fila principal -->
                            <div class="p-4">
                                <div class="flex flex-col sm:flex-row sm:items-start gap-4">

                                    <!-- Icono + datos cliente -->
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <div class="w-10 h-10 bg-gradient-to-br from-cj-morado-profundo to-cj-turquesa rounded-xl flex items-center justify-center flex-shrink-0 shadow">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-cj-morado-profundo truncate">{{ $transaction->user->name }}</p>
                                            <p class="text-xs text-cj-texto-claro truncate">{{ $transaction->user->email }}</p>
                                            <p class="text-xs text-cj-texto-claro">{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>

                                    <!-- Corredor + montos -->
                                    <div class="sm:text-right flex-shrink-0">
                                        <div class="flex sm:justify-end items-center gap-2 mb-1">
                                            <span class="px-2 py-0.5 bg-cj-morado-profundo/10 text-cj-morado-profundo rounded-full text-xs font-semibold">
                                                {{ $fromCode }} → {{ $toCode }}
                                            </span>
                                        </div>
                                        <p class="font-bold text-cj-morado-profundo">
                                            {{ $fromSymbol }} {{ number_format($transaction->amount_pen, 2) }}
                                        </p>
                                        <p class="text-sm text-cj-turquesa font-semibold">
                                            → {{ $toSymbol }} {{ number_format($transaction->amount_ves, 2) }}
                                        </p>
                                        <div class="mt-1.5 flex sm:justify-end">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $sc['class'] }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $sc['dot'] }}"></span>
                                                {{ $sc['label'] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botones de acción -->
                                <div class="flex flex-wrap gap-2 mt-3">
                                    @if($transaction->canBeObserved())
                                    <button @click="observeModal = true; selectedTransaction = {{ $transaction->id }}; observation = ''"
                                            class="px-3 py-1.5 bg-orange-100 hover:bg-orange-200 text-orange-800 rounded-lg text-xs font-semibold transition-all border border-orange-200">
                                        Observar
                                    </button>
                                    @endif

                                    @if($transaction->canBeProcessed())
                                    <form method="POST" action="{{ route('transactions.process', $transaction) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-lg text-xs font-semibold transition-all border border-yellow-200">
                                            Procesar
                                        </button>
                                    </form>
                                    @endif

                                    @if($transaction->status === 'processing')
                                    <button @click="completeModal = true; selectedTransaction = {{ $transaction->id }}; finalVoucherPreview = null"
                                            class="px-3 py-1.5 bg-teal-100 hover:bg-teal-200 text-teal-800 rounded-lg text-xs font-semibold transition-all border border-teal-200">
                                        Completar
                                    </button>
                                    @endif

                                    @if($transaction->canBeCancelled())
                                    <button @click="cancelModal = true; selectedTransaction = {{ $transaction->id }}; cancelReason = ''"
                                            class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-800 rounded-lg text-xs font-semibold transition-all border border-red-200">
                                        Cancelar
                                    </button>
                                    @endif

                                    <button @click="expandedId = expandedId === {{ $transaction->id }} ? null : {{ $transaction->id }}"
                                            class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold transition-all border border-gray-200 ml-auto">
                                        <span x-show="expandedId !== {{ $transaction->id }}">Ver detalles ▼</span>
                                        <span x-show="expandedId === {{ $transaction->id }}">Ocultar ▲</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Detalles expandibles -->
                            <div x-show="expandedId === {{ $transaction->id }}"
                                 x-collapse
                                 class="border-t border-gray-100">
                                <div class="p-4 bg-gray-50/60">
                                    <div class="grid sm:grid-cols-2 gap-4">

                                        <!-- Remitente -->
                                        <div class="bg-white rounded-xl p-4 border border-gray-200">
                                            <h5 class="font-bold text-sm text-cj-morado-profundo mb-3 flex items-center gap-2">
                                                <span class="w-6 h-6 bg-cj-morado-profundo/10 rounded-lg flex items-center justify-center text-xs">↑</span>
                                                Remitente
                                                @if($opType)
                                                <span class="ml-auto text-xs font-normal bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                                                    {{ str_replace('_', ' ', $opType) }}
                                                </span>
                                                @endif
                                            </h5>
                                            <div class="space-y-1.5 text-sm">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Cliente</span>
                                                    <span class="font-medium text-right">{{ $transaction->user?->name ?? '—' }}</span>
                                                </div>
                                                @if($transaction->sender_document_number)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Documento</span>
                                                    <span class="font-mono text-xs">{{ $transaction->sender_document_type ? $transaction->sender_document_type . ': ' : '' }}{{ $transaction->sender_document_number }}</span>
                                                </div>
                                                @elseif($transaction->sender_dni)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">DNI</span>
                                                    <span class="font-mono text-xs">{{ $transaction->sender_dni }}</span>
                                                </div>
                                                @endif
                                                @if($transaction->sender_bank)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Banco</span>
                                                    <span class="font-medium">{{ $transaction->sender_bank }}</span>
                                                </div>
                                                @endif
                                                @if($transaction->sender_account_number)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Cuenta</span>
                                                    <span class="font-mono text-xs">{{ $transaction->sender_account_number }}</span>
                                                </div>
                                                @endif
                                                @if($transaction->sender_phone)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Teléfono</span>
                                                    <span class="font-mono text-xs">{{ $transaction->sender_phone }}</span>
                                                </div>
                                                @endif
                                                @if($transaction->operation_number)
                                                <div class="flex justify-between items-center pt-1 border-t border-gray-100">
                                                    <span class="text-gray-500">Nº Operación</span>
                                                    <span class="font-mono font-bold text-cj-morado-profundo text-sm">{{ $transaction->operation_number }}</span>
                                                </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Beneficiario -->
                                        <div class="bg-white rounded-xl p-4 border border-gray-200">
                                            <h5 class="font-bold text-sm text-cj-turquesa mb-3 flex items-center gap-2">
                                                <span class="w-6 h-6 bg-cj-turquesa/10 rounded-lg flex items-center justify-center text-xs">↓</span>
                                                Beneficiario
                                                <span class="ml-auto text-xs font-normal bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">{{ $toCode }}</span>
                                            </h5>
                                            <div class="space-y-1.5 text-sm">
                                                @if($transaction->recipient_name)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Nombre</span>
                                                    <span class="font-medium text-right">{{ $transaction->recipient_name }}</span>
                                                </div>
                                                @endif
                                                @if($transaction->recipient_document_number)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Documento</span>
                                                    <span class="font-mono text-xs">{{ $transaction->recipient_document_type ? $transaction->recipient_document_type . ': ' : '' }}{{ $transaction->recipient_document_number }}</span>
                                                </div>
                                                @elseif($transaction->recipient_dni)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Cédula</span>
                                                    <span class="font-mono text-xs">{{ $transaction->recipient_dni }}</span>
                                                </div>
                                                @endif
                                                @if($transaction->recipient_bank)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Banco</span>
                                                    <span class="font-medium">{{ $transaction->recipient_bank }}</span>
                                                </div>
                                                @endif
                                                @if($transaction->recipient_account_number)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Cuenta</span>
                                                    <span class="font-mono text-xs">{{ $transaction->recipient_account_number }}</span>
                                                </div>
                                                @endif
                                                @if($transaction->recipient_phone)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Teléfono</span>
                                                    <span class="font-mono text-xs">{{ $transaction->recipient_phone }}</span>
                                                </div>
                                                @endif
                                                @if($transaction->recipient_account_type)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Tipo cuenta</span>
                                                    <span class="text-xs">{{ ucfirst($transaction->recipient_account_type) }}</span>
                                                </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Comprobante del cliente -->
                                        @if($transaction->voucher)
                                        <div class="bg-white rounded-xl p-4 border border-gray-200">
                                            <h5 class="font-bold text-sm text-cj-morado-profundo mb-3">Comprobante del cliente</h5>
                                            <a href="{{ asset('storage/' . $transaction->voucher) }}" target="_blank"
                                               class="inline-flex items-center gap-2 px-4 py-2 bg-cj-turquesa/10 hover:bg-cj-turquesa/20 text-cj-turquesa rounded-xl text-sm font-semibold transition-all border border-cj-turquesa/20">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                Ver comprobante
                                            </a>
                                        </div>
                                        @endif

                                        <!-- Observación actual -->
                                        @if($transaction->observation)
                                        <div class="bg-orange-50 rounded-xl p-4 border border-orange-200 {{ $transaction->voucher ? '' : 'sm:col-span-2' }}">
                                            <h5 class="font-bold text-sm text-orange-700 mb-2">Observación activa</h5>
                                            <p class="text-sm text-orange-800">{{ $transaction->observation }}</p>
                                        </div>
                                        @endif

                                        <!-- Historial -->
                                        @if($transaction->logs->isNotEmpty())
                                        <div class="sm:col-span-2 bg-white rounded-xl p-4 border border-gray-200">
                                            <h5 class="font-bold text-sm text-cj-morado-profundo mb-3">Historial de cambios</h5>
                                            <div class="space-y-2">
                                                @foreach($transaction->logs as $log)
                                                <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                                    <div class="w-7 h-7 bg-cj-morado-profundo/10 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                                        <span class="text-xs text-cj-morado-profundo font-bold">{{ strtoupper(substr($log->user->name, 0, 1)) }}</span>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-baseline justify-between gap-2">
                                                            <span class="font-semibold text-sm text-cj-texto">{{ $log->user->name }}</span>
                                                            <span class="text-xs text-gray-400 flex-shrink-0">{{ $log->created_at->diffForHumans() }}</span>
                                                        </div>
                                                        <p class="text-xs text-gray-500 mt-0.5">
                                                            {{ ucfirst($log->action) }}:
                                                            <span class="font-mono">{{ $log->old_status }}</span>
                                                            →
                                                            <span class="font-mono font-semibold">{{ $log->new_status }}</span>
                                                        </p>
                                                        @if($log->comment)
                                                        <p class="text-xs text-gray-600 italic mt-1 bg-white rounded p-1.5 border border-gray-100">{{ $log->comment }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif

                <!-- Modal: Observar -->
                <div x-show="observeModal"
                     x-cloak
                     class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
                     @click.self="observeModal = false">
                    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-cj-texto">Marcar como observada</h3>
                        </div>
                        <form :action="'/transactions/' + selectedTransaction + '/observe'" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Observación <span class="text-red-500">*</span></label>
                                <textarea x-model="observation" name="observation" rows="4" required
                                          class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-orange-400 focus:ring-2 focus:ring-orange-400/20 transition-all text-sm resize-none"
                                          placeholder="Describe el problema o lo que necesita corregirse..."></textarea>
                            </div>
                            <div class="flex gap-3">
                                <button type="button" @click="observeModal = false"
                                        class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold text-sm transition-all">
                                    Cancelar
                                </button>
                                <button type="submit"
                                        class="flex-1 px-4 py-3 bg-orange-500 hover:bg-orange-600 text-white rounded-xl font-semibold text-sm transition-all shadow shadow-orange-300">
                                    Guardar observación
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal: Completar con comprobante final -->
                <div x-show="completeModal"
                     x-cloak
                     class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
                     @click.self="completeModal = false">
                    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-10 h-10 bg-gradient-to-br from-cj-turquesa to-teal-400 rounded-xl flex items-center justify-center shadow">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-cj-texto">Completar transacción</h3>
                                <p class="text-xs text-cj-texto-claro">Adjunta el comprobante de envío final</p>
                            </div>
                        </div>

                        <form :action="'/transactions/' + selectedTransaction + '/upload-final-voucher'"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="mb-5">
                                <label class="block text-sm font-semibold text-cj-texto mb-2">Comprobante de envío <span class="text-red-500">*</span></label>
                                <label class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-cj-turquesa/40 rounded-xl cursor-pointer bg-cj-turquesa/5 hover:bg-cj-turquesa/10 transition-all"
                                       :class="finalVoucherPreview ? 'border-cj-turquesa bg-cj-turquesa/10' : ''">
                                    <div x-show="!finalVoucherPreview" class="flex flex-col items-center gap-2 p-4">
                                        <svg class="w-8 h-8 text-cj-turquesa" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        <p class="text-sm text-cj-turquesa font-semibold">Seleccionar archivo</p>
                                        <p class="text-xs text-cj-texto-claro">JPG, PNG o PDF — máx. 10 MB</p>
                                    </div>
                                    <div x-show="finalVoucherPreview === 'pdf'" class="flex flex-col items-center gap-2 p-4">
                                        <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="text-sm font-semibold text-cj-texto">PDF listo ✓</p>
                                    </div>
                                    <img x-show="finalVoucherPreview && finalVoucherPreview !== 'pdf'"
                                         :src="finalVoucherPreview"
                                         class="h-32 w-full object-contain rounded-lg p-1">
                                    <input type="file" name="final_voucher" accept="image/*,.pdf"
                                           class="hidden" required
                                           @change="handleFinalVoucher($event)">
                                </label>
                            </div>

                            <div class="flex gap-3">
                                <button type="button" @click="completeModal = false"
                                        class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold text-sm transition-all">
                                    Cancelar
                                </button>
                                <button type="submit"
                                        class="flex-1 px-4 py-3 bg-gradient-to-r from-cj-turquesa to-teal-500 hover:opacity-90 text-white rounded-xl font-semibold text-sm transition-all shadow shadow-teal-300">
                                    Completar transacción
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal: Cancelar -->
                <div x-show="cancelModal"
                     x-cloak
                     class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
                     @click.self="cancelModal = false">
                    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-red-700">Cancelar transacción</h3>
                        </div>
                        <form :action="'/transactions/' + selectedTransaction + '/cancel'" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Motivo de cancelación <span class="text-red-500">*</span></label>
                                <textarea x-model="cancelReason" name="reason" rows="3" required
                                          class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-red-400 focus:ring-2 focus:ring-red-400/20 transition-all text-sm resize-none"
                                          placeholder="¿Por qué se cancela esta transacción?"></textarea>
                            </div>
                            <div class="flex gap-3">
                                <button type="button" @click="cancelModal = false"
                                        class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold text-sm transition-all">
                                    No cancelar
                                </button>
                                <button type="submit"
                                        class="flex-1 px-4 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl font-semibold text-sm transition-all shadow shadow-red-300">
                                    Sí, cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
