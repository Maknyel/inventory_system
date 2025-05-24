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
        <li><span class="">Point of Sale</span></li>
    </ol>
</nav>
<div id="pos-app" class="p-4">
    <h2 class="text-xl font-semibold mb-4">Point of Sale</h2>

    <!-- Item Selection -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
            <label class="block font-medium mb-1">Quantity</label>
            <input v-model.number="quantity" :max="selectedInventory?.current_quantity" type="number" min="1" class="w-full border rounded px-3 py-2">
            <small class="text-sm text-gray-500">Available: {{ selectedInventory?.current_quantity ?? 0 }}</small>
        </div>

        <div class="flex items-end">
            <button @click="addItem" type="button" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-green-600">Add to Cart</button>
        </div>
    </div>

    <!-- Cart -->
    <div class="mt-6">
        <h3 class="text-lg font-semibold mb-2">Cart</h3>
        <table class="min-w-full border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 text-left">Item</th>
                    <th class="p-2 text-left">Quantity</th>
                    <th class="p-2 text-left">Remarks</th>
                    <th class="p-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in cart" :key="index" class="border-b">
                    <td class="p-2">{{ item.name }}</td>
                    <td class="p-2">{{ item.quantity }}</td>
                    <td class="p-2">
                        <input v-model="item.remarks" class="border rounded px-2 py-1 w-full" placeholder="Remarks">
                    </td>
                    <td class="p-2">
                        <button @click="removeItem(index)" type="button" class="text-red-600 hover:underline">Remove</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Type and Distributor -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block font-medium mb-1">Type</label>
            <select v-model="type" @change="changeType" class="w-full border rounded px-3 py-2">
                <option value="">Select type</option>
                <option>For Own Consumption</option>
                <option>For Distribution</option>
            </select>
        </div>

        <div>
            <label class="block font-medium mb-1">Distributor</label>
            <select v-model="distributor_id" class="w-full border rounded px-3 py-2" :disabled="type !== 'For Distribution'">
                <option disabled value="">Select distributor</option>
                <option v-for="item in distributorList" :key="item.id" :value="item.id">
                    {{ item.type }} | {{ item.name }}
                </option>
            </select>
        </div>
    </div>

    <!-- Checkout -->
    <div class="mt-6">
        <button @click="submitPOS" type="button" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Checkout</button>
    </div>
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
            distributorList: [],
            selectedInventoryId: '',
            quantity: 1,
            cart: [],
            type: '',
            distributor_id: ''
        };
    },
    computed: {
        selectedInventory() {
            return this.inventoryList.find(item => item.id == this.selectedInventoryId);
        }
    },
    mounted() {
        this.getInventory();
        this.getDistributors();
    },
    methods: {
        async getInventory() {
            const res = await fetch(`${base_url}inventory/list?inventory_type=${inventoryType}&sub_inventory_type=${subInventoryType}`);
            this.inventoryList = await res.json();
        },
        async getDistributors() {
            const res = await fetch(`${base_url}distributor/list`);
            this.distributorList = await res.json();
        },
        addItem() {
            if (!this.selectedInventoryId || this.quantity <= 0 || this.quantity > this.selectedInventory?.current_quantity) {
                alert("Invalid quantity or item.");
                return;
            }
            const item = this.inventoryList.find(i => i.id == this.selectedInventoryId);
            this.cart.push({
                inventory_id: item.id,
                name: item.name,
                quantity: this.quantity,
                remarks: ''
            });
            this.selectedInventoryId = '';
            this.quantity = 1;
        },
        removeItem(index) {
            this.cart.splice(index, 1);
        },
        changeType() {
            if (this.type !== 'For Distribution') {
                this.distributor_id = '';
            }
        },
        submitPOS() {
            if (this.cart.length === 0) {
                alert("Cart is empty!");
                return;
            }
            fetch(`${base_url}inventory/save-pos-out`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                body: JSON.stringify({
                    items: this.cart,
                    type: this.type,
                    distributor_id: this.distributor_id
                })
            })
            .then(res => res.json())
            .then(response => {
                alert('POS transaction completed!');
                this.cart = [];
                this.type = '';
                this.distributor_id = '';
                this.getInventory();
            });
        }
    }
}).mount('#pos-app');
</script>

<?= $this->endSection() ?>
