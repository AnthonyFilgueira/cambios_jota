<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Cambios Jotta') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-cj-fondo via-purple-50 to-teal-50">
            <!-- Botón Volver al Inicio -->
            <div class="w-full sm:max-w-md px-6 mb-4">
                <a href="/" class="inline-flex items-center gap-2 text-sm text-cj-texto-claro hover:text-cj-morado-profundo transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver al inicio
                </a>
            </div>

            <!-- Card Principal -->
            <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-2xl overflow-hidden sm:rounded-2xl border border-gray-100">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <div class="mt-6 text-center text-xs text-cj-texto-claro">
                <p>&copy; {{ date('Y') }} Cambios Jotta. Todos los derechos reservados.</p>
            </div>
        </div>
    </body>
</html>
