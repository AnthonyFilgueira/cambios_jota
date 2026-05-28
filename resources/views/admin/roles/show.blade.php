<x-app-layout>
<div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
<div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>

<div class="max-w-3xl mx-auto px-4 py-8"
     data-role-name="{{ $role->name }}"
     data-toggle-url="{{ route('admin.roles.togglePermission', $role) }}"
     x-data="gestionPermisos($el)">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio rounded-2xl p-5 mb-6 shadow-2xl">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.roles.index') }}" class="text-purple-300 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 18l-6-6 6-6"/>
                </svg>
            </a>
            <div class="flex-1">
                <h1 class="text-xl font-bold text-white capitalize">Permisos — {{ $role->name }}</h1>
                <p class="text-purple-200 text-xs mt-1">
                    Activa o desactiva permisos con los switches. Los cambios se guardan automáticamente.
                </p>
            </div>
            {{-- Selector de rol rápido --}}
            <div>
                <select onchange="window.location.href='/admin/roles/'+this.value+'/permissions'"
                    class="text-xs font-bold bg-white/20 text-white border border-white/30 rounded-xl px-3 py-2 focus:outline-none">
                    @foreach($allRoles as $r)
                        <option value="{{ $r->id }}" {{ $r->id == $role->id ? 'selected' : '' }} class="text-gray-900">
                            {{ $r->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Toast de feedback --}}
    <div x-show="toast.show" x-cloak x-transition
         :class="toast.ok ? 'bg-green-600' : 'bg-red-600'"
         class="fixed top-6 right-6 z-50 text-white text-sm font-bold px-5 py-3 rounded-2xl shadow-2xl">
        <span x-text="toast.msg"></span>
    </div>

    {{-- Módulos con permisos --}}
    <div class="space-y-4">
        @foreach($modules as $module)
            @if($module->perms->count())
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/50 overflow-hidden">

                    {{-- Header del módulo --}}
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gray-50/70">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">{{ $module->icon }}</span>
                            <div>
                                <p class="font-bold text-gray-900 text-sm">{{ $module->name }}</p>
                                <p class="text-xs text-gray-400">{{ $module->perms->count() }} permisos en este módulo</p>
                            </div>
                        </div>
                        {{-- Toggle todo el módulo --}}
                        <button
                            data-module-slug="{{ $module->slug }}"
                            data-perm-names="{{ implode(',', $module->perms->pluck('name')->toArray()) }}"
                            @click="toggleModule($event.currentTarget.dataset.moduleSlug, $event.currentTarget.dataset.permNames.split(','))"
                            class="text-xs font-bold text-cj-morado-profundo hover:underline">
                            Alternar todos
                        </button>
                    </div>

                    {{-- Lista de permisos --}}
                    <div class="divide-y divide-gray-50">
                        @foreach($module->perms as $perm)
                            <div class="flex items-center justify-between px-5 py-3.5"
                                 data-assigned="{{ $perm->assigned ? '1' : '0' }}"
                                 x-data="{ on: $el.dataset.assigned === '1' }"
                                 id="row-{{ $perm->id }}">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800">{{ $perm->label ?? $perm->name }}</p>
                                    <p class="text-xs text-gray-400 font-mono">{{ $perm->name }}</p>
                                </div>
                                <button
                                    @click="toggle('{{ $perm->name }}')"
                                    :class="on ? 'bg-cj-morado-profundo' : 'bg-gray-200'"
                                    class="relative w-12 h-6 rounded-full transition-colors duration-200 focus:outline-none flex-shrink-0 ml-4">
                                    <span :class="on ? 'translate-x-6' : 'translate-x-0.5'"
                                          class="absolute top-0.5 h-5 w-5 rounded-full bg-white shadow transition-transform duration-200"></span>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>

</div>

<script>
function gestionPermisos(el) {
    const toggleUrl = el.dataset.toggleUrl;
    return {
        toast: { show: false, msg: '', ok: true },

        async toggle(permissionName) {
            try {
                const response = await axios.post(toggleUrl, { permission: permissionName });
                const data = response.data;
                if (data.success) {
                    this.showToast(data.message, true);
                } else {
                    this.showToast('Error al cambiar permiso', false);
                }
            } catch (e) {
                this.showToast('Error de conexión', false);
            }
        },

        async toggleModule(moduleSlug, permNames) {
            for (const name of permNames) {
                await this.toggle(name);
            }
        },

        showToast(msg, ok) {
            this.toast = { show: true, msg, ok };
            setTimeout(() => { this.toast.show = false; }, 2500);
        },
    }
}
</script>
</x-app-layout>
