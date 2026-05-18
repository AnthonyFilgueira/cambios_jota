<x-app-layout>
<div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
<div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

<div class="max-w-4xl mx-auto px-4 py-8">

    <div class="bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio rounded-2xl p-6 mb-6 shadow-2xl">
        <h1 class="text-2xl font-bold text-white">⚙️ Gestión de Roles y Permisos</h1>
        <p class="text-purple-200 text-sm mt-1">Administra qué puede hacer cada rol en el sistema</p>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
        @foreach($roles as $role)
            @php
                $config = match($role->name) {
                    'super-admin' => ['icon' => '👑', 'color' => 'from-yellow-500 to-orange-500', 'badge' => 'bg-yellow-100 text-yellow-800'],
                    'admin'       => ['icon' => '🏢', 'color' => 'from-cj-morado-profundo to-cj-morado-medio', 'badge' => 'bg-purple-100 text-purple-800'],
                    'contador'    => ['icon' => '📊', 'color' => 'from-blue-600 to-blue-400', 'badge' => 'bg-blue-100 text-blue-800'],
                    'vendedor'    => ['icon' => '🤝', 'color' => 'from-teal-600 to-cj-turquesa', 'badge' => 'bg-teal-100 text-teal-800'],
                    'cliente'     => ['icon' => '👤', 'color' => 'from-gray-600 to-gray-400', 'badge' => 'bg-gray-100 text-gray-700'],
                    default       => ['icon' => '🔧', 'color' => 'from-gray-500 to-gray-400', 'badge' => 'bg-gray-100 text-gray-700'],
                };
            @endphp
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/50 overflow-hidden">
                <div class="bg-gradient-to-r {{ $config['color'] }} p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-3xl">{{ $config['icon'] }}</span>
                            <div>
                                <h2 class="text-lg font-bold text-white capitalize">{{ $role->name }}</h2>
                                <span class="text-xs text-white/70">{{ $role->permissions_count }} permisos asignados</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <a href="{{ route('admin.roles.show', $role) }}"
                       class="block w-full text-center bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white font-bold py-3 rounded-xl shadow hover:shadow-lg transition-all text-sm">
                        Gestionar permisos →
                    </a>
                </div>
            </div>
        @endforeach
    </div>

</div>
</x-app-layout>
