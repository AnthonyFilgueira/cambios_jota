<x-app-layout>
    <div class="max-w-md mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4">Registrar Venta</h1>
        <form method="POST" action="{{ route('sales.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1">Vendedor</label>
                <select name="seller_id" class="w-full border-gray-300 rounded p-2">
                    @foreach ($sellers as $seller)
                        <option value="{{ $seller->id }}">{{ $seller->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Monto</label>
                <input type="number" name="amount" step="0.01" class="w-full border-gray-300 rounded p-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Fecha</label>
                <input type="date" name="sale_date" class="w-full border-gray-300 rounded p-2" required>
            </div>

            <button class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">Guardar</button>
        </form>
    </div>
</x-app-layout>