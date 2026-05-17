<x-app-layout>
<div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
<div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

<div class="max-w-4xl mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio rounded-2xl p-6 mb-6 shadow-2xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">Países operativos</h1>
                <p class="text-purple-200 text-sm mt-1">
                    {{ $active->count() }} activos · {{ $inactive->count() }} inactivos
                </p>
            </div>
            <button @click="$dispatch('open-modal', 'nuevo-pais')"
                class="flex items-center gap-2 bg-white text-cj-morado-profundo font-bold px-4 py-2 rounded-xl shadow hover:shadow-lg transition-all text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v14M5 12h14"/>
                </svg>
                Nuevo país
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 rounded-xl px-4 py-3 mb-4 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    {{-- Activos --}}
    @if($active->count())
        <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">Activos</p>
        <div class="space-y-3 mb-6">
            @foreach($active as $country)
                @include('countries._card', ['country' => $country])
            @endforeach
        </div>
    @endif

    {{-- Inactivos --}}
    @if($inactive->count())
        <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3 mt-6">Inactivos</p>
        <div class="space-y-3 opacity-55">
            @foreach($inactive as $country)
                @include('countries._card', ['country' => $country])
            @endforeach
        </div>
    @endif

    @if($active->isEmpty() && $inactive->isEmpty())
        <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/50 p-12 text-center">
            <div class="text-6xl mb-4">🌍</div>
            <p class="text-gray-500 font-medium">No hay países registrados todavía.</p>
            <p class="text-gray-400 text-sm mt-1">Agrega el primer país con el botón de arriba.</p>
        </div>
    @endif

</div>

{{-- Modal Nuevo País --}}
<div x-data="{ open: false }"
     @open-modal.window="if ($event.detail === 'nuevo-pais') open = true"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 flex items-end justify-center"
     @click.self="open = false">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="open = false"></div>
    <div class="relative bg-white rounded-t-3xl w-full max-w-lg p-6 pb-10 shadow-2xl" @click.stop>
        <div class="w-10 h-1 bg-gray-200 rounded-full mx-auto mb-5"></div>
        <h2 class="text-lg font-bold text-gray-900 mb-5">Registrar nuevo país</h2>

        <form action="{{ route('countries.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nombre del país *</label>
                <input type="text" name="name" required placeholder="Ej. Argentina"
                    class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all text-sm font-semibold">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Código ISO *</label>
                    <input type="text" name="code_iso" required maxlength="2" placeholder="Ej. AR"
                        class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all text-sm font-bold uppercase">
                    <p class="text-xs text-gray-400 mt-1">2 letras ISO 3166-1</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Bandera (emoji)</label>
                    <input type="text" name="emoji" placeholder="🇦🇷"
                        class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all text-sm text-2xl">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Moneda principal</label>
                <input type="text" name="currency_name" placeholder="Ej. ARS — Peso argentino"
                    class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Rol operativo *</label>
                <select name="role" required
                    class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all text-sm font-semibold">
                    <option value="">Seleccionar rol</option>
                    <option value="origin">Origen — de donde envían los clientes</option>
                    <option value="destination">Destino — a donde llega el dinero</option>
                    <option value="both">Origen y destino — ambos</option>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="flex-1 bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white font-bold py-3 rounded-xl shadow hover:shadow-lg transition-all">
                    Registrar país
                </button>
                <button type="button" @click="open = false"
                    class="px-5 py-3 border-2 border-gray-200 rounded-xl text-gray-500 font-bold text-sm hover:bg-gray-50 transition-all">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>
</x-app-layout>
