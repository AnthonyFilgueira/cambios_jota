<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-6">Registrar Ventas en Lote</h1>

        <form method="POST" action="{{ route('sales.bulk.store') }}">
            @csrf

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">Vendedor</th>
                            <th class="px-4 py-2 text-left">Monto</th>
                            <th class="px-4 py-2 text-left">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sellers as $index => $seller)
                            <tr class="border-t">
                                <td class="px-4 py-2">
                                    <div class="text-sm font-medium text-gray-800">{{ $seller->name }}</div>
                                    <input type="hidden" name="sales[{{ $index }}][seller_id]" value="{{ $seller->id }}">
                                </td>
                                <td class="px-4 py-2">
                                    <input
                                        type="number"
                                        name="sales[{{ $index }}][amount]"
                                        step="0.01"
                                        placeholder="0.00"
                                        class="w-full border-gray-300 rounded p-2 focus:ring focus:ring-blue-200"
                                    >
                                </td>
                                <td class="px-4 py-2">
                                    <input
                                        type="date"
                                        name="sales[{{ $index }}][sale_date]"
                                        value="{{ now()->toDateString() }}"
                                        class="w-full border-gray-300 rounded p-2 focus:ring focus:ring-blue-200"
                                    >
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-end">
                <button
                    type="submit"
                    class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition"
                >
                    Guardar Ventas
                </button>
            </div>
        </form>
    </div>
</x-app-layout>