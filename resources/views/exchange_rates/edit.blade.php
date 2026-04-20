<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 py-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-cj-texto">Editar Tasa de Cambio</h1>
            <a href="{{ route('exchange_rates.index') }}" class="text-cj-texto-claro hover:text-cj-morado-profundo">
                ← Volver
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <form method="POST" action="{{ route('exchange_rates.update', $exchangeRate) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Referencias BCV -->
                <div class="bg-cj-fondo rounded-lg p-4 border-l-4 border-cj-morado-profundo">
                    <h3 class="text-sm font-semibold text-cj-texto mb-3">📊 Tasas de Referencia BCV (Venezuela)</h3>
                    <p class="text-xs text-cj-texto-claro mb-4">Estas tasas son iguales para todos los pares y solo sirven de referencia informativa</p>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                USD → VES (BCV)
                            </label>
                            <input
                                type="number"
                                name="usd_rate"
                                step="0.01"
                                value="{{ old('usd_rate', $exchangeRate->usd_rate) }}"
                                class="w-full border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-cj-turquesa focus:border-transparent"
                                required
                            >
                            @error('usd_rate')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Ej: 479.78</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                EUR → VES (BCV)
                            </label>
                            <input
                                type="number"
                                name="eur_rate"
                                step="0.01"
                                value="{{ old('eur_rate', $exchangeRate->eur_rate) }}"
                                class="w-full border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-cj-turquesa focus:border-transparent"
                                required
                            >
                            @error('eur_rate')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Ej: 565.98</p>
                        </div>
                    </div>
                </div>

                <!-- Tasa del Par -->
                <div class="bg-white border-2 border-cj-morado-profundo rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-cj-texto mb-3">💱 Tasa Específica del Par</h3>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Tasa VES (cuántos VES por 1 unidad de origen)
                        </label>
                        <input
                            type="number"
                            name="ves_rate"
                            step="0.00001"
                            value="{{ old('ves_rate', $exchangeRate->ves_rate) }}"
                            class="w-full border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-cj-turquesa focus:border-transparent text-lg font-mono"
                            required
                        >
                        @error('ves_rate')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-2">
                            <strong>Ejemplos:</strong><br>
                            • PEN→VES: 173.71 (1 PEN = 173.71 VES)<br>
                            • ARS→VES: 2.50 (1 ARS = 2.50 VES)<br>
                            • USD→VES: 479.78 (1 USD = 479.78 VES)
                        </p>
                    </div>
                </div>

                <!-- Separador visual -->
                <div class="border-t pt-6 mt-6">
                    <h3 class="text-lg font-semibold text-cj-texto mb-4">
                        💼 Configuración de Comisiones
                    </h3>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Comisión del Dueño (%)
                        </label>
                        <input
                            type="number"
                            name="boss_commission_default"
                            step="0.01"
                            min="0"
                            max="100"
                            placeholder="Dejar vacío para no cambiar"
                            value="{{ old('boss_commission_default') }}"
                            class="w-full border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-cj-turquesa focus:border-transparent"
                        >
                        @error('boss_commission_default')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">
                            💡 Solo completa este campo si quieres actualizar la comisión de TODOS los vendedores.
                            Si lo dejas vacío, las comisiones actuales no cambiarán.
                        </p>
                    </div>

                    @php
                        $commissionGroups = \App\Models\Seller::select('boss_commission')
                            ->groupBy('boss_commission')
                            ->selectRaw('boss_commission, count(*) as count')
                            ->orderBy('count', 'desc')
                            ->get();
                    @endphp

                    @if($commissionGroups->isNotEmpty())
                    <div class="mt-3 bg-gray-50 rounded-lg p-3">
                        <p class="text-xs font-semibold text-gray-600 mb-2">📊 Comisiones actuales de vendedores:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($commissionGroups as $group)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-cj-morado-claro text-cj-texto">
                                    {{ number_format($group->boss_commission, 2) }}%
                                    <span class="ml-1 text-cj-texto-claro">({{ $group->count }} vendedor{{ $group->count > 1 ? 'es' : '' }})</span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                @if($exchangeRate->is_active)
                    <div class="bg-cj-turquesa/10 border border-cj-turquesa rounded-lg p-3">
                        <p class="text-sm text-cj-texto">
                            <span class="font-semibold">✓ Esta es la tasa activa.</span> Los cambios se reflejarán inmediatamente en el simulador público.
                        </p>
                    </div>
                @endif

                <div class="flex gap-3 pt-4">
                    <button
                        type="submit"
                        class="flex-1 bg-cj-morado-profundo hover:bg-cj-morado-medio text-white font-semibold py-3 rounded-lg transition-colors"
                    >
                        Actualizar Tasa
                    </button>
                    <a
                        href="{{ route('exchange_rates.index') }}"
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 rounded-lg text-center transition-colors"
                    >
                        Cancelar
                    </a>
                </div>
            </form>
        </div>

        <!-- Historial de Cambios -->
        @if($history && $history->isNotEmpty())
        <div class="mt-6 bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-cj-texto mb-4 flex items-center">
                📜 Historial de Cambios
                <span class="ml-2 text-xs font-normal text-cj-texto-claro">({{ $history->count() }} registro{{ $history->count() > 1 ? 's' : '' }})</span>
            </h3>

            <div class="space-y-3">
                @foreach($history as $entry)
                    <div class="flex items-start gap-4 p-3 rounded-lg {{ $entry->action === 'deleted' ? 'bg-red-50 border border-red-200' : ($entry->action === 'created' ? 'bg-green-50 border border-green-200' : ($entry->action === 'activated' ? 'bg-blue-50 border border-blue-200' : 'bg-gray-50 border border-gray-200')) }}">
                        <!-- Icono de acción -->
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full {{ $entry->action === 'deleted' ? 'bg-red-200' : ($entry->action === 'created' ? 'bg-green-200' : ($entry->action === 'activated' ? 'bg-blue-200' : 'bg-gray-200')) }}">
                            @if($entry->action === 'created')
                                ✨
                            @elseif($entry->action === 'updated')
                                ✏️
                            @elseif($entry->action === 'activated')
                                ✓
                            @elseif($entry->action === 'deactivated')
                                ✕
                            @elseif($entry->action === 'deleted')
                                🗑️
                            @endif
                        </div>

                        <!-- Información del cambio -->
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-semibold text-sm text-cj-texto">{{ $entry->action_label }}</span>
                                <span class="text-xs text-cj-texto-claro">{{ $entry->created_at->diffForHumans() }}</span>
                            </div>

                            @if($entry->description)
                                <p class="text-xs text-cj-texto-claro mb-2">{{ $entry->description }}</p>
                            @endif

                            <!-- Mostrar cambios específicos -->
                            @if($entry->changes && count($entry->changes) > 0)
                                <div class="mt-2 space-y-1">
                                    @foreach($entry->changes as $field => $change)
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="font-medium text-cj-texto">{{ $change['label'] }}:</span>
                                            <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded">{{ is_bool($change['from']) ? ($change['from'] ? 'Sí' : 'No') : $change['from'] }}</span>
                                            <span>→</span>
                                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded">{{ is_bool($change['to']) ? ($change['to'] ? 'Sí' : 'No') : $change['to'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Usuario -->
                            <div class="mt-2 text-xs text-cj-texto-claro">
                                👤 {{ $entry->user_name }}
                                @if($entry->ip_address)
                                    • IP: {{ $entry->ip_address }}
                                @endif
                                • {{ $entry->created_at->format('d/m/Y H:i:s') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($exchangeRate->history()->count() > 20)
                <p class="text-xs text-center text-cj-texto-claro mt-4">
                    Mostrando los 20 cambios más recientes de {{ $exchangeRate->history()->count() }} total
                </p>
            @endif
        </div>
        @endif

        <div class="mt-6 bg-cj-morado-claro rounded-lg p-4">
            <h3 class="font-semibold text-sm text-cj-texto mb-2">💡 Cómo Funciona</h3>
            <ul class="text-xs text-cj-texto-claro space-y-1">
                <li>• <strong>Referencias BCV:</strong> Se copian en todos los pares (solo informativas)</li>
                <li>• <strong>Tasa VES:</strong> Es la tasa específica de conversión del par</li>
                <li>• <strong>Comisiones:</strong> Se calculan sobre el monto en divisa origen (no sobre tasas)</li>
                <li>• <strong>Protección:</strong> No se puede editar si ya tiene transacciones asociadas</li>
            </ul>
        </div>
    </div>
</x-app-layout>
