<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('client.name') }}</title>
        <link rel="icon" href="{{ asset(config('client.favicon', 'favicon.ico')) }}" type="image/x-icon">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased relative overflow-x-hidden">
        <!-- Fondo principal con gradiente animado -->
        <div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>

        <!-- Capa de overlay semitransparente -->
        <div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

        <!-- Círculos decorativos flotantes -->
        <div class="fixed top-20 left-10 w-72 h-72 bg-purple-400/30 rounded-full blur-3xl animate-float"></div>
        <div class="fixed bottom-20 right-10 w-96 h-96 bg-teal-400/30 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <!-- Botón Volver al Inicio -->
            <div class="w-full sm:max-w-md px-6 mb-4">
                <a href="/" class="inline-flex items-center gap-2 text-sm text-cj-texto-claro hover:text-cj-morado-profundo transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver al inicio
                </a>
            </div>

            <!-- Card Principal con efecto glassmorphism -->
            <div class="w-full sm:max-w-md px-6 py-8 bg-white/90 backdrop-blur-lg shadow-2xl overflow-hidden sm:rounded-2xl border-2 border-white/50 hover:shadow-purple-500/20 hover:shadow-3xl transition-all duration-300">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <div class="mt-6 text-center text-xs text-cj-texto-claro">
                <p>&copy; {{ date('Y') }} Cambios Jotta. Todos los derechos reservados.</p>
            </div>
        </div>
    </body>
</html>
