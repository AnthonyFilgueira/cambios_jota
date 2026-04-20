<x-app-layout>
    <div class="max-w-md mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4">Editar Vendedor</h1>
        <form method="POST" action="{{ route('sellers.update', $seller) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium mb-1">Código de Vendedor</label>
                <input type="text" value="{{ $seller->code }}" class="w-full border-gray-300 rounded p-2 bg-gray-100 font-mono text-purple-700" readonly>
                <p class="text-xs text-gray-500 mt-1">Este código es único e inmodificable</p>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Nombre</label>
                <input type="text" name="name" value="{{ old('name', $seller->name) }}" class="w-full border-gray-300 rounded p-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Comisión Vendedor (%)</label>
                <input type="number" name="seller_commission" step="0.01" value="{{ old('seller_commission', $seller->seller_commission) }}" class="w-full border-gray-300 rounded p-2" required>
            </div>

            @php
                $commissionGroups = \App\Models\Seller::select('boss_commission')
                    ->groupBy('boss_commission')
                    ->selectRaw('boss_commission, count(*) as count')
                    ->orderBy('count', 'desc')
                    ->get();
                $mostCommon = $commissionGroups->first();
            @endphp

            <div>
                <label class="block text-sm font-medium mb-1">Comisión Dueño (%)</label>
                <input
                    type="number"
                    name="boss_commission"
                    step="0.01"
                    min="0"
                    max="100"
                    value="{{ old('boss_commission', $seller->boss_commission) }}"
                    class="w-full border-gray-300 rounded p-2 focus:ring-2 focus:ring-purple-500"
                    required
                >
                @if($mostCommon)
                <p class="text-xs text-gray-500 mt-1">
                    💡 La comisión más común es <strong>{{ number_format($mostCommon->boss_commission, 2) }}%</strong>
                    ({{ $mostCommon->count }} vendedor{{ $mostCommon->count > 1 ? 'es' : '' }}).
                    Edítala aquí solo si este vendedor necesita un porcentaje especial.
                </p>
                @endif

                @if($seller->sales()->exists())
                <div class="mt-2 bg-blue-50 border border-blue-200 rounded-lg p-2">
                    <p class="text-xs text-blue-800">
                        ℹ️ Este vendedor ya tiene ventas registradas. Cambiar la comisión NO afectará las ventas pasadas (se guardan snapshots).
                    </p>
                </div>
                @endif
            </div>

            <button class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">Actualizar</button>
        </form>
    </div>
</x-app-layout>