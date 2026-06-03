<x-app-layout>
    <div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
    <div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

    <div class="max-w-3xl mx-auto px-4 py-8">

        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ route('sellers.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-3">
                ← Volver a vendedores
            </a>
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">✏️ Editar Vendedor</h1>
                    <p class="text-gray-600 mt-1">Actualiza datos y cuentas bancarias asignadas</p>
                </div>
                <span class="ml-auto bg-gradient-to-r from-purple-600 to-purple-700 text-white font-mono text-sm px-4 py-2 rounded-xl shadow">
                    {{ $seller->code }}
                </span>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                <ul class="text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6 text-sm text-green-700">
                ✅ {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('sellers.update', $seller) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Card 1: Datos básicos --}}
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/50 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-5 flex items-center gap-2">
                    <span class="w-8 h-8 bg-purple-100 text-purple-700 rounded-lg flex items-center justify-center text-sm font-bold">1</span>
                    Datos del vendedor
                </h2>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Código de vendedor</label>
                        <div class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-100 rounded-xl text-purple-700 font-mono font-bold text-sm">
                            {{ $seller->code }}
                        </div>
                        <p class="mt-1 text-xs text-gray-400">Este código es único e inmodificable</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nombre completo *</label>
                        <input type="text" name="name" value="{{ old('name', $seller->name) }}" required
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all">
                        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Correo electrónico *</label>
                        <input type="email" name="email" value="{{ old('email', $seller->user?->email) }}" required
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all">
                        @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    {{-- Cambio de contraseña (opcional) --}}
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">🔑 Cambiar contraseña <span class="font-normal normal-case text-gray-400">(dejar vacío para mantener la actual)</span></p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1.5">Nueva contraseña</label>
                                <input type="password" name="password"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all"
                                       placeholder="Mínimo 8 caracteres">
                                @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1.5">Confirmar contraseña</label>
                                <input type="password" name="password_confirmation"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all"
                                       placeholder="Repite la nueva contraseña">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Comisión Vendedor (%)</label>
                            <div class="relative">
                                <input type="number" name="seller_commission"
                                       value="{{ old('seller_commission', $seller->seller_commission) }}"
                                       step="0.01" min="0" max="100" required
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all pr-10">
                                <span class="absolute right-3 top-3.5 text-gray-400 font-bold">%</span>
                            </div>
                            @error('seller_commission')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Comisión Dueño (%)</label>
                            <div class="relative">
                                <input type="number" name="boss_commission"
                                       value="{{ old('boss_commission', $seller->boss_commission) }}"
                                       step="0.01" min="0" max="100" required
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all pr-10">
                                <span class="absolute right-3 top-3.5 text-gray-400 font-bold">%</span>
                            </div>
                            @error('boss_commission')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    @if ($seller->sales()->exists())
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 flex items-start gap-2">
                            <span class="text-blue-500">ℹ️</span>
                            <p class="text-xs text-blue-800">
                                Este vendedor ya tiene ventas registradas. Cambiar la comisión NO afectará ventas pasadas.
                                Usa <a href="{{ route('sellers.commissions', $seller) }}" class="underline font-semibold">Gestión de Comisiones</a> para el historial completo.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Card 2: Cuentas bancarias por país --}}
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/50 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
                    <span class="w-8 h-8 bg-teal-100 text-teal-700 rounded-lg flex items-center justify-center text-sm font-bold">2</span>
                    Cuentas bancarias asignadas
                </h2>
                <p class="text-sm text-gray-500 mb-5 ml-10">Activa o desactiva las cuentas bancarias de este vendedor por país</p>

                @forelse ($countries as $countryId => $country)
                    <div class="mb-6 last:mb-0">
                        <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-100">
                            <span class="text-xl">{{ $country->emoji }}</span>
                            <span class="font-semibold text-gray-700">{{ $country->name }}</span>
                            <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded font-mono">{{ $country->code_iso }}</span>
                        </div>

                        <div class="space-y-2">
                            @foreach ($businessAccounts[$countryId] as $account)
                                @php $isAssigned = in_array($account->id, $assignedAccountIds); @endphp
                                <label for="account_{{ $account->id }}"
                                       class="flex items-center justify-between p-4 border-2 rounded-xl cursor-pointer transition-all
                                              {{ $isAssigned ? 'border-purple-300 bg-purple-50/40' : 'border-gray-100 hover:border-purple-200 hover:bg-purple-50/20' }}">
                                    <div class="flex items-center gap-3">
                                        <span class="text-lg">🏦</span>
                                        <div>
                                            <p class="font-semibold text-gray-800 text-sm">
                                                {{ $account->alias ?? $account->bank->name }}
                                                @if($isAssigned)
                                                    <span class="ml-1 text-xs bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded font-normal">Activa</span>
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                {{ $account->bank->name }} · {{ $account->account_number }}
                                                <span class="ml-1 bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded">{{ ucfirst($account->account_type) }}</span>
                                            </p>
                                        </div>
                                    </div>

                                    <input type="checkbox"
                                           name="business_accounts[]"
                                           value="{{ $account->id }}"
                                           id="account_{{ $account->id }}"
                                           class="peer sr-only"
                                           {{ $isAssigned ? 'checked' : '' }}>
                                    <div class="relative w-11 h-6 bg-gray-200 peer-checked:bg-purple-600 rounded-full transition-colors shrink-0
                                                after:content-[''] after:absolute after:top-0.5 after:left-0.5
                                                after:bg-white after:rounded-full after:h-5 after:w-5
                                                after:transition-all peer-checked:after:translate-x-5"></div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400">
                        <p class="text-3xl mb-2">🏦</p>
                        <p class="text-sm">No hay cuentas bancarias configuradas.</p>
                    </div>
                @endforelse
            </div>

            {{-- Botones --}}
            <div class="flex gap-3">
                <a href="{{ route('sellers.index') }}"
                   class="flex-1 text-center px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:border-gray-400 transition-all font-semibold">
                    Cancelar
                </a>
                <button type="submit"
                        class="flex-1 bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-3 rounded-xl shadow-lg hover:shadow-2xl hover:-translate-y-0.5 transition-all font-semibold">
                    Actualizar Vendedor
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
