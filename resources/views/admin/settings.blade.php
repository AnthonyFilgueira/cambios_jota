<x-app-layout>
    <div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
    <div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-cj-texto leading-tight">Configuración del Sistema</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 space-y-6">

            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-800 rounded-xl px-4 py-3 text-sm font-medium">
                {{ session('success') }}
            </div>
            @endif

            <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                @csrf

                @php
                $groupIcons = [
                    'negocio'       => ['icon' => '🏢', 'color' => 'from-cj-morado-profundo to-cj-morado-medio'],
                    'transacciones' => ['icon' => '💸', 'color' => 'from-cj-turquesa to-teal-500'],
                    'comisiones'    => ['icon' => '📊', 'color' => 'from-cj-rosa to-pink-600'],
                    'sistema'       => ['icon' => '⚙️', 'color' => 'from-gray-600 to-gray-700'],
                ];
                $definitions = \App\Models\Setting::DEFINITIONS;
                $grouped = collect($definitions)->groupBy(fn ($d) => $d['group']);
                @endphp

                @foreach($grouped as $group => $defs)
                @php $gi = $groupIcons[$group] ?? ['icon' => '📋', 'color' => 'from-gray-500 to-gray-600']; @endphp

                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow border border-white/50 overflow-hidden">
                    <!-- Group header -->
                    <div class="bg-gradient-to-r {{ $gi['color'] }} px-6 py-4 flex items-center gap-3">
                        <span class="text-2xl">{{ $gi['icon'] }}</span>
                        <h3 class="font-bold text-white capitalize">{{ $group }}</h3>
                    </div>

                    <div class="p-6 space-y-5">
                        @foreach($defs as $key => $def)
                        @php $setting = $settings[$key] ?? null; $currentVal = $setting?->value ?? $def['default']; @endphp

                        <div>
                            <label class="block text-sm font-bold text-cj-texto mb-1">
                                {{ $def['label'] }}
                            </label>
                            @if($def['description'])
                            <p class="text-xs text-cj-texto-claro mb-2">{{ $def['description'] }}</p>
                            @endif

                            @if($def['type'] === 'boolean')
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative">
                                    <input type="hidden" name="{{ $key }}" value="0">
                                    <input type="checkbox" name="{{ $key }}" value="1"
                                           {{ $currentVal ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer-checked:bg-cj-morado-profundo transition-colors peer"></div>
                                    <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                                </div>
                                <span class="text-sm font-semibold text-cj-texto">
                                    {{ $currentVal ? 'Activado' : 'Desactivado' }}
                                </span>
                            </label>

                            @elseif(str_ends_with($key, 'message') || str_ends_with($key, 'note') || str_ends_with($key, 'address'))
                            <textarea name="{{ $key }}" rows="3"
                                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all text-sm resize-none">{{ $currentVal }}</textarea>

                            @else
                            <div class="relative">
                                @if($def['type'] === 'decimal' || $def['type'] === 'integer')
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-cj-texto-claro">
                                    {{ str_contains($key, 'commission') ? '%' : 'S/' }}
                                </span>
                                @endif
                                <input type="{{ in_array($def['type'], ['decimal','integer']) ? 'number' : 'text' }}"
                                       name="{{ $key }}"
                                       value="{{ $currentVal }}"
                                       step="{{ $def['type'] === 'decimal' ? '0.01' : '1' }}"
                                       min="0"
                                       class="w-full {{ in_array($def['type'], ['decimal','integer']) ? 'pl-10' : 'pl-4' }} pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-cj-morado-profundo focus:ring-2 focus:ring-cj-morado-profundo/20 transition-all font-mono">
                            </div>
                            @endif
                        </div>

                        @if(!$loop->last)<div class="border-t border-gray-100"></div>@endif
                        @endforeach
                    </div>
                </div>
                @endforeach

                <div class="sticky bottom-4">
                    <button type="submit"
                            class="w-full py-4 bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white font-bold rounded-2xl shadow-2xl shadow-purple-400/30 hover:opacity-90 transition-all text-base">
                        Guardar configuración
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
