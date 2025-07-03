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

                async fetchSellers() {
                    try {
                        const res = await axios.get('sellers-api');
                        this.sales = res.data.map(seller => ({
                            seller_id: seller.id,
                            name: seller.name,
                            amount: '',
                            sale_date:limaDate, // Use the Lima date format

new Date().toISOString().split('T')[0],
                        }));
                    } catch (e) {
                        this.error = 'Error al cargar vendedores.';
                    }
                },

                async submit() {
                    try {
                        const payload = {
                            sales: this.sales.map(({ seller_id, amount, sale_date }) => ({
                                seller_id,
                                amount,
                                sale_date
                            }))
                        };

                        const res = await axios.post('/sales/bulk', payload);
                        this.message = res.data.message;
                        this.error = '';
                        this.sales.forEach(s => {
                            s.amount = '';
                            s.sale_date = new Date().toISOString().split('T')[0];
                        });
                    } catch (e) {
                        this.error = e.response?.data?.message || 'Error al registrar ventas.';
                        this.message = '';
                    }
                },

                init() {
                    this.fetchSellers();
                }
            }
        }
    </script>
</x-app-layout>