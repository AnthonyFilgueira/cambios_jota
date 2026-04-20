<x-app-layout>
    <div class="max-w-md mx-auto px-4 py-6">
        <!-- Encabezado con paleta Cambio J -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                {{ $sale->isObserved() ? 'Corregir Venta' : 'Editar Venta' }}
            </h1>
            <p class="text-gray-600 mt-2">
                {{ $sale->isObserved() ? 'Realiza las correcciones solicitadas' : 'Modificar datos de la venta #'.$sale->id }}
            </p>
        </div>

        <!-- Alerta de observación (solo si está observada) -->
        @if($sale->isObserved() && $sale->admin_observation)
            <div class="mb-6 bg-orange-100 border-l-4 border-orange-500 p-4 rounded">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-orange-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <p class="font-semibold text-orange-900 mb-2">Observación del Administrador:</p>
                        <p class="text-sm text-orange-800 leading-relaxed">{{ $sale->admin_observation }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Mensajes flash -->
        <x-notifications />

        <form action="{{ route('sales.update', $sale) }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Vendedor</label>
                <select name="seller_id" class="w-full border-gray-300 rounded-md p-2.5 focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                    @foreach($sellers as $seller)
                        <option value="{{ $seller->id }}" {{ $sale->seller_id == $seller->id ? 'selected' : '' }}>
                            {{ $seller->name }}
                        </option>
                    @endforeach
                </select>
                @error('seller_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Monto</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-500">S/.</span>
                    <input
                        type="number"
                        name="amount"
                        value="{{ old('amount', $sale->amount) }}"
                        step="0.01"
                        class="w-full border-gray-300 rounded-md p-2.5 pl-10 focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        placeholder="0.00"
                        required
                    >
                </div>
                @error('amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Fecha</label>
                <input
                    type="date"
                    name="sale_date"
                    value="{{ old('sale_date', $sale->sale_date->format('Y-m-d')) }}"
                    class="w-full border-gray-300 rounded-md p-2.5 focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                    required
                >
                @error('sale_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <a
                    href="{{ $sale->isObserved() ? route('sales.observed') : route('sales.index') }}"
                    class="flex-1 text-center py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
                >
                    Cancelar
                </a>
                <button
                    type="submit"
                    class="flex-1 inline-flex items-center justify-center py-2.5 text-sm font-medium text-white rounded-md transition-colors shadow-sm"
                    :class="{{ $sale->isObserved() ? 'bg-purple-600 hover:bg-purple-700' : 'bg-purple-600 hover:bg-purple-700' }}"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($sale->isObserved())
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        @endif
                    </svg>
                    {{ $sale->isObserved() ? 'Guardar y Re-enviar' : 'Guardar Cambios' }}
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
