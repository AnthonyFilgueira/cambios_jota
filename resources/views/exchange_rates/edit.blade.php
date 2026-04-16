<x-app-layout>
    <div class="max-w-md mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-6">Editar Tasa de Cambio</h1>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <form method="POST" action="{{ route('exchange_rates.update', $exchangeRate) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tasa USD (BCV)
                    </label>
                    <input
                        type="number"
                        name="usd_rate"
                        step="0.00001"
                        value="{{ old('usd_rate', $exchangeRate->usd_rate) }}"
                        class="w-full border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-cj-turquesa focus:border-transparent"
                        required
                    >
                    @error('usd_rate')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Tasa BCV del dólar</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tasa EUR (BCV)
                    </label>
                    <input
                        type="number"
                        name="eur_rate"
                        step="0.00001"
                        value="{{ old('eur_rate', $exchangeRate->eur_rate) }}"
                        class="w-full border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-cj-turquesa focus:border-transparent"
                        required
                    >
                    @error('eur_rate')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Tasa BCV del euro</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tasa VES/PEN (Propia)
                    </label>
                    <input
                        type="number"
                        name="ves_rate"
                        step="0.00001"
                        value="{{ old('ves_rate', $exchangeRate->ves_rate) }}"
                        class="w-full border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-cj-turquesa focus:border-transparent"
                        required
                    >
                    @error('ves_rate')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Tu tasa de conversión VES/PEN</p>
                </div>

                @if($exchangeRate->is_active)
                    <div class="bg-cj-turquesa/10 border border-cj-turquesa rounded-lg p-3">
                        <p class="text-sm text-cj-texto">
                            <span class="font-semibold">✓ Esta es la tasa activa.</span> Los cambios se reflejarán inmediatamente en el simulador público.
                        </p>
                    </div>
                @endif

                <div class="flex gap-3 pt-4">
                    <button
                        type="submit"
                        class="flex-1 bg-cj-morado-profundo hover:bg-cj-morado-medio text-white font-semibold py-3 rounded-lg transition-colors"
                    >
                        Actualizar Tasa
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
    </div>
</x-app-layout>
