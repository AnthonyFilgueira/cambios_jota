<x-guest-layout>
    <div class="mb-6 text-center">
        <!-- Logo -->
        <div class="flex justify-center mb-4">
            <div class="w-16 h-16 bg-gradient-to-br from-cj-turquesa to-cj-morado-medio rounded-2xl flex items-center justify-center shadow-lg">
                <span class="text-3xl font-bold text-white">CJ</span>
            </div>
        </div>

        <!-- Título -->
        <h2 class="text-2xl font-bold text-cj-texto">Crea tu cuenta</h2>
        <p class="text-sm text-cj-texto-claro mt-1">Únete a Cambios Jotta y envía dinero fácilmente</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" value="Nombre completo" class="text-cj-texto font-semibold" />
            <div class="relative mt-1">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-cj-texto-claro">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </span>
                <x-text-input
                    id="name"
                    class="block w-full pl-11 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all"
                    type="text"
                    name="name"
                    :value="old('name')"
                    placeholder="Juan Pérez"
                    required
                    autofocus
                    autocomplete="name"
                />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Correo electrónico" class="text-cj-texto font-semibold" />
            <div class="relative mt-1">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-cj-texto-claro">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </span>
                <x-text-input
                    id="email"
                    class="block w-full pl-11 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all"
                    type="email"
                    name="email"
                    :value="old('email')"
                    placeholder="tu@email.com"
                    required
                    autocomplete="username"
                />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Contraseña" class="text-cj-texto font-semibold" />
            <div class="relative mt-1">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-cj-texto-claro">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </span>
                <x-text-input
                    id="password"
                    class="block w-full pl-11 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all"
                    type="password"
                    name="password"
                    placeholder="Mínimo 8 caracteres"
                    required
                    autocomplete="new-password"
                />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" value="Confirmar contraseña" class="text-cj-texto font-semibold" />
            <div class="relative mt-1">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-cj-texto-claro">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <x-text-input
                    id="password_confirmation"
                    class="block w-full pl-11 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all"
                    type="password"
                    name="password_confirmation"
                    placeholder="Repite tu contraseña"
                    required
                    autocomplete="new-password"
                />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Términos (opcional) -->
        <div class="bg-purple-50 border-l-4 border-cj-morado-profundo p-3 rounded">
            <p class="text-xs text-cj-texto-claro">
                Al registrarte, aceptas nuestros Términos de Servicio y Política de Privacidad.
            </p>
        </div>

        <!-- Botón de Registro -->
        <button type="submit" class="w-full bg-gradient-to-r from-cj-turquesa to-cj-morado-medio text-white font-bold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
            Crear cuenta →
        </button>

        <!-- Link a Login -->
        <div class="text-center pt-4 border-t border-gray-200">
            <p class="text-sm text-cj-texto-claro">
                ¿Ya tienes una cuenta?
                <a href="{{ route('login') }}" class="font-semibold text-cj-morado-profundo hover:text-cj-turquesa transition">
                    Inicia sesión
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
