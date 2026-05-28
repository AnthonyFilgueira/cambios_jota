<x-app-layout>
    <div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
    <div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-cj-texto leading-tight">Reportes de Transacciones</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Navegación de reportes -->
            <div class="flex gap-3">
                <a href="{{ route('reports.index') }}"
                   class="px-5 py-2.5 bg-cj-morado-profundo text-white rounded-xl font-semibold text-sm shadow">
                    Transacciones
                </a>
                <a href="{{ route('reports.conciliation') }}"
                   class="px-5 py-2.5 bg-white/80 text-cj-texto rounded-xl font-semibold text-sm border border-white/60 hover:bg-white transition-all">
                    Conciliación Bancaria
                </a>
            </div>

            <!-- Filtros -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/50 p-6">
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-semibold text-cj-texto-claro mb-1.5 uppercase tracking-wider">Desde</label>
                        <input type="date" name="start_date" value="{{ $start }}"
                               class="w-full p-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-cj-texto-claro mb-1.5 uppercase tracking-wider">Hasta</label>
                        <input type="date" name="end_date" value="{{ $end }}"
                               class="w-full p-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-cj-texto-claro mb-1.5 uppercase tracking-wider">Estado</label>
                        <select name="status" class="w-full p-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-cj-turquesa transition-all">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Todos</option>
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pendiente</option>
                            <option value="processing" {{ $status === 'processing' ? 'selected' : '' }}>En proceso</option>
                            <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completadas</option>
                            <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Canceladas</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-cj-texto-claro mb-1.5 uppercase tracking-wider">Vendedor</label>
                        <select name="seller_id" class="w-full p-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-cj-turquesa transition-all">
                            <option value="">Todos los vendedores</option>
                            @foreach($sellers as $seller)
                            <option value="{{ $seller->id }}" {{ $sellerId == $seller->id ? 'selected' : '' }}>
                                {{ $seller->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                                class="flex-1 px-4 py-2.5 bg-cj-morado-profundo text-white rounded-xl font-semibold text-sm hover:bg-cj-morado-medio transition-all">
                            Filtrar
                        </button>
                        <a href="{{ route('reports.export.transactions', ['start_date' => $start, 'end_date' => $end, 'status' => $status, 'seller_id' => $sellerId]) }}"
                           class="px-4 py-2.5 bg-green-600 text-white rounded-xl font-semibold text-sm hover:bg-green-700 transition-all flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Excel
                        </a>
                    </div>
                </form>
            </div>

            <!-- Totales -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-5">
                    <p class="text-xs font-semibold text-cj-texto-claro uppercase tracking-wider mb-1">Total transacciones</p>
                    <p class="text-3xl font-bold text-cj-morado-profundo">{{ $totals['count'] }}</p>
                </div>
                <div class="bg-gradient-to-br from-cj-morado-profundo to-cj-morado-medio text-white rounded-2xl shadow p-5">
                    <p class="text-xs font-semibold opacity-80 uppercase tracking-wider mb-1">Monto total enviado</p>
                    <p class="text-2xl font-bold">S/ {{ number_format($totals['amount_pen'], 2) }}</p>
                </div>
                <div class="bg-gradient-to-br from-cj-turquesa to-teal-400 text-white rounded-2xl shadow p-5">
                    <p class="text-xs font-semibold opacity-80 uppercase tracking-wider mb-1">Monto total recibido</p>
                    <p class="text-2xl font-bold">Bs. {{ number_format($totals['amount_ves'], 2) }}</p>
                </div>
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-5">
                    <p class="text-xs font-semibold text-cj-texto-claro uppercase tracking-wider mb-1">Completadas / Pendientes</p>
                    <p class="text-2xl font-bold text-cj-texto">
                        <span class="text-green-600">{{ $totals['completed'] }}</span>
                        <span class="text-gray-300 mx-1">/</span>
                        <span class="text-yellow-600">{{ $totals['pending'] }}</span>
                    </p>
                </div>
            </div>

            <!-- Tabla de transacciones -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/50 overflow-hidden">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="font-bold text-cj-morado-profundo">
                        {{ $totals['count'] }} transacciones — {{ $start }} al {{ $end }}
                    </h3>
                </div>

                @if($transactions->isEmpty())
                <div class="p-12 text-center">
                    <p class="text-cj-texto-claro text-lg">No hay transacciones en este período.</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-cj-texto-claro uppercase tracking-wider">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-cj-texto-claro uppercase tracking-wider">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-cj-texto-claro uppercase tracking-wider">Cliente</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-cj-texto-claro uppercase tracking-wider">Vendedor</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-cj-texto-claro uppercase tracking-wider">Enviado</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-cj-texto-claro uppercase tracking-wider">Recibido</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-cj-texto-claro uppercase tracking-wider">Tipo</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-cj-texto-claro uppercase tracking-wider">Nº Operación</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-cj-texto-claro uppercase tracking-wider">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($transactions as $tx)
                            @php
                                $statusConfig = [
                                    'pending'    => ['label' => 'Pendiente',  'class' => 'bg-yellow-100 text-yellow-800'],
                                    'processing' => ['label' => 'En proceso', 'class' => 'bg-blue-100 text-blue-800'],
                                    'completed'  => ['label' => 'Completada', 'class' => 'bg-green-100 text-green-800'],
                                    'observed'   => ['label' => 'Observada',  'class' => 'bg-orange-100 text-orange-800'],
                                    'cancelled'  => ['label' => 'Cancelada',  'class' => 'bg-gray-100 text-gray-600'],
                                ];
                                $sc = $statusConfig[$tx->status] ?? ['label' => $tx->status, 'class' => 'bg-gray-100 text-gray-600'];
                                $fromSymbol = $tx->exchangeRate?->currencyPair?->fromCurrency?->symbol ?? 'S/';
                                $toSymbol   = $tx->exchangeRate?->currencyPair?->toCurrency?->symbol ?? 'Bs.';
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 font-mono text-xs text-cj-texto-claro">#{{ str_pad($tx->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-4 py-3 text-cj-texto">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 text-cj-texto">{{ $tx->user?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-cj-texto">{{ $tx->seller?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-right font-mono font-semibold text-cj-morado-profundo">
                                    {{ $fromSymbol }} {{ number_format($tx->amount_pen, 2) }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-semibold text-cj-turquesa">
                                    {{ $toSymbol }} {{ number_format($tx->amount_ves, 2) }}
                                </td>
                                <td class="px-4 py-3 text-cj-texto capitalize">
                                    {{ $tx->operation_type === 'pago_movil' ? 'Pago Móvil' : 'Transferencia' }}
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-cj-texto">{{ $tx->operation_number ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $sc['class'] }}">
                                        {{ $sc['label'] }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                            <tr>
                                <td colspan="4" class="px-4 py-3 font-bold text-cj-texto text-sm">TOTALES</td>
                                <td class="px-4 py-3 text-right font-bold font-mono text-cj-morado-profundo">
                                    S/ {{ number_format($totals['amount_pen'], 2) }}
                                </td>
                                <td class="px-4 py-3 text-right font-bold font-mono text-cj-turquesa">
                                    Bs. {{ number_format($totals['amount_ves'], 2) }}
                                </td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
