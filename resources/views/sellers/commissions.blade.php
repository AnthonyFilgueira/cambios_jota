<x-app-layout>
    <div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
    <div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-cj-texto leading-tight">
                    Comisiones — {{ $seller->name }}
                </h2>
                <p class="text-sm text-cj-texto-claro mt-0.5">Código: <span class="font-mono font-bold text-cj-morado-profundo">{{ $seller->code }}</span></p>
            </div>
            <a href="{{ route('sellers.index') }}"
               class="text-sm text-cj-morado-profundo font-semibold hover:underline">
                ← Vendedores
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 space-y-6">

            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-800 rounded-xl px-4 py-3 text-sm font-medium">
                {{ session('success') }}
            </div>
            @endif

            <!-- Comisión vigente + Simulador -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-6"
                 x-data="{
                     tipo: '{{ $latest?->commission_type ?? 'percentage' }}',
                     sellerVal: {{ $latest?->seller_value ?? $seller->seller_commission }},
                     bossVal: {{ $latest?->boss_value ?? $seller->boss_commission }},
                     monto: 500,
                     get sellerGana() {
                         if (this.tipo === 'fixed') return parseFloat(this.sellerVal).toFixed(2);
                         return (this.monto * this.sellerVal / 100).toFixed(2);
                     },
                     get jefGana() {
                         if (this.tipo === 'fixed') return parseFloat(this.bossVal).toFixed(2);
                         return (this.monto * this.bossVal / 100).toFixed(2);
                     },
                     setRapido(v) { this.sellerVal = v; }
                 }">

                <h3 class="font-bold text-cj-texto mb-5">Configurar nueva regla de comisión</h3>

                <form action="{{ route('sellers.commissions.store', $seller) }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Tipo -->
                    <div>
                        <label class="block text-xs font-bold text-cj-texto-claro uppercase tracking-widest mb-2">Tipo de comisión</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="commission_type" value="percentage" class="sr-only" x-model="tipo">
                                <div :class="tipo === 'percentage' ? 'border-cj-morado-profundo bg-cj-morado-claro' : 'border-gray-200 bg-white'"
                                     class="border-2 rounded-xl p-4 text-center transition-all">
                                    <p class="text-2xl font-black text-cj-morado-profundo">%</p>
                                    <p class="text-sm font-semibold text-cj-texto mt-1">Porcentaje</p>
                                    <p class="text-xs text-cj-texto-claro">Del monto enviado</p>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="commission_type" value="fixed" class="sr-only" x-model="tipo">
                                <div :class="tipo === 'fixed' ? 'border-cj-turquesa bg-teal-50' : 'border-gray-200 bg-white'"
                                     class="border-2 rounded-xl p-4 text-center transition-all">
                                    <p class="text-2xl font-black text-cj-turquesa">S/</p>
                                    <p class="text-sm font-semibold text-cj-texto mt-1">Monto fijo</p>
                                    <p class="text-xs text-cj-texto-claro">Por transacción</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Valores -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-cj-texto-claro uppercase tracking-widest mb-2">
                                Comisión vendedor
                                <span class="text-cj-morado-profundo" x-text="tipo === 'fixed' ? '(S/)' : '(%)'"></span>
                            </label>
                            <!-- Pills rápidas (solo porcentaje) -->
                            <div x-show="tipo === 'percentage'" class="flex gap-2 mb-2 flex-wrap">
                                @foreach([2, 3, 4, 5] as $pct)
                                <button type="button" @click="setRapido({{ $pct }})"
                                        :class="sellerVal == {{ $pct }} ? 'bg-cj-morado-profundo text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                        class="px-3 py-1 rounded-full text-xs font-bold transition-all">
                                    {{ $pct }}%
                                </button>
                                @endforeach
                            </div>
                            <input type="number" name="seller_value" step="0.01" min="0" max="100"
                                   x-model="sellerVal" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all font-mono font-bold text-lg text-cj-morado-profundo">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-cj-texto-claro uppercase tracking-widest mb-2">
                                Comisión jefe
                                <span class="text-cj-rosa" x-text="tipo === 'fixed' ? '(S/)' : '(%)'"></span>
                            </label>
                            <div x-show="tipo === 'percentage'" class="flex gap-2 mb-2 flex-wrap">
                                @foreach([2, 3, 4, 5] as $pct)
                                <button type="button" @click="bossVal = {{ $pct }}"
                                        :class="bossVal == {{ $pct }} ? 'bg-cj-rosa text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                        class="px-3 py-1 rounded-full text-xs font-bold transition-all">
                                    {{ $pct }}%
                                </button>
                                @endforeach
                            </div>
                            <input type="number" name="boss_value" step="0.01" min="0" max="100"
                                   x-model="bossVal" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-rosa focus:ring-2 focus:ring-cj-rosa/20 transition-all font-mono font-bold text-lg text-cj-rosa">
                        </div>
                    </div>

                    <!-- Simulador -->
                    <div class="bg-gradient-to-br from-cj-morado-claro to-teal-50 rounded-2xl p-5 border border-cj-morado-profundo/10">
                        <p class="text-xs font-bold uppercase tracking-widest text-cj-texto-claro mb-3">Simulador de ganancias</p>
                        <div class="flex items-center gap-3 mb-4">
                            <label class="text-sm font-semibold text-cj-texto whitespace-nowrap">Si el cliente envía</label>
                            <div class="relative flex-1">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-bold text-cj-texto-claro">S/</span>
                                <input type="number" x-model="monto" min="1" step="50"
                                       class="w-full pl-9 pr-4 py-2.5 border-2 border-cj-morado-profundo/20 rounded-xl font-mono font-bold text-cj-morado-profundo focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-white rounded-xl p-4 text-center shadow-sm">
                                <p class="text-xs text-cj-texto-claro font-semibold mb-1">El vendedor gana</p>
                                <p class="text-2xl font-black text-cj-morado-profundo font-mono">S/ <span x-text="sellerGana"></span></p>
                            </div>
                            <div class="bg-white rounded-xl p-4 text-center shadow-sm">
                                <p class="text-xs text-cj-texto-claro font-semibold mb-1">El jefe gana</p>
                                <p class="text-2xl font-black text-cj-rosa font-mono">S/ <span x-text="jefGana"></span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Notas -->
                    <div>
                        <label class="block text-xs font-bold text-cj-texto-claro uppercase tracking-widest mb-2">Notas (opcional)</label>
                        <textarea name="notes" rows="2" maxlength="500" placeholder="Ej: Ajuste por volumen de mes"
                                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all text-sm resize-none"></textarea>
                    </div>

                    <button type="submit"
                            class="w-full py-3.5 bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white font-bold rounded-xl shadow-lg hover:opacity-90 transition-all">
                        Guardar y aplicar nueva regla
                    </button>
                </form>
            </div>

            <!-- Historial de reglas -->
            @if($rules->isNotEmpty())
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-6">
                <h3 class="font-bold text-cj-texto mb-4">Historial de comisiones</h3>
                <div class="space-y-3">
                    @foreach($rules as $i => $rule)
                    <div class="flex items-start gap-4 p-4 {{ $i === 0 ? 'bg-cj-morado-claro border-2 border-cj-morado-profundo/20' : 'bg-gray-50 border border-gray-100' }} rounded-xl">
                        <div class="flex-shrink-0">
                            @if($i === 0)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-cj-morado-profundo text-white">Vigente</span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-200 text-gray-600">Anterior</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 flex-wrap">
                                <span class="font-mono font-black text-cj-morado-profundo text-lg">
                                    @if($rule->commission_type === 'fixed')
                                        S/ {{ $rule->seller_value }} fijo
                                    @else
                                        {{ $rule->seller_value }}% vendedor · {{ $rule->boss_value }}% jefe
                                    @endif
                                </span>
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $rule->commission_type === 'fixed' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                    {{ $rule->typeLabel() }}
                                </span>
                            </div>
                            @if($rule->notes)
                            <p class="text-xs text-cj-texto-claro mt-1 italic">{{ $rule->notes }}</p>
                            @endif
                            <p class="text-xs text-cj-texto-claro mt-1">
                                {{ $rule->created_at->format('d M Y, H:i') }}
                                @if($rule->appliedBy)
                                · por {{ $rule->appliedBy->name }}
                                @endif
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
