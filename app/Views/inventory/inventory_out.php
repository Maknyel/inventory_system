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
            <input
                type="text"
                v-model="inventorySearch"
                @input="filterInventory"
                @focus="showInventorySuggestions = true"
                @blur="hideInventorySuggestions"
                class="w-full border rounded px-3 py-2"
                placeholder="Search inventory..."
                autocomplete="off"
            />
            <ul v-if="showInventorySuggestions && filteredInventory.length" class="border rounded mt-1 max-h-40 overflow-auto bg-white shadow-lg z-10 absolute w-full">
                <li
                    v-for="item in filteredInventory"
                    :key="item.id"
                    @mousedown.prevent="selectInventory(item)"
                    class="px-3 py-2 hover:bg-blue-500 hover:text-white cursor-pointer"
                >
                    {{ item.name }} - {{ item.description }} - ({{ item.unit }}) - ₱{{ item.current_price }}, Qty: {{ item.current_quantity }}
                </li>
            </ul>
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

        <!-- Distributor search input -->
        <div class="<?=($sub_inventory_type_parse['has_distributor'] == 0)?'hidden':''?>" style="position: relative;">
            <label class="block font-medium mb-1">Distributor</label>
            <input
                type="text"
                v-model="distributorSearch"
                @input="filterDistributor"
                @focus="showDistributorSuggestions = true"
                @blur="hideDistributorSuggestions"
                :disabled="type !== 'For Distribution'"
                class="w-full border rounded px-3 py-2"
                placeholder="Search distributor..."
                autocomplete="off"
            />
            <ul v-if="showDistributorSuggestions && filteredDistributor.length && type == 'For Distribution'" class="border rounded mt-1 max-h-40 overflow-auto bg-white shadow-lg z-10 absolute w-full">
                <li
                    v-for="item in filteredDistributor"
                    :key="item.id"
                    @mousedown.prevent="selectDistributor(item)"
                    class="px-3 py-2 hover:bg-blue-500 hover:text-white cursor-pointer"
                >
                    {{ item.type }} | {{ item.name }}
                </li>
            </ul>
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

            // For inventory search input
            inventorySearch: '',
            filteredInventory: [],
            showInventorySuggestions: false,

            // For distributor search input
            distributorSearch: '',
            filteredDistributor: [],
            showDistributorSuggestions: false,
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
        changeType() {
            if(this.type != 'For Distribution') {
                this.distributor_id = null;
                this.distributorSearch = '';
                this.showDistributorSuggestions = false;
            }
        },
        filterInventory() {
            const search = this.inventorySearch.toLowerCase();
            this.filteredInventory = this.inventoryList.filter(item =>
                item.name.toLowerCase().includes(search) ||
                (item.description && item.description.toLowerCase().includes(search))
            );
        },
        selectInventory(item) {
            this.selectedInventoryId = item.id;
            this.inventorySearch = `${item.name} - ${item.description} - (${item.unit}) - ₱${item.current_price}, Qty: ${item.current_quantity}`;
            this.showInventorySuggestions = false;
        },
        hideInventorySuggestions() {
            // Delay hiding to allow click to register
            setTimeout(() => {
                this.showInventorySuggestions = false;
            }, 100);
        },
        filterDistributor() {
            const search = this.distributorSearch.toLowerCase();
            this.filteredDistributor = this.distributorList.filter(item =>
                item.name.toLowerCase().includes(search) ||
                (item.type && item.type.toLowerCase().includes(search))
            );
        },
        selectDistributor(item) {
            this.distributor_id = item.id;
            this.distributorSearch = `${item.type} | ${item.name}`;
            this.showDistributorSuggestions = false;
        },
        hideDistributorSuggestions() {
            setTimeout(() => {
                this.showDistributorSuggestions = false;
            }, 100);
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
