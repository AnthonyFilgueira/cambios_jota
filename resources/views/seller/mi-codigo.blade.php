<x-app-layout>
    <div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
    <div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-cj-texto leading-tight">Mi Código</h2>
            <a href="{{ route('seller.bandeja') }}"
               class="text-sm text-cj-morado-profundo font-semibold hover:underline">
                ← Bandeja
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto px-4 sm:px-6 space-y-5">

            <!-- Card código + QR -->
            <div class="bg-white/90 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/50 overflow-hidden">
                <!-- Header gradiente -->
                <div class="bg-gradient-to-br from-cj-morado-profundo to-cj-morado-medio px-6 pt-8 pb-10 text-white text-center relative">
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute top-4 left-4 w-20 h-20 rounded-full bg-white blur-2xl"></div>
                        <div class="absolute bottom-4 right-4 w-16 h-16 rounded-full bg-white blur-2xl"></div>
                    </div>
                    <p class="text-xs uppercase tracking-widest opacity-70 mb-2">Tu código de vendedor</p>
                    <p class="text-5xl font-black font-mono tracking-widest drop-shadow-lg">{{ $seller->code }}</p>
                    <p class="text-sm opacity-70 mt-2">Comparte este código con tus clientes</p>
                </div>

                <!-- QR Code -->
                <div class="flex flex-col items-center py-6 px-6 border-b border-gray-100">
                    <p class="text-xs font-bold uppercase tracking-widest text-cj-texto-claro mb-4">Escanea para iniciar</p>
                    <div id="qrcode" class="p-3 bg-white rounded-2xl shadow-md border border-gray-100"></div>
                    <p class="text-xs text-cj-texto-claro mt-3 text-center max-w-xs">
                        El QR lleva al simulador público pre-cargado con tu código de vendedor
                    </p>
                </div>

                <!-- Acciones -->
                <div class="p-5 space-y-3">
                    <!-- URL de invitación -->
                    <div x-data="{ copied: false }">
                        <p class="text-xs font-bold uppercase tracking-widest text-cj-texto-claro mb-2">Enlace de invitación</p>
                        <div class="flex gap-2">
                            <input type="text" value="{{ $publicUrl }}" readonly
                                   class="flex-1 text-xs font-mono bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-cj-texto-claro select-all">
                            <button @click="navigator.clipboard.writeText('{{ $publicUrl }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                    class="flex-shrink-0 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all
                                           bg-cj-morado-profundo text-white hover:bg-cj-morado-medio">
                                <span x-show="!copied">Copiar</span>
                                <span x-show="copied">¡Copiado!</span>
                            </button>
                        </div>
                    </div>

                    <!-- Descargar QR -->
                    <button onclick="downloadQR()"
                            class="w-full flex items-center justify-center gap-2 py-3 border-2 border-cj-morado-profundo/20 text-cj-morado-profundo font-semibold rounded-xl hover:bg-cj-morado-claro transition-all text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Descargar QR como imagen
                    </button>
                </div>
            </div>

            <!-- KPIs -->
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-5 text-center">
                    <p class="text-3xl font-bold text-cj-rosa">{{ $stats['pending'] }}</p>
                    <p class="text-xs text-cj-texto-claro mt-1 font-medium uppercase tracking-wider">Pendientes</p>
                </div>
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-5 text-center">
                    <p class="text-3xl font-bold text-cj-morado-profundo">{{ $stats['total_clients'] }}</p>
                    <p class="text-xs text-cj-texto-claro mt-1 font-medium uppercase tracking-wider">Clientes</p>
                </div>
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-5 text-center">
                    <p class="text-2xl font-bold text-cj-turquesa">S/ {{ number_format($stats['mes_volume'], 0) }}</p>
                    <p class="text-xs text-cj-texto-claro mt-1 font-medium uppercase tracking-wider">Volumen este mes</p>
                </div>
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-5 text-center">
                    <p class="text-3xl font-bold text-green-600">{{ $stats['total_completed'] }}</p>
                    <p class="text-xs text-cj-texto-claro mt-1 font-medium uppercase tracking-wider">Completadas</p>
                </div>
            </div>

            <!-- Comisión vigente -->
            @php $rule = $seller->commissionRules->first(); @endphp
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 p-5">
                <p class="text-xs font-bold uppercase tracking-widest text-cj-texto-claro mb-4">Comisión vigente</p>
                @if($rule)
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $rule->commission_type === 'fixed' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                            {{ $rule->typeLabel() }}
                        </span>
                        <p class="text-2xl font-bold text-cj-morado-profundo mt-2">
                            {{ $rule->commission_type === 'fixed' ? 'S/ ' : '' }}{{ $rule->seller_value }}{{ $rule->commission_type === 'percentage' ? '%' : '' }}
                        </p>
                        @if($rule->notes)
                        <p class="text-xs text-cj-texto-claro mt-1 italic">{{ $rule->notes }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-cj-texto-claro">Jefe</p>
                        <p class="text-lg font-bold text-cj-rosa">
                            {{ $rule->boss_value }}{{ $rule->commission_type === 'percentage' ? '%' : '' }}
                        </p>
                    </div>
                </div>
                @else
                <p class="text-sm text-cj-texto-claro">
                    Comisión: <span class="font-bold text-cj-morado-profundo">{{ $seller->seller_commission }}%</span>
                    · Jefe: <span class="font-bold text-cj-rosa">{{ $seller->boss_commission }}%</span>
                </p>
                @endif
            </div>

        </div>
    </div>

    <!-- QR Code Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        const qr = new QRCode(document.getElementById('qrcode'), {
            text: '{{ $publicUrl }}',
            width: 200,
            height: 200,
            colorDark: '#4C1D95',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });

        function downloadQR() {
            const canvas = document.querySelector('#qrcode canvas');
            if (!canvas) return;
            const link = document.createElement('a');
            link.download = 'codigo-{{ $seller->code }}.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        }
    </script>
</x-app-layout>
