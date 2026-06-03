@php
    $user = Auth::user();
    $isAdmin    = $user && ($user->hasRole('super-admin') || $user->hasRole('admin') || $user->hasRole('contador'));
    $isSeller   = $user && $user->hasRole('vendedor');
    $isClient   = $user && $user->hasRole('cliente');
@endphp

<nav x-data="{ open: false }" class="bg-white/80 backdrop-blur-xl shadow-lg sticky top-0 z-50 border-b border-white/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            {{-- Logo --}}
            <div class="shrink-0 flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    @if(config('client.logo'))
                        <img src="{{ asset(config('client.logo')) }}" alt="{{ config('client.name') }}" class="h-9 w-auto hover:scale-110 transition-transform">
                    @else
                        <div class="w-9 h-9 bg-gradient-to-br from-cj-morado-profundo to-cj-turquesa rounded-xl flex items-center justify-center shadow-md hover:scale-110 transition-transform">
                            <span class="text-sm font-black text-white">{{ strtoupper(substr(config('client.name'), 0, 2)) }}</span>
                        </div>
                    @endif
                    <div class="hidden sm:block">
                        <p class="text-base font-bold text-cj-morado-profundo leading-none">{{ config('client.name') }}</p>
                        <p class="text-xs text-gray-400">
                            @if($isAdmin) Administración @elseif($isSeller) Panel Vendedor @else Mi cuenta @endif
                        </p>
                    </div>
                </a>
            </div>

            {{-- ==================== MENÚ ADMIN ==================== --}}
            @if($isAdmin)
            <div class="hidden lg:flex items-center gap-1">

                {{-- Operaciones --}}
                <x-nav-link :href="route('owner.dashboard')" :active="request()->routeIs('owner.dashboard')">
                    🏠 Dashboard
                </x-nav-link>

                {{-- Ventas dropdown --}}
                <div class="relative" x-data="{ openVentas: false }" @click.outside="openVentas=false">
                    <button @click="openVentas = !openVentas" class="inline-flex items-center gap-1 px-3 py-2 text-sm font-semibold text-gray-600 hover:text-cj-morado-profundo rounded-lg hover:bg-purple-50 transition-all">
                        📋 Ventas
                        <svg class="w-3 h-3 transition-transform" :class="openVentas ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openVentas" x-cloak class="absolute top-full left-0 mt-1 w-52 bg-white rounded-2xl shadow-2xl border border-gray-100 py-2 z-50">
                        <a href="{{ route('sales.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-cj-morado-profundo font-medium">📋 Todas las ventas</a>
                        <a href="{{ route('sales.pending.admin') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-cj-morado-profundo font-medium">⏳ Pendientes de aprobación</a>
                        <a href="{{ route('sales.approved') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-cj-morado-profundo font-medium">✅ Aprobadas</a>
                        <a href="{{ route('sales.observed') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-cj-morado-profundo font-medium">👁️ Observadas</a>
                        <a href="{{ route('sales.create') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-cj-morado-profundo font-medium">➕ Nueva venta</a>
                    </div>
                </div>

                {{-- Transacciones --}}
                <x-nav-link :href="route('transactions.manage')" :active="request()->routeIs('transactions.*')">
                    💸 Transacciones
                </x-nav-link>

                {{-- Gestión dropdown --}}
                <div class="relative" x-data="{ openGestion: false }" @click.outside="openGestion=false">
                    <button @click="openGestion = !openGestion" class="inline-flex items-center gap-1 px-3 py-2 text-sm font-semibold text-gray-600 hover:text-cj-morado-profundo rounded-lg hover:bg-purple-50 transition-all">
                        👥 Gestión
                        <svg class="w-3 h-3 transition-transform" :class="openGestion ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openGestion" x-cloak class="absolute top-full left-0 mt-1 w-52 bg-white rounded-2xl shadow-2xl border border-gray-100 py-2 z-50">
                        <a href="{{ route('sellers.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-cj-morado-profundo font-medium">👥 Vendedores</a>
                        <a href="{{ route('liquidations.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-cj-morado-profundo font-medium">💰 Liquidaciones</a>
                        <a href="{{ route('reports.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-cj-morado-profundo font-medium">📊 Reportes</a>
                        <a href="{{ route('reports.rankings') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-cj-morado-profundo font-medium">🏆 Rankings</a>
                    </div>
                </div>

                {{-- Configuración dropdown --}}
                <div class="relative" x-data="{ openConfig: false }" @click.outside="openConfig=false">
                    <button @click="openConfig = !openConfig" class="inline-flex items-center gap-1 px-3 py-2 text-sm font-semibold text-gray-600 hover:text-cj-morado-profundo rounded-lg hover:bg-purple-50 transition-all">
                        ⚙️ Config
                        <svg class="w-3 h-3 transition-transform" :class="openConfig ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openConfig" x-cloak class="absolute top-full left-0 mt-1 w-56 bg-white rounded-2xl shadow-2xl border border-gray-100 py-2 z-50">
                        <p class="px-4 py-1 text-xs font-bold uppercase tracking-widest text-gray-400">Finanzas</p>
                        <a href="{{ route('exchange_rates.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-cj-morado-profundo font-medium">💱 Tasas de cambio</a>
                        <p class="px-4 py-1 mt-1 text-xs font-bold uppercase tracking-widest text-gray-400">Divisas</p>
                        <a href="{{ route('currencies.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-cj-morado-profundo font-medium">🌐 Divisas</a>
                        <a href="{{ route('currency-pairs.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-cj-morado-profundo font-medium">🔄 Pares de divisas</a>
                        <a href="{{ route('corridors.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-cj-morado-profundo font-medium">🛤️ Corredores</a>
                        <a href="{{ route('corridor-matrix.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-cj-morado-profundo font-medium">🗂️ Matriz de corredores</a>
                        <p class="px-4 py-1 mt-1 text-xs font-bold uppercase tracking-widest text-gray-400">Operativa</p>
                        <a href="{{ route('countries.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-cj-morado-profundo font-medium">🌍 Países y cuentas</a>
                        <a href="{{ route('admin.roles.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-cj-morado-profundo font-medium">🔑 Roles y permisos</a>
                        <a href="{{ route('admin.incentives.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-cj-morado-profundo font-medium">⭐ Incentivos</a>
                        <a href="{{ route('admin.audit-logs') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-cj-morado-profundo font-medium">🔍 Auditoría</a>
                        <a href="{{ route('admin.settings') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-cj-morado-profundo font-medium">⚙️ Configuración</a>
                    </div>
                </div>
            </div>
            @endif

            {{-- ==================== MENÚ VENDEDOR ==================== --}}
            @if($isSeller)
            <div class="hidden sm:flex items-center gap-1">
                <x-nav-link :href="route('seller.dashboard')" :active="request()->routeIs('seller.dashboard')">
                    🏠 Mi Panel
                </x-nav-link>
                <x-nav-link :href="route('seller.bandeja')" :active="request()->routeIs('seller.bandeja')">
                    ⏳ Bandeja
                </x-nav-link>
                <x-nav-link :href="route('wallet.index')" :active="request()->routeIs('wallet.index')">
                    💰 Mi Monedero
                </x-nav-link>
            </div>
            @endif

            {{-- ==================== MENÚ CLIENTE ==================== --}}
            @if($isClient)
            <div class="hidden sm:flex items-center gap-2">
                <x-nav-link :href="route('client.dashboard')" :active="request()->routeIs('client.dashboard')">
                    🏠 Inicio
                </x-nav-link>
                <x-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.index')">
                    📄 Mis Envíos
                </x-nav-link>
                <a href="{{ route('transactions.create') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-cj-rosa to-pink-600 text-white rounded-xl font-bold text-sm shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Iniciar Envío
                </a>
            </div>
            @endif

            {{-- Usuario dropdown (todos los roles) --}}
            <div class="flex items-center gap-3">
                @auth
                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-200 hover:border-cj-morado-profundo/30 hover:bg-purple-50 transition-all">
                            <div class="w-7 h-7 bg-gradient-to-br from-cj-morado-profundo to-cj-turquesa rounded-full flex items-center justify-center">
                                <span class="text-xs font-bold text-white">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                            </div>
                            <span class="hidden sm:block text-sm font-semibold text-gray-700 max-w-24 truncate">{{ Auth::user()->name }}</span>
                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-xs font-bold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                            <span class="inline-block mt-1 text-xs font-bold bg-purple-100 text-cj-morado-profundo rounded-full px-2 py-0.5">
                                {{ Auth::user()->getRoleNames()->first() ?? 'sin rol' }}
                            </span>
                        </div>

                        <x-dropdown-link :href="route('profile.edit')">
                            👤 Mi Perfil
                        </x-dropdown-link>

                        @if($isAdmin)
                            <x-dropdown-link :href="route('owner.dashboard')">🏠 Dashboard Admin</x-dropdown-link>
                            <x-dropdown-link :href="route('admin.roles.index')">🔑 Roles y Permisos</x-dropdown-link>
                            <x-dropdown-link :href="route('countries.index')">🌍 Países y Cuentas</x-dropdown-link>
                        @endif
                        @if($isSeller)
                            <x-dropdown-link :href="route('seller.dashboard')">🤝 Mi Panel</x-dropdown-link>
                            <x-dropdown-link :href="route('wallet.index')">💰 Mi Monedero</x-dropdown-link>
                        @endif

                        <div class="border-t border-gray-100 mt-1 pt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    🚪 Cerrar Sesión
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
                @endauth
            </div>

            {{-- Hamburger --}}
            <div class="flex items-center lg:hidden">
                <button @click="open = ! open" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-all">
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ==================== MENÚ MÓVIL ==================== --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden lg:hidden bg-white/95 backdrop-blur-lg border-t border-gray-100">
        <div class="px-4 py-3 space-y-1">

            @if($isAdmin)
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 px-2 py-1">Operaciones</p>
                <x-responsive-nav-link :href="route('owner.dashboard')">🏠 Dashboard</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('sales.index')">📋 Ventas</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('sales.pending.admin')">⏳ Pendientes aprobación</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('transactions.manage')">💸 Transacciones</x-responsive-nav-link>

                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 px-2 py-1 mt-2">Gestión</p>
                <x-responsive-nav-link :href="route('sellers.index')">👥 Vendedores</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('liquidations.index')">💰 Liquidaciones</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('reports.index')">📊 Reportes</x-responsive-nav-link>

                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 px-2 py-1 mt-2">Configuración</p>
                <x-responsive-nav-link :href="route('exchange_rates.index')">💱 Tasas de cambio</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('currencies.index')">🌐 Divisas</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('currency-pairs.index')">🔄 Pares de divisas</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('corridors.index')">🛤️ Corredores</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('corridor-matrix.index')">🗂️ Matriz corredores</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('countries.index')">🌍 Países y Cuentas</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.roles.index')">🔑 Roles y Permisos</x-responsive-nav-link>
            @endif

            @if($isSeller)
                <x-responsive-nav-link :href="route('seller.dashboard')">🏠 Mi Panel</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('seller.bandeja')">⏳ Bandeja de solicitudes</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('wallet.index')">💰 Mi Monedero</x-responsive-nav-link>
            @endif

            @if($isClient)
                <x-responsive-nav-link :href="route('client.dashboard')">🏠 Inicio</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('transactions.index')">📄 Mis Envíos</x-responsive-nav-link>
                <div class="px-2 py-2">
                    <a href="{{ route('transactions.create') }}"
                       class="block w-full text-center py-3 bg-gradient-to-r from-cj-rosa to-pink-600 text-white rounded-xl font-bold text-sm shadow-md">
                        ➕ Iniciar Envío
                    </a>
                </div>
            @endif
        </div>

        {{-- Usuario móvil --}}
        <div class="border-t border-gray-100 px-4 py-3 space-y-1">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 bg-gradient-to-br from-cj-morado-profundo to-cj-turquesa rounded-full flex items-center justify-center">
                    <span class="text-sm font-bold text-white">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400">{{ Auth::user()->getRoleNames()->first() ?? '' }}</p>
                </div>
            </div>
            <x-responsive-nav-link :href="route('profile.edit')">👤 Mi Perfil</x-responsive-nav-link>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                    🚪 Cerrar Sesión
                </x-responsive-nav-link>
            </form>
        </div>
    </div>
</nav>
