<x-app-layout>
    <div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
    <div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-cj-texto leading-tight">Auditoría del Sistema</h2>
                <p class="text-sm text-cj-texto-claro mt-0.5">Registro de todas las acciones realizadas en el sistema</p>
            </div>
            <span class="text-sm font-semibold text-cj-morado-profundo bg-cj-morado-claro px-3 py-1.5 rounded-full">
                {{ number_format($logs->total()) }} registros
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            <!-- Filtros -->
            <form method="GET" action="{{ route('admin.audit-logs') }}"
                  class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-5 space-y-4">

                <!-- Pills de acción -->
                @php
                    $actionPills = [
                        ''         => ['icon' => '⚡', 'label' => 'Todas'],
                        'created'  => ['icon' => '✨', 'label' => 'Creación'],
                        'updated'  => ['icon' => '✏️',  'label' => 'Actualización'],
                        'deleted'  => ['icon' => '🗑️', 'label' => 'Eliminación'],
                        'restored' => ['icon' => '♻️', 'label' => 'Restauración'],
                    ];
                    $currentAction = request('action', '');
                @endphp
                <div class="flex flex-wrap gap-2">
                    @foreach($actionPills as $val => $pill)
                    <button type="submit" name="action" value="{{ $val }}"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-semibold border transition-all
                                   {{ $currentAction === $val
                                       ? 'bg-cj-morado-profundo text-white border-cj-morado-profundo shadow-md'
                                       : 'bg-white text-gray-600 border-gray-200 hover:border-cj-morado-profundo/40 hover:text-cj-morado-profundo' }}">
                        {{ $pill['icon'] }} {{ $pill['label'] }}
                    </button>
                    @endforeach
                </div>

                <!-- Campos de búsqueda -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="🔍 Usuario o entidad..."
                           class="col-span-2 md:col-span-1 px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all">

                    <select name="model"
                            class="px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all">
                        <option value="">Todas las entidades</option>
                        @foreach($modelTypes as $mt)
                        <option value="{{ $mt }}" {{ request('model') === $mt ? 'selected' : '' }}>{{ $mt }}</option>
                        @endforeach
                    </select>

                    <input type="date" name="from" value="{{ request('from') }}"
                           class="px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all">

                    <input type="date" name="to" value="{{ request('to') }}"
                           class="px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all">
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                            class="px-5 py-2.5 bg-cj-morado-profundo text-white rounded-xl text-sm font-semibold hover:bg-cj-morado-medio transition-all">
                        Buscar
                    </button>
                    <a href="{{ route('admin.audit-logs') }}"
                       class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200 transition-all">
                        Limpiar filtros
                    </a>
                </div>
            </form>

            <!-- Timeline de actividad -->
            @if($logs->isEmpty())
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-16 text-center">
                <p class="text-5xl mb-4">🔍</p>
                <p class="text-lg font-semibold text-cj-texto">No hay registros para los filtros aplicados</p>
                <p class="text-sm text-cj-texto-claro mt-1">Intenta ampliar el rango de fechas o quitar algunos filtros</p>
            </div>
            @else
            <div class="space-y-3" x-data="{ detail: null }">
                @foreach($logs as $log)
                @php
                    $info    = $log->actionInfo();
                    $actor   = $log->user?->name ?? 'Sistema';
                    $role    = $log->user_role ? ucfirst($log->user_role) : 'Sistema';
                @endphp
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 overflow-hidden
                            transition-all hover:shadow-md">

                    <!-- Fila principal -->
                    <div class="flex items-start gap-4 p-5">

                        <!-- Icono de acción -->
                        <div class="flex-shrink-0 w-11 h-11 {{ $info['iconBg'] }} rounded-full flex items-center justify-center text-lg ring-4 {{ $info['ring'] }} ring-offset-1">
                            {{ $info['icon'] }}
                        </div>

                        <!-- Contenido -->
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-cj-texto text-base leading-snug">
                                <span class="text-cj-morado-profundo">{{ $actor }}</span>
                                {{ $log->actionSentence() }}
                            </p>
                            <p class="text-xs text-cj-texto-claro mt-0.5">
                                {{ $role }}
                                <span class="mx-1.5 text-gray-300">·</span>
                                <span title="{{ $log->created_at->format('d/m/Y H:i:s') }}">
                                    {{ $log->created_at->diffForHumans() }}
                                </span>
                                <span class="mx-1.5 text-gray-300">·</span>
                                <span class="font-mono">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                            </p>

                            <!-- Badges + IP + botón -->
                            <div class="flex flex-wrap items-center gap-2 mt-2.5">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $info['bg'] }} {{ $info['text'] }}">
                                    {{ $info['label'] }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                    {{ $log->modelLabelEs() }}
                                </span>
                                @if($log->ip_address)
                                <span class="text-xs font-mono text-gray-400">{{ $log->ip_address }}</span>
                                @endif

                                @if($log->old_values || $log->new_values)
                                <button @click="detail = detail === {{ $log->id }} ? null : {{ $log->id }}"
                                        class="ml-auto flex items-center gap-1 text-xs font-semibold text-cj-morado-profundo hover:text-cj-morado-medio transition-colors">
                                    <span x-text="detail === {{ $log->id }} ? 'Ocultar cambios' : 'Ver cambios'">Ver cambios</span>
                                    <svg class="w-3.5 h-3.5 transition-transform" :class="detail === {{ $log->id }} ? 'rotate-180' : ''"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Diff expandible -->
                    @if($log->old_values || $log->new_values)
                    <div x-show="detail === {{ $log->id }}"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="border-t border-gray-100 bg-gray-50/70 px-5 py-4">
                        <div class="grid md:grid-cols-2 gap-4 text-xs">

                            @if($log->old_values)
                            <div>
                                <p class="font-bold text-red-600 mb-2 uppercase tracking-wider text-xs">
                                    ← Antes
                                </p>
                                <div class="bg-red-50 border border-red-200 rounded-xl p-3 space-y-2 max-h-52 overflow-y-auto">
                                    @foreach($log->old_values as $k => $v)
                                    <div class="flex gap-2 items-start">
                                        <span class="font-semibold text-red-700 min-w-32 flex-shrink-0">
                                            {{ \App\Models\AuditLog::fieldLabel($k) }}
                                        </span>
                                        <span class="text-red-900 break-all">
                                            {{ is_array($v) ? json_encode($v, JSON_UNESCAPED_UNICODE) : ($v ?? '—') }}
                                        </span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if($log->new_values)
                            <div>
                                <p class="font-bold text-green-600 mb-2 uppercase tracking-wider text-xs">
                                    @if(!$log->old_values)
                                        ✅ Registro inicial
                                    @else
                                        → Después
                                    @endif
                                </p>
                                <div class="bg-green-50 border border-green-200 rounded-xl p-3 space-y-2 max-h-52 overflow-y-auto">
                                    @foreach($log->new_values as $k => $v)
                                    <div class="flex gap-2 items-start">
                                        <span class="font-semibold text-green-700 min-w-32 flex-shrink-0">
                                            {{ \App\Models\AuditLog::fieldLabel($k) }}
                                        </span>
                                        <span class="text-green-900 break-all">
                                            {{ is_array($v) ? json_encode($v, JSON_UNESCAPED_UNICODE) : ($v ?? '—') }}
                                        </span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            <!-- Paginación -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 px-6 py-4">
                {{ $logs->appends(request()->query())->links() }}
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
