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
<div id="pos-app" class="p-4">
    <h2 class="text-xl font-semibold mb-4">Add Inventory Stock</h2>

    <!-- Inventory Selector -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block mb-1 font-semibold">Select Inventory Item</label>
            <select v-model="selectedInventoryId" class="w-full border rounded px-3 py-2">
                <option disabled value="">-- Choose Item --</option>
                <option v-for="item in inventoryList" :key="item.id" :value="item.id">
                    {{ item.name }} ({{ item.unit }}) - {{ item.description }}
                </option>
            </select>
        </div>
        <div>
            <label class="block mb-1 font-semibold">Quantity</label>
            <input v-model.number="quantity" type="number" min="1" class="w-full border rounded px-3 py-2" placeholder="Qty">
        </div>
        <div>
            <label class="block mb-1 font-semibold">Price</label>
            <input v-model.number="price" type="number" min="0" class="w-full border rounded px-3 py-2" placeholder="Price">
        </div>
        <div class="flex items-end">
            <button @click="addToCart" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">
                Add to Cart
            </button>
        </div>
    </div>

    <!-- Cart Table -->
    <div v-if="cart.length" class="mt-6">
        <h3 class="text-lg font-semibold mb-2">Cart</h3>
        <table class="min-w-full border text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-3 py-2 text-left">Item</th>
                    <th class="border px-3 py-2">Qty</th>
                    <th class="border px-3 py-2">Price</th>
                    <th class="border px-3 py-2">Total</th>
                    <th class="border px-3 py-2">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in cart" :key="index">
                    <td class="border px-3 py-2">{{ item.name }}</td>
                    <td class="border px-3 py-2 text-center">{{ item.quantity }}</td>
                    <td class="border px-3 py-2 text-right">{{ item.price.toFixed(2) }}</td>
                    <td class="border px-3 py-2 text-right">{{ (item.quantity * item.price).toFixed(2) }}</td>
                    <td class="border px-3 py-2 text-center">
                        <button @click="removeFromCart(index)" class="text-red-600 hover:underline">Remove</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Supplier & Checkout -->
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-medium mb-1">Supplier</label>
                <select v-model="selectedSupplierId" class="w-full border rounded px-3 py-2">
                    <option disabled value="">Select supplier</option>
                    <option v-for="item in supplierList" :key="item.id" :value="item.id">
                        {{ item.name }}
                    </option>
                </select>
            </div>
            <div class="flex items-end justify-between">
                <div class="text-lg font-semibold">Total: ₱{{ cartTotal.toFixed(2) }}</div>
                <button @click="checkout" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                    Checkout
                </button>
            </div>
        </div>
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
            supplierList: [],
            selectedInventoryId: '',
            selectedSupplierId: '',
            quantity: 1,
            price: 0,
            cart: [],
        };
    },
    computed: {
        cartTotal() {
            return this.cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
        }
    },
    mounted() {
        fetch(`${base_url}inventory/list?inventory_type=${inventoryType}&sub_inventory_type=${subInventoryType}`)
            .then(res => res.json()).then(data => this.inventoryList = data);

        fetch(base_url + 'supplier/list')
            .then(res => res.json()).then(data => this.supplierList = data);
    },
    methods: {
        addToCart() {
            const selected = this.inventoryList.find(i => i.id === this.selectedInventoryId);
            if (!selected || this.quantity <= 0 || this.price < 0) return alert("Invalid input");

            this.cart.push({
                id: this.selectedInventoryId,
                name: selected.name,
                quantity: this.quantity,
                price: this.price,
            });

            // Reset
            this.selectedInventoryId = '';
            this.quantity = 1;
            this.price = 0;
        },
        removeFromCart(index) {
            this.cart.splice(index, 1);
        },
        checkout() {
            if (!this.selectedSupplierId || this.cart.length === 0) {
                return alert("Please add items and select a supplier.");
            }

            const payload = {
                supplier_id: this.selectedSupplierId,
                items: this.cart
            };

            fetch(base_url + 'inventory/save-pos-stock', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(response => {
                alert('Transaction completed!');
                this.cart = [];
                this.selectedSupplierId = '';
            });
        }
    }
}).mount('#pos-app');
</script>
<?= $this->endSection() ?>
