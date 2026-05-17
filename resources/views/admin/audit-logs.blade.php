<x-app-layout>
    <div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
    <div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-cj-texto leading-tight">Auditoría del Sistema</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            <!-- Filtros -->
            <form method="GET" action="{{ route('admin.audit-logs') }}"
                  class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-5">
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Usuario o entidad..."
                           class="px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all">

                    <select name="action"
                            class="px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all">
                        <option value="">Todas las acciones</option>
                        @foreach(['created', 'updated', 'deleted', 'restored'] as $a)
                        <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>
                            {{ ucfirst($a) }}
                        </option>
                        @endforeach
                    </select>

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
                <div class="flex gap-2 mt-3">
                    <button type="submit"
                            class="px-5 py-2.5 bg-cj-morado-profundo text-white rounded-xl text-sm font-semibold hover:bg-cj-morado-medio transition-all">
                        Filtrar
                    </button>
                    <a href="{{ route('admin.audit-logs') }}"
                       class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200 transition-all">
                        Limpiar
                    </a>
                    <span class="ml-auto text-sm text-cj-texto-claro self-center">
                        {{ number_format($logs->total()) }} registros
                    </span>
                </div>
            </form>

            <!-- Tabla de logs -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 overflow-hidden">
                @if($logs->isEmpty())
                <div class="p-12 text-center text-cj-texto-claro">No hay registros de auditoría.</div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm" x-data="{ detail: null }">
                        <thead class="bg-gradient-to-r from-cj-morado-profundo/5 to-cj-turquesa/5 border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-cj-texto-claro">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-cj-texto-claro">Usuario</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-cj-texto-claro">Acción</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-cj-texto-claro">Entidad</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-cj-texto-claro">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-cj-texto-claro">IP</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($logs as $log)
                            <tr class="hover:bg-gray-50/50 transition-all">
                                <td class="px-4 py-3 text-xs text-cj-texto-claro font-mono whitespace-nowrap">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-cj-texto">{{ $log->user?->name ?? 'Sistema' }}</div>
                                    @if($log->user_role)
                                    <div class="text-xs text-cj-texto-claro">{{ $log->user_role }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $log->actionBadgeClass() }}">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-semibold text-cj-texto">{{ $log->modelLabel() }}</td>
                                <td class="px-4 py-3 font-mono text-cj-texto-claro">#{{ $log->model_id }}</td>
                                <td class="px-4 py-3 text-xs font-mono text-cj-texto-claro">{{ $log->ip_address }}</td>
                                <td class="px-4 py-3">
                                    @if($log->old_values || $log->new_values)
                                    <button @click="detail = detail === {{ $log->id }} ? null : {{ $log->id }}"
                                            class="text-xs text-cj-morado-profundo font-semibold hover:underline">
                                        Ver cambios
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            <!-- Fila de detalle expandible -->
                            @if($log->old_values || $log->new_values)
                            <tr x-show="detail === {{ $log->id }}" class="bg-cj-morado-claro/30">
                                <td colspan="7" class="px-6 py-4">
                                    <div class="grid md:grid-cols-2 gap-4 text-xs">
                                        @if($log->old_values)
                                        <div>
                                            <p class="font-bold text-red-600 mb-2 uppercase tracking-wider">Antes</p>
                                            <div class="bg-red-50 border border-red-200 rounded-xl p-3 space-y-1 max-h-48 overflow-y-auto">
                                                @foreach($log->old_values as $k => $v)
                                                <div class="flex gap-2">
                                                    <span class="font-bold text-red-700 min-w-28 flex-shrink-0">{{ $k }}</span>
                                                    <span class="text-red-900 break-all">{{ is_array($v) ? json_encode($v) : $v }}</span>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                        @if($log->new_values)
                                        <div>
                                            <p class="font-bold text-green-600 mb-2 uppercase tracking-wider">Después</p>
                                            <div class="bg-green-50 border border-green-200 rounded-xl p-3 space-y-1 max-h-48 overflow-y-auto">
                                                @foreach($log->new_values as $k => $v)
                                                <div class="flex gap-2">
                                                    <span class="font-bold text-green-700 min-w-28 flex-shrink-0">{{ $k }}</span>
                                                    <span class="text-green-900 break-all">{{ is_array($v) ? json_encode($v) : $v }}</span>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $logs->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
