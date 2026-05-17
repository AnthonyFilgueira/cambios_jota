<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="flex justify-center mb-4">
            <div class="w-16 h-16 bg-gradient-to-br from-cj-turquesa to-cj-morado-medio rounded-2xl flex items-center justify-center shadow-lg">
                <span class="text-3xl font-bold text-white">CJ</span>
            </div>
        </div>
        <h2 class="text-2xl font-bold text-cj-texto">Crea tu cuenta</h2>
        <p class="text-sm text-cj-texto-claro mt-1">Rápido, gratis y sin complicaciones</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5"
          x-data="{
              vendorCode: '{{ old('vendor_code') }}',
              vendorSearching: false,
              vendorFound: false,
              vendorError: '',
              vendorName: '',
              password: '',
              passwordStrength: 0,
              passwordLabel: '',
              showPassword: false,
              showConfirm: false,

              async searchVendor() {
                  const code = this.vendorCode.trim().toUpperCase();
                  if (code.length < 4) { this.vendorFound = false; this.vendorError = ''; return; }
                  this.vendorSearching = true;
                  this.vendorFound = false;
                  this.vendorError = '';
                  try {
                      const res = await fetch('/api/sellers/search/' + encodeURIComponent(code));
                      const data = await res.json();
                      if (data.seller) {
                          this.vendorFound = true;
                          this.vendorName = data.seller.name;
                      } else {
                          this.vendorError = 'Código no encontrado. Pídelo a tu vendedor.';
                      }
                  } catch {
                      this.vendorError = 'Error de conexión. Intenta de nuevo.';
                  } finally {
                      this.vendorSearching = false;
                  }
              },

              checkStrength(val) {
                  this.password = val;
                  let score = 0;
                  if (val.length >= 8)  score++;
                  if (/[A-Z]/.test(val)) score++;
                  if (/[0-9]/.test(val)) score++;
                  if (/[^A-Za-z0-9]/.test(val)) score++;
                  this.passwordStrength = score;
                  this.passwordLabel = ['', 'Débil', 'Regular', 'Buena', 'Fuerte'][score] ?? '';
              }
          }">
        @csrf

        <!-- Nombre -->
        <div>
            <x-input-label for="name" value="Nombre completo" class="text-cj-texto font-semibold" />
            <div class="relative mt-1">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-cj-texto-claro">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                </span>
                <x-text-input id="name" class="block w-full pl-11 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all"
                    type="text" name="name" :value="old('name')" placeholder="Juan Pérez" required autofocus autocomplete="name" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Teléfono -->
        <div>
            <x-input-label for="phone" value="Teléfono" class="text-cj-texto font-semibold" />
            <div class="relative mt-1">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-cj-texto-claro">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                </span>
                <x-text-input id="phone" class="block w-full pl-11 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all"
                    type="tel" name="phone" :value="old('phone')" placeholder="+51 999 000 000" required autocomplete="tel" />
            </div>
            <p class="mt-1 text-xs text-cj-texto-claro">Con código de país, ej: +51 para Perú</p>
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" value="Correo electrónico" class="text-cj-texto font-semibold" />
            <div class="relative mt-1">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-cj-texto-claro">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" /></svg>
                </span>
                <x-text-input id="email" class="block w-full pl-11 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all"
                    type="email" name="email" :value="old('email')" placeholder="tu@email.com" required autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Código de Vendedor -->
        <div>
            <x-input-label for="vendor_code" class="text-cj-texto font-semibold">
                Código de vendedor <span class="text-red-500">*</span>
            </x-input-label>
            <p class="text-xs text-cj-texto-claro mb-1">Tu vendedor te compartió este código. Es obligatorio para registrarte.</p>
            <div class="relative mt-1">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-cj-texto-claro font-bold text-sm">#</span>
                <input id="vendor_code"
                    type="text"
                    name="vendor_code"
                    x-model="vendorCode"
                    @input.debounce.500ms="searchVendor()"
                    value="{{ old('vendor_code') }}"
                    placeholder="VEN-XXXXXX"
                    required
                    autocomplete="off"
                    class="block w-full pl-9 pr-10 py-3 border-2 rounded-xl transition-all uppercase tracking-widest font-mono
                           focus:ring-2 focus:ring-cj-turquesa/20
                           focus:border-cj-turquesa border-gray-200"
                    :class="{
                        'border-green-400 bg-green-50': vendorFound,
                        'border-red-400 bg-red-50': vendorError && !vendorSearching
                    }" />
                <!-- Spinner / Check / X -->
                <span class="absolute right-3 top-1/2 -translate-y-1/2">
                    <svg x-show="vendorSearching" class="animate-spin h-5 w-5 text-cj-turquesa" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <svg x-show="vendorFound && !vendorSearching" class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg x-show="vendorError && !vendorSearching && !vendorFound" class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </span>
            </div>
            <!-- Feedback dinámico -->
            <p x-show="vendorFound" class="mt-1 text-sm text-green-600 font-medium flex items-center gap-1">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                Vendedor: <span x-text="vendorName" class="font-bold"></span>
            </p>
            <p x-show="vendorError && !vendorSearching" class="mt-1 text-sm text-red-600" x-text="vendorError"></p>
            <x-input-error :messages="$errors->get('vendor_code')" class="mt-2" />
        </div>

        <!-- Separador -->
        <div class="relative flex items-center">
            <div class="flex-grow border-t border-gray-200"></div>
            <span class="mx-3 text-xs text-cj-texto-claro uppercase tracking-wider">Contraseña</span>
            <div class="flex-grow border-t border-gray-200"></div>
        </div>

        <!-- Contraseña -->
        <div>
            <x-input-label for="password" value="Contraseña" class="text-cj-texto font-semibold" />
            <div class="relative mt-1">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-cj-texto-claro">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                </span>
                <input id="password"
                    :type="showPassword ? 'text' : 'password'"
                    name="password"
                    @input="checkStrength($event.target.value)"
                    placeholder="Mínimo 8 caracteres"
                    required
                    autocomplete="new-password"
                    class="block w-full pl-11 pr-12 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all" />
                <button type="button" @click="showPassword = !showPassword"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-cj-texto-claro hover:text-cj-morado-profundo transition">
                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                </button>
            </div>
            <!-- Barra de fortaleza -->
            <div x-show="password.length > 0" class="mt-2">
                <div class="flex gap-1 h-1.5">
                    <div class="flex-1 rounded-full transition-all" :class="passwordStrength >= 1 ? (passwordStrength <= 1 ? 'bg-red-400' : passwordStrength <= 2 ? 'bg-yellow-400' : 'bg-green-400') : 'bg-gray-200'"></div>
                    <div class="flex-1 rounded-full transition-all" :class="passwordStrength >= 2 ? (passwordStrength <= 2 ? 'bg-yellow-400' : 'bg-green-400') : 'bg-gray-200'"></div>
                    <div class="flex-1 rounded-full transition-all" :class="passwordStrength >= 3 ? 'bg-green-400' : 'bg-gray-200'"></div>
                    <div class="flex-1 rounded-full transition-all" :class="passwordStrength >= 4 ? 'bg-green-500' : 'bg-gray-200'"></div>
                </div>
                <p class="text-xs mt-1" :class="{
                    'text-red-500': passwordStrength <= 1,
                    'text-yellow-600': passwordStrength === 2,
                    'text-green-600': passwordStrength >= 3
                }" x-text="passwordLabel"></p>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirmar Contraseña -->
        <div>
            <x-input-label for="password_confirmation" value="Confirmar contraseña" class="text-cj-texto font-semibold" />
            <div class="relative mt-1">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-cj-texto-claro">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </span>
                <input id="password_confirmation"
                    :type="showConfirm ? 'text' : 'password'"
                    name="password_confirmation"
                    placeholder="Repite tu contraseña"
                    required
                    autocomplete="new-password"
                    class="block w-full pl-11 pr-12 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all" />
                <button type="button" @click="showConfirm = !showConfirm"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-cj-texto-claro hover:text-cj-morado-profundo transition">
                    <svg x-show="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg x-show="showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- T&C -->
        <div class="flex items-start gap-3 bg-purple-50 border border-cj-morado-claro rounded-xl p-4">
            <input id="terms" name="terms" type="checkbox" required
                   class="mt-0.5 h-4 w-4 rounded border-gray-300 text-cj-morado-profundo focus:ring-cj-turquesa" />
            <label for="terms" class="text-xs text-cj-texto-claro leading-relaxed cursor-pointer">
                Al registrarme acepto los <a href="#" class="text-cj-morado-profundo font-semibold hover:underline">Términos y Condiciones</a>
                y la <a href="#" class="text-cj-morado-profundo font-semibold hover:underline">Política de Privacidad</a> de Cambio J.
            </label>
        </div>

        <!-- Botón -->
        <button type="submit"
                class="w-full bg-gradient-to-r from-cj-morado-profundo via-cj-rosa to-cj-turquesa text-white font-bold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all animate-gradient-x">
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
