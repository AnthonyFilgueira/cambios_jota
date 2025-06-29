<x-app-layout>
    <div class="max-w-md mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4">Nuevo Vendedor</h1>
        <form method="POST" action="{{ route('sellers.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1">Nombre</label>
                <input type="text" name="name" class="w-full border-gray-300 rounded p-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Comisión Vendedor (%)</label>
                <input type="number" name="seller_commission" step="0.01" class="w-full border-gray-300 rounded p-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Comisión Jefe (%)</label>
                <input type="number" name="boss_commission" step="0.01" class="w-full border-gray-300 rounded p-2" required>
            </div>

            <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Guardar</button>
        </form>
    </div>
</x-app-layout>