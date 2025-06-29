<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4">Vendedores</h1>
        <a href="{{ route('sellers.create') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Nuevo Vendedor</a>

        <div class="overflow-x-auto mt-6">
            <table class="min-w-full bg-white border border-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Nombre</th>
                        <th class="px-4 py-2 text-left">Comisión Vendedor</th>
                        <th class="px-4 py-2 text-left">Comisión Jefe</th>
                        <th class="px-4 py-2 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sellers as $seller)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $seller->name }}</td>
                            <td class="px-4 py-2">{{ $seller->seller_commission }}%</td>
                            <td class="px-4 py-2">{{ $seller->boss_commission }}%</td>
                            <td class="px-4 py-2 space-x-2">
                                <a href="{{ route('sellers.edit', $seller) }}" class="text-blue-600 hover:underline">Editar</a>
                                <form action="{{ route('sellers.destroy', $seller) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline" onclick="return confirm('¿Eliminar?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>