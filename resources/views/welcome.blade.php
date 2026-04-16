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
    <body class="bg-cj-fondo min-h-screen flex items-center justify-center p-4">
        <div x-data="simulador()" class="w-full max-w-md">
            <!-- Header con logo y marca -->
            <header class="bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio rounded-t-3xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-14 h-14 bg-cj-turquesa rounded-xl flex items-center justify-center shadow-md">
                            <span class="text-2xl font-bold text-white">CJ</span>
                        </div>
                        <div class="text-white">
                            <h1 class="text-xl font-bold">Cambios JottaA</h1>
                            <p class="text-xs opacity-90">Simulador de envíos</p>
                        </div>
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

                <!-- Ruta fija: Perú → Venezuela -->
                <div class="bg-cj-morado-claro p-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">🇵🇪</span>
                        <span class="font-semibold text-cj-texto">Perú (PEN)</span>
                    </div>
                    <div class="text-cj-morado-profundo text-xl font-bold">→</div>
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-cj-texto">Venezuela (VES)</span>
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

                    <!-- Fila: PEN solo (ancho completo) -->
                    <div>
                        <label class="block text-xs uppercase tracking-wider font-semibold text-cj-texto-claro mb-2">
                            En soles (Directo)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-cj-texto-claro font-medium">S/</span>
                            <input
                                type="number"
                                step="0.01"
                                placeholder="0.00"
                                x-model="inputPEN"
                                @input="calcularDesdePEN()"
                                class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all"
                            >
                        </div>
                    </div>
                </div>

                <!-- Resultado: TÚ ENVÍAS -->
                <div class="bg-gradient-to-br from-cj-morado-profundo to-cj-morado-medio text-white p-6">
                    <div class="text-xs uppercase tracking-widest opacity-90 mb-2 font-semibold">Tú envías</div>
                    <div class="text-4xl font-bold">
                        S/ <span x-text="formatearMonto(penEnviar)">0.00</span>
                    </div>
                    <div class="text-xs opacity-75 mt-1">Soles peruanos</div>
                </div>

                <!-- Separador con tasa aplicada -->
                <div class="bg-cj-morado-medio text-white text-center py-3">
                    <div class="text-xs uppercase tracking-wider opacity-90 mb-1">Tasa de conversión</div>
                    <div class="font-mono text-lg font-bold">
                        1 PEN = <span x-text="tasas.ves.toFixed(2)">0.00</span> VES
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
                    // Tasas del día (cargadas desde el backend)
                    tasas: {
                        usd: {{ $rates->usd_rate }},
                        eur: {{ $rates->eur_rate }},
                        ves: {{ $rates->ves_rate }}
                    },

                    // Inputs del usuario
                    inputUSD: '',
                    inputEUR: '',
                    inputPEN: '',

                    // Resultados calculados
                    penEnviar: 0,
                    vesRecibir: 0,

                    // CASO 1: Cliente ingresa PEN directamente
                    calcularDesdePEN() {
                        this.inputUSD = '';
                        this.inputEUR = '';
                        const pen = parseFloat(this.inputPEN) || 0;
                        this.penEnviar = pen;
                        this.vesRecibir = pen * this.tasas.ves;
                    },

                    // CASO 2: Cliente ingresa USD (conversión a tasa BCV dólar)
                    calcularDesdeUSD() {
                        this.inputEUR = '';
                        this.inputPEN = '';
                        const usd = parseFloat(this.inputUSD) || 0;
                        const vesIntermedios = usd * this.tasas.usd;
                        this.penEnviar = vesIntermedios / this.tasas.ves;
                        this.vesRecibir = vesIntermedios;
                    },

                    // CASO 3: Cliente ingresa EUR (conversión a tasa BCV euro)
                    calcularDesdeEUR() {
                        this.inputUSD = '';
                        this.inputPEN = '';
                        const eur = parseFloat(this.inputEUR) || 0;
                        const vesIntermedios = eur * this.tasas.eur;
                        this.penEnviar = vesIntermedios / this.tasas.ves;
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
