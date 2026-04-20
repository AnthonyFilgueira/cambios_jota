<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 py-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-cj-texto">Nueva Tasa de Cambio</h1>
            <a href="{{ route('exchange_rates.index') }}" class="text-cj-texto-claro hover:text-cj-morado-profundo">
                ← Volver
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <form method="POST" action="{{ route('exchange_rates.store') }}" class="space-y-6">
                @csrf

                <!-- Selección de Par de Divisas -->
                <div class="bg-cj-morado-claro rounded-lg p-4 border-2 border-cj-morado-profundo">
                    <h3 class="text-sm font-semibold text-cj-texto mb-3">🔀 Seleccionar Par de Divisas</h3>
                    <p class="text-xs text-cj-texto-claro mb-4">Elige qué conversión estás configurando</p>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Par de Divisas
                        </label>
                        <select
                            name="currency_pair_id"
                            class="w-full border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-cj-turquesa focus:border-transparent text-lg"
                            required
                        >
                            <option value="">Selecciona un par...</option>
                            @php
                                $pairs = \App\Models\CurrencyPair::with(['fromCurrency', 'toCurrency'])
                                    ->orderBy('id')
                                    ->get();
                            @endphp
                            @foreach($pairs as $pair)
                                <option value="{{ $pair->id }}" {{ old('currency_pair_id') == $pair->id ? 'selected' : '' }}>
                                    {{ $pair->fromCurrency->code }} → {{ $pair->toCurrency->code }}
                                    ({{ $pair->fromCurrency->name }} a {{ $pair->toCurrency->name }})
                                </option>
                            @endforeach
                        </select>
                        @error('currency_pair_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">
                            💡 Solo puedes tener 1 tasa activa por par. Al crear, las tasas anteriores del mismo par se desactivarán automáticamente.
                        </p>
                    </div>
                </div>

                <!-- Referencias BCV -->
                <div class="bg-cj-fondo rounded-lg p-4 border-l-4 border-cj-morado-profundo">
                    <h3 class="text-sm font-semibold text-cj-texto mb-3">📊 Tasas de Referencia BCV (Venezuela)</h3>
                    <p class="text-xs text-cj-texto-claro mb-4">Estas tasas son iguales para todos los pares y solo sirven de referencia informativa</p>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                USD → VES (BCV)
                            </label>
                            <input
                                type="number"
                                name="usd_rate"
                                step="0.01"
                                value="{{ old('usd_rate', 479.78) }}"
                                class="w-full border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-cj-turquesa focus:border-transparent"
                                required
                            >
                            @error('usd_rate')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Ej: 479.78</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                EUR → VES (BCV)
                            </label>
                            <input
                                type="number"
                                name="eur_rate"
                                step="0.01"
                                value="{{ old('eur_rate', 565.98) }}"
                                class="w-full border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-cj-turquesa focus:border-transparent"
                                required
                            >
                            @error('eur_rate')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Ej: 565.98</p>
                        </div>
                    </div>
                </div>

                <!-- Tasa del Par -->
                <div class="bg-white border-2 border-cj-morado-profundo rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-cj-texto mb-3">💱 Tasa Específica del Par</h3>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Tasa VES (cuántos VES por 1 unidad de origen)
                        </label>
                        <input
                            type="number"
                            name="ves_rate"
                            step="0.00001"
                            value="{{ old('ves_rate', 173.71000) }}"
                            class="w-full border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-cj-turquesa focus:border-transparent text-lg font-mono"
                            required
                        >
                        @error('ves_rate')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-2">
                            <strong>Ejemplos:</strong><br>
                            • PEN→VES: 173.71 (1 PEN = 173.71 VES)<br>
                            • ARS→VES: 2.50 (1 ARS = 2.50 VES)<br>
                            • USD→VES: 479.78 (1 USD = 479.78 VES)
                        </p>
                    </div>
                </div>

                <!-- Separador visual -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-cj-texto mb-4">
                        💼 Configuración de Comisiones
                    </h3>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Comisión del Dueño (%)
                        </label>
                        <input
                            type="number"
                            name="boss_commission_default"
                            step="0.01"
                            min="0"
                            max="100"
                            value="{{ old('boss_commission_default', 15.00) }}"
                            class="w-full border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-cj-turquesa focus:border-transparent"
                            required
                        >
                        @error('boss_commission_default')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">
                            ⚠️ Esta comisión se aplicará AUTOMÁTICAMENTE a todos los vendedores existentes
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 pt-4">
                    <button
                        type="submit"
                        class="flex-1 bg-cj-morado-profundo hover:bg-cj-morado-medio text-white font-semibold py-3 rounded-lg transition-colors"
                    >
                        Guardar Tasa
                    </button>
                    <a
                        href="{{ route('exchange_rates.index') }}"
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 rounded-lg text-center transition-colors"
                    >
                        Cancelar
                    </a>
                </div>
            </form>
        </div>

        <div class="mt-6 bg-cj-morado-claro rounded-lg p-4">
            <h3 class="font-semibold text-sm text-cj-texto mb-2">💡 Cómo Funciona</h3>
            <ul class="text-xs text-cj-texto-claro space-y-1">
                <li>• <strong>Referencias BCV:</strong> Se copian en todos los pares (solo informativas)</li>
                <li>• <strong>Tasa VES:</strong> Es la tasa específica de conversión del par</li>
                <li>• <strong>Comisiones:</strong> Se calculan sobre el monto en divisa origen (no sobre tasas)</li>
                <li>• <strong>Ejemplo PEN→VES:</strong> Cliente envía 100 PEN → recibe 100 × 173.71 = 17,371 VES</li>
                <li>• <strong>Comisión:</strong> Se calcula sobre 100 PEN (vendedor 5% + dueño 15%)</li>
            </ul>
        </div>
    </div>
</x-app-layout>
