<x-app-layout>
    <!-- Fondo gradiente animado -->
    <div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
    <div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            💸 Iniciar Envío
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/50 p-8">
                <h3 class="text-2xl font-bold text-cj-morado-profundo mb-6">Solicitud de Envío</h3>

                <form method="POST" action="{{ route('transactions.store') }}" class="space-y-6">
                    @csrf

                    <!-- Monto a enviar -->
                    <div>
                        <label for="amount_pen" class="block text-sm font-medium text-gray-700 mb-2">
                            Monto a Enviar (Soles - PEN)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-medium">S/.</span>
                            <input
                                type="number"
                                step="0.01"
                                name="amount_pen"
                                id="amount_pen"
                                value="{{ old('amount_pen') }}"
                                required
                                class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all"
                                placeholder="0.00">
                        </div>
                        @error('amount_pen')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tasa de cambio -->
                    <div>
                        <label for="exchange_rate_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Tasa de Cambio
                        </label>
                        <select
                            name="exchange_rate_id"
                            id="exchange_rate_id"
                            required
                            class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all">
                            @foreach($pairs as $pair)
                                <option value="{{ $rates->id }}">
                                    {{ $pair['from_code'] }} → VES (1 {{ $pair['from_code'] }} = {{ number_format($pair['ves_rate'], 2) }} Bs.)
                                </option>
                            @endforeach
                        </select>
                        @error('exchange_rate_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notas adicionales -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
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
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                        <div class="flex">
                            <svg class="h-5 w-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm text-blue-700">
                                    <strong>Importante:</strong> Esta es una solicitud de envío. Un vendedor se pondrá en contacto contigo para completar la transacción.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-4">
                        <a href="{{ route('dashboard') }}" class="flex-1 px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition text-center">
                            Cancelar
                        </a>
                        <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white rounded-lg hover:opacity-90 transition shadow-lg">
                            Enviar Solicitud
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
