<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            💱 Editar Divisa: {{ $currency->code }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white rounded-lg shadow p-6">
                <form action="{{ route('currencies.update', $currency) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            Código ISO (3 letras) *
                        </label>
                        <input type="text"
                               id="code"
                               name="code"
                               value="{{ old('code', $currency->code) }}"
                               maxlength="3"
                               class="w-full uppercase rounded-md border-gray-300 shadow-sm focus:border-cj-morado-medio focus:ring focus:ring-cj-morado-claro @error('code') border-red-500 @enderror"
                               required>
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre Completo *
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name', $currency->name) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-cj-morado-medio focus:ring focus:ring-cj-morado-claro @error('name') border-red-500 @enderror"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="symbol" class="block text-sm font-medium text-gray-700 mb-2">
                            Símbolo *
                        </label>
                        <input type="text"
                               id="symbol"
                               name="symbol"
                               value="{{ old('symbol', $currency->symbol) }}"
                               maxlength="10"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-cj-morado-medio focus:ring focus:ring-cj-morado-claro @error('symbol') border-red-500 @enderror"
                               required>
                        @error('symbol')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                            País *
                        </label>
                        <input type="text"
                               id="country"
                               name="country"
                               value="{{ old('country', $currency->country) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-cj-morado-medio focus:ring focus:ring-cj-morado-claro @error('country') border-red-500 @enderror"
                               required>
                        @error('country')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="flag_emoji" class="block text-sm font-medium text-gray-700 mb-2">
                            Emoji de Bandera (opcional)
                        </label>
                        <input type="text"
                               id="flag_emoji"
                               name="flag_emoji"
                               value="{{ old('flag_emoji', $currency->flag_emoji) }}"
                               maxlength="10"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-cj-morado-medio focus:ring focus:ring-cj-morado-claro">
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox"
                                   name="is_active"
                                   {{ old('is_active', $currency->is_active) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-cj-morado-profundo shadow-sm focus:border-cj-morado-medio focus:ring focus:ring-cj-morado-claro">
                            <span class="ml-2 text-sm text-gray-700">Divisa activa</span>
                        </label>
                    </div>

                    <div class="flex gap-4">
                        <button type="submit"
                                class="px-6 py-2 bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white rounded-md hover:opacity-90">
                            Actualizar Divisa
                        </button>
                        <a href="{{ route('currencies.index') }}"
                           class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
