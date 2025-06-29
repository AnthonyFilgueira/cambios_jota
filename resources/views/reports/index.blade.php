<x-app-layout>
    <div class="max-w-6xl mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-6">Reporte de Ventas por Vendedor</h1>

        <form method="GET" class="flex flex-col sm:flex-row gap-2 mb-6">
            <input type="date" name="start_date" value="{{ $start }}" class="border-gray-300 rounded p-2 w-full sm:w-auto">
            <input type="date" name="end_date" value="{{ $end }}" class="border-gray-300 rounded p-2 w-full sm:w-auto">
            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full sm:w-auto">Filtrar</button>
        </form>

        @foreach ($sellers as $seller)
            <div class="mb-10">
                <h2 class="text-xl font-semibold text-gray-800 mb-2">{{ $seller->name }}</h2>

                @if ($seller->sales->isEmpty())
                    <p class="text-gray-500">No hay ventas registradas en este rango.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left">Fecha</th>
                                    <th class="px-4 py-2 text-left">Monto</th>
                                    <th class="px-4 py-2 text-left">Comisión Vendedor</th>
                                    <th class="px-4 py-2 text-left">Comisión Jefe</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total = 0;
                                    $totalSellerCommission = 0;
                                    $totalBossCommission = 0;
                                @endphp

                                @foreach ($seller->sales as $sale)
                                    @php
                                        $sellerCommission = $sale->sellerCommissionAmount();
                                        $bossCommission = $sale->bossCommissionAmount();
                                        $total += $sale->amount;
                                        $totalSellerCommission += $sellerCommission;
                                        $totalBossCommission += $bossCommission;
                                    @endphp
                                    <tr class="border-t">
                                        <td class="px-4 py-2">{{ $sale->sale_date->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2">S/. {{ number_format($sale->amount, 2) }}</td>
                                        <td class="px-4 py-2">S/. {{ number_format($sellerCommission, 2) }}</td>
                                        <td class="px-4 py-2">S/. {{ number_format($bossCommission, 2) }}</td>
                                    </tr>
                                @endforeach

                                <tr class="bg-gray-50 font-semibold border-t">
                                    <td class="px-4 py-2">Totales</td>
                                    <td class="px-4 py-2">S/. {{ number_format($total, 2) }}</td>
                                    <td class="px-4 py-2">S/. {{ number_format($totalSellerCommission, 2) }}</td>
                                    <td class="px-4 py-2">S/. {{ number_format($totalBossCommission, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</x-app-layout>