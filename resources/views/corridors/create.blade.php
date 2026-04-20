<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🏦 Nuevo Corredor
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white rounded-lg shadow p-6">
                <form action="{{ route('corridors.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre del Corredor *
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-cj-morado-medio focus:ring focus:ring-cj-morado-claro @error('name') border-red-500 @enderror"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Ejemplo: Western Union, MoneyGram, Remitly</p>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Descripción
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-cj-morado-medio focus:ring focus:ring-cj-morado-claro @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Breve descripción del servicio (opcional)</p>
                    </div>

                    <div class="mb-4">
                        <label for="logo_url" class="block text-sm font-medium text-gray-700 mb-2">
                            URL del Logo
                        </label>
                        <input type="url"
                               id="logo_url"
                               name="logo_url"
                               value="{{ old('logo_url') }}"
                               placeholder="https://ejemplo.com/logo.png"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-cj-morado-medio focus:ring focus:ring-cj-morado-claro @error('logo_url') border-red-500 @enderror">
                        @error('logo_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">URL completa del logo (opcional)</p>
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox"
                                   name="is_active"
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-cj-morado-profundo shadow-sm focus:border-cj-morado-medio focus:ring focus:ring-cj-morado-claro">
                            <span class="ml-2 text-sm text-gray-700">Corredor activo</span>
                        </label>
                    </div>

                    <div class="flex gap-4">
                        <button type="submit"
                                class="px-6 py-2 bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white rounded-md hover:opacity-90">
                            Crear Corredor
                        </button>
                        <a href="{{ route('corridors.index') }}"
                           class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
