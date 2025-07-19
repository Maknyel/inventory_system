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
        <li><span class="">Inventory Return</span></li>
    </ol>
</nav>
<div id="inventory-return-app" class="p-4">
    <h2 class="text-xl font-semibold mb-4">Inventory Return</h2>

    <form @submit.prevent="submitForm" class="space-y-4 max-w-md">
        <div style="position: relative;">
            <label class="block font-medium mb-1">Inventory History List</label>
            <input
                type="text"
                v-model="inventoryHistorySearch"
                @input="filterInventoryHistory"
                @focus="showInventoryHistorySuggestions = true"
                @blur="hideInventoryHistorySuggestions"
                autocomplete="off"
                placeholder="Search inventory history..."
                class="w-full border rounded px-3 py-2"
            />
            <ul v-if="showInventoryHistorySuggestions && filteredInventoryHistory.length" 
                class="absolute z-10 w-full bg-white border rounded max-h-40 overflow-auto shadow mt-1">
                <li
                    v-for="item in filteredInventoryHistory"
                    :key="item.id"
                    @mousedown.prevent="selectInventoryHistory(item)"
                    class="px-3 py-2 hover:bg-blue-500 hover:text-white cursor-pointer"
                >
                    {{ item.name }} - {{ item.description }} - ₱{{ item.price }} - Qty: {{ item.quantity }} - Returned: {{ item.return_quantity ?? 0 }} - Created: {{ formatDate(item.created_at) }}
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
            <small class="text-sm text-gray-500">Available: {{ parseInt(selectedInventory?.quantity ?? 0) - parseInt(selectedInventory?.return_quantity ?? 0) }}</small>
        </div>

        <div>
            <label class="block font-medium mb-1">Remarks</label>
            <input v-model="remarks" type="text" class="w-full border rounded px-3 py-2">
        </div>


        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
            Inventory Return
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
            inventoryHistoryList: [],
            distributorList: [],
            selectedInventoryId: '',
            quantity: 0,
            remarks: '',

            inventoryHistorySearch: '',
            filteredInventoryHistory: [],
            showInventoryHistorySuggestions: false,
        };
    },
    computed: {
        selectedInventory() {
            return this.inventoryHistoryList.find(item => item.id == this.selectedInventoryId);
        }
    },
    mounted() {
        this.distributorListFunction();
        this.getInventoryHistory();
    },
    methods: {
        filterInventoryHistory() {
            const search = this.inventoryHistorySearch.toLowerCase();
            this.filteredInventoryHistory = this.inventoryHistoryList.filter(item =>
                item.name.toLowerCase().includes(search) ||
                (item.description && item.description.toLowerCase().includes(search)) ||
                String(item.price).includes(search) ||
                String(item.quantity).includes(search)
            );
        },
        selectInventoryHistory(item) {
            this.selectedInventoryId = item.id;
            this.inventoryHistorySearch = `${item.name} - ${item.description} - ₱${item.price} - Qty: ${item.quantity} - Returned: ${item.return_quantity ?? 0} - Created: ${this.formatDate(item.created_at)}`;
            this.showInventoryHistorySuggestions = false;
        },
        hideInventoryHistorySuggestions() {
            setTimeout(() => {
                this.showInventoryHistorySuggestions = false;
            }, 100);
        },
        formatDate (dateString){
            const date = new Date(dateString);
            return date.toLocaleString();
        },
        async getInventoryHistory(){
            fetch(`${base_url}inventory/in/list?inventory_type=${inventoryType}&sub_inventory_type=${subInventoryType}`)
            .then(res => res.json())
            .then(data => {
                this.inventoryHistoryList = data;
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
                fetch(base_url + 'inventory/save-return', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                    },
                    body: JSON.stringify({
                        inventory_history_id: this.selectedInventoryId,
                        quantity: this.quantity,
                        remarks: this.remarks,
                    })
                })
                .then(res => res.json())
                .then(response => {
                    alert('Inventory return recorded!');
                    this.selectedInventoryId = '';
                    this.quantity = 0;
                    this.remarks = '';
                    this.getInventoryHistory();
                });
            }
        }
    }
}).mount('#inventory-return-app');
</script>

<?= $this->endSection() ?>
