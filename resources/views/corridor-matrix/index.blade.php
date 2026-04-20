<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🎯 Matriz de Corredores
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('currencies.index') }}"
                   class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                    Divisas
                </a>
                <a href="{{ route('currency-pairs.index') }}"
                   class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                    Pares
                </a>
                <a href="{{ route('corridors.index') }}"
                   class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                    Corredores
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6" x-data="matrixManager()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Alert de éxito/error -->
            <div x-show="alert.show"
                 x-transition
                 :class="alert.type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'"
                 class="mb-4 border px-4 py-3 rounded relative">
                <span x-text="alert.message"></span>
                <button @click="alert.show = false" class="absolute top-0 right-0 px-4 py-3">
                    <span class="text-2xl">&times;</span>
                </button>
            </div>

            <!-- Instrucciones -->
            <div class="mb-4 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded">
                <p class="text-sm">
                    <strong>Instrucciones:</strong> Haz click en los checkboxes para habilitar/deshabilitar corredores para cada par de divisas. Los cambios se guardan automáticamente.
                </p>
            </div>

            <!-- Matriz -->
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white">
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase sticky left-0 bg-cj-morado-profundo z-10">
                                Par de Divisas
                            </th>
                            @foreach($corridors as $corridor)
                                <th class="px-4 py-3 text-center text-xs font-medium uppercase whitespace-nowrap">
                                    {{ $corridor->name }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($pairs as $pair)
                            <tr class="hover:bg-gray-50">
                                <!-- Par de divisas (sticky) -->
                                <td class="px-4 py-3 font-semibold text-sm sticky left-0 bg-white z-10 border-r border-gray-200">
                                    <div class="flex items-center gap-2">
                                        <span>{{ $pair->full_display }}</span>
                                    </div>
                                </td>

                                <!-- Checkboxes de corredores -->
                                @foreach($corridors as $corridor)
                                    @php
                                        $isAssigned = isset($assignments[$pair->id]) && in_array($corridor->id, $assignments[$pair->id]);
                                    @endphp
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex justify-center">
                                            <input
                                                type="checkbox"
                                                :disabled="loading[{{ $pair->id }}_{{ $corridor->id }}]"
                                                {{ $isAssigned ? 'checked' : '' }}
                                                @change="toggleAssignment({{ $pair->id }}, {{ $corridor->id }}, $event.target)"
                                                class="w-5 h-5 rounded border-gray-300 text-cj-morado-profundo shadow-sm focus:border-cj-morado-medio focus:ring focus:ring-cj-morado-claro cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                                            >
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $corridors->count() + 1 }}" class="px-6 py-8 text-center text-gray-500">
                                    No hay pares de divisas activos.
                                    <a href="{{ route('currency-pairs.create') }}" class="text-cj-morado-medio hover:underline">
                                        Crear el primer par
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($pairs->count() > 0 && $corridors->count() > 0)
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600">
                            Matriz de <strong>{{ $pairs->count() }}</strong> pares × <strong>{{ $corridors->count() }}</strong> corredores
                        </p>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <script>
        function matrixManager() {
            return {
                loading: {},
                alert: {
                    show: false,
                    type: 'success',
                    message: ''
                },

                showAlert(type, message) {
                    this.alert = {
                        show: true,
                        type: type,
                        message: message
                    };

                    // Auto-ocultar después de 3 segundos
                    setTimeout(() => {
                        this.alert.show = false;
                    }, 3000);
                },

                async toggleAssignment(pairId, corridorId, checkbox) {
                    const key = `${pairId}_${corridorId}`;
                    this.loading[key] = true;

                    try {
                        const response = await fetch('{{ route("corridor-matrix.toggle") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                pair_id: pairId,
                                corridor_id: corridorId
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Actualizar checkbox según el estado devuelto
                            checkbox.checked = data.enabled;
                            this.showAlert('success', data.message);
                        } else {
                            // Revertir checkbox si falla
                            checkbox.checked = !checkbox.checked;
                            this.showAlert('error', 'Error al actualizar');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        // Revertir checkbox si falla
                        checkbox.checked = !checkbox.checked;
                        this.showAlert('error', 'Error de conexión');
                    } finally {
                        this.loading[key] = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>
