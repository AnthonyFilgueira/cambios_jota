<x-app-layout>
    <div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
    <div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-cj-texto leading-tight">Incentivos y Beneficios</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 space-y-6">

            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-800 rounded-xl px-4 py-3 text-sm font-medium">
                {{ session('success') }}
            </div>
            @endif

            <!-- Formulario de creación -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-6"
                 x-data="{
                     tipo: 'bono_volumen',
                     valueType: 'percentage',
                     value: 5,
                     minAmount: '',
                     get preview() {
                         if (this.valueType === 'fixed') return 'S/ ' + parseFloat(this.value || 0).toFixed(2);
                         return parseFloat(this.value || 0).toFixed(2) + '%';
                     }
                 }">
                <h3 class="font-bold text-cj-texto mb-5">Crear nuevo incentivo</h3>

                <form action="{{ route('admin.incentives.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid md:grid-cols-2 gap-4">
                        <!-- Nombre -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-widest text-cj-texto-claro mb-2">Nombre del incentivo *</label>
                            <input type="text" name="name" required maxlength="150"
                                   placeholder="Ej: Bono Estrella Mayo 2026"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all">
                        </div>

                        <!-- Tipo de incentivo -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-cj-texto-claro mb-2">Tipo *</label>
                            <select name="type" x-model="tipo" required
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all">
                                <option value="bono_volumen">Bono por Volumen — al superar monto mínimo</option>
                                <option value="bono_estrella">Bono Estrella — al superar N transacciones</option>
                                <option value="descuento_cliente">Descuento Cliente — rebaja en la comisión</option>
                                <option value="bono_nuevo_cliente">Bono Nuevo Cliente — por cada nuevo cliente registrado</option>
                            </select>
                        </div>

                        <!-- Target -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-cj-texto-claro mb-2">Aplica a *</label>
                            <select name="target_type" required
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all">
                                <option value="all">Todos los vendedores</option>
                                <option value="seller">Vendedor específico</option>
                                <option value="client">Cliente específico</option>
                            </select>
                        </div>

                        <!-- Tipo de valor -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-cj-texto-claro mb-2">Tipo de valor *</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" name="value_type" value="percentage" class="sr-only" x-model="valueType">
                                    <div :class="valueType === 'percentage' ? 'border-cj-morado-profundo bg-cj-morado-claro' : 'border-gray-200'"
                                         class="border-2 rounded-xl p-3 text-center transition-all">
                                        <p class="font-bold text-cj-morado-profundo">%</p>
                                        <p class="text-xs text-cj-texto-claro">Porcentaje</p>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="value_type" value="fixed" class="sr-only" x-model="valueType">
                                    <div :class="valueType === 'fixed' ? 'border-cj-turquesa bg-teal-50' : 'border-gray-200'"
                                         class="border-2 rounded-xl p-3 text-center transition-all">
                                        <p class="font-bold text-cj-turquesa">S/</p>
                                        <p class="text-xs text-cj-texto-claro">Monto fijo</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Valor -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-cj-texto-claro mb-2">
                                Valor <span x-text="valueType === 'fixed' ? '(S/)' : '(%)'"></span> *
                            </label>
                            <input type="number" name="value" step="0.01" min="0" x-model="value" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all font-mono font-bold text-lg text-cj-morado-profundo">
                        </div>

                        <!-- Monto mínimo -->
                        <div x-show="tipo === 'bono_volumen'">
                            <label class="block text-xs font-bold uppercase tracking-widest text-cj-texto-claro mb-2">Monto mínimo (S/)</label>
                            <input type="number" name="min_amount" step="0.01" min="0" x-model="minAmount"
                                   placeholder="Ej: 500"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all font-mono">
                        </div>

                        <!-- Transacciones mínimas -->
                        <div x-show="tipo === 'bono_estrella'">
                            <label class="block text-xs font-bold uppercase tracking-widest text-cj-texto-claro mb-2">Transacciones mínimas en el período</label>
                            <input type="number" name="min_transactions" min="1"
                                   placeholder="Ej: 10"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all font-mono">
                        </div>

                        <!-- Vigencia -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-cj-texto-claro mb-2">Fecha inicio *</label>
                            <input type="date" name="starts_at" required value="{{ date('Y-m-d') }}"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-cj-texto-claro mb-2">Fecha fin (vacío = sin vencimiento)</label>
                            <input type="date" name="ends_at"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all">
                        </div>

                        <!-- Descripción -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-widest text-cj-texto-claro mb-2">Descripción (opcional)</label>
                            <textarea name="description" rows="2" maxlength="500"
                                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all text-sm resize-none"
                                      placeholder="Descripción interna del incentivo..."></textarea>
                        </div>
                    </div>

                    <!-- Preview del incentivo -->
                    <div class="bg-gradient-to-br from-cj-morado-claro to-teal-50 rounded-2xl p-4 border border-cj-morado-profundo/10 flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-cj-morado-profundo to-cj-rosa rounded-full flex items-center justify-center text-white text-xl flex-shrink-0">
                            ⭐
                        </div>
                        <div>
                            <p class="text-xs text-cj-texto-claro font-semibold uppercase tracking-wider">Vista previa del beneficio</p>
                            <p class="font-bold text-cj-morado-profundo text-lg"><span x-text="preview"></span> adicional</p>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full py-3.5 bg-gradient-to-r from-cj-morado-profundo to-cj-rosa text-white font-bold rounded-xl shadow-lg hover:opacity-90 transition-all">
                        Crear incentivo
                    </button>
                </form>
            </div>

            <!-- Incentivos activos -->
            @if($active->isNotEmpty())
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">Activos ahora ({{ $active->count() }})</p>
                <div class="space-y-3">
                    @foreach($active as $rule)
                    <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border-2 border-cj-turquesa/30 p-5 flex items-start justify-between gap-4">
                        <div class="flex items-start gap-4 min-w-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-cj-turquesa to-teal-400 rounded-xl flex items-center justify-center text-white text-xl flex-shrink-0 shadow-md shadow-teal-400/30">
                                @if($rule->type === 'bono_estrella') ⭐
                                @elseif($rule->type === 'bono_volumen') 📈
                                @elseif($rule->type === 'descuento_cliente') 🎁
                                @else 🆕 @endif
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold text-cj-texto">{{ $rule->name }}</p>
                                <div class="flex items-center gap-2 mt-1 flex-wrap">
                                    <span class="text-xs font-bold text-cj-turquesa bg-cj-turquesa/10 px-2 py-0.5 rounded-full">{{ $rule->typeLabel() }}</span>
                                    <span class="text-xs font-bold text-cj-morado-profundo bg-cj-morado-claro px-2 py-0.5 rounded-full">{{ $rule->valueLabel() }}</span>
                                    <span class="text-xs text-cj-texto-claro">{{ $rule->targetLabel() }}</span>
                                </div>
                                @if($rule->description)
                                <p class="text-xs text-cj-texto-claro mt-1 italic">{{ $rule->description }}</p>
                                @endif
                                <p class="text-xs text-cj-texto-claro mt-1">
                                    Desde {{ $rule->starts_at->format('d M Y') }}
                                    @if($rule->ends_at) hasta {{ $rule->ends_at->format('d M Y') }} @else · Sin vencimiento @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <form action="{{ route('admin.incentives.toggle', $rule) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="px-3 py-1.5 text-xs font-semibold bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition-all">
                                    Desactivar
                                </button>
                            </form>
                            <form action="{{ route('admin.incentives.destroy', $rule) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('¿Eliminar este incentivo?')"
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

            <!-- Incentivos inactivos/vencidos -->
            @if($inactive->isNotEmpty())
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3 mt-2">Inactivos / Vencidos ({{ $inactive->count() }})</p>
                <div class="space-y-3 opacity-60">
                    @foreach($inactive as $rule)
                    <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow border border-gray-200 p-4 flex items-center justify-between gap-4">
                        <div>
                            <p class="font-semibold text-cj-texto text-sm">{{ $rule->name }}</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-xs text-gray-500">{{ $rule->typeLabel() }} · {{ $rule->valueLabel() }}</span>
                                @if($rule->ends_at && $rule->ends_at->isPast())
                                <span class="text-xs font-semibold text-red-600 bg-red-50 px-2 py-0.5 rounded-full">Vencido</span>
                                @else
                                <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">Inactivo</span>
                                @endif
                            </div>
                        </div>
                        <form action="{{ route('admin.incentives.toggle', $rule) }}" method="POST">
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
                <div class="text-5xl mb-4">⭐</div>
                <p class="font-semibold text-cj-texto">No hay incentivos registrados</p>
                <p class="text-sm text-cj-texto-claro mt-1">Crea el primer incentivo con el formulario de arriba.</p>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
