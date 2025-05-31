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
        <li><span class="">Inventory Out</span></li>
    </ol>
</nav>
<div id="inventory-out-app" class="p-4">
    <h2 class="text-xl font-semibold mb-4">Inventory Out</h2>

    <form @submit.prevent="submitForm" class="space-y-4 max-w-md">
        <div>
            <label class="block font-medium mb-1">Inventory Item</label>
            <select v-model="selectedInventoryId" class="w-full border rounded px-3 py-2">
                <option disabled value="">Select inventory</option>
                <option v-for="item in inventoryList" :key="item.id" :value="item.id">
                    {{ item.name }} - {{ item.description }} - ({{ item.unit }}) - ₱{{ (item.current_price) }}, Qty: {{ (item.current_quantity) }}
                </option>
            </select>
        </div>

        <div>
            <label class="block font-medium mb-1">Quantity</label>
            <input
                v-model.number="quantity"
                :max="selectedInventory?.current_quantity"
                type="number"
                min="1"
                class="w-full border rounded px-3 py-2"
            >
            <small class="text-sm text-gray-500">Available: {{ selectedInventory?.current_quantity ?? 0 }}</small>
        </div>

        <div>
            <label class="block font-medium mb-1">Remarks</label>
            <input v-model="remarks" type="text" class="w-full border rounded px-3 py-2">
        </div>

        <div class="<?=($sub_inventory_type_parse['has_purpose'] == 0)?'hidden':''?>">
            <label class="block font-medium mb-1">Purpose</label>
            <select v-model="type" @change="changeType" class="w-full border rounded px-3 py-2">
                <option></option>
                <option>For Own Consumption</option>
                <option>For Distribution</option>
            </select>
        </div>

        <div class="<?=($sub_inventory_type_parse['has_distributor'] == 0)?'hidden':''?>">
            <label class="block font-medium mb-1">Distributor</label>
            <select v-model="distributor_id" class="w-full border rounded px-3 py-2">
                <option></option>
                <option v-if="type=='For Distribution'" v-for="item in distributorList" :key="item.id" :value="item.id">
                    {{ item.type }} | {{ item.name }}
                </option>
            </select>
        </div>

        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
            Inventory Out
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
            distributorList: [],
            selectedInventoryId: '',
            quantity: 0,
            remarks: '',
            type: null,
            distributor_id: null,
        };
    },
    computed: {
        selectedInventory() {
            return this.inventoryList.find(item => item.id == this.selectedInventoryId);
        }
    },
    mounted() {
        this.distributorListFunction();
        this.getInventory();
    },
    methods: {
        async changeType(){
            if(this.type!='For Distribution'){
                this.distributor_id = null;
            }
        },
        async getInventory(){
            fetch(`${base_url}inventory/list?inventory_type=${inventoryType}&sub_inventory_type=${subInventoryType}`)
            .then(res => res.json())
            .then(data => {
                this.inventoryList = data.filter(item => {
                    return item.current_quantity !== null && Number(item.current_quantity) !== 0;
                });
            });
        },
        async distributorListFunction(){
            fetch(`${base_url}distributor/list`)
            .then(res => res.json())
            .then(data => {
                this.distributorList = data;
            });
        },
        submitForm() {
            if(this.quantity > this.selectedInventory?.current_quantity){
                alert("The entered quantity exceeds the current available inventory.");
            }else{
                fetch(base_url + 'inventory/save-out', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                    },
                    body: JSON.stringify({
                        inventory_id: this.selectedInventoryId,
                        quantity: this.quantity,
                        remarks: this.remarks,
                        customer_own_distribution: this.type,
                        distributor_id: this.distributor_id
                    })
                })
                .then(res => res.json())
                .then(response => {
                    alert('Inventory out recorded!');
                    this.selectedInventoryId = '';
                    this.quantity = 0;
                    this.remarks = '';
                    this.getInventory();
                });
            }
        }
    }
}).mount('#inventory-out-app');
</script>

<?= $this->endSection() ?>
