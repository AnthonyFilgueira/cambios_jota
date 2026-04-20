<x-app-layout>
    <div class="max-w-md mx-auto px-4 py-6" x-data="saleForm()">
        <!-- Encabezado con paleta Cambio J -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Registrar Venta</h1>
            <p class="text-gray-600 mt-2">Nueva venta individual</p>
        </div>

        <form @submit.prevent="submit" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Vendedor</label>
                <select x-model="form.seller_id" class="w-full border-gray-300 rounded-md p-2.5 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <template x-for="seller in sellers" :key="seller.id">
                        <option :value="seller.id" x-text="seller.name"></option>
                    </template>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Monto</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-500">S/.</span>
                    <input type="number" x-model="form.amount" step="0.01" class="w-full border-gray-300 rounded-md p-2.5 pl-10 focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="0.00" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Fecha</label>
                <input type="date" x-model="form.sale_date" class="w-full border-gray-300 rounded-md p-2.5 focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('sales.index') }}" class="flex-1 text-center py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button class="flex-1 inline-flex items-center justify-center py-2.5 text-sm font-medium text-white bg-purple-600 rounded-md hover:bg-purple-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar
                </button>
            </div>

            <!-- Mensajes ahora manejados por componente notifications -->
            <div x-show="message" class="p-3 bg-green-50 border-l-4 border-green-500 text-green-700 rounded text-sm" x-text="message"></div>
            <div x-show="error" class="p-3 bg-red-50 border-l-4 border-red-500 text-red-700 rounded text-sm" x-text="error"></div>
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