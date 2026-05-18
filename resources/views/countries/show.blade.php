<x-app-layout>
<div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
<div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

<div class="max-w-4xl mx-auto px-4 py-8"
     x-data="paisDetalle()">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio rounded-2xl p-5 mb-6 shadow-2xl">
        <div class="flex items-center gap-3">
            <a href="{{ route('countries.index') }}"
               class="text-purple-300 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 18l-6-6 6-6"/>
                </svg>
            </a>
            <div class="flex-1">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">{{ $country->emoji ?: '🌍' }}</span>
                    <h1 class="text-xl font-bold text-white">{{ $country->name }}</h1>
                </div>
                <p class="text-purple-200 text-xs mt-1">
                    {{ $country->code_iso }} · {{ $country->role_label }} ·
                    {{ $activeBanks->count() }} bancos · {{ $activeAccounts->count() }} cuentas activas
                </p>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="flex gap-1 mt-4">
            <button @click="tab='bancos'"
                :class="tab==='bancos' ? 'bg-white/20 text-white' : 'text-purple-300 hover:text-white'"
                class="px-4 py-2 rounded-lg text-sm font-bold transition-all">
                🏦 Bancos
            </button>
            <button @click="tab='cuentas'"
                :class="tab==='cuentas' ? 'bg-white/20 text-white' : 'text-purple-300 hover:text-white'"
                class="px-4 py-2 rounded-lg text-sm font-bold transition-all">
                💳 Cuentas del negocio
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 rounded-xl px-4 py-3 mb-4 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    {{-- ==================== TAB BANCOS ==================== --}}
    <div x-show="tab==='bancos'" x-cloak>

        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-bold uppercase tracking-widest text-gray-500">
                {{ $activeBanks->count() }} bancos activos
            </p>
            <button @click="formBancoOpen = !formBancoOpen"
                class="flex items-center gap-1.5 border-2 border-dashed border-gray-300 rounded-xl px-3 py-1.5 text-xs font-bold text-gray-500 hover:border-cj-turquesa hover:text-cj-turquesa transition-all">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v14M5 12h14"/>
                </svg>
                Agregar banco
            </button>
        </div>

        {{-- Formulario inline agregar banco --}}
        <div x-show="formBancoOpen" x-cloak
             class="bg-white/90 backdrop-blur-lg rounded-2xl border-2 border-dashed border-cj-turquesa p-5 mb-4 shadow-lg">
            <p class="text-sm font-bold text-gray-900 mb-4">Nuevo banco en {{ $country->name }}</p>
            <form action="{{ route('banks.store', $country) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Nombre del banco *</label>
                    <input type="text" name="name" required placeholder="Ej. Banco de Venezuela"
                        class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all text-sm font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Código SWIFT (opcional)</label>
                    <input type="text" name="swift_code" placeholder="Ej. BVNAVE2B"
                        class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all text-sm font-mono uppercase">
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white font-bold py-2.5 rounded-xl text-sm shadow hover:shadow-lg transition-all">
                        Guardar banco
                    </button>
                    <button type="button" @click="formBancoOpen = false"
                        class="px-4 py-2.5 border-2 border-gray-200 rounded-xl text-gray-500 font-bold text-sm hover:bg-gray-50 transition-all">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>

        {{-- Lista de bancos activos --}}
        <div class="space-y-3">
            @forelse($activeBanks as $bank)
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-lg border border-white/50 overflow-hidden"
                     x-data="{ editando: false }">
                    <div class="flex items-center gap-3 p-4">
                        <div class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-4.5 h-4.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <rect x="3" y="9" width="18" height="13" rx="2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                <path d="M3 9l9-6 9 6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-900 text-sm">{{ $bank->name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                @if($bank->swift_code)
                                    <span class="font-mono bg-gray-100 px-1.5 py-0.5 rounded">{{ $bank->swift_code }}</span>
                                @else
                                    Sin SWIFT
                                @endif
                            </p>
                        </div>
                        <form action="{{ route('banks.toggleActive', [$country, $bank]) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="relative w-11 h-6 rounded-full transition-colors bg-cj-morado-profundo focus:outline-none">
                                <span class="absolute top-0.5 right-0.5 h-5 w-5 rounded-full bg-white shadow"></span>
                            </button>
                        </form>
                    </div>
                    <div class="border-t border-gray-100">
                        <button @click="editando = !editando"
                            class="w-full flex items-center justify-center gap-1.5 py-2.5 text-xs font-bold text-gray-500 hover:bg-gray-50 transition-all">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                            Editar
                        </button>
                    </div>
                    {{-- Formulario de edición inline --}}
                    <div x-show="editando" x-cloak class="border-t border-gray-100 p-4 bg-gray-50/50">
                        <form action="{{ route('banks.update', [$country, $bank]) }}" method="POST" class="space-y-3">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $bank->name }}" required
                                class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa text-sm font-semibold transition-all">
                            <input type="text" name="swift_code" value="{{ $bank->swift_code }}" placeholder="SWIFT (opcional)"
                                class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa text-sm font-mono uppercase transition-all">
                            <div class="flex gap-2">
                                <button type="submit"
                                    class="flex-1 bg-cj-morado-profundo text-white font-bold py-2 rounded-xl text-xs shadow transition-all">
                                    Guardar cambios
                                </button>
                                <button type="button" @click="editando = false"
                                    class="px-4 py-2 border-2 border-gray-200 rounded-xl text-gray-500 font-bold text-xs hover:bg-white transition-all">
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl p-8 text-center shadow-lg border border-white/50">
                    <p class="text-gray-400 text-sm">No hay bancos activos en {{ $country->name }}.</p>
                    <p class="text-gray-300 text-xs mt-1">Usa el botón "Agregar banco" para registrar el primero.</p>
                </div>
            @endforelse
        </div>

        {{-- Bancos inactivos --}}
        @if($inactiveBanks->count())
            <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mt-6 mb-3">Inactivos</p>
            <div class="space-y-2 opacity-50">
                @foreach($inactiveBanks as $bank)
                    <div class="bg-white/70 rounded-xl px-4 py-3 flex items-center justify-between shadow border border-white/30">
                        <span class="text-sm font-semibold text-gray-600">{{ $bank->name }}</span>
                        <form action="{{ route('banks.toggleActive', [$country, $bank]) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="relative w-11 h-6 rounded-full bg-gray-200 focus:outline-none">
                                <span class="absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white shadow"></span>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ==================== TAB CUENTAS ==================== --}}
    <div x-show="tab==='cuentas'" x-cloak>

        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-bold uppercase tracking-widest text-gray-500">
                {{ $activeAccounts->count() }} cuentas activas
            </p>
            <button @click="formCuentaOpen = !formCuentaOpen"
                class="flex items-center gap-1.5 border-2 border-dashed border-gray-300 rounded-xl px-3 py-1.5 text-xs font-bold text-gray-500 hover:border-cj-turquesa hover:text-cj-turquesa transition-all">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v14M5 12h14"/>
                </svg>
                Agregar cuenta
            </button>
        </div>

        {{-- Formulario inline agregar cuenta --}}
        <div x-show="formCuentaOpen" x-cloak
             class="bg-white/90 backdrop-blur-lg rounded-2xl border-2 border-dashed border-cj-turquesa p-5 mb-4 shadow-lg">
            <p class="text-sm font-bold text-gray-900 mb-4">Nueva cuenta en {{ $country->name }}</p>
            <form action="{{ route('business-accounts.store', $country) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Banco *</label>
                    <select name="bank_id" required
                        class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa text-sm font-semibold transition-all">
                        <option value="">Seleccionar banco</option>
                        @foreach($activeBanks as $bank)
                            <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Número de cuenta *</label>
                    <input type="text" name="account_number" required placeholder="Ej. 01020000000000000000"
                        class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa font-mono text-sm transition-all">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Titular *</label>
                        <input type="text" name="account_holder" required placeholder="Nombre completo"
                            class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa text-sm font-semibold transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Tipo de cuenta *</label>
                        <select name="account_type" required
                            class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa text-sm font-semibold transition-all">
                            <option value="ahorro">Ahorros</option>
                            <option value="corriente">Corriente</option>
                            <option value="movil">Pago móvil</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">DNI / RUC titular</label>
                        <input type="text" name="dni_ruc" placeholder="Ej. V-12345678"
                            class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa text-sm transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Alias interno</label>
                        <input type="text" name="alias" placeholder="Ej. Principal VES"
                            class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa text-sm transition-all">
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white font-bold py-2.5 rounded-xl text-sm shadow hover:shadow-lg transition-all">
                        Guardar cuenta
                    </button>
                    <button type="button" @click="formCuentaOpen = false"
                        class="px-4 py-2.5 border-2 border-gray-200 rounded-xl text-gray-500 font-bold text-sm hover:bg-gray-50 transition-all">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>

        {{-- Lista de cuentas activas --}}
        <div class="space-y-3">
            @forelse($activeAccounts as $account)
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-lg border border-white/50 overflow-hidden"
                     x-data="{ editando: false, asignando: false }">
                    <div class="flex items-start gap-3 p-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="font-bold text-gray-900 text-sm">
                                    {{ $account->alias ?: $account->bank->name }}
                                </p>
                                <span class="text-xs bg-purple-100 text-cj-morado-profundo font-bold rounded-full px-2 py-0.5">
                                    {{ $account->account_type_label }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">{{ $account->bank->name }}</p>
                            <p class="font-mono font-bold text-sm text-cj-morado-profundo mt-1 bg-gray-100 inline-block rounded px-2 py-0.5">
                                {{ $account->account_number }}
                            </p>
                            <div class="flex items-center gap-1 mt-2 text-xs text-gray-400">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 7a4 4 0 100 8 4 4 0 000-8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                                </svg>
                                Asignada a {{ $account->sellers->count() }} vendedor(es)
                            </div>
                        </div>
                        <form action="{{ route('business-accounts.toggleActive', [$country, $account]) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="relative w-11 h-6 rounded-full bg-cj-morado-profundo focus:outline-none mt-1">
                                <span class="absolute top-0.5 right-0.5 h-5 w-5 rounded-full bg-white shadow"></span>
                            </button>
                        </form>
                    </div>

                    {{-- Acciones --}}
                    <div class="border-t border-gray-100 flex">
                        <button @click="editando = !editando; asignando = false"
                            class="flex-1 flex items-center justify-center gap-1.5 py-2.5 text-xs font-bold text-gray-500 hover:bg-gray-50 transition-all border-r border-gray-100">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                            Editar
                        </button>
                        <button @click="asignando = !asignando; editando = false"
                            class="flex-1 flex items-center justify-center gap-1.5 py-2.5 text-xs font-bold text-cj-turquesa hover:bg-teal-50 transition-all">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 7a4 4 0 100 8 4 4 0 000-8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                            </svg>
                            Asignar vendedores
                        </button>
                    </div>

                    {{-- Formulario edición --}}
                    <div x-show="editando" x-cloak class="border-t border-gray-100 p-4 bg-gray-50/50">
                        <form action="{{ route('business-accounts.update', [$country, $account]) }}" method="POST" class="space-y-3">
                            @csrf @method('PUT')
                            <select name="bank_id"
                                class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa text-sm font-semibold transition-all">
                                @foreach($activeBanks as $bank)
                                    <option value="{{ $bank->id }}" {{ $account->bank_id == $bank->id ? 'selected' : '' }}>
                                        {{ $bank->name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="text" name="account_number" value="{{ $account->account_number }}"
                                class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa font-mono text-sm transition-all">
                            <input type="text" name="account_holder" value="{{ $account->account_holder }}"
                                class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa text-sm transition-all">
                            <div class="grid grid-cols-2 gap-2">
                                <select name="account_type"
                                    class="px-3 py-2 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa text-sm transition-all">
                                    <option value="ahorro" {{ $account->account_type === 'ahorro' ? 'selected' : '' }}>Ahorros</option>
                                    <option value="corriente" {{ $account->account_type === 'corriente' ? 'selected' : '' }}>Corriente</option>
                                    <option value="movil" {{ $account->account_type === 'movil' ? 'selected' : '' }}>Pago móvil</option>
                                </select>
                                <input type="text" name="dni_ruc" value="{{ $account->dni_ruc }}" placeholder="DNI / RUC"
                                    class="px-3 py-2 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa text-sm transition-all">
                            </div>
                            <input type="text" name="alias" value="{{ $account->alias }}" placeholder="Alias interno"
                                class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa text-sm transition-all">
                            <div class="flex gap-2">
                                <button type="submit"
                                    class="flex-1 bg-cj-morado-profundo text-white font-bold py-2 rounded-xl text-xs shadow transition-all">
                                    Guardar cambios
                                </button>
                                <button type="button" @click="editando = false"
                                    class="px-4 py-2 border-2 border-gray-200 rounded-xl text-gray-500 font-bold text-xs transition-all">
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Panel de asignación de vendedores --}}
                    <div x-show="asignando" x-cloak class="border-t border-gray-100 p-4 bg-teal-50/50"
                         x-data="asignacionVendedor({{ $account->id }}, {{ json_encode($account->sellers->pluck('id')->toArray()) }})">
                        <p class="text-xs font-bold text-teal-700 mb-3">Vendedores asignados a esta cuenta</p>

                        {{-- Vendedores ya asignados --}}
                        @foreach($account->sellers as $sellerAsignado)
                            <div class="flex items-center justify-between py-2 border-b border-teal-100">
                                <div>
                                    <p class="text-sm font-bold text-gray-800">{{ $sellerAsignado->name }}</p>
                                    <p class="text-xs font-mono text-gray-400">{{ $sellerAsignado->code }}</p>
                                </div>
                                <button @click="desasignar({{ $account->id }}, {{ $sellerAsignado->id }})"
                                    class="text-xs text-red-500 font-bold hover:text-red-700 transition-colors">
                                    Quitar
                                </button>
                            </div>
                        @endforeach

                        {{-- Asignar nuevo vendedor --}}
                        <div class="mt-3 flex gap-2">
                            <select x-model="sellerIdSeleccionado"
                                class="flex-1 px-3 py-2 border-2 border-teal-200 rounded-xl text-sm font-semibold focus:border-cj-turquesa transition-all">
                                <option value="">Agregar vendedor...</option>
                                @foreach(\App\Models\Seller::all() as $vendedor)
                                    <option value="{{ $vendedor->id }}">{{ $vendedor->name }} ({{ $vendedor->code }})</option>
                                @endforeach
                            </select>
                            <button @click="asignar({{ $account->id }})"
                                :disabled="!sellerIdSeleccionado"
                                class="px-4 py-2 bg-cj-turquesa text-white font-bold rounded-xl text-xs disabled:opacity-40 transition-all">
                                Asignar
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl p-8 text-center shadow-lg border border-white/50">
                    <p class="text-gray-400 text-sm">No hay cuentas activas en {{ $country->name }}.</p>
                    <p class="text-gray-300 text-xs mt-1">Agrega bancos primero, luego crea cuentas asociadas.</p>
                </div>
            @endforelse
        </div>

        {{-- Cuentas inactivas --}}
        @if($inactiveAccounts->count())
            <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mt-6 mb-3">Inactivas</p>
            <div class="space-y-2 opacity-50">
                @foreach($inactiveAccounts as $account)
                    <div class="bg-white/70 rounded-xl px-4 py-3 flex items-center justify-between shadow border border-white/30">
                        <div>
                            <p class="text-sm font-semibold text-gray-600">{{ $account->alias ?: $account->bank->name }}</p>
                            <p class="font-mono text-xs text-gray-400">{{ $account->account_number }}</p>
                        </div>
                        <form action="{{ route('business-accounts.toggleActive', [$country, $account]) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="relative w-11 h-6 rounded-full bg-gray-200 focus:outline-none">
                                <span class="absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white shadow"></span>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

<script>
function paisDetalle() {
    return {
        tab: 'bancos',
        formBancoOpen: false,
        formCuentaOpen: false,
    }
}

function asignacionVendedor(accountId, asignadosIds) {
    return {
        sellerIdSeleccionado: '',
        asignadosIds: asignadosIds,

        asignar(accountId) {
            if (!this.sellerIdSeleccionado) return;
            fetch(`/business-accounts/${accountId}/assign`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ seller_id: this.sellerIdSeleccionado }),
            }).then(() => window.location.reload());
        },

        desasignar(accountId, sellerId) {
            fetch(`/business-accounts/${accountId}/unassign/${sellerId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            }).then(() => window.location.reload());
        },
    }
}
</script>
</x-app-layout>
