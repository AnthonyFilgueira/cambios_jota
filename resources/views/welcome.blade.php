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
                            <p class="text-xs text-cj-texto-claro hidden sm:block">Envíos Perú - Venezuela</p>
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
                            <div class="font-mono text-sm font-semibold text-cj-morado-profundo" x-text="tasas.usd.toFixed(2)"></div>
                        </div>

                        <!-- Tasa EUR -->
                        <div class="text-center">
                            <div class="text-xs text-cj-texto-claro uppercase tracking-wide mb-1 font-medium">EUR</div>
                            <div class="font-mono text-sm font-semibold text-cj-morado-profundo" x-text="tasas.eur.toFixed(2)"></div>
                        </div>

                        <!-- Tasa VES -->
                        <div class="text-center">
                            <div class="text-xs text-cj-texto-claro uppercase tracking-wide mb-1 font-medium">VES</div>
                            <div class="font-mono text-sm font-semibold text-cj-turquesa" x-text="tasas.ves.toFixed(2)"></div>
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
                            <option :value="pair.id" x-text="`${pair.flag} ${pair.from_country} (${pair.from_code})`"></option>
                        </template>
                    </select>

                    <!-- Indicador visual de ruta -->
                    <div class="mt-3 flex items-center justify-center gap-3 text-sm">
                        <span x-text="currentPair.flag" class="text-2xl"></span>
                        <span x-text="currentPair.from_country" class="font-semibold text-cj-texto"></span>
                        <span class="text-cj-morado-profundo text-xl font-bold">→</span>
                        <span class="font-semibold text-cj-texto">Venezuela</span>
                        <span class="text-2xl">🇻🇪</span>
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
                    <div class="text-4xl font-bold">
                        <span x-text="currentPair.from_symbol"></span> <span x-text="formatearMonto(montoEnviar)">0.00</span>
                    </div>
                    <div class="text-xs opacity-75 mt-1" x-text="currentPair.from_name"></div>
                </div>

                <!-- Separador con tasa aplicada -->
                <div class="bg-cj-morado-medio text-white text-center py-3">
                    <div class="text-xs uppercase tracking-wider opacity-90 mb-1">Tasa de conversión</div>
                    <div class="font-mono text-lg font-bold">
                        1 <span x-text="currentPair.from_code"></span> = <span x-text="currentPair.ves_rate.toFixed(2)">0.00</span> VES
                    </div>
                </div>

                <!-- Resultado: TU FAMILIAR RECIBE -->
                <div class="bg-gradient-to-br from-cj-turquesa to-cj-rosa text-white p-6">
                    <div class="text-xs uppercase tracking-widest opacity-95 mb-2 font-semibold">Tu familiar recibe</div>
                    <div class="text-4xl font-bold flex items-center gap-2">
                        Bs. <span x-text="formatearMonto(vesRecibir)">0.00</span>
                        <span class="text-2xl">🇻🇪</span>
                    </div>
                    <div class="text-xs opacity-90 mt-1">Bolívares venezolanos</div>
                </div>

                <!-- Botón CTA -->
                <div class="p-6 bg-white">
                    <button class="w-full bg-cj-rosa hover:bg-pink-600 text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                        Iniciar envío →
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

        <script>
            function simulador() {
                return {
                    // Pares disponibles (cargados desde backend)
                    pairs: @json($pairs),
                    selectedPairId: {{ $pairs->firstWhere('is_active', true)->id ?? $pairs->first()->id }},
                    currentPair: {},

                    // Tasas de referencia BCV
                    tasas: {
                        usd: {{ $rates->usd_rate }},
                        eur: {{ $rates->eur_rate }},
                        ves: {{ $rates->ves_rate }}
                    },

                    // Inputs del usuario
                    inputUSD: '',
                    inputEUR: '',
                    inputOrigen: '',

                    // Resultados calculados
                    montoEnviar: 0,
                    vesRecibir: 0,

                    // Inicializar
                    init() {
                        this.cambiarPar();
                    },

                    // Cambiar par seleccionado
                    cambiarPar() {
                        this.currentPair = this.pairs.find(p => p.id == this.selectedPairId) || this.pairs[0];
                        this.limpiarInputs();
                    },

                    limpiarInputs() {
                        this.inputUSD = '';
                        this.inputEUR = '';
                        this.inputOrigen = '';
                        this.montoEnviar = 0;
                        this.vesRecibir = 0;
                    },

                    // CASO 1: Cliente ingresa moneda origen directamente
                    calcularDesdeOrigen() {
                        this.inputUSD = '';
                        this.inputEUR = '';
                        const monto = parseFloat(this.inputOrigen) || 0;
                        this.montoEnviar = monto;
                        this.vesRecibir = monto * this.currentPair.ves_rate;
                    },

                    // CASO 2: Cliente ingresa USD (conversión a tasa BCV dólar)
                    calcularDesdeUSD() {
                        this.inputEUR = '';
                        this.inputOrigen = '';
                        const usd = parseFloat(this.inputUSD) || 0;
                        const vesIntermedios = usd * this.currentPair.usd_rate;
                        this.montoEnviar = vesIntermedios / this.currentPair.ves_rate;
                        this.vesRecibir = vesIntermedios;
                    },

                    // CASO 3: Cliente ingresa EUR (conversión a tasa BCV euro)
                    calcularDesdeEUR() {
                        this.inputUSD = '';
                        this.inputOrigen = '';
                        const eur = parseFloat(this.inputEUR) || 0;
                        const vesIntermedios = eur * this.currentPair.eur_rate;
                        this.montoEnviar = vesIntermedios / this.currentPair.ves_rate;
                        this.vesRecibir = vesIntermedios;
                    },

                    // Formatear montos con separadores de miles y 2 decimales
                    formatearMonto(valor) {
                        return new Intl.NumberFormat('es-PE', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }).format(valor);
                    }
                }
            }
        </script>
    </body>
</html>
