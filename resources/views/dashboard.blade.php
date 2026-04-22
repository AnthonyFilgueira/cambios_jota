<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>

    <script>
        // Verificar si hay una transacción pendiente del simulador
        document.addEventListener('DOMContentLoaded', function() {
            const pendingTransaction = sessionStorage.getItem('pendingTransaction');

            if (pendingTransaction) {
                // Redirigir automáticamente a crear transacción
                // El formulario leerá los datos del sessionStorage
                window.location.href = '{{ route('transactions.create') }}';
            }
        });
    </script>
</x-app-layout>
