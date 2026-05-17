<div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-lg border border-white/50 overflow-hidden">
    <div class="flex items-center gap-3 p-4 cursor-pointer hover:bg-gray-50/50 transition-all"
         onclick="window.location.href='{{ route('countries.show', $country) }}'">
        <span class="text-3xl">{{ $country->emoji ?: '🌍' }}</span>
        <div class="flex-1 min-w-0">
            <p class="font-bold text-gray-900">{{ $country->name }}</p>
            <div class="flex items-center gap-2 mt-0.5">
                <span class="font-mono text-xs font-bold bg-gray-100 text-gray-600 rounded px-1.5 py-0.5">
                    {{ $country->code_iso }}
                </span>
                @php
                    $roleClasses = match($country->role) {
                        'both'        => 'bg-green-100 text-green-700',
                        'destination' => 'bg-blue-100 text-blue-700',
                        default       => 'bg-purple-100 text-purple-700',
                    };
                @endphp
                <span class="text-xs font-bold {{ $roleClasses }} rounded-full px-2 py-0.5">
                    {{ $country->role_label }}
                </span>
            </div>
        </div>
        <form action="{{ route('countries.toggleActive', $country) }}" method="POST" @click.stop>
            @csrf @method('PATCH')
            <button type="submit"
                class="relative w-11 h-6 rounded-full transition-colors focus:outline-none
                       {{ $country->active ? 'bg-cj-morado-profundo' : 'bg-gray-200' }}">
                <span class="absolute top-0.5 h-5 w-5 rounded-full bg-white shadow transition-transform
                             {{ $country->active ? 'translate-x-5' : 'translate-x-0.5' }}"></span>
            </button>
        </form>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 border-t border-gray-100">
        <div class="py-3 text-center border-r border-gray-100">
            <p class="text-xl font-black text-gray-900">{{ $country->activeBanks->count() }}</p>
            <p class="text-xs text-gray-400 font-semibold mt-0.5">Bancos</p>
        </div>
        <div class="py-3 text-center border-r border-gray-100">
            <p class="text-xl font-black text-gray-900">{{ $country->activeBusinessAccounts->count() }}</p>
            <p class="text-xs text-gray-400 font-semibold mt-0.5">Cuentas</p>
        </div>
        <div class="py-3 text-center">
            <p class="text-xl font-black text-gray-900">—</p>
            <p class="text-xs text-gray-400 font-semibold mt-0.5">Operaciones</p>
        </div>
    </div>

    {{-- Action --}}
    <div class="border-t border-gray-100">
        <a href="{{ route('countries.show', $country) }}"
           class="flex items-center justify-center gap-2 py-3 text-xs font-bold text-gray-500 hover:bg-gray-50 transition-all">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
            </svg>
            Ver bancos y cuentas
        </a>
    </div>
</div>
