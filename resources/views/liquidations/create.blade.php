<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-6">Nueva Liquidación</h1>

        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div x-data="{
            selectedSellerId: '{{ old('seller_id') }}',
            sellerData: @js($sellers->map(fn($s) => ['id' => $s->id, 'name' => $s->name, 'code' => $s->code, 'balance' => $s->walletBalance()])->keyBy('id')),

            get selectedSeller() {
                return this.sellerData[this.selectedSellerId] || null;
            }
        }" class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('liquidations.store') }}" class="space-y-5">
                @csrf

                <!-- Vendedor -->
                <div>
                    <label class="block text-sm font-medium text-cj-texto mb-2">Vendedor *</label>
                    <select name="seller_id" x-model="selectedSellerId" required class="w-full border-2 border-gray-200 rounded-lg p-3 focus:border-cj-morado-profundo">
                        <option value="">Seleccione un vendedor</option>
                        @foreach($sellers as $seller)
                            <option value="{{ $seller->id }}">{{ $seller->name }} ({{ $seller->code }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Saldo disponible -->
                <div x-show="selectedSeller" class="bg-purple-50 border-l-4 border-cj-morado-profundo p-4 rounded">
                    <p class="text-sm text-cj-texto">
                        <span class="font-semibold">Saldo disponible:</span>
                        <span class="text-cj-morado-profundo font-bold text-lg ml-2" x-text="selectedSeller ? 'S/. ' + parseFloat(selectedSeller.balance).toFixed(2) : ''"></span>
                    </p>
                </div>

                <!-- Monto -->
                <div>
                    <label class="block text-sm font-medium text-cj-texto mb-2">Monto a pagar *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-cj-texto-claro font-medium">S/.</span>
                        <input
                            type="number"
                            name="amount"
                            step="0.01"
                            min="0.01"
                            value="{{ old('amount') }}"
                            placeholder="0.00"
                            required
                            class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:border-cj-turquesa"
                        >
                    </div>
                </div>

                <!-- Método de pago -->
                <div>
                    <label class="block text-sm font-medium text-cj-texto mb-2">Método de pago *</label>
                    <select name="payment_method" required class="w-full border-2 border-gray-200 rounded-lg p-3 focus:border-cj-morado-profundo">
                        <option value="">Seleccione método</option>
                        @foreach($paymentMethods as $key => $label)
                            <option value="{{ $key }}" {{ old('payment_method') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Referencia/Comprobante -->
                <div>
                    <label class="block text-sm font-medium text-cj-texto mb-2">Número de operación / Comprobante</label>
                    <input
                        type="text"
                        name="reference"
                        value="{{ old('reference') }}"
                        placeholder="OP-123456 o dejar vacío"
                        class="w-full border-2 border-gray-200 rounded-lg p-3 focus:border-cj-morado-profundo"
                    >
                </div>

                <!-- Fecha de pago -->
                <div>
                    <label class="block text-sm font-medium text-cj-texto mb-2">Fecha de pago *</label>
                    <input
                        type="date"
                        name="payment_date"
                        value="{{ old('payment_date', date('Y-m-d')) }}"
                        max="{{ date('Y-m-d') }}"
                        required
                        class="w-full border-2 border-gray-200 rounded-lg p-3 focus:border-cj-morado-profundo"
                    >
                </div>

                <!-- Notas -->
                <div>
                    <label class="block text-sm font-medium text-cj-texto mb-2">Notas / Observaciones</label>
                    <textarea
                        name="notes"
                        rows="3"
                        placeholder="Información adicional (opcional)"
                        class="w-full border-2 border-gray-200 rounded-lg p-3 focus:border-cj-morado-profundo"
                    >{{ old('notes') }}</textarea>
                </div>

                <!-- Botones -->
                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white font-bold py-3 rounded-lg hover:shadow-lg transition">
                        Registrar Liquidación
                    </button>
                    <a href="{{ route('liquidations.index') }}" class="flex-1 text-center border-2 border-gray-300 text-cj-texto font-semibold py-3 rounded-lg hover:bg-gray-50 transition">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
