<x-app-layout>
    <div class="max-w-6xl mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-6">Ventas Registradas</h1>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Fecha</th>
                        <th class="px-4 py-2 text-left">Vendedor</th>
                        <th class="px-4 py-2 text-left">Monto</th>
                        <th class="px-4 py-2 text-left">Comisión Vendedor</th>
                        <th class="px-4 py-2 text-left">Comisión Jefe</th>
                        <th class="px-4 py-2 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $sale->sale_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-2">{{ $sale->seller->name }}</td>
                            <td class="px-4 py-2">S/. {{ number_format($sale->amount, 2) }}</td>
                            <td class="px-4 py-2">S/. {{ number_format($sale->sellerCommissionAmount(), 2) }}</td>
                            <td class="px-4 py-2">S/. {{ number_format($sale->bossCommissionAmount(), 2) }}</td>
                            <td class="px-4 py-2">
                                <form action="{{ route('sales.destroy', $sale) }}" method="POST" onsubmit="return confirm('¿Eliminar esta venta?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center text-gray-500">No hay ventas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $sales->links() }}
        </div>
    </div>
</x-app-layout>