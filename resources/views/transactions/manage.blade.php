<x-app-layout>
    <!-- Fondo gradiente animado -->
    <div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
    <div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-cj-texto leading-tight">
            🔧 Gestión de Transacciones
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Estadísticas -->
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
                <div class="bg-white/90 backdrop-blur-lg rounded-xl shadow-lg p-4 border border-white/50">
                    <p class="text-xs text-gray-500 uppercase mb-1">Total</p>
                    <p class="text-2xl font-bold text-cj-morado-profundo">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-pink-50 backdrop-blur-lg rounded-xl shadow-lg p-4 border border-pink-200">
                    <p class="text-xs text-pink-600 uppercase mb-1">Pendientes</p>
                    <p class="text-2xl font-bold text-pink-700">{{ $stats['pending'] }}</p>
                </div>
                <div class="bg-orange-50 backdrop-blur-lg rounded-xl shadow-lg p-4 border border-orange-200">
                    <p class="text-xs text-orange-600 uppercase mb-1">Observadas</p>
                    <p class="text-2xl font-bold text-orange-700">{{ $stats['observed'] }}</p>
                </div>
                <div class="bg-yellow-50 backdrop-blur-lg rounded-xl shadow-lg p-4 border border-yellow-200">
                    <p class="text-xs text-yellow-600 uppercase mb-1">En proceso</p>
                    <p class="text-2xl font-bold text-yellow-700">{{ $stats['processing'] }}</p>
                </div>
                <div class="bg-teal-50 backdrop-blur-lg rounded-xl shadow-lg p-4 border border-teal-200">
                    <p class="text-xs text-teal-600 uppercase mb-1">Completadas</p>
                    <p class="text-2xl font-bold text-teal-700">{{ $stats['completed'] }}</p>
                </div>
                <div class="bg-gray-50 backdrop-blur-lg rounded-xl shadow-lg p-4 border border-gray-200">
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
                <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-cj-morado-profundo/5 to-cj-turquesa/5">
                    <h3 class="text-lg font-bold text-cj-morado-profundo">Transacciones</h3>
                    <p class="text-sm text-cj-texto-claro mt-1">Gestiona las solicitudes de envío de divisas</p>
                </div>

                @if($transactions->isEmpty())
                    <div class="p-12 text-center">
                        <p class="text-cj-texto-claro">No hay transacciones registradas</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($transactions as $transaction)
                        <div class="p-4 hover:bg-gradient-to-r hover:from-cj-morado-profundo/5 hover:to-cj-turquesa/5 transition-all">
                            <!-- Header -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-4">
                                    <div class="bg-gradient-to-br from-cj-morado-profundo to-cj-turquesa rounded-xl p-3 shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-cj-morado-profundo">{{ $transaction->user->name }}</p>
                                        <p class="text-xs text-cj-texto-claro">{{ $transaction->user->email }}</p>
                                        <p class="text-xs text-cj-texto-claro">{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xl font-bold text-cj-morado-profundo">S/ {{ number_format($transaction->amount_pen, 2) }}</p>
                                    <p class="text-sm text-cj-turquesa font-semibold">→ Bs. {{ number_format($transaction->amount_ves, 2) }}</p>
                                    @php
                                        $statusConfig = [
                                            'pending' => ['label' => 'Pendiente', 'class' => 'bg-pink-100 text-pink-800'],
                                            'observed' => ['label' => '⚠ Observada', 'class' => 'bg-orange-100 text-orange-800'],
                                            'processing' => ['label' => 'En proceso', 'class' => 'bg-yellow-100 text-yellow-800'],
                                            'completed' => ['label' => '✓ Completada', 'class' => 'bg-teal-100 text-teal-800'],
                                            'cancelled' => ['label' => 'Cancelada', 'class' => 'bg-gray-100 text-gray-800'],
                                        ];
                                        $config = $statusConfig[$transaction->status] ?? ['label' => $transaction->status, 'class' => 'bg-gray-100 text-gray-800'];
                                    @endphp
                                    <span class="inline-block px-3 py-1 rounded-lg text-xs font-bold {{ $config['class'] }} mt-2">
                                        {{ $config['label'] }}
                                    </span>
                                </div>
                            </div>

                            <!-- Acciones rápidas -->
                            <div class="flex gap-2 mb-3">
                                @if($transaction->canBeObserved())
                                <button @click="observeModal = true; selectedTransaction = {{ $transaction->id }}; observation = ''"
                                        class="px-3 py-1 bg-orange-100 hover:bg-orange-200 text-orange-800 rounded-lg text-xs font-semibold transition-all">
                                    ⚠ Observar
                                </button>
                                @endif

                                @if($transaction->canBeProcessed())
                                <form method="POST" action="{{ route('transactions.process', $transaction) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-lg text-xs font-semibold transition-all">
                                        ▶ Procesar
                                    </button>
                                </form>
                                @endif

                                @if($transaction->status === 'processing')
                                <button @click="completeModal = true; selectedTransaction = {{ $transaction->id }}; finalVoucherPreview = null"
                                        class="px-3 py-1 bg-teal-100 hover:bg-teal-200 text-teal-800 rounded-lg text-xs font-semibold transition-all">
                                    ✓ Completar + Comprobante
                                </button>
                                @endif

                                @if($transaction->canBeCancelled())
                                <button @click="cancelModal = true; selectedTransaction = {{ $transaction->id }}; cancelReason = ''"
                                        class="px-3 py-1 bg-red-100 hover:bg-red-200 text-red-800 rounded-lg text-xs font-semibold transition-all">
                                    ✕ Cancelar
                                </button>
                                @endif

                                <button @click="expandedId = expandedId === {{ $transaction->id }} ? null : {{ $transaction->id }}"
                                        class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg text-xs font-semibold transition-all">
                                    <span x-show="expandedId !== {{ $transaction->id }}">Ver detalles ▼</span>
                                    <span x-show="expandedId === {{ $transaction->id }}">Ocultar ▲</span>
                                </button>
                            </div>

                            <!-- Detalles expandibles -->
                            <div x-show="expandedId === {{ $transaction->id }}" x-collapse class="mt-4">
                                <div class="grid md:grid-cols-2 gap-4 p-4 bg-gray-50/50 rounded-xl">
                                    <!-- Datos bancarios receptor -->
                                    <div class="space-y-2">
                                        <h5 class="font-bold text-sm text-cj-morado-profundo">Receptor (Venezuela)</h5>
                                        <div class="text-sm space-y-1">
                                            <p><span class="text-gray-500">Banco:</span> {{ $transaction->recipient_bank }}</p>
                                            <p><span class="text-gray-500">Cuenta:</span> {{ $transaction->recipient_account_number }}</p>
                                            <p><span class="text-gray-500">Cédula:</span> {{ $transaction->recipient_dni }}</p>
                                            <p><span class="text-gray-500">Tipo:</span> {{ ucfirst($transaction->recipient_account_type) }}</p>
                                        </div>
                                    </div>

                                    <!-- Datos origen -->
                                    <div class="space-y-2">
                                        <h5 class="font-bold text-sm text-cj-morado-profundo">Origen (Perú)</h5>
                                        <div class="text-sm space-y-1">
                                            <p><span class="text-gray-500">Banco:</span> {{ $transaction->sender_bank }}</p>
                                            <p><span class="text-gray-500">Cuenta:</span> {{ $transaction->sender_account_number }}</p>
                                        </div>
                                    </div>

                                    <!-- Comprobante -->
                                    @if($transaction->voucher)
                                    <div class="space-y-2">
                                        <h5 class="font-bold text-sm text-cj-morado-profundo">Comprobante</h5>
                                        <a href="{{ asset('storage/' . $transaction->voucher) }}" target="_blank"
                                           class="inline-flex items-center gap-2 px-3 py-2 bg-cj-turquesa/10 hover:bg-cj-turquesa/20 text-cj-turquesa rounded-lg text-sm transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Ver comprobante
                                        </a>
                                    </div>
                                    @endif

                                    <!-- Observación actual -->
                                    @if($transaction->observation)
                                    <div class="md:col-span-2 space-y-2">
                                        <h5 class="font-bold text-sm text-orange-600">⚠ Observación actual</h5>
                                        <div class="bg-orange-50 border-l-4 border-orange-500 p-3 rounded text-sm text-orange-800">
                                            {{ $transaction->observation }}
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Historial de logs -->
                                    @if($transaction->logs->isNotEmpty())
                                    <div class="md:col-span-2 space-y-2">
                                        <h5 class="font-bold text-sm text-cj-morado-profundo">Historial de cambios</h5>
                                        <div class="space-y-2">
                                            @foreach($transaction->logs as $log)
                                            <div class="bg-white p-3 rounded-lg text-sm border border-gray-200">
                                                <div class="flex justify-between items-start">
                                                    <div>
                                                        <p class="font-semibold text-cj-morado-profundo">{{ $log->user->name }}</p>
                                                        <p class="text-gray-600">{{ ucfirst($log->action) }}: {{ $log->old_status }} → {{ $log->new_status }}</p>
                                                        @if($log->comment)
                                                        <p class="text-gray-500 italic mt-1">{{ $log->comment }}</p>
                                                        @endif
                                                    </div>
                                                    <span class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif

                <!-- Modal de observación -->
                <div x-show="observeModal"
                     x-cloak
                     class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
                     @click.self="observeModal = false">
                    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                        <h3 class="text-xl font-bold text-cj-morado-profundo mb-4">⚠ Marcar como observada</h3>
                        <form :action="'/transactions/' + selectedTransaction + '/observe'" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Observación</label>
                                <textarea x-model="observation" name="observation" rows="4" required
                                          class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all"
                                          placeholder="Describe el problema o lo que necesita corregirse..."></textarea>
                            </div>
                            <div class="flex gap-3">
                                <button type="button" @click="observeModal = false"
                                        class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition-all">
                                    Cancelar
                                </button>
                                <button type="submit"
                                        class="flex-1 px-4 py-3 bg-orange-500 hover:bg-orange-600 text-white rounded-xl font-semibold transition-all">
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
                            <div class="w-10 h-10 bg-gradient-to-br from-cj-turquesa to-teal-400 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-cj-texto">Completar transacción</h3>
                        </div>

                        <form :action="'/transactions/' + selectedTransaction + '/upload-final-voucher'"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf

                            <p class="text-sm text-cj-texto-claro mb-4">
                                Sube el comprobante de transferencia que confirma que el dinero fue enviado a Venezuela. Al completar, el cliente y el vendedor serán notificados.
                            </p>

                            <!-- Drop zone -->
                            <div class="mb-5">
                                <label class="block text-sm font-semibold text-cj-texto mb-2">Comprobante de envío final</label>
                                <label class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-cj-turquesa/40 rounded-xl cursor-pointer bg-cj-turquesa/5 hover:bg-cj-turquesa/10 transition-all"
                                       :class="finalVoucherPreview ? 'border-cj-turquesa' : ''">
                                    <div x-show="!finalVoucherPreview" class="flex flex-col items-center justify-center gap-2 p-4">
                                        <svg class="w-8 h-8 text-cj-turquesa" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        <p class="text-sm text-cj-turquesa font-semibold">Seleccionar comprobante</p>
                                        <p class="text-xs text-cj-texto-claro">JPG, PNG o PDF — máx. 10 MB</p>
                                    </div>
                                    <div x-show="finalVoucherPreview === 'pdf'" class="flex flex-col items-center gap-2 p-4">
                                        <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="text-sm font-semibold text-cj-texto">PDF cargado ✓</p>
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
                                        class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition-all">
                                    Cancelar
                                </button>
                                <button type="submit"
                                        class="flex-1 px-4 py-3 bg-gradient-to-r from-cj-turquesa to-teal-500 hover:opacity-90 text-white rounded-xl font-semibold transition-all shadow-lg shadow-teal-400/30">
                                    Completar transacción
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal de cancelación -->
                <div x-show="cancelModal"
                     x-cloak
                     class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
                     @click.self="cancelModal = false">
                    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                        <h3 class="text-xl font-bold text-red-600 mb-4">✕ Cancelar transacción</h3>
                        <form :action="'/transactions/' + selectedTransaction + '/cancel'" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Motivo de cancelación</label>
                                <textarea x-model="cancelReason" name="reason" rows="3" required
                                          class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-red-500 focus:ring-2 focus:ring-red-500/20 transition-all"
                                          placeholder="¿Por qué se cancela esta transacción?"></textarea>
                            </div>
                            <div class="flex gap-3">
                                <button type="button" @click="cancelModal = false"
                                        class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition-all">
                                    No cancelar
                                </button>
                                <button type="submit"
                                        class="flex-1 px-4 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl font-semibold transition-all">
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
