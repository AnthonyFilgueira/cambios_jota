<nav x-data="{ open: false }" class="bg-white/70 backdrop-blur-xl shadow-2xl sticky top-0 z-50 border-b-2 border-white/50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-cj-morado-profundo to-cj-turquesa rounded-lg flex items-center justify-center shadow-lg hover:scale-110 transition-transform duration-300">
                            <span class="text-lg font-bold text-white">CJ</span>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-cj-texto">Cambios Jotta</h1>
                            <p class="text-xs text-cj-texto-claro hidden sm:block">Sistema Admin</p>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-4 sm:ms-8 sm:flex items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        📊 Dashboard
                    </x-nav-link>

                    <x-dropdown align="top" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 text-sm font-semibold text-cj-morado-profundo hover:text-cj-turquesa transition-all duration-300">
                                💼 Ventas
                                <svg class="ms-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('sales.bulk.create')">Registrar Ventas</x-dropdown-link>
                            <x-dropdown-link :href="route('sales.pending.admin')">Pendientes Admin</x-dropdown-link>
                            <x-dropdown-link :href="route('sales.approved')">Aprobadas</x-dropdown-link>
                            <x-dropdown-link :href="route('sales.observed')">Observadas</x-dropdown-link>
                            <x-dropdown-link :href="route('sales.index')">Todas</x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    <x-dropdown align="top" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 text-sm font-semibold text-cj-morado-profundo hover:text-cj-turquesa transition-all duration-300">
                                👥 Vendedores
                                <svg class="ms-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('sellers.index')">Lista Vendedores</x-dropdown-link>
                            <x-dropdown-link :href="route('wallet.index')">Monedero</x-dropdown-link>
                            <x-dropdown-link :href="route('liquidations.index')">Liquidaciones</x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    <x-dropdown align="top" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 text-sm font-semibold text-cj-morado-profundo hover:text-cj-turquesa transition-all duration-300">
                                📈 Reportes
                                <svg class="ms-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('reports.rankings')">🏆 Rankings</x-dropdown-link>
                            <x-dropdown-link :href="route('reports.index')">Reporte Ventas</x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    <x-dropdown align="top" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 text-sm font-semibold text-cj-morado-profundo hover:text-cj-turquesa transition-all duration-300">
                                ⚙️ Config
                                <svg class="ms-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('exchange_rates.index')">Tasas de Cambio</x-dropdown-link>
                            <x-dropdown-link :href="route('currencies.index')">Divisas</x-dropdown-link>
                            <x-dropdown-link :href="route('currency-pairs.index')">Pares</x-dropdown-link>
                            <x-dropdown-link :href="route('corridors.index')">Corredores</x-dropdown-link>
                            <x-dropdown-link :href="route('corridor-matrix.index')">Matriz</x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    <x-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">
                        📱 Mis Transacciones
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-semibold rounded-lg text-white bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio hover:opacity-90 focus:outline-none transition-all duration-300 shadow-md">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('sellers.index')" :active="request()->routeIs('sellers.index')">
                        {{ __('Vendedores') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('sales.bulk.create')" :active="request()->routeIs('sales.bulk.creates')">
                        {{ __('Registro de ventas') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.index')">
                        {{ __('Reporte de ventas') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('exchange_rates.index')" :active="request()->routeIs('exchange_rates.*')">
                        {{ __('Tasas de Cambio') }}
                    </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
