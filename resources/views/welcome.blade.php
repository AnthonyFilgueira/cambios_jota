<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Cambios Jota - Simulador de Envíos</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen relative overflow-x-hidden">
        <!-- Fondo principal con gradiente animado -->
        <div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>

        <!-- Capa de overlay semitransparente -->
        <div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

        <!-- Círculos decorativos flotantes -->
        <div class="fixed top-20 left-10 w-72 h-72 bg-purple-400/30 rounded-full blur-3xl animate-float"></div>
        <div class="fixed bottom-20 right-10 w-96 h-96 bg-teal-400/30 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
        <div class="fixed top-1/2 left-1/2 w-64 h-64 bg-pink-400/20 rounded-full blur-3xl animate-float" style="animation-delay: 4s;"></div>

        <!-- Navbar Superior con glassmorphism mejorado -->
        <nav class="bg-white/70 backdrop-blur-xl shadow-2xl sticky top-0 z-50 border-b-2 border-white/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo y Marca -->
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-cj-morado-profundo to-cj-turquesa rounded-lg flex items-center justify-center shadow-lg hover:scale-110 transition-transform duration-300">
                            <span class="text-lg font-bold text-white">CJ</span>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-cj-texto">Cambios Jotta</h1>
                            <p class="text-xs text-cj-texto-claro hidden sm:block">Envíos Internacionales</p>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex items-center gap-3">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-sm font-semibold text-cj-morado-profundo hover:text-cj-morado-medio transition">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-semibold text-cj-morado-profundo hover:text-cj-turquesa transition-all duration-300 relative after:content-[''] after:absolute after:w-0 after:h-0.5 after:bg-cj-turquesa after:left-0 after:-bottom-1 after:transition-all after:duration-300 hover:after:w-full">
                                Ingresar
                            </a>
                            <a href="{{ route('register') }}" class="bg-gradient-to-r from-cj-morado-profundo via-cj-rosa to-cj-turquesa text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg hover:shadow-2xl transform hover:-translate-y-1 hover:scale-105 transition-all duration-300 animate-gradient-x">
                                Registrarse ✨
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Contenido Principal -->
        <div class="flex items-center justify-center p-4 py-8">
            <div x-data="simulador()" class="w-full max-w-md">
                <!-- Header del Simulador -->
                <header class="bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio rounded-t-3xl p-6 shadow-lg">
                    <div class="text-center">
                        <div class="flex justify-center mb-3">
                            <div class="w-14 h-14 bg-cj-turquesa rounded-xl flex items-center justify-center shadow-md">
                                <span class="text-2xl font-bold text-white">CJ</span>
                            </div>
                        </div>
                        <div class="text-white">
                            <h2 class="text-xl font-bold">Simulador de Envíos</h2>
                            <p class="text-xs opacity-90">Calcula cuánto recibirá tu familiar</p>
                        </div>
                    </div>
                </header>

            <!-- Card principal con sombra -->
            <div class="bg-white shadow-xl">
                <!-- Sección de tasas del día (Solo lectura) -->
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-cj-texto uppercase tracking-wider mb-4">Tasas del día</h3>

                    <div class="grid grid-cols-3 gap-3">
                        <!-- Tasa USD -->
                        <div class="text-center">
                            <div class="text-xs text-cj-texto-claro uppercase tracking-wide mb-1 font-medium">USD</div>
                            <div class="font-mono text-sm font-semibold text-cj-morado-profundo" x-text="formatTasa(tasas.usd)"></div>
                        </div>

                        <!-- Tasa EUR -->
                        <div class="text-center">
                            <div class="text-xs text-cj-texto-claro uppercase tracking-wide mb-1 font-medium">EUR</div>
                            <div class="font-mono text-sm font-semibold text-cj-morado-profundo" x-text="formatTasa(tasas.eur)"></div>
                        </div>

                        <!-- Tasa destino (dinámica según par seleccionado) -->
                        <div class="text-center">
                            <div class="text-xs text-cj-texto-claro uppercase tracking-wide mb-1 font-medium" x-text="currentPair.to_code || 'VES'"></div>
                            <div class="font-mono text-sm font-semibold text-cj-turquesa" x-text="formatTasa(tasas.ves)"></div>
                        </div>
                    </div>
                </div>

                <!-- Selector de País Origen -->
                <div class="bg-cj-morado-claro p-4">
                    <label class="block text-xs uppercase tracking-wider font-semibold text-cj-texto mb-2">
                        Selecciona el país de origen
                    </label>
                    <select
                        x-model="selectedPairId"
                        @change="cambiarPar()"
                        class="w-full p-3 border-2 border-cj-morado-profundo/20 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all font-semibold text-cj-texto">
                        <template x-for="pair in pairs" :key="pair.id">
                            <option :value="pair.id" x-text="`${pair.flag} ${pair.from_country} → ${pair.to_country} ${pair.to_flag}`"></option>
                        </template>
                    </select>

                    <!-- Indicador visual de ruta -->
                    <div class="mt-3 flex items-center justify-center gap-3 text-sm">
                        <span x-text="currentPair.flag" class="text-2xl"></span>
                        <span x-text="currentPair.from_country" class="font-semibold text-cj-texto"></span>
                        <span class="text-cj-morado-profundo text-xl font-bold">→</span>
                        <span class="font-semibold text-cj-texto" x-text="currentPair.to_country || 'Venezuela'"></span>
                        <span class="text-2xl" x-text="currentPair.to_flag || '🇻🇪'"></span>
                    </div>
                </div>

                <!-- Inputs de conversión -->
                <div class="p-6 space-y-4 bg-gradient-to-b from-white to-gray-50">
                    <!-- Fila: USD y EUR lado a lado -->
                    <div class="grid grid-cols-2 gap-3">
                        <!-- Input USD -->
                        <div>
                            <label class="block text-xs uppercase tracking-wider font-semibold text-cj-texto-claro mb-2">
                                En dólares (Tasa BCV USD)
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-cj-texto-claro font-medium">$</span>
                                <input
                                    type="number"
                                    step="0.01"
                                    placeholder="0.00"
                                    x-model="inputUSD"
                                    @input="calcularDesdeUSD()"
                                    class="w-full pl-8 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all"
                                >
                            </div>
                        </div>

                        <!-- Input EUR -->
                        <div>
                            <label class="block text-xs uppercase tracking-wider font-semibold text-cj-texto-claro mb-2">
                                En euros (Tasa BCV EUR)
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-cj-texto-claro font-medium">€</span>
                                <input
                                    type="number"
                                    step="0.01"
                                    placeholder="0.00"
                                    x-model="inputEUR"
                                    @input="calcularDesdeEUR()"
                                    class="w-full pl-8 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Fila: Moneda origen (dinámico) -->
                    <div>
                        <label class="block text-xs uppercase tracking-wider font-semibold text-cj-texto-claro mb-2">
                            <span x-text="`En ${currentPair.from_name} (Directo)`"></span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-cj-texto-claro font-medium" x-text="currentPair.from_symbol"></span>
                            <input
                                type="number"
                                step="0.01"
                                placeholder="0.00"
                                x-model="inputOrigen"
                                @input="calcularDesdeOrigen()"
                                class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all"
                            >
                        </div>
                    </div>
                </div>

                <!-- Resultado: TÚ ENVÍAS -->
                <div class="bg-gradient-to-br from-cj-morado-profundo to-cj-morado-medio text-white p-6">
                    <div class="text-xs uppercase tracking-widest opacity-90 mb-2 font-semibold">Tú envías</div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <div class="text-4xl font-bold">
                            <span x-text="currentPair.from_symbol"></span>&nbsp;<span x-text="formatearMonto(montoEnviar)">0.00</span>
                        </div>
                        <!-- Badge de bono al lado del monto -->
                        <div x-show="bonusTotal > 0 && montoEnviar > 0" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100"
                             class="flex items-center gap-1.5 bg-yellow-400 text-yellow-900 font-black text-sm px-3 py-1.5 rounded-full shadow-lg animate-bounce">
                            <span>🎁</span>
                            <span>+<span x-text="formatearMonto(bonusTotal)"></span> BONO</span>
                        </div>
                    </div>
                    <div class="text-xs opacity-75 mt-1" x-text="currentPair.from_name"></div>
                    <!-- Detalle de bonos activos -->
                    <div x-show="bonusTotal > 0 && montoEnviar > 0" class="mt-2 space-y-0.5">
                        <template x-for="rule in bonusRules" :key="rule.name">
                            <div class="text-xs opacity-80 flex items-center gap-1">
                                <span x-text="rule.type_icon"></span>
                                <span x-text="rule.name + ': +' + (rule.value_type === 'fixed' ? currentPair.from_symbol + ' ' + rule.value.toFixed(2) : rule.value.toFixed(1) + '% = ' + currentPair.from_symbol + ' ' + formatearMonto(calcularBonusRegla(rule, montoEnviar)))"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Separador con tasa aplicada -->
                <div class="bg-cj-morado-medio text-white text-center py-3">
                    <div class="text-xs uppercase tracking-wider opacity-90 mb-1">Tasa de conversión</div>
                    <div class="font-mono text-lg font-bold">
                        1 <span x-text="currentPair.from_code"></span> = <span x-text="formatTasa(currentPair.ves_rate)">0.00</span> <span x-text="currentPair.to_code || 'VES'"></span>
                    </div>
                    <div x-show="bonusTotal > 0 && montoEnviar > 0" class="text-xs mt-1 opacity-80">
                        Base <span x-text="formatearMonto(montoEnviar)"></span> + 🎁 <span x-text="formatearMonto(bonusTotal)"></span> bono = <span x-text="formatearMonto(montoEnviar + bonusTotal)"></span> efectivo
                    </div>
                </div>

                <!-- Resultado: TU FAMILIAR RECIBE -->
                <div class="bg-gradient-to-br from-cj-turquesa to-cj-rosa text-white p-6">
                    <div class="text-xs uppercase tracking-widest opacity-95 mb-2 font-semibold">Tu familiar recibe</div>
                    <div class="text-4xl font-bold flex items-center gap-2">
                        <span x-text="currentPair.to_symbol || 'Bs.'"></span> <span x-text="formatearMonto(vesRecibir)">0.00</span>
                        <span class="text-2xl" x-text="currentPair.to_flag || '🇻🇪'"></span>
                    </div>
                    <!-- Comparativa con/sin bono -->
                    <div x-show="bonusTotal > 0 && montoEnviar > 0" class="mt-3">
                        <div class="flex items-center justify-between text-xs bg-black/20 rounded-xl px-3 py-2">
                            <span class="opacity-80 line-through">Sin bono: <span x-text="currentPair.to_symbol || 'Bs.'"></span> <span x-text="formatearMonto(round(montoEnviar * currentPair.ves_rate))"></span></span>
                            <span class="font-black text-yellow-300 text-sm">🎁 +<span x-text="currentPair.to_symbol || 'Bs.'"></span> <span x-text="formatearMonto(round(bonusTotal * currentPair.ves_rate))"></span> extra</span>
                        </div>
                    </div>
                    <div class="text-xs opacity-90 mt-2" x-text="currentPair.to_name || 'Bolívar Digital'">Bolívares venezolanos</div>
                </div>

                <!-- Botón CTA -->
                <div class="p-6 bg-white">
                    <button
                        @click="iniciarEnvio({{ auth()->check() ? 'true' : 'false' }})"
                        class="block w-full bg-gradient-to-r from-cj-rosa to-pink-600 hover:opacity-90 text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 text-center">
                        @auth
                            Iniciar envío →
                        @else
                            Registrarse e iniciar envío →
                        @endauth
                    </button>
                </div>
            </div>

            <!-- Footer informativo -->
            <div class="bg-white rounded-b-3xl shadow-lg p-4 text-center">
                <p class="text-xs text-cj-texto-claro">
                    Las tasas mostradas son referenciales. El tipo de cambio final se confirmará al momento de la transacción.
                </p>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════
             MODAL WOW — Celebración de bono
        ══════════════════════════════════════════════════ -->
        <div x-data="{ open: false }" x-init="$watch('$store.bonusModal.show', v => open = v)"
             x-show="open" style="display:none"
             class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4">

            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"
                 @click="open = false; $store.bonusModal.show = false"></div>

            <!-- Card modal -->
            <div class="relative w-full max-w-sm bg-white rounded-3xl shadow-2xl overflow-hidden"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100">

                <!-- Header con gradiente festivo -->
                <div class="bg-gradient-to-br from-cj-morado-profundo via-purple-600 to-cj-rosa p-6 text-center relative overflow-hidden">
                    <!-- Destellos decorativos -->
                    <div class="absolute top-2 left-6 text-2xl opacity-60 animate-bounce" style="animation-delay:0.1s">✨</div>
                    <div class="absolute top-3 right-8 text-xl opacity-60 animate-bounce" style="animation-delay:0.3s">🎊</div>
                    <div class="absolute bottom-2 left-12 text-lg opacity-50 animate-bounce" style="animation-delay:0.5s">⭐</div>
                    <div class="absolute bottom-3 right-6 text-2xl opacity-60 animate-bounce" style="animation-delay:0.2s">✨</div>

                    <div class="text-5xl mb-2">🎉</div>
                    <h2 class="text-2xl font-black text-white">¡Felicidades!</h2>
                    <p class="text-white/80 text-sm mt-1">Por confiar en <span class="font-bold text-yellow-300">Cambios Jota</span></p>
                    <p class="text-white/70 text-xs mt-0.5">tienes un <span class="font-bold text-yellow-300">bono especial</span> en tu envío</p>
                </div>

                <!-- Cuerpo del modal -->
                <div class="p-5 space-y-4" x-data="simuladorModal()">

                    <!-- Desglose del bono -->
                    <div class="bg-gradient-to-br from-purple-50 to-teal-50 rounded-2xl p-4 border border-purple-100 space-y-2">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Tú envías</span>
                            <span class="font-bold text-gray-800">
                                <span x-text="$store.bonusModal.symbol"></span> <span x-text="$store.bonusModal.montoBase"></span>
                            </span>
                        </div>

                        <template x-for="rule in $store.bonusModal.rules" :key="rule.name">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-green-700 font-semibold flex items-center gap-1">
                                    <span x-text="rule.type_icon"></span>
                                    <span x-text="rule.name"></span>
                                </span>
                                <span class="font-black text-green-600">
                                    +<span x-text="$store.bonusModal.symbol"></span> <span x-text="rule.bonusStr"></span>
                                </span>
                            </div>
                        </template>

                        <div class="border-t border-purple-200 pt-2 mt-1">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">Tu familiar recibe</span>
                                <div class="text-right">
                                    <div class="text-xl font-black text-teal-600">
                                        Bs. <span x-text="$store.bonusModal.vesConBono"></span> 🇻🇪
                                    </div>
                                    <div class="text-xs text-green-600 font-bold">
                                        🎁 +Bs. <span x-text="$store.bonusModal.vesBonoStr"></span> extra gratis
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mensaje motivador -->
                    <div class="text-center">
                        <p class="text-xs text-gray-500 leading-relaxed">
                            Tu familiar recibirá
                            <strong class="text-teal-600">Bs. <span x-text="$store.bonusModal.vesBonoStr"></span> más</strong>
                            gracias a nuestros bonos de bienvenida.<br>
                            <span class="text-purple-700 font-semibold">¡No dejes pasar esta oportunidad!</span>
                        </p>
                    </div>

                    <!-- CTAs -->
                    <div class="space-y-2">
                        <button @click="open = false; $store.bonusModal.show = false; $store.bonusModal.ctaCallback()"
                                class="w-full py-3.5 bg-gradient-to-r from-cj-morado-profundo via-purple-600 to-cj-rosa text-white font-black rounded-2xl shadow-lg hover:opacity-90 hover:scale-[1.02] transition-all text-sm">
                            ✈️ Aprovechar mi bono y enviar →
                        </button>
                        <button @click="open = false; $store.bonusModal.show = false"
                                class="w-full py-2.5 text-gray-400 hover:text-gray-600 text-xs font-medium transition-colors">
                            Seguir calculando
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Store global para comunicar el simulador con el modal
            document.addEventListener('alpine:init', () => {
                Alpine.store('bonusModal', {
                    show: false,
                    symbol: '',
                    montoBase: '0.00',
                    rules: [],
                    vesConBono: '0.00',
                    vesBonoStr: '0.00',
                    ctaCallback: () => {},
                });
            });

            function simuladorModal() {
                return {}; // solo accede al store, no necesita estado propio
            }

            function simulador() {
                return {
                    pairs: @json($pairs),
                    selectedPairId: {{ (collect($pairs)->firstWhere('is_active', true) ?? collect($pairs)->first())['id'] ?? 0 }},
                    currentPair: {},
                    tasas: {
                        usd: {{ $rates->usd_rate }},
                        eur: {{ $rates->eur_rate }},
                        ves: {{ $rates->ves_rate }}
                    },
                    inputUSD: '',
                    inputEUR: '',
                    inputOrigen: '',
                    montoEnviar: 0,
                    vesRecibir: 0,

                    // Reglas de bonos con tipo y valor para recalcular en el cliente
                    bonusRules: @json($bonusPreview['rules'] ?? []),
                    bonusModalShown: false,

                    // Computed: bono total según el monto actual
                    get bonusTotal() {
                        return this.calcularBonusTotal(this.montoEnviar);
                    },

                    init() {
                        this.cambiarPar();
                    },

                    cambiarPar() {
                        this.currentPair = this.pairs.find(p => p.id == this.selectedPairId) || this.pairs[0];
                        this.tasas.usd = this.currentPair.usd_rate;
                        this.tasas.eur = this.currentPair.eur_rate;
                        this.tasas.ves = this.currentPair.ves_rate;
                        this.limpiarInputs();
                    },

                    limpiarInputs() {
                        this.inputUSD = '';
                        this.inputEUR = '';
                        this.inputOrigen = '';
                        this.montoEnviar = 0;
                        this.vesRecibir = 0;
                    },

                    round(value) {
                        return Math.round(value * 100) / 100;
                    },

                    formatearMonto(valor) {
                        return new Intl.NumberFormat('es-PE', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }).format(valor);
                    },

                    formatTasa(val) {
                        if (!val || val === 0) return '0.00';
                        if (val >= 100)  return val.toFixed(2);
                        if (val >= 1)    return val.toFixed(4);
                        if (val >= 0.01) return val.toFixed(4);
                        return val.toFixed(6);
                    },

                    // Calcula el bono de una regla para un monto dado
                    calcularBonusRegla(rule, monto) {
                        if (!monto || monto <= 0) return 0;
                        if (rule.value_type === 'fixed') return rule.value;
                        if (rule.value_type === 'percentage') return this.round(monto * rule.value / 100);
                        return 0;
                    },

                    // Suma todos los bonos para un monto dado, filtrado por moneda del par activo
                    calcularBonusTotal(monto) {
                        if (!monto || monto <= 0 || !this.bonusRules.length) return 0;
                        const activeCurrencyId = this.currentPair?.from_currency_id ?? null;
                        const applicable = this.bonusRules.filter(rule =>
                            rule.currency_id === null || rule.currency_id === activeCurrencyId
                        );
                        return this.round(
                            applicable.reduce((sum, rule) => sum + this.calcularBonusRegla(rule, monto), 0)
                        );
                    },

                    calcularDesdeOrigen() {
                        this.inputUSD = '';
                        this.inputEUR = '';
                        const monto = parseFloat(this.inputOrigen) || 0;
                        this.montoEnviar = this.round(monto);
                        const bonus = this.calcularBonusTotal(monto);
                        this.vesRecibir = this.round((monto + bonus) * this.currentPair.ves_rate);
                        this.dispararModalBono(monto, bonus);
                    },

                    calcularDesdeUSD() {
                        this.inputEUR = '';
                        this.inputOrigen = '';
                        const usd = parseFloat(this.inputUSD) || 0;
                        const vesIntermedios = usd * this.currentPair.usd_rate;
                        this.montoEnviar = this.round(vesIntermedios / this.currentPair.ves_rate);
                        const bonus = this.calcularBonusTotal(this.montoEnviar);
                        this.vesRecibir = this.round(vesIntermedios + bonus * this.currentPair.ves_rate);
                        this.dispararModalBono(this.montoEnviar, bonus);
                    },

                    calcularDesdeEUR() {
                        this.inputUSD = '';
                        this.inputOrigen = '';
                        const eur = parseFloat(this.inputEUR) || 0;
                        const vesIntermedios = eur * this.currentPair.eur_rate;
                        this.montoEnviar = this.round(vesIntermedios / this.currentPair.ves_rate);
                        const bonus = this.calcularBonusTotal(this.montoEnviar);
                        this.vesRecibir = this.round(vesIntermedios + bonus * this.currentPair.ves_rate);
                        this.dispararModalBono(this.montoEnviar, bonus);
                    },

                    // Dispara el modal de celebración (solo una vez por sesión)
                    dispararModalBono(monto, bonus) {
                        if (!bonus || bonus <= 0 || monto <= 0 || this.bonusModalShown) return;
                        this.bonusModalShown = true;

                        const vesSinBono = this.round(monto * this.currentPair.ves_rate);
                        const vesConBono = this.round((monto + bonus) * this.currentPair.ves_rate);
                        const vesBono   = this.round(vesConBono - vesSinBono);

                        // Preparar datos del store para el modal
                        Alpine.store('bonusModal').symbol    = this.currentPair.from_symbol;
                        Alpine.store('bonusModal').montoBase = this.formatearMonto(monto);
                        Alpine.store('bonusModal').vesConBono = this.formatearMonto(vesConBono);
                        Alpine.store('bonusModal').vesBonoStr = this.formatearMonto(vesBono);
                        Alpine.store('bonusModal').rules = this.bonusRules.map(rule => ({
                            ...rule,
                            bonusStr: this.formatearMonto(this.calcularBonusRegla(rule, monto))
                        }));
                        Alpine.store('bonusModal').ctaCallback = () => this.iniciarEnvio({{ auth()->check() ? 'true' : 'false' }});

                        // Pequeño delay para que el usuario vea el resultado primero
                        setTimeout(() => { Alpine.store('bonusModal').show = true; }, 600);
                    },

                    iniciarEnvio(isAuthenticated) {
                        if (this.montoEnviar <= 0) {
                            alert('Por favor, ingresa un monto válido antes de continuar.');
                            return;
                        }
                        const simulatorData = {
                            amount_pen: this.montoEnviar.toFixed(2),
                            amount_ves: this.vesRecibir.toFixed(2),
                            pair_id: this.currentPair.id,
                            exchange_rate_id: this.currentPair.exchange_rate_id,
                            from_currency: this.currentPair.from_code,
                            ves_rate: this.currentPair.ves_rate,
                            usd_bcv_rate: this.tasas.usd,
                            eur_bcv_rate: this.tasas.eur
                        };
                        sessionStorage.setItem('pendingTransaction', JSON.stringify(simulatorData));
                        if (isAuthenticated) {
                            const params = new URLSearchParams(simulatorData);
                            window.location.href = `{{ route('transactions.create') }}?${params.toString()}`;
                        } else {
                            window.location.href = '{{ route('register') }}';
                        }
                    }
                }
            }
        </script>
    </body>
</html>
