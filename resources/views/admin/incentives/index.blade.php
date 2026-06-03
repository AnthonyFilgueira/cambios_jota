<x-app-layout>
    <div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
    <div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-cj-texto leading-tight">Motor de Incentivos</h2>
    </x-slot>

    <div class="py-8" x-data="incentivosApp()">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 space-y-6">

            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-800 rounded-xl px-4 py-3 text-sm font-medium">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-800 rounded-xl px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- ══════════════════════════════════════════
                 FORMULARIO — 4 SECCIONES
            ══════════════════════════════════════════ -->
            <form action="{{ route('admin.incentives.store') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Nombre (siempre visible) -->
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-5">
                    <label class="block text-xs font-bold uppercase tracking-widest text-cj-texto-claro mb-2">Nombre del incentivo *</label>
                    <input type="text" name="name" required maxlength="150" value="{{ old('name') }}"
                           placeholder="Ej: Bono Lanzamiento Mayo 2026, Comisión Extra Estrella..."
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all text-cj-texto font-semibold">
                    <input type="text" name="description" maxlength="500" value="{{ old('description') }}"
                           placeholder="Descripción interna opcional..."
                           class="w-full mt-2 px-4 py-2 border border-gray-200 rounded-xl text-sm text-cj-texto-claro focus:border-cj-morado-profundo/40 focus:ring-1 focus:ring-cj-morado-profundo/10 transition-all">
                </div>

                <!-- SECCIÓN 1 — ¿Qué hace este incentivo? -->
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-5">
                    <p class="text-xs font-bold uppercase tracking-widest text-cj-texto-claro mb-4">
                        <span class="inline-flex items-center justify-center w-5 h-5 bg-cj-morado-profundo text-white rounded-full text-xs font-bold mr-2">1</span>
                        ¿Qué hace este incentivo?
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="extra_receptor" class="sr-only" x-model="tipo">
                            <div :class="tipo === 'extra_receptor'
                                    ? 'border-cj-morado-profundo bg-cj-morado-claro ring-2 ring-cj-morado-profundo/20'
                                    : 'border-gray-200 hover:border-cj-morado-profundo/30'"
                                 class="border-2 rounded-2xl p-4 transition-all">
                                <div class="flex items-start gap-3">
                                    <span class="text-2xl">🎁</span>
                                    <div>
                                        <p class="font-bold text-cj-texto text-sm">Extra al receptor</p>
                                        <p class="text-xs text-cj-texto-claro mt-0.5">El familiar en Venezuela recibe más bolívares. Se suma al monto enviado antes de calcular la tasa.</p>
                                    </div>
                                </div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="extra_comision" class="sr-only" x-model="tipo">
                            <div :class="tipo === 'extra_comision'
                                    ? 'border-cj-turquesa bg-teal-50 ring-2 ring-cj-turquesa/20'
                                    : 'border-gray-200 hover:border-cj-turquesa/30'"
                                 class="border-2 rounded-2xl p-4 transition-all">
                                <div class="flex items-start gap-3">
                                    <span class="text-2xl">⭐</span>
                                    <div>
                                        <p class="font-bold text-cj-texto text-sm">Extra comisión al vendedor</p>
                                        <p class="text-xs text-cj-texto-claro mt-0.5">El vendedor recibe comisión adicional cuando se completa la transacción. Se acredita en su monedero.</p>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- SECCIÓN 2 — ¿A quién aplica? -->
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-5">
                    <p class="text-xs font-bold uppercase tracking-widest text-cj-texto-claro mb-4">
                        <span class="inline-flex items-center justify-center w-5 h-5 bg-cj-morado-profundo text-white rounded-full text-xs font-bold mr-2">2</span>
                        ¿A quién aplica?
                    </p>
                    <select name="target_type" x-model="targetType" required
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all text-cj-texto">
                        <optgroup label="Clientes">
                            <option value="todos_clientes">Todos los clientes</option>
                            <option value="cliente_nuevo">Clientes nuevos (0 envíos previos)</option>
                            <option value="cliente_especifico">Cliente específico</option>
                        </optgroup>
                        <optgroup label="Vendedores">
                            <option value="todos_vendedores">Todos los vendedores</option>
                            <option value="vendedor_especifico">Vendedor específico</option>
                            <option value="clientes_de_vendedor">Clientes de un vendedor específico</option>
                        </optgroup>
                    </select>

                    <!-- Target ID — solo cuando se elige un específico -->
                    <div x-show="targetType === 'cliente_especifico'" x-transition class="mt-3">
                        <label class="block text-xs font-semibold text-cj-texto-claro mb-1">ID del usuario (cliente)</label>
                        <input type="number" name="target_id" min="1" placeholder="Ej: 42"
                               x-bind:value="targetType === 'cliente_especifico' ? '' : null"
                               class="w-full px-4 py-3 border-2 border-cj-morado-profundo/30 rounded-xl focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all font-mono">
                    </div>
                    <div x-show="targetType === 'vendedor_especifico'" x-transition class="mt-3">
                        <label class="block text-xs font-semibold text-cj-texto-claro mb-1">ID del vendedor (seller)</label>
                        <input type="number" name="target_id" min="1" placeholder="Ej: 7"
                               class="w-full px-4 py-3 border-2 border-cj-turquesa/30 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all font-mono">
                    </div>
                    <div x-show="targetType === 'clientes_de_vendedor'" x-transition class="mt-3">
                        <label class="block text-xs font-semibold text-cj-texto-claro mb-1">ID del vendedor (sus clientes recibirán el incentivo)</label>
                        <input type="number" name="target_id" min="1" placeholder="Ej: 7"
                               class="w-full px-4 py-3 border-2 border-cj-turquesa/30 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all font-mono">
                    </div>
                </div>

                <!-- SECCIÓN MONEDA — ¿A qué moneda aplica? -->
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-5">
                    <p class="text-xs font-bold uppercase tracking-widest text-cj-texto-claro mb-3">
                        Moneda de aplicación
                        <span class="text-gray-400 font-normal normal-case tracking-normal ml-1">— vacío = aplica a todas las monedas</span>
                    </p>
                    <select name="currency_id" x-model="currencyId" @change="onCurrencyChange()"
                            data-old-currency-id="{{ old('currency_id', '') }}"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all text-cj-texto">
                        <option value="">Todas las monedas (incentivo global)</option>
                        <template x-for="currency in currencies" :key="currency.id">
                            <option :value="currency.id" :selected="currency.id == currencyId"
                                    x-text="`${currency.symbol} ${currency.code} — ${currency.name}`"></option>
                        </template>
                    </select>
                    <p class="text-xs text-cj-texto-claro mt-2">Si seleccionas una moneda, el incentivo solo se activará cuando el par de cambio involucre esa moneda origen.</p>
                </div>

                <!-- SECCIÓN 3 — ¿Cuánto? -->
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-5">
                    <p class="text-xs font-bold uppercase tracking-widest text-cj-texto-claro mb-4">
                        <span class="inline-flex items-center justify-center w-5 h-5 bg-cj-morado-profundo text-white rounded-full text-xs font-bold mr-2">3</span>
                        ¿Cuánto?
                    </p>
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="value_type" value="fixed" class="sr-only" x-model="valueType">
                            <div :class="valueType === 'fixed'
                                    ? 'border-cj-turquesa bg-teal-50 ring-2 ring-cj-turquesa/20'
                                    : 'border-gray-200 hover:border-cj-turquesa/30'"
                                 class="border-2 rounded-2xl p-4 text-center transition-all">
                                <p class="text-2xl font-black text-cj-turquesa" x-text="currencySymbol"></p>
                                <p class="text-xs text-cj-texto-claro mt-1" x-text="'Monto fijo en ' + currencyCode"></p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="value_type" value="percentage" class="sr-only" x-model="valueType">
                            <div :class="valueType === 'percentage'
                                    ? 'border-cj-morado-profundo bg-cj-morado-claro ring-2 ring-cj-morado-profundo/20'
                                    : 'border-gray-200 hover:border-cj-morado-profundo/30'"
                                 class="border-2 rounded-2xl p-4 text-center transition-all">
                                <p class="text-2xl font-black text-cj-morado-profundo">%</p>
                                <p class="text-xs text-cj-texto-claro mt-1">Porcentaje del monto</p>
                            </div>
                        </label>
                    </div>
                    <div class="flex gap-3 items-end">
                        <div class="flex-1">
                            <label class="block text-xs font-semibold text-cj-texto-claro mb-1">
                                Valor <span x-text="valueType === 'fixed' ? '(' + currencySymbol + ')' : '(%)'"></span> *
                            </label>
                            <input type="number" name="value" step="0.01" min="0" x-model.number="valor" required
                                   placeholder="Ej: 10"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all font-mono font-bold text-xl text-cj-morado-profundo">
                        </div>
                        <!-- Preview inline -->
                        <div class="flex-1 bg-gradient-to-br from-cj-morado-claro to-teal-50 rounded-xl p-3 border border-cj-morado-profundo/10">
                            <p class="text-xs text-cj-texto-claro font-semibold" x-text="'Ejemplo con ' + currencySymbol + '100'"></p>
                            <p class="font-bold text-cj-morado-profundo text-lg mt-0.5">
                                +<span x-text="valueType === 'fixed'
                                    ? currencySymbol + ' ' + parseFloat(valor || 0).toFixed(2)
                                    : currencySymbol + ' ' + (100 * (parseFloat(valor || 0) / 100)).toFixed(2) + ' (' + parseFloat(valor||0).toFixed(1) + '%)'">
                                </span>
                            </p>
                            <p class="text-xs text-cj-turquesa font-semibold" x-text="tipo === 'extra_receptor' ? 'El familiar recibe más Bs' : 'El vendedor gana más'"></p>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 4 — Condiciones y vigencia -->
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-5">
                    <p class="text-xs font-bold uppercase tracking-widest text-cj-texto-claro mb-4">
                        <span class="inline-flex items-center justify-center w-5 h-5 bg-cj-morado-profundo text-white rounded-full text-xs font-bold mr-2">4</span>
                        Condiciones y vigencia
                    </p>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-cj-texto-claro mb-1">Monto mínimo de envío (S/) <span class="text-gray-400 font-normal">— opcional</span></label>
                            <input type="number" name="min_amount" step="0.01" min="0" value="{{ old('min_amount') }}"
                                   placeholder="Ej: 50 (vacío = sin mínimo)"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-morado-profundo/40 transition-all font-mono text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-cj-texto-claro mb-1">Máximo de usos <span class="text-gray-400 font-normal">— opcional</span></label>
                            <input type="number" name="max_uses" min="1" value="{{ old('max_uses') }}"
                                   placeholder="Ej: 100 (vacío = ilimitado)"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-morado-profundo/40 transition-all font-mono text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-cj-texto-claro mb-1">Fecha de inicio *</label>
                            <input type="date" name="starts_at" required value="{{ old('starts_at', date('Y-m-d')) }}"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-morado-profundo/40 transition-all text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-cj-texto-claro mb-1">Fecha de fin <span class="text-gray-400 font-normal">— vacío = sin vencimiento</span></label>
                            <input type="date" name="ends_at" value="{{ old('ends_at') }}"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-morado-profundo/40 transition-all text-sm">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="flex items-start gap-3 cursor-pointer group">
                                <input type="checkbox" name="condition_new_client" value="1"
                                       {{ old('condition_new_client') ? 'checked' : '' }}
                                       class="mt-1 w-4 h-4 text-cj-morado-profundo border-2 border-gray-300 rounded focus:ring-cj-morado-profundo/20 transition-all">
                                <div>
                                    <span class="text-sm font-semibold text-cj-texto group-hover:text-cj-morado-profundo transition-colors">Solo para clientes nuevos</span>
                                    <p class="text-xs text-cj-texto-claro mt-0.5">El incentivo solo aplica si el cliente tiene 0 transacciones completadas anteriormente.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <button type="submit"
                            class="mt-5 w-full py-4 bg-gradient-to-r from-cj-morado-profundo via-purple-700 to-cj-rosa text-white font-bold rounded-2xl shadow-lg hover:opacity-90 hover:scale-[1.01] transition-all text-base">
                        Guardar incentivo
                    </button>
                </div>

            </form>

            <!-- ══════════════════════════════════════════
                 LISTA — INCENTIVOS ACTIVOS
            ══════════════════════════════════════════ -->
            @if($active->isNotEmpty())
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">Activos ahora ({{ $active->count() }})</p>
                <div class="space-y-3">
                    @foreach($active as $rule)
                    <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border-2 border-cj-turquesa/30 p-5 flex items-start justify-between gap-4">
                        <div class="flex items-start gap-4 min-w-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-cj-morado-profundo to-cj-rosa rounded-xl flex items-center justify-center text-white text-xl flex-shrink-0 shadow-md shadow-purple-400/30">
                                {{ $rule->typeIcon() }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold text-cj-texto">{{ $rule->name }}</p>
                                <div class="flex items-center gap-2 mt-1 flex-wrap">
                                    <span class="text-xs font-bold text-cj-morado-profundo bg-cj-morado-claro px-2 py-0.5 rounded-full">{{ $rule->typeLabel() }}</span>
                                    <span class="text-xs font-bold text-cj-turquesa bg-cj-turquesa/10 px-2 py-0.5 rounded-full">{{ $rule->valueLabel() }}</span>
                                    <span class="text-xs text-cj-texto-claro">{{ $rule->targetLabel() }}</span>
                                    @if($rule->currency)
                                    <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">{{ $rule->currency->symbol }} {{ $rule->currency->code }}</span>
                                    @else
                                    <span class="text-xs font-semibold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">Global</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3 mt-1.5 flex-wrap">
                                    <span class="text-xs text-cj-texto-claro">
                                        {{ $rule->starts_at->format('d M Y') }}
                                        @if($rule->ends_at) → {{ $rule->ends_at->format('d M Y') }} @else · Sin vencimiento @endif
                                    </span>
                                    <span class="text-xs font-semibold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-full">
                                        {{ $rule->usesLabel() }}
                                    </span>
                                    @if($rule->condition_new_client)
                                    <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Solo nuevos</span>
                                    @endif
                                </div>
                                @if($rule->description)
                                <p class="text-xs text-cj-texto-claro mt-1 italic">{{ $rule->description }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <form action="{{ route('admin.incentives.toggle', $rule) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="px-3 py-1.5 text-xs font-semibold bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition-all whitespace-nowrap">
                                    Pausar
                                </button>
                            </form>
                            <form action="{{ route('admin.incentives.destroy', $rule) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('¿Eliminar el incentivo «{{ $rule->name }}»? Esta acción no se puede deshacer.')"
                                        class="px-3 py-1.5 text-xs font-semibold bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-all">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- LISTA — INCENTIVOS INACTIVOS / VENCIDOS -->
            @if($inactive->isNotEmpty())
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">Inactivos / Vencidos ({{ $inactive->count() }})</p>
                <div class="space-y-2 opacity-60">
                    @foreach($inactive as $rule)
                    <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow border border-gray-200 p-4 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="text-xl">{{ $rule->typeIcon() }}</span>
                            <div class="min-w-0">
                                <p class="font-semibold text-cj-texto text-sm truncate">{{ $rule->name }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs text-gray-500">{{ $rule->typeLabel() }} · {{ $rule->valueLabel() }} · {{ $rule->targetLabel() }}</span>
                                    @if($rule->ends_at && $rule->ends_at->isPast())
                                    <span class="text-xs font-semibold text-red-600 bg-red-50 px-2 py-0.5 rounded-full">Vencido</span>
                                    @elseif($rule->max_uses && $rule->uses_count >= $rule->max_uses)
                                    <span class="text-xs font-semibold text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full">Usos agotados</span>
                                    @else
                                    <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">Inactivo</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('admin.incentives.toggle', $rule) }}" method="POST" class="flex-shrink-0">
                            @csrf @method('PATCH')
                            <button type="submit" class="px-3 py-1.5 text-xs font-semibold bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-all">
                                Reactivar
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($active->isEmpty() && $inactive->isEmpty())
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-12 text-center">
                <div class="text-5xl mb-4">🎁</div>
                <p class="font-semibold text-cj-texto">No hay incentivos registrados</p>
                <p class="text-sm text-cj-texto-claro mt-1">Completa el formulario de arriba para crear tu primer incentivo.</p>
            </div>
            @endif

        </div>
    </div>

    <!-- ══════════════════════════════════════════
         FAB — BOTÓN FLOTANTE DE AYUDA
    ══════════════════════════════════════════ -->
    <div x-data="{ helpOpen: false, helpTab: 0 }">

        <!-- Botón flotante -->
        <button @click="helpOpen = true"
                class="fixed bottom-6 right-6 z-40 flex items-center gap-2 px-4 py-3 bg-gradient-to-r from-cj-morado-profundo to-purple-700 text-white font-semibold text-sm rounded-2xl shadow-lg shadow-purple-900/30 hover:scale-105 hover:shadow-xl hover:shadow-purple-900/40 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>¿Cómo funciona?</span>
        </button>

        <!-- Backdrop -->
        <div x-show="helpOpen" x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="helpOpen = false"
             class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm"
             style="display:none"></div>

        <!-- Modal -->
        <div x-show="helpOpen" x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="fixed inset-x-4 bottom-4 sm:inset-auto sm:bottom-20 sm:right-6 sm:w-[480px] z-50 bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden"
             style="display:none">

            <!-- Header del modal -->
            <div class="bg-gradient-to-r from-cj-morado-profundo to-purple-700 px-5 py-4 flex items-center justify-between">
                <div>
                    <p class="text-white font-bold text-base">🎯 Motor de Incentivos</p>
                    <p class="text-purple-200 text-xs mt-0.5">Configura beneficios que se aplican automáticamente</p>
                </div>
                <button @click="helpOpen = false" class="text-white/70 hover:text-white transition-colors p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Tabs de navegación -->
            <div class="flex border-b border-gray-100">
                <button @click="helpTab = 0"
                        :class="helpTab === 0 ? 'text-cj-morado-profundo border-b-2 border-cj-morado-profundo font-bold' : 'text-cj-texto-claro hover:text-cj-texto'"
                        class="flex-1 py-3 text-xs font-semibold transition-all">¿Qué es esto?</button>
                <button @click="helpTab = 1"
                        :class="helpTab === 1 ? 'text-cj-morado-profundo border-b-2 border-cj-morado-profundo font-bold' : 'text-cj-texto-claro hover:text-cj-texto'"
                        class="flex-1 py-3 text-xs font-semibold transition-all">¿Cómo se usa?</button>
                <button @click="helpTab = 2"
                        :class="helpTab === 2 ? 'text-cj-morado-profundo border-b-2 border-cj-morado-profundo font-bold' : 'text-cj-texto-claro hover:text-cj-texto'"
                        class="flex-1 py-3 text-xs font-semibold transition-all">Ejemplos</button>
            </div>

            <!-- Cuerpo scrollable -->
            <div class="max-h-80 overflow-y-auto p-5 space-y-4">

                <!-- Tab 0 — ¿Qué es esto? -->
                <div x-show="helpTab === 0" x-transition class="space-y-3">
                    <div class="bg-gradient-to-br from-cj-morado-claro to-purple-50 rounded-2xl p-4 border border-cj-morado-profundo/10">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl">🎁</span>
                            <div>
                                <p class="font-bold text-cj-morado-profundo text-sm">Extra al receptor</p>
                                <p class="text-xs text-cj-texto-claro mt-1">El cliente envía S/100 pero su familiar en Venezuela recibe el equivalente a S/110. El extra lo absorbe la campaña, no el cliente.</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-teal-50 to-emerald-50 rounded-2xl p-4 border border-cj-turquesa/20">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl">⭐</span>
                            <div>
                                <p class="font-bold text-cj-turquesa text-sm">Extra comisión al vendedor</p>
                                <p class="text-xs text-cj-texto-claro mt-1">Cuando se completa una transacción, el vendedor recibe su comisión habitual más un porcentaje o monto fijo extra en su monedero virtual.</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-amber-50 rounded-2xl p-3 border border-amber-200">
                        <p class="text-xs text-amber-700 font-semibold">💡 El sistema aplica el incentivo automáticamente — no necesitas hacer nada manual en cada transacción.</p>
                    </div>
                </div>

                <!-- Tab 1 — ¿Cómo se usa? -->
                <div x-show="helpTab === 1" x-transition class="space-y-3">
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50">
                        <span class="flex-shrink-0 w-6 h-6 bg-cj-morado-profundo text-white rounded-full flex items-center justify-center text-xs font-bold">1</span>
                        <div>
                            <p class="text-sm font-semibold text-cj-texto">Elige qué hace el incentivo</p>
                            <p class="text-xs text-cj-texto-claro mt-0.5">¿El familiar recibe más bolívares? → <strong>Extra receptor</strong>. ¿El vendedor gana más? → <strong>Extra comisión</strong>.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50">
                        <span class="flex-shrink-0 w-6 h-6 bg-cj-morado-profundo text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
                        <div>
                            <p class="text-sm font-semibold text-cj-texto">Define a quién aplica</p>
                            <p class="text-xs text-cj-texto-claro mt-0.5">Todos los clientes, solo clientes nuevos, un cliente específico, todos los vendedores, un vendedor, o los clientes de un vendedor.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50">
                        <span class="flex-shrink-0 w-6 h-6 bg-cj-morado-profundo text-white rounded-full flex items-center justify-center text-xs font-bold">3</span>
                        <div>
                            <p class="text-sm font-semibold text-cj-texto">Configura el valor</p>
                            <p class="text-xs text-cj-texto-claro mt-0.5">Monto fijo en soles (S/10) o porcentaje del envío (5%). El preview te muestra cuánto sería con S/100.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50">
                        <span class="flex-shrink-0 w-6 h-6 bg-cj-morado-profundo text-white rounded-full flex items-center justify-center text-xs font-bold">4</span>
                        <div>
                            <p class="text-sm font-semibold text-cj-texto">Agrega condiciones opcionales</p>
                            <p class="text-xs text-cj-texto-claro mt-0.5">Monto mínimo de envío, máximo de usos, rango de fechas, o que solo aplique a clientes sin historial.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50">
                        <span class="flex-shrink-0 w-6 h-6 bg-cj-morado-profundo text-white rounded-full flex items-center justify-center text-xs font-bold">5</span>
                        <div>
                            <p class="text-sm font-semibold text-cj-texto">Guarda — el sistema hace el resto</p>
                            <p class="text-xs text-cj-texto-claro mt-0.5">El incentivo entra en vigor automáticamente. Se muestra en el simulador público y en el formulario de envío.</p>
                        </div>
                    </div>
                </div>

                <!-- Tab 2 — Ejemplos prácticos -->
                <div x-show="helpTab === 2" x-transition class="space-y-3">
                    <div class="border-2 border-cj-morado-profundo/20 rounded-2xl p-4 bg-cj-morado-claro/40">
                        <p class="text-xs font-bold uppercase tracking-widest text-cj-morado-profundo mb-2">Ejemplo A — Bono de lanzamiento</p>
                        <div class="space-y-1 text-xs text-cj-texto-claro">
                            <p>• Tipo: <strong>Extra al receptor</strong></p>
                            <p>• Aplica a: <strong>Todos los clientes</strong></p>
                            <p>• Valor: <strong>S/ 10 fijo</strong></p>
                            <p>• Resultado: cliente envía S/100, familiar recibe el equivalente a S/110 en Bs.</p>
                        </div>
                    </div>
                    <div class="border-2 border-cj-turquesa/20 rounded-2xl p-4 bg-teal-50/40">
                        <p class="text-xs font-bold uppercase tracking-widest text-cj-turquesa mb-2">Ejemplo B — Bono clientes nuevos</p>
                        <div class="space-y-1 text-xs text-cj-texto-claro">
                            <p>• Tipo: <strong>Extra al receptor</strong></p>
                            <p>• Aplica a: <strong>Clientes nuevos</strong></p>
                            <p>• Valor: <strong>5%</strong> · Mínimo: S/50 · Máx. usos: 100</p>
                            <p>• Resultado: primer envío de S/80 → familiar recibe equivalente a S/84.</p>
                        </div>
                    </div>
                    <div class="border-2 border-amber-200 rounded-2xl p-4 bg-amber-50/40">
                        <p class="text-xs font-bold uppercase tracking-widest text-amber-700 mb-2">Ejemplo C — Comisión extra este mes</p>
                        <div class="space-y-1 text-xs text-cj-texto-claro">
                            <p>• Tipo: <strong>Extra comisión al vendedor</strong></p>
                            <p>• Aplica a: <strong>Todos los vendedores</strong></p>
                            <p>• Valor: <strong>1%</strong> · Vigencia: solo mayo 2026</p>
                            <p>• Resultado: transacción de S/500 → vendedor recibe S/5 extra en su monedero.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer del modal -->
            <div class="px-5 pb-4 pt-2 border-t border-gray-100">
                <button @click="helpOpen = false"
                        class="w-full py-2.5 bg-gradient-to-r from-cj-morado-profundo to-purple-700 text-white font-semibold text-sm rounded-xl hover:opacity-90 transition-all">
                    ¡Entendido, voy a configurarlo!
                </button>
            </div>
        </div>

    </div><!-- /FAB container -->

    @push('scripts')
    <script>
    function incentivosApp() {
        return {
            tipo: 'extra_receptor',
            targetType: 'todos_clientes',
            valueType: 'fixed',
            valor: 10,
            currencyId: '',
            currencySymbol: 'S/',
            currencyCode: 'PEN',
            currencies: [],

            async init() {
                const resp = await axios.get('/api/currencies/active');
                this.currencies = resp.data;
                const select = document.querySelector('[data-old-currency-id]');
                const oldId  = select ? parseInt(select.dataset.oldCurrencyId) || '' : '';
                if (oldId) {
                    this.currencyId = oldId;
                    this.onCurrencyChange();
                }
            },

            onCurrencyChange() {
                const found = this.currencyId
                    ? this.currencies.find(c => c.id == this.currencyId)
                    : null;
                this.currencySymbol = found ? found.symbol : 'S/';
                this.currencyCode   = found ? found.code   : 'PEN';
            },
        };
    }
    </script>
    @endpush

</x-app-layout>
