<x-app-layout>
    <div class="max-w-md mx-auto px-4 py-6" x-data="saleForm()">
        <h1 class="text-2xl font-bold mb-4">Registrar Venta</h1>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Vendedor</label>
                <select x-model="form.seller_id" class="w-full border-gray-300 rounded p-2">
                    <template x-for="seller in sellers" :key="seller.id">
                        <option :value="seller.id" x-text="seller.name"></option>
                    </template>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Monto</label>
                <input type="number" x-model="form.amount" step="0.01" class="w-full border-gray-300 rounded p-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Fecha</label>
                <input type="date" x-model="form.sale_date" class="w-full border-gray-300 rounded p-2" required>
            </div>

            <button class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">Guardar</button>

            <p x-show="message" class="text-green-600 mt-2" x-text="message"></p>
            <p x-show="error" class="text-red-600 mt-2" x-text="error"></p>
        </form>
    </div>

    <script>
        function saleForm() {
            return {
                form: {
                    seller_id: '',
                    amount: '',
                    sale_date: '',
                },
                sellers: [],
                message: '',
                error: '',

                async fetchSellers() {

                    try {
                        const res = await axios.get('/api/sellers');
                        this.sellers = res.data;
                        if (this.sellers.length > 0) {
                            this.form.seller_id = this.sellers[0].id;

                            console.console.log(`Vendedores cargados: ${this.sellers.length}`);
                            
                        }
                    } catch (e) {
                        this.error = 'Error al cargar vendedores.';
                    }
                },

                async submit() {
                    try {
                        const res = await axios.post('/sales', this.form);
                        this.message = res.data.message;
                        this.error = '';
                        this.form.amount = '';
                        this.form.sale_date = '';
                    } catch (e) {
                        this.error = e.response?.data?.message || 'Error al registrar la venta.';
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