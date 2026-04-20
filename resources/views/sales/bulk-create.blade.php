<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 py-6" x-data="bulkSalesForm()">
        <!-- Encabezado con paleta Cambio J -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Registrar Ventas Masivas</h1>
            <p class="text-gray-600 mt-2">Carga múltiples ventas para todos los vendedores</p>
        </div>

        <form @submit.prevent="submit">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-purple-700 to-purple-600 text-white">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Vendedor</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Monto</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <template x-for="(sale, index) in sales" :key="sale.seller_id">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900" x-text="sale.name"></div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" step="0.01" class="w-full border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                                x-model="sale.amount" placeholder="0.00">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="date" class="w-full border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                                x-model="sale.sale_date">
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('sales.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-purple-600 rounded-md hover:bg-purple-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar Ventas
                </button>
            </div>

            <!-- Mensajes ahora manejados por componente notifications -->
            <div x-show="message" class="mt-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded" x-text="message"></div>
            <div x-show="error" class="mt-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded" x-text="error"></div>
        </form>
    </div>

    <script>
        // Get the current date in Lima, Peru, formatted as YYYY-MM-DD
        const limaDate = new Date().toLocaleString('en-CA', {
            timeZone: 'America/Lima',
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        }).replace(/\//g, '-');


        function bulkSalesForm() {
            return {
                sales: [],
                message: '',
                error: '',

                // Fetches seller data from an API and initializes the sales array
                async fetchSellers() {
                    try {
                        // Assuming 'axios' is available globally for API calls
                        const res = await axios.get('sellers-api');
                        // Map the fetched seller data to the sales array,
                        // initializing amount to empty and sale_date to the current Lima date
                        this.sales = res.data.map(seller => ({
                            seller_id: seller.id,
                            name: seller.name,
                            amount: '',
                            sale_date: limaDate, // Use the Lima date format for initial load
                        }));
                    } catch (e) {
                        // Display an error message if fetching sellers fails
                        this.error = 'Error al cargar vendedores.';
                        console.error('Error fetching sellers:', e);
                    }
                },

                // Handles the form submission
                async submit() {
                    try {
                        // Prepare the payload for the API request,
                        // including only necessary fields for each sale
                        const payload = {
                            sales: this.sales.map(({ seller_id, amount, sale_date }) => ({
                                seller_id,
                                amount,
                                sale_date
                            }))
                        };

                        // Send the sales data to the '/sales/bulk' endpoint
                        const res = await axios.post('/sales/bulk', payload);
                        // Display success message
                        this.message = res.data.message;
                        this.error = ''; // Clear any previous errors

                        // Reset amounts after successful submission, but keep the selected date
                        this.sales.forEach(s => {
                            s.amount = '';
                            // s.sale_date is intentionally NOT reset here,
                            // so the user's selected date persists.
                        });
                    } catch (e) {
                        // Display error message if submission fails
                        this.error = e.response?.data?.message || 'Error al registrar ventas.';
                        this.message = ''; // Clear any previous success messages
                        console.error('Error submitting sales:', e);
                    }
                },

                // Initialization function called when the Alpine.js component is mounted
                init() {
                    this.fetchSellers(); // Fetch sellers when the component initializes
                }
            }
        }
    </script>
</x-app-layout>
