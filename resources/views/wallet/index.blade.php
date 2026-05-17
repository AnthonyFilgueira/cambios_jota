<x-app-layout>
    <div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
    <div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-cj-texto leading-tight">Mi Monedero</h2>
            <span class="font-mono text-sm font-bold text-cj-morado-profundo bg-cj-morado-claro px-3 py-1 rounded-full">
                {{ $seller->code }}
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 space-y-6">

            <!-- Hero saldo -->
            <div class="bg-gradient-to-br from-cj-morado-profundo to-cj-morado-medio rounded-3xl p-6 text-white shadow-2xl shadow-purple-400/30 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-32 translate-x-32"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-24 -translate-x-24"></div>
                <div class="relative">
                    <p class="text-xs uppercase tracking-widest opacity-70 mb-1">Saldo disponible</p>
                    <p class="text-5xl font-black font-mono">S/ {{ number_format($balance, 2) }}</p>
                    <p class="text-sm opacity-60 mt-1">{{ $seller->name }}</p>
                </div>
            </div>

            <!-- KPIs -->
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-4 text-center">
                    <p class="text-xs text-cj-texto-claro font-semibold uppercase tracking-wider mb-1">Total ganado</p>
                    <p class="text-xl font-bold text-cj-turquesa font-mono">S/ {{ number_format($totalEarned, 2) }}</p>
                </div>
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-4 text-center">
                    <p class="text-xs text-cj-texto-claro font-semibold uppercase tracking-wider mb-1">Comisiones</p>
                    <p class="text-xl font-bold text-cj-morado-profundo font-mono">S/ {{ number_format($totalCommissions, 2) }}</p>
                </div>
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-4 text-center">
                    <p class="text-xs text-cj-texto-claro font-semibold uppercase tracking-wider mb-1">Liquidado</p>
                    <p class="text-xl font-bold text-cj-rosa font-mono">S/ {{ number_format($totalLiquidated, 2) }}</p>
                </div>
            </div>

            <!-- Gráfico semanal -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-6">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="font-bold text-cj-texto">Comisiones por semana</h3>
                        <p class="text-xs text-cj-texto-claro mt-0.5">Últimas 8 semanas</p>
                    </div>
                    <div class="w-3 h-3 rounded-full bg-cj-turquesa shadow-md shadow-teal-400/50"></div>
                </div>
                <div class="h-48">
                    <canvas id="walletChart"></canvas>
                </div>
            </div>

            <!-- Filtros + tabla -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 overflow-hidden">
                <!-- Filtros -->
                <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-cj-morado-profundo/5 to-cj-turquesa/5">
                    <form method="GET" action="{{ route('wallet.index') }}" class="flex flex-wrap gap-3 items-end">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-cj-texto-claro mb-1.5">Tipo</label>
                            <select name="type"
                                    class="px-3 py-2 border-2 border-gray-200 rounded-xl text-sm focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all">
                                <option value="">Todos</option>
                                <option value="commission"  {{ $type === 'commission'  ? 'selected' : '' }}>Comisiones</option>
                                <option value="liquidation" {{ $type === 'liquidation' ? 'selected' : '' }}>Liquidaciones</option>
                                <option value="adjustment"  {{ $type === 'adjustment'  ? 'selected' : '' }}>Ajustes</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-cj-texto-claro mb-1.5">Período</label>
                            <select name="days"
                                    class="px-3 py-2 border-2 border-gray-200 rounded-xl text-sm focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all">
                                <option value="7"   {{ $days == 7   ? 'selected' : '' }}>7 días</option>
                                <option value="30"  {{ $days == 30  ? 'selected' : '' }}>30 días</option>
                                <option value="90"  {{ $days == 90  ? 'selected' : '' }}>90 días</option>
                                <option value="all" {{ $days === 'all' ? 'selected' : '' }}>Todo</option>
                            </select>
                        </div>
                        <button type="submit"
                                class="px-5 py-2 bg-cj-morado-profundo text-white rounded-xl text-sm font-semibold hover:bg-cj-morado-medio transition-all">
                            Filtrar
                        </button>
                    </form>
                </div>

                <!-- Tabla -->
                @if($transactions->isEmpty())
                <div class="p-12 text-center text-cj-texto-claro">No hay movimientos en este período.</div>
                @else
                <div class="divide-y divide-gray-50">
                    @foreach($transactions as $tx)
                    <div class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50/50 transition-all">
                        <!-- Ícono tipo -->
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-base flex-shrink-0
                                    {{ $tx->type === 'commission' ? 'bg-green-100' : ($tx->type === 'liquidation' ? 'bg-blue-100' : 'bg-yellow-100') }}">
                            {{ $tx->type === 'commission' ? '💰' : ($tx->type === 'liquidation' ? '📤' : '⚖️') }}
                        </div>
                        <!-- Descripción + fecha -->
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-cj-texto text-sm truncate">{{ $tx->description }}</p>
                            <p class="text-xs text-cj-texto-claro">{{ $tx->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <!-- Monto -->
                        <div class="text-right flex-shrink-0">
                            <p class="font-bold font-mono {{ $tx->amount >= 0 ? 'text-green-600' : 'text-red-500' }}">
                                {{ $tx->amount >= 0 ? '+' : '' }}S/ {{ number_format($tx->amount, 2) }}
                            </p>
                            <p class="text-xs text-cj-texto-claro font-mono">Saldo: S/ {{ number_format($tx->balance_after, 2) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($transactions->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $transactions->links() }}
                </div>
                @endif
                @endif
            </div>

        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const ctx = document.getElementById('walletChart').getContext('2d');
        const weeklyData = @json($weeklyData);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: weeklyData.map(d => d.label),
                datasets: [{
                    label: 'Comisiones (S/)',
                    data: weeklyData.map(d => d.amount),
                    backgroundColor: weeklyData.map((d, i) =>
                        i === weeklyData.length - 1
                            ? 'rgba(13,148,136,0.9)'   // última semana turquesa
                            : 'rgba(76,29,149,0.25)'    // anteriores morado claro
                    ),
                    borderColor: weeklyData.map((d, i) =>
                        i === weeklyData.length - 1 ? 'rgb(13,148,136)' : 'rgba(76,29,149,0.5)'
                    ),
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => 'S/ ' + ctx.parsed.y.toLocaleString('es-PE', {minimumFractionDigits: 2})
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                    y: {
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: {
                            font: { size: 11 },
                            callback: v => 'S/ ' + v.toLocaleString('es-PE')
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>
