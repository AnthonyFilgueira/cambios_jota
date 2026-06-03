<x-guest-layout>
    <div class="mb-6 text-center">
        <!-- Logo -->
        <div class="flex justify-center mb-4">
            <div class="w-16 h-16 bg-gradient-to-br from-cj-morado-profundo to-cj-morado-medio rounded-2xl flex items-center justify-center shadow-lg">
                <span class="text-3xl font-bold text-white">CJ</span>
            </div>
        </div>

        <!-- Título -->
        <h2 class="text-2xl font-bold text-cj-texto">¡Bienvenido de nuevo!</h2>
        <p class="text-sm text-cj-texto-claro mt-1">Ingresa a tu cuenta de {{ config('client.name') }}</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

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
                    class="block w-full pl-11 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all"
                    type="email"
                    name="email"
                    :value="old('email')"
                    placeholder="tu@email.com"
                    required
                    autofocus
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
                    class="block w-full pl-11 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all"
                    type="password"
                    name="password"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me y Forgot Password -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input
                    id="remember_me"
                    type="checkbox"
                    class="rounded border-gray-300 text-cj-morado-profundo shadow-sm focus:ring-cj-morado-profundo"
                    name="remember"
                >
                <span class="ms-2 text-sm text-cj-texto">Recordarme</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-cj-turquesa hover:text-cj-morado-profundo font-semibold transition" href="{{ route('password.request') }}">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        <!-- Botón de Login -->
        <button type="submit" class="w-full bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white font-bold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
            Ingresar →
        </button>

        <!-- Link a Registro -->
        <div class="text-center pt-4 border-t border-gray-200">
            <p class="text-sm text-cj-texto-claro">
                ¿No tienes una cuenta?
                <a href="{{ route('register') }}" class="font-semibold text-cj-turquesa hover:text-cj-morado-profundo transition">
                    Regístrate aquí
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
