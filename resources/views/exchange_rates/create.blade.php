<x-app-layout>
    <div class="max-w-md mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-6">Nueva Tasa de Cambio</h1>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <form method="POST" action="{{ route('exchange_rates.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tasa USD (BCV)
                    </label>
                    <input
                        type="number"
                        name="usd_rate"
                        step="0.00001"
                        value="{{ old('usd_rate', 479.77750) }}"
                        class="w-full border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-cj-turquesa focus:border-transparent"
                        required
                    >
                    @error('usd_rate')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Tasa BCV del dólar (ej: 479.77750)</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tasa EUR (BCV)
                    </label>
                    <input
                        type="number"
                        name="eur_rate"
                        step="0.00001"
                        value="{{ old('eur_rate', 565.98392) }}"
                        class="w-full border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-cj-turquesa focus:border-transparent"
                        required
                    >
                    @error('eur_rate')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Tasa BCV del euro (ej: 565.98392)</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tasa VES/PEN (Propia)
                    </label>
                    <input
                        type="number"
                        name="ves_rate"
                        step="0.00001"
                        value="{{ old('ves_rate', 173.71000) }}"
                        class="w-full border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-cj-turquesa focus:border-transparent"
                        required
                    >
                    @error('ves_rate')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Tu tasa de conversión VES/PEN (ej: 173.71000)</p>
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
            <h3 class="font-semibold text-sm text-cj-texto mb-2">💡 Información</h3>
            <ul class="text-xs text-cj-texto-claro space-y-1">
                <li>• La primera tasa que crees se activará automáticamente</li>
                <li>• Solo puede haber una tasa activa a la vez</li>
                <li>• Los clientes verán la tasa activa en el simulador</li>
            </ul>
        </div>
    </div>
</x-app-layout>
