<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🔄 Nuevo Par de Divisas
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white rounded-lg shadow p-6">
                <form action="{{ route('currency-pairs.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="from_currency_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Divisa Origen *
                        </label>
                        <select
                            id="from_currency_id"
                            name="from_currency_id"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-cj-morado-medio focus:ring focus:ring-cj-morado-claro @error('from_currency_id') border-red-500 @enderror"
                            required>
                            <option value="">-- Seleccionar divisa origen --</option>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}" {{ old('from_currency_id') == $currency->id ? 'selected' : '' }}>
                                    {{ $currency->flag_emoji }} {{ $currency->code }} - {{ $currency->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('from_currency_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="to_currency_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Divisa Destino *
                        </label>
                        <select
                            id="to_currency_id"
                            name="to_currency_id"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-cj-morado-medio focus:ring focus:ring-cj-morado-claro @error('to_currency_id') border-red-500 @enderror"
                            required>
                            <option value="">-- Seleccionar divisa destino --</option>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}" {{ old('to_currency_id') == $currency->id ? 'selected' : '' }}>
                                    {{ $currency->flag_emoji }} {{ $currency->code }} - {{ $currency->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('to_currency_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">La divisa destino debe ser diferente a la origen</p>
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox"
                                   name="is_active"
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-cj-morado-profundo shadow-sm focus:border-cj-morado-medio focus:ring focus:ring-cj-morado-claro">
                            <span class="ml-2 text-sm text-gray-700">Par activo</span>
                        </label>
                        <p class="mt-1 ml-6 text-xs text-gray-500">
                            Podrás asignar corredores después de crear el par
                        </p>
                    </div>

                    <div class="flex gap-4">
                        <button type="submit"
                                class="px-6 py-2 bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white rounded-md hover:opacity-90">
                            Crear Par
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
