<x-app-layout>
    <!-- Fondo gradiente animado -->
    <div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
    <div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>
    <div class="fixed top-20 left-10 w-72 h-72 bg-purple-400/30 rounded-full blur-3xl animate-float"></div>
    <div class="fixed bottom-20 right-10 w-96 h-96 bg-teal-400/30 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🏅 Mi Dashboard
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/50 p-8">
                <h3 class="text-2xl font-bold text-cj-morado-profundo mb-4">Dashboard del Vendedor</h3>
                <p class="text-gray-600">Vista en construcción... Próximamente métricas y accesos rápidos.</p>
            </div>
        </div>
    </div>
</x-app-layout>
