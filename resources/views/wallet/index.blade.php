<x-app-layout>
    <div class="max-w-6xl mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-6">Mi Monedero</h1>

        {{-- Saldo Destacado --}}
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg p-6 mb-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-200 text-sm mb-1">Saldo Disponible</p>
                    <p class="text-4xl font-bold">S/. {{ number_format($balance, 2) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-purple-200 text-sm mb-1">Vendedor</p>
                    <p class="text-lg font-semibold">{{ $seller->name }}</p>
                    <p class="text-purple-300 text-xs font-mono">{{ $seller->code }}</p>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form method="GET" action="{{ route('wallet.index') }}" class="flex gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Tipo</label>
                    <select name="type" class="border-gray-300 rounded px-3 py-2">
                        <option value="">Todos</option>
                        <option value="commission" {{ $type === 'commission' ? 'selected' : '' }}>Comisiones</option>
                        <option value="liquidation" {{ $type === 'liquidation' ? 'selected' : '' }}>Liquidaciones</option>
                        <option value="adjustment" {{ $type === 'adjustment' ? 'selected' : '' }}>Ajustes</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Período</label>
                    <select name="days" class="border-gray-300 rounded px-3 py-2">
                        <option value="7" {{ $days == 7 ? 'selected' : '' }}>Últimos 7 días</option>
                        <option value="30" {{ $days == 30 ? 'selected' : '' }}>Últimos 30 días</option>
                        <option value="90" {{ $days == 90 ? 'selected' : '' }}>Últimos 90 días</option>
                        <option value="all" {{ $days === 'all' ? 'selected' : '' }}>Todas</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                        Filtrar
                    </button>
                </div>
            </form>
        </div>

        {{-- Tabla de Transacciones --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-left">Tipo</th>
                        <th class="px-4 py-3 text-left">Descripción</th>
                        <th class="px-4 py-3 text-right">Monto</th>
                        <th class="px-4 py-3 text-right">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $tx)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <span class="text-gray-600">{{ $tx->created_at->format('d/m/Y') }}</span>
                                <span class="text-xs text-gray-400 block">{{ $tx->created_at->format('H:i') }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($tx->type === 'commission')
                                    <span class="inline-block bg-green-100 text-green-700 px-2 py-1 rounded text-xs">
                                        Comisión
                                    </span>
                                @elseif($tx->type === 'liquidation')
                                    <span class="inline-block bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs">
                                        Liquidación
                                    </span>
                                @else
                                    <span class="inline-block bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs">
                                        Ajuste
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-gray-700">{{ $tx->description }}</span>
                                @if($tx->reference)
                                    <span class="text-xs text-gray-400 block">
                                        Ref: {{ class_basename($tx->reference_type) }} #{{ $tx->reference_id }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="font-semibold {{ $tx->amount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $tx->amount >= 0 ? '+' : '' }}S/. {{ number_format($tx->amount, 2) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="text-gray-700 font-mono">
                                    S/. {{ number_format($tx->balance_after, 2) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                No hay transacciones para mostrar
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Paginación --}}
            @if($transactions->hasPages())
                <div class="px-4 py-3 border-t">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
