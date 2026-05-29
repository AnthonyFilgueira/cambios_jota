<x-app-layout>
    <div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
    <div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-cj-texto leading-tight">Conciliación Bancaria</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Navegación de reportes -->
            <div class="flex gap-3">
                <a href="{{ route('reports.index') }}"
                   class="px-5 py-2.5 bg-white/80 text-cj-texto rounded-xl font-semibold text-sm border border-white/60 hover:bg-white transition-all">
                    Transacciones
                </a>
                <a href="{{ route('reports.conciliation') }}"
                   class="px-5 py-2.5 bg-cj-morado-profundo text-white rounded-xl font-semibold text-sm shadow">
                    Conciliación Bancaria
                </a>
            </div>

            <!-- Filtros -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/50 p-6">
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
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
                    <div class="flex gap-2">
                        <button type="submit"
                                class="flex-1 px-4 py-2.5 bg-cj-morado-profundo text-white rounded-xl font-semibold text-sm hover:bg-cj-morado-medio transition-all">
                            Filtrar
                        </button>
                        <a href="{{ route('reports.export.conciliation', ['start_date' => $start, 'end_date' => $end]) }}"
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
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-5">
                    <p class="text-xs font-semibold text-cj-texto-claro uppercase tracking-wider mb-1">Operaciones completadas</p>
                    <p class="text-3xl font-bold text-cj-morado-profundo">{{ $totals['count'] }}</p>
                </div>
                <div class="bg-gradient-to-br from-cj-morado-profundo to-cj-morado-medio text-white rounded-2xl shadow p-5">
                    <p class="text-xs font-semibold opacity-80 uppercase tracking-wider mb-1">Total monto enviado</p>
                    <p class="text-2xl font-bold">S/ {{ number_format($totals['amount_pen'], 2) }}</p>
                </div>
                <div class="bg-gradient-to-br from-cj-turquesa to-teal-400 text-white rounded-2xl shadow p-5">
                    <p class="text-xs font-semibold opacity-80 uppercase tracking-wider mb-1">Total monto recibido</p>
                    <p class="text-2xl font-bold">{{ number_format($totals['amount_ves'], 2) }}</p>
                </div>
            </div>

            <!-- Tabla de conciliación -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/50 overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-cj-morado-profundo">Operaciones completadas</h3>
                        <p class="text-xs text-cj-texto-claro mt-0.5">{{ $start }} al {{ $end }} — Todos los corredores y tipos de operación</p>
                    </div>
                </div>

                @if($transactions->isEmpty())
                <div class="p-12 text-center">
                    <p class="text-cj-texto-claro text-lg">No hay operaciones completadas en este período.</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-cj-texto-claro uppercase tracking-wider">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-cj-texto-claro uppercase tracking-wider">Fecha y hora</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-cj-texto-claro uppercase tracking-wider">Corredor</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-cj-texto-claro uppercase tracking-wider">Tipo operación</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-cj-texto-claro uppercase tracking-wider">Remitente</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-cj-texto-claro uppercase tracking-wider">Banco / Método remitente</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-cj-texto-claro uppercase tracking-wider">Nº Operación</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-cj-texto-claro uppercase tracking-wider">Monto enviado</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-cj-texto-claro uppercase tracking-wider">Beneficiario</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-cj-texto-claro uppercase tracking-wider">Banco / Método beneficiario</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-cj-texto-claro uppercase tracking-wider">Monto recibido</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($transactions as $tx)
                            @php
                                $fromCurrency = $tx->exchangeRate?->currencyPair?->fromCurrency;
                                $toCurrency   = $tx->exchangeRate?->currencyPair?->toCurrency;
                                $fromSymbol   = $fromCurrency?->symbol ?? '';
                                $toSymbol     = $toCurrency?->symbol ?? '';
                                $fromCode     = $fromCurrency?->code ?? '—';
                                $toCode       = $toCurrency?->code ?? '—';

                                $docRemitente = $tx->sender_document_number
                                    ? ($tx->sender_document_type ? $tx->sender_document_type . ': ' : '') . $tx->sender_document_number
                                    : ($tx->sender_dni ?? null);

                                $opType = $tx->sender_operation_type ?? $tx->operation_type ?? '—';
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors {{ $tx->operation_number ? '' : 'bg-yellow-50/40' }}">
                                <td class="px-4 py-3 font-mono text-xs text-cj-texto-claro">#{{ str_pad($tx->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-4 py-3 text-cj-texto whitespace-nowrap">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 bg-cj-morado-profundo/10 text-cj-morado-profundo rounded-full text-xs font-semibold whitespace-nowrap">
                                        {{ $fromCode }} → {{ $toCode }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-cj-texto text-xs">
                                    {{ $opType !== '—' ? str_replace('_', ' ', $opType) : '—' }}
                                </td>
                                <td class="px-4 py-3 text-cj-texto">
                                    <div class="font-medium">{{ $tx->user?->name ?? '—' }}</div>
                                    @if($docRemitente)
                                    <div class="text-xs text-cj-texto-claro font-mono">{{ $docRemitente }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-cj-texto text-xs">
                                    @if($tx->sender_bank)
                                        <div>{{ $tx->sender_bank }}</div>
                                    @endif
                                    @if($tx->sender_account_number)
                                        <div class="font-mono text-cj-texto-claro">{{ $tx->sender_account_number }}</div>
                                    @elseif($tx->sender_phone)
                                        <div class="font-mono text-cj-texto-claro">{{ $tx->sender_phone }}</div>
                                    @endif
                                    @if(!$tx->sender_bank && !$tx->sender_account_number && !$tx->sender_phone)
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($tx->operation_number)
                                    <span class="font-mono font-semibold text-cj-morado-profundo">{{ $tx->operation_number }}</span>
                                    @else
                                    <span class="text-yellow-600 font-semibold text-xs">Sin nº</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-semibold text-cj-morado-profundo whitespace-nowrap">
                                    {{ $fromSymbol }} {{ number_format($tx->amount_pen, 2) }}
                                </td>
                                <td class="px-4 py-3 text-cj-texto">
                                    <div class="font-medium">{{ $tx->recipient_name ?? '—' }}</div>
                                    @if($tx->recipient_document_number)
                                    <div class="text-xs text-cj-texto-claro font-mono">
                                        {{ $tx->recipient_document_type ? $tx->recipient_document_type . ': ' : '' }}{{ $tx->recipient_document_number }}
                                    </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-cj-texto text-xs">
                                    @if($tx->recipient_bank)
                                        <div>{{ $tx->recipient_bank }}</div>
                                    @endif
                                    @if($tx->recipient_account_number)
                                        <div class="font-mono text-cj-texto-claro">{{ $tx->recipient_account_number }}</div>
                                    @elseif($tx->recipient_phone)
                                        <div class="font-mono text-cj-texto-claro">{{ $tx->recipient_phone }}</div>
                                    @endif
                                    @if(!$tx->recipient_bank && !$tx->recipient_account_number && !$tx->recipient_phone)
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-semibold text-cj-turquesa whitespace-nowrap">
                                    {{ $toSymbol }} {{ number_format($tx->amount_ves, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                            <tr>
                                <td colspan="7" class="px-4 py-3 font-bold text-cj-texto text-sm">TOTAL</td>
                                <td class="px-4 py-3 text-right font-bold font-mono text-cj-morado-profundo">
                                    S/ {{ number_format($totals['amount_pen'], 2) }}
                                </td>
                                <td colspan="2"></td>
                                <td class="px-4 py-3 text-right font-bold font-mono text-cj-turquesa">
                                    {{ number_format($totals['amount_ves'], 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="p-3 bg-yellow-50 border-t border-yellow-100 text-xs text-yellow-700">
                    Las filas con fondo amarillo claro no tienen número de operación registrado.
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
