<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
    <nav class="text-sm text-gray-600" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <li>
                <a href="<?=base_url('inventory')?>" class="text-blue-600 hover:underline">Inventory</a>
            </li>
            <li><span class="mx-1 text-gray-400">/</span></li>
            <li>
                <a href="<?=base_url('inventory/'.$inventory_type_parse['id'])?>" class="text-blue-600 hover:underline"><?=($inventory_type_parse['name'])?></a>
            </li>
            <li><span class="mx-1 text-gray-400">/</span></li>
            <li>
                <a href="<?=base_url('inventory/'.$inventory_type_parse['id'].'/'.$sub_inventory_type_parse['id'])?>" class="text-blue-600 hover:underline"><?=($sub_inventory_type_parse['name'])?></a>
            </li>
            <li><span class="mx-1 text-gray-400">/</span></li>
            <li><span class="">Inventory In</span></li>
        </ol>
    </nav>
    <div id="inventory-app" class="p-4">
        <h2 class="text-xl font-semibold mb-4">Add Inventory Stock</h2>

        <form @submit.prevent="submitForm" class="space-y-4 max-w-md">
            <div>
                <label class="block font-medium mb-1">Inventory Item</label>
                <select v-model="selectedInventoryId" class="w-full border rounded px-3 py-2">
                    <option disabled value="">Select inventory</option>
                    <option v-for="item in inventoryList" :key="item.id" :value="item.id">
                        {{ item.name }}
                    </option>
                </select>
            </div>

            <div>
                <label class="block font-medium mb-1">Supplier</label>
                <select v-model="selectedSupplierId" class="w-full border rounded px-3 py-2">
                    <option disabled value="">Select supplier</option>
                    <option v-for="item in supplierList" :key="item.id" :value="item.id">
                        {{ item.name }}
                    </option>
                </select>
            </div>

            <div>
                <label class="block font-medium mb-1">Quantity</label>
                <input v-model.number="quantity" type="number" min="1" class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block font-medium mb-1">Price</label>
                <input v-model.number="price" type="number" step="0.01" min="0" class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block font-medium mb-1">Total</label>
                <input :value="computedTotal" type="text" step="0.01" min="0" class="w-full border rounded px-3 py-2" disabled>
            </div>


            <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-600">
                Add Stock
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
    <script>
        const inventoryType = <?= json_encode($_GET['inventory_type'] ?? ''); ?>;
        const subInventoryType = <?= json_encode($_GET['sub_inventory_type'] ?? ''); ?>;
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                inventoryList: [],
                supplierList: [],
                selectedInventoryId: '',
                selectedSupplierId: '',
                quantity: 0,
                price: 0,
            };
        },
        computed: {
            computedTotal(){
                return this.quantity*this.price;
            }
        },
        mounted() {
            fetch(`${base_url}inventory/list?inventory_type=${inventoryType}&sub_inventory_type=${subInventoryType}`)
                .then(res => res.json())
                .then(data => {
                    this.inventoryList = data;
                });
            fetch(base_url+'supplier/list')
                .then(res => res.json())
                .then(data => {
                    this.supplierList = data;
                });
        },
        methods: {
            submitForm() {
                fetch(base_url  +'inventory/save-stock', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                    },
                    body: JSON.stringify({
                        inventory_id: this.selectedInventoryId,
                        supplier_id: this.selectedSupplierId,
                        quantity: this.quantity,
                        price: this.price,
                    })
                })
                .then(res => res.json())
                .then(response => {
                    alert('Stock added!');
                    this.selectedInventoryId = '';
                    this.selectedSupplierId = '';
                    this.quantity = 0;
                    this.price = 0;
                });
            }
        }
    }).mount('#inventory-app');
    </script>

<?= $this->endSection() ?>