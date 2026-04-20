<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🔄 Editar Par: {{ $currencyPair->full_display }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white rounded-lg shadow p-6">
                <form action="{{ route('currency-pairs.update', $currencyPair) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="from_currency_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Divisa Origen *
                            </label>
                            <select
                                id="from_currency_id"
                                name="from_currency_id"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-cj-morado-medio focus:ring focus:ring-cj-morado-claro @error('from_currency_id') border-red-500 @enderror"
                                required>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->id }}" {{ old('from_currency_id', $currencyPair->from_currency_id) == $currency->id ? 'selected' : '' }}>
                                        {{ $currency->flag_emoji }} {{ $currency->code }} - {{ $currency->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('from_currency_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="to_currency_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Divisa Destino *
                            </label>
                            <select
                                id="to_currency_id"
                                name="to_currency_id"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-cj-morado-medio focus:ring focus:ring-cj-morado-claro @error('to_currency_id') border-red-500 @enderror"
                                required>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->id }}" {{ old('to_currency_id', $currencyPair->to_currency_id) == $currency->id ? 'selected' : '' }}>
                                        {{ $currency->flag_emoji }} {{ $currency->code }} - {{ $currency->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('to_currency_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox"
                                   name="is_active"
                                   {{ old('is_active', $currencyPair->is_active) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-cj-morado-profundo shadow-sm focus:border-cj-morado-medio focus:ring focus:ring-cj-morado-claro">
                            <span class="ml-2 text-sm text-gray-700">Par activo</span>
                        </label>
                    </div>

                    <hr class="my-6 border-gray-200">

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            🏦 Corredores Disponibles
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Selecciona los corredores que pueden procesar este par de divisas:
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @forelse($corridors as $corridor)
                                <label class="flex items-center p-3 border rounded-md hover:bg-gray-50 cursor-pointer {{ in_array($corridor->id, $assignedCorridors) ? 'border-cj-morado-medio bg-cj-morado-claro bg-opacity-10' : 'border-gray-300' }}">
                                    <input type="checkbox"
                                           name="corridors[]"
                                           value="{{ $corridor->id }}"
                                           {{ in_array($corridor->id, $assignedCorridors) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-cj-morado-profundo shadow-sm focus:border-cj-morado-medio focus:ring focus:ring-cj-morado-claro">
                                    <span class="ml-3 text-sm font-medium text-gray-700">
                                        {{ $corridor->name }}
                                    </span>
                                </label>
                            @empty
                                <p class="col-span-2 text-sm text-gray-500">
                                    No hay corredores disponibles.
                                    <a href="{{ route('corridors.create') }}" class="text-cj-morado-medio hover:underline">
                                        Crear corredor
                                    </a>
                                </p>
                            @endforelse
                        </div>

                        @if($corridors->count() > 0)
                            <p class="mt-3 text-xs text-gray-500">
                                {{ count($assignedCorridors) }} de {{ $corridors->count() }} corredores seleccionados
                            </p>
                        @endif
                    </div>

                    <div class="flex gap-4">
                        <button type="submit"
                                class="px-6 py-2 bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white rounded-md hover:opacity-90">
                            Actualizar Par
                        </button>
                        <a href="{{ route('currency-pairs.index') }}"
                           class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
