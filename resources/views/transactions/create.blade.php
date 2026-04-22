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

                    <!-- SECCIÓN 2: DATOS BANCARIOS EN VENEZUELA -->
                    <div class="bg-gradient-to-r from-cj-rosa/5 to-cj-morado-medio/5 rounded-xl p-6 border border-pink-200">
                        <h4 class="text-lg font-bold text-cj-morado-profundo mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            Datos Bancarios del Receptor (Venezuela)
                        </h4>

                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Banco receptor -->
                            <div>
                                <label for="recipient_bank" class="block text-sm font-medium text-cj-texto mb-2">
                                    Banco Receptor *
                                </label>
                                <input
                                    type="text"
                                    name="recipient_bank"
                                    id="recipient_bank"
                                    value="{{ old('recipient_bank') }}"
                                    required
                                    class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all"
                                    placeholder="Ej: Banco de Venezuela">
                                @error('recipient_bank')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Número de cuenta -->
                            <div>
                                <label for="recipient_account_number" class="block text-sm font-medium text-cj-texto mb-2">
                                    Número de Cuenta *
                                </label>
                                <input
                                    type="text"
                                    name="recipient_account_number"
                                    id="recipient_account_number"
                                    value="{{ old('recipient_account_number') }}"
                                    required
                                    class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all"
                                    placeholder="0000-0000-00-0000000000">
                                @error('recipient_account_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- DNI/Cédula -->
                            <div>
                                <label for="recipient_dni" class="block text-sm font-medium text-cj-texto mb-2">
                                    Cédula de Identidad *
                                </label>
                                <input
                                    type="text"
                                    name="recipient_dni"
                                    id="recipient_dni"
                                    value="{{ old('recipient_dni') }}"
                                    required
                                    class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all"
                                    placeholder="V-12345678">
                                @error('recipient_dni')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tipo de cuenta -->
                            <div>
                                <label for="recipient_account_type" class="block text-sm font-medium text-cj-texto mb-2">
                                    Tipo de Cuenta *
                                </label>
                                <select
                                    name="recipient_account_type"
                                    id="recipient_account_type"
                                    required
                                    class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all">
                                    <option value="">Seleccione</option>
                                    <option value="ahorro" {{ old('recipient_account_type') == 'ahorro' ? 'selected' : '' }}>Ahorro</option>
                                    <option value="corriente" {{ old('recipient_account_type') == 'corriente' ? 'selected' : '' }}>Corriente</option>
                                </select>
                                @error('recipient_account_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 3: DATOS DE TRANSFERENCIA (PERÚ) -->
                    <div class="bg-gradient-to-r from-cj-turquesa/5 to-cj-morado-profundo/5 rounded-xl p-6 border border-teal-200">
                        <h4 class="text-lg font-bold text-cj-morado-profundo mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                            Datos de Transferencia desde Perú
                        </h4>

                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Banco origen -->
                            <div>
                                <label for="sender_bank" class="block text-sm font-medium text-cj-texto mb-2">
                                    Banco desde donde Transfiere *
                                </label>
                                <input
                                    type="text"
                                    name="sender_bank"
                                    id="sender_bank"
                                    value="{{ old('sender_bank') }}"
                                    required
                                    class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all"
                                    placeholder="Ej: BCP, Interbank, BBVA">
                                @error('sender_bank')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Cuenta origen -->
                            <div>
                                <label for="sender_account_number" class="block text-sm font-medium text-cj-texto mb-2">
                                    Número de Cuenta Origen *
                                </label>
                                <input
                                    type="text"
                                    name="sender_account_number"
                                    id="sender_account_number"
                                    value="{{ old('sender_account_number') }}"
                                    required
                                    class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all"
                                    placeholder="000-000000-0-00">
                                @error('sender_account_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Comprobante -->
                            <div class="md:col-span-2">
                                <label for="voucher" class="block text-sm font-medium text-cj-texto mb-2">
                                    Comprobante de Transferencia *
                                </label>
                                <input
                                    type="file"
                                    name="voucher"
                                    id="voucher"
                                    accept="image/*,.pdf"
                                    required
                                    class="w-full p-3 border-2 border-dashed border-gray-300 rounded-xl hover:border-cj-turquesa transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-cj-morado-profundo file:text-white hover:file:bg-cj-morado-medio cursor-pointer">
                                <p class="text-xs text-cj-texto-claro mt-2">Formatos aceptados: JPG, PNG, PDF (máx. 2MB)</p>
                                @error('voucher')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
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

            // Inputs de cotización
            inputUSD: '',
            inputEUR: '',

            init() {
                // Intentar cargar datos del simulador
                this.loadSimulatorData();
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

            // Recalcular VES basado en PEN
            recalculate() {
                const pen = parseFloat(this.amountPen) || 0;
                const rate = parseFloat(this.selectedRate) || 0;
                this.amountVes = this.round(pen * rate);
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
