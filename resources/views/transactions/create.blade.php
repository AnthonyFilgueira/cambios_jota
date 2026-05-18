<x-app-layout>
    <!-- Fondo gradiente animado -->
    <div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
    <div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-cj-texto leading-tight">
            💸 Iniciar Envío
        </h2>
    </x-slot>

    <div class="py-6" x-data="transactionForm()">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/50 p-8">
                <div class="mb-6">
                    <h3 class="text-2xl font-bold text-cj-morado-profundo">Solicitud de Envío</h3>
                    <p class="text-sm text-cj-texto-claro mt-1">Complete todos los datos para procesar su envío Perú → Venezuela</p>
                </div>

                <form method="POST" action="{{ route('transactions.store') }}" enctype="multipart/form-data" class="space-y-8">
                    @csrf

                    <!-- Hidden fields -->
                    <input type="hidden" name="amount_ves" x-model="amountVes">
                    <input type="hidden" name="bonus_amount_pen" x-model="bonusAmountPen">

                    <!-- SECCIÓN 1: DATOS DEL ENVÍO -->
                    <div class="bg-gradient-to-r from-cj-morado-profundo/5 to-cj-turquesa/5 rounded-xl p-6 border border-cj-morado-claro">
                        <h4 class="text-lg font-bold text-cj-morado-profundo mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Datos del Envío - Cotización
                        </h4>

                        <!-- Selector de tasa primero -->
                        <div class="mb-6">
                            <label for="exchange_rate_id" class="block text-sm font-medium text-cj-texto mb-2">
                                Selecciona la Tasa de Cambio *
                            </label>
                            <select
                                name="exchange_rate_id"
                                id="exchange_rate_id"
                                x-model="selectedRateId"
                                @change="onRateChange()"
                                required
                                class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all">
                                <option value="">Seleccione una tasa</option>
                                @foreach($pairs as $pair)
                                    <option
                                        value="{{ $pair['id'] }}"
                                        data-rate="{{ $pair['ves_rate'] }}"
                                        data-usd="{{ $pair['usd_rate'] }}"
                                        data-eur="{{ $pair['eur_rate'] }}">
                                        {{ $pair['from_code'] }} → VES (1 {{ $pair['from_code'] }} = {{ number_format($pair['ves_rate'], 2) }} Bs.)
                                    </option>
                                @endforeach
                            </select>
                            @error('exchange_rate_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <!-- Hidden inputs para tasas BCV -->
                            <input type="hidden" name="usd_bcv_rate" x-model="usdBcvRate">
                            <input type="hidden" name="eur_bcv_rate" x-model="eurBcvRate">
                        </div>

                        <!-- Vendedor asignado (readonly — viene del registro) -->
                        @if($seller)
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-cj-texto mb-2">Tu vendedor asignado</label>
                            <div class="bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio rounded-xl p-4 flex items-center gap-4">
                                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                                    {{ strtoupper(substr($seller->name, 0, 2)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-white font-bold">{{ $seller->name }}</p>
                                    <p class="text-white/70 text-sm font-mono">{{ $seller->code }}</p>
                                </div>
                                <div class="flex items-center gap-1 bg-green-400/20 border border-green-400/40 rounded-full px-3 py-1">
                                    <svg class="w-4 h-4 text-green-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    <span class="text-green-300 text-xs font-semibold">Verificado</span>
                                </div>
                            </div>
                            <p class="text-xs text-cj-texto-claro mt-2">Tu vendedor fue asignado al registrarte y es permanente.</p>
                        </div>

                        <!-- Cuentas del vendedor para depositar -->
                        @if($sellerAccounts->isNotEmpty())
                        <div class="mb-6 bg-green-50 border-2 border-green-300 rounded-xl p-5">
                            <h5 class="text-sm font-bold text-green-800 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                Cuentas habilitadas para tu depósito
                            </h5>
                            @foreach($sellerAccounts as $i => $account)
                            <div class="bg-white rounded-xl p-4 mb-3 border border-green-200">
                                <p class="text-xs uppercase tracking-wider text-green-700 font-bold mb-2">
                                    {{ $i === 0 ? 'Cuenta Principal' : 'Cuenta Alternativa ' . $i }}
                                </p>
                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <span class="text-gray-400 text-xs">Banco</span>
                                        <p class="font-bold text-gray-900">{{ $account->bank->name ?? '—' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 text-xs">Tipo</span>
                                        <p class="font-semibold text-gray-900">{{ ucfirst($account->account_type ?? '—') }}</p>
                                    </div>
                                    <div class="col-span-2">
                                        <span class="text-gray-400 text-xs">Nº de Cuenta</span>
                                        <p class="font-mono font-bold text-lg text-green-700">{{ $account->account_number }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 text-xs">Titular</span>
                                        <p class="font-semibold text-gray-900">{{ $account->account_holder }}</p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-3">
                                <p class="text-xs text-yellow-800">
                                    <strong>Importante:</strong> Deposita el monto exacto a una de estas cuentas y sube el comprobante más abajo.
                                </p>
                            </div>
                        </div>
                        @else
                        <div class="mb-6 bg-yellow-50 border-2 border-yellow-300 rounded-xl p-4">
                            <p class="text-sm text-yellow-800 font-medium">
                                ⚠️ Tu vendedor aún no tiene cuentas asignadas. Contáctalo directamente.
                            </p>
                        </div>
                        @endif
                        @else
                        <div class="mb-6 bg-red-50 border-2 border-red-300 rounded-xl p-4">
                            <p class="text-sm text-red-700 font-medium">
                                No tienes un vendedor asignado. Por favor contacta con soporte para resolver esto.
                            </p>
                        </div>
                        @endif

                        <!-- Sección de cotización -->
                        <div class="bg-cj-morado-claro/20 rounded-xl p-5 mb-6">
                            <h5 class="text-sm font-bold text-cj-morado-profundo mb-3 uppercase tracking-wider">¿Cuánto quieres cotizar?</h5>
                            <div class="grid md:grid-cols-3 gap-4">
                                <!-- Cotizar en USD -->
                                <div>
                                    <label class="block text-xs uppercase tracking-wider font-semibold text-cj-texto-claro mb-2">
                                        En Dólares (USD)
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-cj-texto-claro font-medium">$</span>
                                        <input
                                            type="number"
                                            step="0.01"
                                            x-model="inputUSD"
                                            @input="calculateFromUSD()"
                                            class="w-full pl-8 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all"
                                            placeholder="0.00">
                                    </div>
                                </div>

                                <!-- Cotizar en EUR -->
                                <div>
                                    <label class="block text-xs uppercase tracking-wider font-semibold text-cj-texto-claro mb-2">
                                        En Euros (EUR)
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-cj-texto-claro font-medium">€</span>
                                        <input
                                            type="number"
                                            step="0.01"
                                            x-model="inputEUR"
                                            @input="calculateFromEUR()"
                                            class="w-full pl-8 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all"
                                            placeholder="0.00">
                                    </div>
                                </div>

                                <!-- Monto directo en PEN -->
                                <div>
                                    <label class="block text-xs uppercase tracking-wider font-semibold text-cj-texto-claro mb-2">
                                        En Soles (PEN) *
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-cj-texto-claro font-medium">S/.</span>
                                        <input
                                            type="number"
                                            step="0.01"
                                            name="amount_pen"
                                            id="amount_pen"
                                            x-model="amountPen"
                                            @input="calculateFromPEN()"
                                            value="{{ old('amount_pen') }}"
                                            required
                                            class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all"
                                            placeholder="0.00">
                                    </div>
                                    @error('amount_pen')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Resumen visual del envío -->
                        <div class="grid md:grid-cols-2 gap-4">
                            <!-- Tú envías -->
                            <div class="bg-gradient-to-br from-cj-morado-profundo to-cj-morado-medio text-white rounded-xl p-6">
                                <div class="text-xs uppercase tracking-widest opacity-90 mb-2 font-semibold">Tú envías</div>
                                <div class="text-3xl font-bold">
                                    S/. <span x-text="formatMoney(amountPen)">0.00</span>
                                </div>
                                <div class="text-xs opacity-75 mt-1">Soles peruanos</div>
                            </div>

                            <!-- Tu familiar recibe -->
                            <div class="bg-gradient-to-br from-cj-turquesa to-cj-rosa text-white rounded-xl p-6">
                                <div class="text-xs uppercase tracking-widest opacity-90 mb-2 font-semibold">Tu familiar recibe</div>
                                <div class="text-3xl font-bold flex items-center gap-2">
                                    Bs. <span x-text="formatMoney(amountVes)">0.00</span>
                                </div>
                                <div class="text-xs opacity-90 mt-1">Bolívares venezolanos</div>
                                <div x-show="bonusAmountPen > 0" class="mt-3 bg-white/20 rounded-lg px-3 py-2 flex items-center gap-2">
                                    <span>🎁</span>
                                    <p class="text-xs font-semibold">Incluye bono de S/ <span x-text="bonusAmountPen.toFixed(2)"></span></p>
                                </div>
                            </div>

                            <!-- Tasa aplicada -->
                            <div class="md:col-span-2 bg-cj-morado-medio/10 border-2 border-cj-morado-medio/20 rounded-xl p-4 text-center">
                                <span class="text-xs text-cj-texto-claro uppercase tracking-wider">Tasa de conversión: </span>
                                <span class="text-lg font-bold text-cj-morado-profundo font-mono">
                                    1 PEN = <span x-text="selectedRate.toFixed(2)">0.00</span> VES
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: RECEPTOR EN VENEZUELA -->
                    <div class="bg-gradient-to-r from-cj-rosa/5 to-cj-morado-medio/5 rounded-xl p-6 border border-pink-200"
                         x-data="{ opType: '{{ old('operation_type', 'transferencia') }}' }">
                        <h4 class="text-lg font-bold text-cj-morado-profundo mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            🇻🇪 Receptor en Venezuela
                        </h4>

                        <!-- Tipo de operación -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-cj-texto mb-3">Tipo de operación *</label>
                            <input type="hidden" name="operation_type" :value="opType">
                            <div class="grid grid-cols-2 gap-3">
                                <button type="button"
                                        @click="opType = 'transferencia'"
                                        :class="opType === 'transferencia'
                                            ? 'border-cj-morado-profundo bg-cj-morado-profundo text-white shadow-lg'
                                            : 'border-gray-200 bg-white text-cj-texto hover:border-cj-morado-profundo'"
                                        class="flex flex-col items-center gap-2 p-4 border-2 rounded-xl transition-all">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                    <span class="text-sm font-semibold">Transferencia Bancaria</span>
                                </button>
                                <button type="button"
                                        @click="opType = 'pago_movil'"
                                        :class="opType === 'pago_movil'
                                            ? 'border-cj-turquesa bg-cj-turquesa text-white shadow-lg'
                                            : 'border-gray-200 bg-white text-cj-texto hover:border-cj-turquesa'"
                                        class="flex flex-col items-center gap-2 p-4 border-2 rounded-xl transition-all">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-sm font-semibold">Pago Móvil</span>
                                </button>
                            </div>
                            <p x-show="opType === 'pago_movil'" class="mt-2 text-xs text-cj-turquesa font-medium">
                                Solo necesitas cédula, banco y teléfono — sin número de cuenta.
                            </p>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Cédula del titular -->
                            <div>
                                <label for="recipient_dni" class="block text-sm font-medium text-cj-texto mb-2">Cédula del titular *</label>
                                <input type="text" name="recipient_dni" id="recipient_dni"
                                    value="{{ old('recipient_dni') }}" required
                                    class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all"
                                    placeholder="V-12345678">
                                @error('recipient_dni')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <!-- Banco receptor -->
                            <div>
                                <label for="recipient_bank" class="block text-sm font-medium text-cj-texto mb-2">Banco receptor *</label>
                                <select name="recipient_bank" id="recipient_bank" required
                                    class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all">
                                    <option value="">Selecciona banco</option>
                                    @foreach(['Banco de Venezuela','Banesco','Banco Mercantil','BBVA Provincial','Banco Nacional de Crédito (BNC)','Banco Bicentenario','Banco del Tesoro','Banco Exterior','Corp Banca','Banco Caroni','Sofitasa','Bangente','Bancrecer'] as $b)
                                        <option value="{{ $b }}" {{ old('recipient_bank') == $b ? 'selected' : '' }}>{{ $b }}</option>
                                    @endforeach
                                </select>
                                @error('recipient_bank')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <!-- Teléfono del titular -->
                            <div>
                                <label for="recipient_phone" class="block text-sm font-medium text-cj-texto mb-2">Teléfono del titular *</label>
                                <input type="tel" name="recipient_phone" id="recipient_phone"
                                    value="{{ old('recipient_phone') }}" required
                                    class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all"
                                    placeholder="0412-1234567">
                                @error('recipient_phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <!-- Número de cuenta (solo Transferencia) -->
                            <div x-show="opType === 'transferencia'" x-cloak>
                                <label for="recipient_account_number" class="block text-sm font-medium text-cj-texto mb-2">
                                    Número de cuenta *
                                </label>
                                <input type="text" name="recipient_account_number" id="recipient_account_number"
                                    value="{{ old('recipient_account_number') }}"
                                    :required="opType === 'transferencia'"
                                    class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all"
                                    placeholder="0102-0000-00-0000123456">
                                @error('recipient_account_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <!-- Tipo de cuenta (solo Transferencia) -->
                            <div x-show="opType === 'transferencia'" x-cloak>
                                <label for="recipient_account_type" class="block text-sm font-medium text-cj-texto mb-2">
                                    Tipo de cuenta *
                                </label>
                                <div class="flex gap-3 mt-1">
                                    <label class="flex-1 flex items-center gap-2 border-2 rounded-xl p-3 cursor-pointer transition-all"
                                           :class="$refs.actype?.value === 'ahorro' ? 'border-cj-turquesa bg-cj-turquesa/5' : 'border-gray-200'">
                                        <input type="radio" name="recipient_account_type" value="ahorro" x-ref="actype"
                                               {{ old('recipient_account_type', 'ahorro') === 'ahorro' ? 'checked' : '' }}
                                               class="text-cj-turquesa">
                                        <span class="text-sm font-medium">Ahorro</span>
                                    </label>
                                    <label class="flex-1 flex items-center gap-2 border-2 rounded-xl p-3 cursor-pointer transition-all"
                                           :class="$refs.actype?.value === 'corriente' ? 'border-cj-turquesa bg-cj-turquesa/5' : 'border-gray-200'">
                                        <input type="radio" name="recipient_account_type" value="corriente"
                                               {{ old('recipient_account_type') === 'corriente' ? 'checked' : '' }}
                                               class="text-cj-turquesa">
                                        <span class="text-sm font-medium">Corriente</span>
                                    </label>
                                </div>
                                @error('recipient_account_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 3: TU TRANSFERENCIA DESDE PERÚ -->
                    <div class="bg-gradient-to-r from-cj-turquesa/5 to-cj-morado-profundo/5 rounded-xl p-6 border border-teal-200">
                        <h4 class="text-lg font-bold text-cj-morado-profundo mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            🇵🇪 Tu transferencia desde Perú
                        </h4>

                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- DNI del titular que transfiere -->
                            <div>
                                <label for="sender_dni" class="block text-sm font-medium text-cj-texto mb-2">DNI del titular que transfiere *</label>
                                <input type="text" name="sender_dni" id="sender_dni"
                                    value="{{ old('sender_dni') }}" required
                                    class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all"
                                    placeholder="12345678">
                                @error('sender_dni')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <!-- Banco origen (Perú) -->
                            <div>
                                <label for="sender_bank" class="block text-sm font-medium text-cj-texto mb-2">Banco desde donde transferiste *</label>
                                <select name="sender_bank" id="sender_bank" required
                                    class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all">
                                    <option value="">Selecciona banco</option>
                                    @foreach(['BCP — Banco de Crédito del Perú','Interbank','BBVA Perú','Scotiabank Perú','Banco de la Nación','BanBif','Mibanco','Banco Pichincha','Banco GNB','Banco Falabella Perú','Banco Ripley','Caja Metropolitana'] as $b)
                                        <option value="{{ $b }}" {{ old('sender_bank') == $b ? 'selected' : '' }}>{{ $b }}</option>
                                    @endforeach
                                </select>
                                @error('sender_bank')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <!-- Nº cuenta origen (opcional) -->
                            <div>
                                <label for="sender_account_number" class="block text-sm font-medium text-cj-texto mb-2">
                                    Nº de cuenta origen <span class="text-cj-texto-claro font-normal">(opcional)</span>
                                </label>
                                <input type="text" name="sender_account_number" id="sender_account_number"
                                    value="{{ old('sender_account_number') }}"
                                    class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all"
                                    placeholder="000-000000-0-00">
                                @error('sender_account_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <!-- Comprobante con preview -->
                            <div x-data="{ preview: null, fileName: '' }" class="md:col-span-2">
                                <label class="block text-sm font-medium text-cj-texto mb-2">Comprobante de depósito *</label>

                                <!-- Dropzone -->
                                <label for="voucher"
                                       class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-cj-turquesa hover:bg-cj-turquesa/5 transition-all"
                                       :class="preview ? 'border-green-400 bg-green-50' : ''"
                                       @dragover.prevent @drop.prevent="
                                           const f = $event.dataTransfer.files[0];
                                           if (f) {
                                               fileName = f.name;
                                               if (f.type.startsWith('image/')) {
                                                   const r = new FileReader();
                                                   r.onload = e => preview = e.target.result;
                                                   r.readAsDataURL(f);
                                               } else { preview = 'pdf'; }
                                               const dt = new DataTransfer();
                                               dt.items.add(f);
                                               $refs.voucherInput.files = dt.files;
                                           }">
                                    <template x-if="!preview">
                                        <div class="text-center px-4">
                                            <svg class="mx-auto h-10 w-10 text-cj-texto-claro mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                            <p class="text-sm font-medium text-cj-texto">Arrastra tu comprobante aquí</p>
                                            <p class="text-xs text-cj-texto-claro mt-1">o haz clic para seleccionar</p>
                                            <p class="text-xs text-cj-texto-claro mt-1">JPG, PNG, PDF — máx. 10 MB</p>
                                        </div>
                                    </template>
                                    <template x-if="preview && preview !== 'pdf'">
                                        <div class="relative w-full h-full flex items-center justify-center">
                                            <img :src="preview" class="max-h-36 max-w-full object-contain rounded-lg">
                                            <span class="absolute top-2 right-2 bg-green-500 text-white text-xs rounded-full px-2 py-0.5">✓ Listo</span>
                                        </div>
                                    </template>
                                    <template x-if="preview === 'pdf'">
                                        <div class="text-center">
                                            <svg class="mx-auto h-10 w-10 text-red-400 mb-1" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z"/><path d="M14 2v6h6"/></svg>
                                            <p class="text-sm font-medium text-cj-texto" x-text="fileName"></p>
                                            <span class="text-xs text-green-600 font-medium">✓ PDF seleccionado</span>
                                        </div>
                                    </template>
                                </label>

                                <input type="file" name="voucher" id="voucher" x-ref="voucherInput"
                                    accept="image/*,.pdf" required class="hidden"
                                    @change="
                                        const f = $event.target.files[0];
                                        if (f) {
                                            fileName = f.name;
                                            if (f.type.startsWith('image/')) {
                                                const r = new FileReader();
                                                r.onload = e => preview = e.target.result;
                                                r.readAsDataURL(f);
                                            } else { preview = 'pdf'; }
                                        }">

                                <!-- Instrucciones -->
                                <div class="mt-3 bg-blue-50 border border-blue-200 rounded-xl p-3">
                                    <p class="text-xs font-semibold text-blue-700 mb-1">¿Qué debe mostrar el comprobante?</p>
                                    <ul class="text-xs text-blue-600 space-y-0.5 list-disc list-inside">
                                        <li>El monto exacto transferido</li>
                                        <li>Número de cuenta destino y banco</li>
                                        <li>Fecha y hora de la operación</li>
                                    </ul>
                                </div>

                                @error('voucher')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 4: NOTAS ADICIONALES -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-cj-texto mb-2">
                            Notas Adicionales (Opcional)
                        </label>
                        <textarea
                            name="notes"
                            id="notes"
                            rows="3"
                            class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all"
                            placeholder="Información adicional sobre el envío...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Información importante -->
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-xl">
                        <div class="flex">
                            <svg class="h-5 w-5 text-blue-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm text-blue-700">
                                    <strong>Importante:</strong> Asegúrese de que todos los datos bancarios sean correctos. Una vez procesada la transferencia, no se podrán hacer cambios.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-4 pt-4">
                        <a href="{{ route('dashboard') }}" class="flex-1 px-6 py-3 border-2 border-gray-300 rounded-xl text-cj-texto font-semibold hover:bg-gray-50 transition-all text-center">
                            Cancelar
                        </a>
                        <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white rounded-xl font-bold hover:shadow-2xl transform hover:-translate-y-1 transition-all shadow-lg">
                            Enviar Solicitud
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
    function transactionForm() {
        return {
            // Datos principales
            amountPen: 0,
            selectedRateId: '',
            selectedRate: 0,
            amountVes: 0,
            usdBcvRate: 0,
            eurBcvRate: 0,

            // Bono activo (cargado desde backend)
            bonusRules: @json($bonusPreview['rules'] ?? []),
            bonusAmountPen: 0,

            // Inputs de cotización
            inputUSD: '',
            inputEUR: '',

            // Búsqueda de vendedor
            sellerCode: '',
            sellerData: null,
            sellerAccounts: [],
            sellerFound: false,
            sellerSearching: false,
            sellerError: '',

            init() {
                // Intentar cargar datos del simulador
                this.loadSimulatorData();
            },

            async searchSeller() {
                const code = this.sellerCode.trim().toUpperCase();

                if (code.length < 3) {
                    this.sellerFound = false;
                    this.sellerData = null;
                    this.sellerError = '';
                    return;
                }

                this.sellerSearching = true;
                this.sellerError = '';
                this.sellerFound = false;

                try {
                    const response = await fetch(`/api/sellers/search/${code}`);
                    const data = await response.json();

                    if (response.ok && data.success) {
                        this.sellerData = data.seller;
                        this.sellerAccounts = data.accounts || [];
                        this.sellerFound = true;
                        this.sellerError = '';
                    } else {
                        this.sellerFound = false;
                        this.sellerData = null;
                        this.sellerAccounts = [];
                        this.sellerError = '❌ Código de vendedor no encontrado';
                    }
                } catch (error) {
                    console.error('Error buscando vendedor:', error);
                    this.sellerError = 'Error al buscar vendedor. Intenta de nuevo.';
                    this.sellerFound = false;
                    this.sellerData = null;
                } finally {
                    this.sellerSearching = false;
                }
            },

            loadSimulatorData() {
                // Primero intentar desde URL params
                const urlParams = new URLSearchParams(window.location.search);
                let data = null;

                if (urlParams.has('amount_pen')) {
                    data = {
                        amount_pen: urlParams.get('amount_pen'),
                        amount_ves: urlParams.get('amount_ves'),
                        exchange_rate_id: urlParams.get('exchange_rate_id'),
                        ves_rate: urlParams.get('ves_rate'),
                        usd_bcv_rate: urlParams.get('usd_bcv_rate'),
                        eur_bcv_rate: urlParams.get('eur_bcv_rate')
                    };
                } else {
                    // Intentar desde sessionStorage
                    const stored = sessionStorage.getItem('pendingTransaction');
                    if (stored) {
                        data = JSON.parse(stored);
                        // Limpiar después de cargar
                        sessionStorage.removeItem('pendingTransaction');
                    }
                }

                // Si hay datos, pre-llenar el formulario
                if (data) {
                    this.amountPen = parseFloat(data.amount_pen) || 0;
                    this.amountVes = parseFloat(data.amount_ves) || 0;
                    this.selectedRateId = data.exchange_rate_id || '';
                    this.selectedRate = parseFloat(data.ves_rate) || 0;
                    this.usdBcvRate = parseFloat(data.usd_bcv_rate) || 0;
                    this.eurBcvRate = parseFloat(data.eur_bcv_rate) || 0;
                }
            },

            onRateChange() {
                const select = document.getElementById('exchange_rate_id');
                const option = select.options[select.selectedIndex];

                if (option && option.dataset.rate) {
                    this.selectedRate = parseFloat(option.dataset.rate);
                    this.usdBcvRate = parseFloat(option.dataset.usd);
                    this.eurBcvRate = parseFloat(option.dataset.eur);
                    this.recalculate();
                }
            },

            // Calcular desde PEN (input directo)
            calculateFromPEN() {
                this.inputUSD = '';
                this.inputEUR = '';
                this.recalculate();
            },

            // Redondear a 2 decimales
            round(value) {
                return Math.round(value * 100) / 100;
            },

            // Calcular desde USD (BCV)
            calculateFromUSD() {
                this.inputEUR = '';
                const usd = parseFloat(this.inputUSD) || 0;
                const usdRate = parseFloat(this.usdBcvRate) || 0;
                const vesRate = parseFloat(this.selectedRate) || 0;

                if (usdRate > 0 && vesRate > 0) {
                    // USD → VES → PEN
                    const vesIntermedios = usd * usdRate;
                    this.amountPen = this.round(vesIntermedios / vesRate);
                    this.amountVes = this.round(vesIntermedios);
                } else {
                    alert('Por favor, selecciona primero una tasa de cambio.');
                    this.inputUSD = '';
                }
            },

            // Calcular desde EUR (BCV)
            calculateFromEUR() {
                this.inputUSD = '';
                const eur = parseFloat(this.inputEUR) || 0;
                const eurRate = parseFloat(this.eurBcvRate) || 0;
                const vesRate = parseFloat(this.selectedRate) || 0;

                if (eurRate > 0 && vesRate > 0) {
                    // EUR → VES → PEN
                    const vesIntermedios = eur * eurRate;
                    this.amountPen = this.round(vesIntermedios / vesRate);
                    this.amountVes = this.round(vesIntermedios);
                } else {
                    alert('Por favor, selecciona primero una tasa de cambio.');
                    this.inputEUR = '';
                }
            },

            // Calcula el bono de una regla específica para el monto actual
            calcularBonusRegla(rule, monto) {
                if (!monto || monto <= 0) return 0;
                if (rule.value_type === 'fixed') return rule.value;
                if (rule.value_type === 'percentage') return Math.round(monto * rule.value / 100 * 100) / 100;
                return 0;
            },

            // Suma todos los bonos para el monto dado
            calcularBonusTotal(monto) {
                if (!monto || monto <= 0 || !this.bonusRules.length) return 0;
                return Math.round(
                    this.bonusRules.reduce((sum, rule) => sum + this.calcularBonusRegla(rule, monto), 0) * 100
                ) / 100;
            },

            // Recalcular VES basado en PEN (incluye bono)
            recalculate() {
                const pen = parseFloat(this.amountPen) || 0;
                const rate = parseFloat(this.selectedRate) || 0;
                const bonus = this.calcularBonusTotal(pen);
                this.bonusAmountPen = bonus;
                this.amountVes = this.round((pen + bonus) * rate);
            },

            // Formatear montos
            formatMoney(value) {
                return new Intl.NumberFormat('es-PE', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(value || 0);
            }
        }
    }
    </script>
</x-app-layout>
