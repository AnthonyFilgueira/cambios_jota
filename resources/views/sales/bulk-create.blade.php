<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 py-6" x-data="bulkSalesForm()">
        <h1 class="text-2xl font-bold mb-6">Registrar Ventas</h1>

        <form @submit.prevent="submit">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">Vendedor</th>
                            <th class="px-4 py-2 text-left">Monto</th>
                            <th class="px-4 py-2 text-left">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(sale, index) in sales" :key="sale.seller_id">
                            <tr class="border-t">
                                <td class="px-4 py-2">
                                    <div class="text-sm font-medium text-gray-800" x-text="sale.name"></div>
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" step="0.01" class="w-full border-gray-300 rounded p-2"
                                            x-model="sale.amount" placeholder="0.00">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="date" class="w-full border-gray-300 rounded p-2"
                                            x-model="sale.sale_date">
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit"
                        class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition">
                    Guardar Ventas
                </button>
            </div>

            <p x-show="message" class="text-green-600 mt-4" x-text="message"></p>
            <p x-show="error" class="text-red-600 mt-4" x-text="error"></p>
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
