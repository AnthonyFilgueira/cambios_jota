<x-app-layout>
    <div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
    <div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-cj-texto leading-tight">
                Bandeja de solicitudes
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('seller.mi-codigo') }}"
                   class="flex items-center gap-1.5 text-xs font-bold text-cj-morado-profundo border border-cj-morado-profundo/20 rounded-full px-3 py-1.5 hover:bg-cj-morado-claro transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    Mi Código
                </a>
                <div class="flex items-center gap-2 bg-cj-morado-profundo/10 border border-cj-morado-profundo/20 rounded-full px-3 py-1">
                    <div class="w-2 h-2 rounded-full bg-cj-turquesa animate-pulse"></div>
                    <span class="text-sm font-mono font-semibold text-cj-morado-profundo">{{ $seller->code }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <x-notifications />

            <!-- KPI Strip -->
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-5 text-center">
                    <p class="text-3xl font-bold text-cj-rosa">{{ $stats['pending'] }}</p>
                    <p class="text-xs text-cj-texto-claro mt-1 font-medium uppercase tracking-wider">Pendientes</p>
                </div>
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-5 text-center">
                    <p class="text-3xl font-bold text-cj-morado-profundo">{{ $stats['mes_count'] }}</p>
                    <p class="text-xs text-cj-texto-claro mt-1 font-medium uppercase tracking-wider">Este mes</p>
                </div>
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-5 text-center">
                    <p class="text-2xl font-bold text-cj-turquesa">S/ {{ number_format($stats['mes_volume'], 0) }}</p>
                    <p class="text-xs text-cj-texto-claro mt-1 font-medium uppercase tracking-wider">Volumen</p>
                </div>
            </div>

            <!-- Alerta de pendientes urgentes -->
            @if($stats['pending'] > 0)
            <div class="bg-cj-rosa/10 border-2 border-cj-rosa/30 rounded-2xl p-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-cj-rosa rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-cj-texto">{{ $stats['pending'] }} solicitud{{ $stats['pending'] !== 1 ? 'es' : '' }} esperando tu revisión</p>
                        <p class="text-xs text-cj-texto-claro">Revisa y aprueba o devuelve cada una con un motivo.</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Filtros por estado -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-4">
                <div class="flex gap-2 flex-wrap">
                    @php
                        $filtros = [
                            'all'        => ['label' => 'Todas',      'color' => 'gray'],
                            'pending'    => ['label' => 'Pendiente',  'color' => 'yellow'],
                            'observed'   => ['label' => 'Observada',  'color' => 'orange'],
                            'processing' => ['label' => 'Aprobada',   'color' => 'blue'],
                            'completed'  => ['label' => 'Completada', 'color' => 'green'],
                            'cancelled'  => ['label' => 'Denegada',   'color' => 'red'],
                        ];
                        $colorMap = [
                            'gray'   => 'bg-gray-100 text-gray-700 border-gray-300',
                            'yellow' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                            'orange' => 'bg-orange-100 text-orange-800 border-orange-300',
                            'blue'   => 'bg-blue-100 text-blue-800 border-blue-300',
                            'green'  => 'bg-green-100 text-green-800 border-green-300',
                            'red'    => 'bg-red-100 text-red-800 border-red-300',
                        ];
                        $activeMap = [
                            'gray'   => 'bg-gray-600 text-white border-gray-600',
                            'yellow' => 'bg-yellow-500 text-white border-yellow-500',
                            'orange' => 'bg-orange-500 text-white border-orange-500',
                            'blue'   => 'bg-blue-600 text-white border-blue-600',
                            'green'  => 'bg-green-600 text-white border-green-600',
                            'red'    => 'bg-red-600 text-white border-red-600',
                        ];
                    @endphp
                    @foreach($filtros as $key => $f)
                    <a href="{{ route('seller.bandeja', ['status' => $key]) }}"
                       class="px-4 py-1.5 rounded-full border text-sm font-semibold transition-all {{ $statusFilter === $key ? $activeMap[$f['color']] : $colorMap[$f['color']] }}">
                        {{ $f['label'] }}
                        @if($key === 'pending' && $stats['pending'] > 0)
                        <span class="ml-1 bg-white/30 rounded-full px-1.5 text-xs">{{ $stats['pending'] }}</span>
                        @endif
                    </a>
                    @endforeach
                </div>
            </div>

            <!-- Lista de solicitudes -->
            @if($transactions->isEmpty())
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-cj-texto-claro font-medium">No hay solicitudes en esta categoría</p>
            </div>
            @else
            <div class="space-y-3">
                @foreach($transactions as $tx)
                @php
                    $fromSymbol = $tx->exchangeRate->currencyPair->fromCurrency->symbol ?? '';
                    $toSymbol   = $tx->exchangeRate->currencyPair->toCurrency->symbol ?? '';
                    $statusStyles = [
                        'pending'    => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'dot' => 'bg-yellow-400', 'label' => 'Pendiente'],
                        'observed'   => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'dot' => 'bg-orange-400', 'label' => 'Observada'],
                        'processing' => ['bg' => 'bg-blue-100',   'text' => 'text-blue-800',   'dot' => 'bg-blue-400',   'label' => 'Aprobada'],
                        'completed'  => ['bg' => 'bg-green-100',  'text' => 'text-green-800',  'dot' => 'bg-green-400',  'label' => 'Completada'],
                        'cancelled'  => ['bg' => 'bg-red-100',    'text' => 'text-red-800',    'dot' => 'bg-red-400',    'label' => 'Denegada'],
                    ];
                    $s = $statusStyles[$tx->status] ?? $statusStyles['pending'];
                @endphp
                <a href="{{ route('seller.solicitud.show', $tx) }}"
                   class="block bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-5 hover:shadow-lg hover:-translate-y-0.5 transition-all group">
                    <div class="flex items-center justify-between gap-4">
                        <!-- Cliente + ID -->
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-11 h-11 bg-gradient-to-br from-cj-morado-profundo to-cj-morado-medio rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                {{ strtoupper(substr($tx->user->name ?? '?', 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold text-cj-texto truncate">{{ $tx->user->name ?? '—' }}</p>
                                <p class="text-xs text-cj-texto-claro font-mono">#{{ $tx->id }} · {{ $tx->created_at->diffForHumans() }}</p>
                            </div>
                        </div>

                        <!-- Montos -->
                        <div class="text-right flex-shrink-0">
                            <p class="font-bold text-cj-morado-profundo">{{ $fromSymbol }} {{ number_format($tx->amount_pen, 2) }}</p>
                            <p class="text-xs text-cj-texto-claro">→ {{ $toSymbol }} {{ number_format($tx->amount_ves, 2) }}</p>
                        </div>

                        <!-- Estado + flecha -->
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $s['bg'] }} {{ $s['text'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $s['dot'] }}"></span>
                                {{ $s['label'] }}
                            </span>
                            @if(in_array($tx->status, ['pending', 'observed']))
                            <svg class="w-5 h-5 text-cj-morado-profundo group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            @endif
                        </div>
                    </div>

                    <!-- Observación si existe -->
                    @if($tx->observation && $tx->status === 'observed')
                    <div class="mt-3 bg-orange-50 border border-orange-200 rounded-xl px-4 py-2 text-xs text-orange-700">
                        <span class="font-semibold">Motivo observación:</span> {{ Str::limit($tx->observation, 80) }}
                    </div>
                    @endif
                </a>
                @endforeach
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
