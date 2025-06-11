<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<!-- Vue -->
<script src="https://unpkg.com/vue@3.4.21/dist/vue.global.js"></script>

<!-- html2pdf as a global object -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<!-- Your app -->
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
    <!-- pdf -->
    <div v-show="generateReceiptDistributors" id="dr-form-to-distributors" 
        class="mt-6 mx-auto p-4 text-sm w-full max-w-[700px]">
        <img 
            src="<?= base_url('public/images/imgHeader.png') ?>"
            class="w-full h-auto"
        >
        <div class="flex justify-between mb-4">
            <div>
                <p><strong>DELIVERED TO:</strong> {{ supplier }}</p>
                <p><strong>Address:</strong> {{ attention }}</p>
            </div>
            <div>
                <p><strong>REF. P.O. #:</strong> {{ dr_number }}</p>
                <p><strong>DATE:</strong> {{ orderDate }}</p>
            </div>
        </div>

        

        <table class="min-w-full border text-xs">
            <thead>
                <tr>
                    <th colspan="6"><h2 class="text-center font-semibold mb-2">DELIVERY RECEIPT</h2></th>
                </tr>
                <tr>
                    <th class="border p-2">ITEM</th>
                    <th class="border p-2">DESCRIPTION</th>
                    <th class="border p-2">QTY</th>
                    <th class="border p-2">UNIT</th>
                    <th class="border p-2">UNIT COST</th>
                    <th class="border p-2">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in cart" :key="index">
                    <td class="border p-2">{{ index + 1 }}</td>
                    <td class="border p-2">{{ item.name }}</td>
                    <td class="border p-2 text-right">{{ item.quantity }}</td>
                    <td class="border p-2 text-center">{{ item.unit }}</td>
                    <td class="border p-2 text-right">₱ {{ (item.unit_cost ?? 0).toFixed(2) }}</td>
                    <td class="border p-2 text-left">₱ {{ ((item.unit_cost ?? 0) * item.quantity).toFixed(2) }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="border p-2 text-right font-semibold">SUBTOTAL:</td>
                    <td class="border p-2 text-left font-semibold">₱ {{ totalAmount }}</td>
                </tr>
                <tr>
                    <td colspan="5" class="border p-2 text-right font-semibold">DISCOUNT:</td>
                    <td class="border p-2 text-left font-semibold">₱ </td>
                </tr>
                <tr>
                    <td colspan="5" class="border p-2 text-right font-semibold">TOTAL DISCOUNTED AMAOUNT:</td>
                    <td class="border p-2 text-left font-semibold">₱ </td>
                </tr>
            </tfoot>
        </table>
        <p class="mr-10 mt-10 text-right">Received the above goods in good order and conditions.</p>
        <div class="flex justify-between mt-10 ">
            <div class="flex flex-col justify-start items-start ml-2 w-1/4">
                <div class="text-center">
                    <div class="border-t w-32 mx-auto"></div>
                    <p class="mt-1">Prepared by:</p>
                </div>
                <div class="text-center mt-10">
                    <div class="border-t w-32 mx-auto"></div>
                    <p class="mt-1">Checked by:</p>
                </div>
            </div>
            <div class="flex flex-col justify-start items-center ml-2 w-3/4">
                
                <div class="text-center">
                    <div class="border-t w-32 mx-auto"></div>
                    <p class="mt-1">Received by:</p>
                </div>
            </div>
        </div>
    </div>

    <div v-show="generateReceiptCustomer" id="dr-form-to-customer" 
        class="mt-6 mx-auto p-4 text-sm w-full max-w-[700px]">
        <img 
            src="<?= base_url('public/images/imgHeader.png') ?>"
            class="w-full h-auto"
        >
        <div class="flex justify-between mb-4">
            <div>
                <p><strong>DELIVERED TO:</strong> {{ supplier }}</p>
                <p><strong>Address:</strong> {{ attention }}</p>
            </div>
            <div>
                <p><strong>REF. P.O. #:</strong> {{ dr_number }}</p>
                <p><strong>DATE:</strong> {{ orderDate }}</p>
            </div>
        </div>

        <h2 class="text-center font-semibold mb-2">DELIVERY RECEIPT</h2>

        <table class="min-w-full border text-xs">
            <thead>
                <tr>
                    <th class="border p-2">QTY</th>
                    <th class="border p-2">UNIT</th>
                    <th class="border p-2">DESCRIPTION</th>
                    <th class="border p-2">UNIT COST</th>
                    <th class="border p-2">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in cart" :key="index">
                    
                    <td class="border p-2 text-right">{{ item.quantity }}</td>
                    <td class="border p-2 text-center">{{ item.unit }}</td>
                    <td class="border p-2">{{ item.name }}</td>
                    <td class="border p-2 text-right">₱ {{ (item.unit_cost ?? 0).toFixed(2) }}</td>
                    <td class="border p-2 text-left">₱ {{ ((item.unit_cost ?? 0) * item.quantity).toFixed(2) }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="border p-2 text-left font-semibold">SUBTOTAL:</td>
                    <td class="border p-2 text-left font-semibold">₱ {{ totalAmount }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="border p-2 text-left font-semibold">TOTAL:</td>
                    <td class="border p-2 text-left font-semibold">₱ {{ totalAmount }}</td>
                </tr>
            </tfoot>
        </table>

        <p class="mt-10 text-center">Received the above goods in good order and conditions.</p>

        <div class="flex mt-10">
            <div class="w-1/2 text-left">
                <div class="w-40 mx-auto"></div>
                <p class="mt-1">Prepared by:</p>
            </div>
            <div class="w-1/2 text-left">
                <div class="w-40 mx-auto"></div>
                <p class="mt-1">By:</p>
            </div>
        </div>
        <div class="flex mt-10 gap-2">
            <div class="w-1/2 border text-left">

            </div>
            <div class="w-1/2 border text-left">

            </div>
        </div>
        <div class="flex gap-2">
            <div class="w-1/2 text-left">

            </div>
            <div class="w-1/2 text-center">
                Authorized Signature
            </div>
        </div>
    </div>
     
    <div v-show="generateReceiptPurchaceOrder" id="purchase-order" 
        class="mt-6 mx-auto p-4 text-sm w-full max-w-[700px]">
        <img 
            src="<?= base_url('public/images/imgHeader.png') ?>"
            class="w-full h-auto"
        >
        <div class="flex justify-between mb-4">
            <div>
                <p><strong>SUPPLIER:</strong> {{ supplier }}</p>
                <p><strong>ATTENTION:</strong> {{ attention }}</p>
            </div>
            <div>
                <p><strong>ORDER DATE:</strong> {{ orderDate }}</p>
                <p><strong>PO REF. NO:</strong> {{ dr_number }}</p>
            </div>
        </div>

        <h2 class="text-center font-semibold mb-2">PURCHASE ORDER</h2>

        <table class="min-w-full border text-xs">
            <thead>
                <tr>
                    <th class="border p-2">ITEM</th>
                    <th class="border p-2">DESCRIPTION</th>
                    <th class="border p-2">P.O. QTY.</th>
                    <th class="border p-2">U.M</th>
                    <th class="border p-2">UNIT COST</th>
                    <th class="border p-2">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
            <tr v-for="(item, index) in cart" :key="index">
                <td class="border p-2">{{ index + 1 }}</td>
                <td class="border p-2">{{ item.name }}</td>
                <td class="border p-2 text-right">{{ item.quantity }}</td>
                <td class="border p-2 text-center">{{ item.unit }}</td>
                <td class="border p-2 text-right">₱ {{ (item.unit_cost ?? 0).toFixed(2) }}</td>
                <td class="border p-2 text-left">₱ {{ ((item.unit_cost ?? 0) * item.quantity).toFixed(2) }}</td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="5" class="border p-2 text-right font-semibold">TOTAL:</td>
                <td class="border p-2 text-left font-semibold">₱ {{ totalAmount }}</td>
            </tr>
            </tfoot>
        </table>

        <div class="flex justify-between mt-10">
            <div class="text-center">
                <div class="border-t w-32 mx-auto"></div>
                <p class="mt-1">PREPARED BY:</p>
            </div>
            <div class="text-center">
                <div class="border-t w-32 mx-auto"></div>
                <p class="mt-1">APPROVED BY:</p>
            </div>
        </div>
    </div>

    <!-- pdf -->

    <h2 class="text-xl font-semibold mb-4">Point of Sale</h2>

    <!-- Item Selection -->
    <div  v-show="!dr_number" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block font-medium mb-1">Inventory Item</label>
            <select v-model="selectedInventoryId" class="w-full border rounded px-3 py-2">
                <option disabled value="">Select inventory</option>
                <option v-for="item in inventoryList" :key="item.id" :value="item.id">
                    {{ item.name }} - {{ item.description }} - ({{ item.unit }}) - ₱{{ (item.current_price) }}, Qty: {{ (item.current_quantity) }}
                </option>
            </select>
        </div>

        <input type="text" class="hidden" v-model="selectedInventoryPrice">
        <input type="text" class="hidden" v-model="selectedUnit">
        

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
    <div v-show="!dr_number" class="mt-6">
        <h3 class="text-lg font-semibold mb-2">Cart</h3>
        <table class="min-w-full border">
            <thead>
                <tr>
                    <th class="p-2 text-left">Item</th>
                    <th class="p-2 text-left">Quantity</th>
                    <th class="p-2 text-left">Unit Cost</th>
                    <th class="p-2 text-left">Total</th>
                    <th class="p-2 text-left">Remarks</th>
                    <th class="p-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in cart" :key="index" class="border-b">
                    <td class="p-2">{{ item.name }}</td>
                    <td class="p-2">{{ item.quantity }}</td>
                    <td class="p-2">₱ {{ (item.unit_cost).toFixed(2) }}</td>
                    <td class="p-2">₱ {{ (item.quantity * item.unit_cost).toFixed(2) }}</td>
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
    <div  v-show="!dr_number" class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="<?=($sub_inventory_type_parse['has_purpose'] == 0)?'hidden':''?>">
            <label class="block font-medium mb-1">Purpose</label>
            <select :disabled="dr_number?true:false" v-model="type" @change="changeType" class="w-full border rounded px-3 py-2">
                <option value="">Select Purpose</option>
                <option>For Own Consumption</option>
                <option>For Distribution</option>
            </select>
        </div>

        <div class="<?=($sub_inventory_type_parse['has_distributor'] == 0)?'hidden':''?>">
            <label class="block font-medium mb-1">Distributor</label>
            <select v-model="distributor_id" class="w-full border rounded px-3 py-2" :disabled="(type !== 'For Distribution' || dr_number)?true:false">
                <option disabled value="">Select distributor</option>
                <option v-for="item in distributorList" :key="item.id" :value="item.id">
                    {{ item.type }} | {{ item.name }}
                </option>
            </select>
        </div>
    </div>

    <div v-show="!dr_number" class="mt-4 text-right font-semibold text-lg">
        Total Price: ₱ {{ totalAmount }}
    </div>
    
    <div v-show="!dr_number" class="mt-6">
        <button @click="submitPOS" type="button" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Checkout</button>
    </div>

    <div v-show="dr_number" class="text-yellow-600 w-full text-center font-semibold text-3xl mt-4">
        Transaction submitted successfully!
    </div>

    <div v-show="dr_number" class="flex mt-6 items-center justify-between">
    
        <div class="w-3/4">
            <label class="block font-medium mb-1">Download Forms</label>
            <select v-model="selectedForms" multiple class="w-full border rounded px-3 py-2">
                <option value="purchaseOrder">Purchase Order</option>
                <option value="customerDR">DR Form to Customer</option>
                <option value="distributorDR">DR Form to Distributors</option>
            </select>
        </div>

        <div class="">
            <button @click="handleDownloads" type="button" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                Download Selected Forms
            </button>
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
            dr_number: "",
            selectedForms: [],
            price: 0,
            supplier: '',
            attention: '',
            orderDate: new Date().toISOString().split('T')[0],
            poRefNo: '',
            generateReceiptPurchaceOrder: false,
            generateReceiptDistributors: false,
            generateReceiptCustomer: false,
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
        totalAmount() {
            return this.cart.reduce((sum, item) => {
                const unitCost = parseFloat(item.unit_cost ?? 0);
                return sum + unitCost * item.quantity;
            }, 0).toFixed(2);
        },
        selectedInventory() {
            return this.inventoryList.find(item => item.id == this.selectedInventoryId);
        },
        selectedInventoryPrice() {
            return this.inventoryList.find(item => item.id == this.selectedInventoryId)?.current_price;
        },
        selectedUnit() {
            return this.inventoryList.find(item => item.id == this.selectedInventoryId)?.unit;
        }
    },
    mounted() {
        this.getInventory();
        this.getDistributors();
    },
    methods: {
        downloadPDFPurchaseOrder() {
            const element = document.getElementById('purchase-order');
            const options = {
                margin: 0.5,
                filename: 'purchase-order.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

            html2pdf().set(options).from(element).save();
            setTimeout(() => {
                this.generateReceiptPurchaceOrder = false;    
            }, 1000);
            
        },
        downloadPDFCustomer(){
            const element = document.getElementById('dr-form-to-customer');
            const options = {
                margin: 0.5,
                filename: 'DR FORM TO THE CUSTOMERS.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

            html2pdf().set(options).from(element).save();
            setTimeout(() => {
                this.generateReceiptCustomer = false;    
            }, 1000);
            
        },
        downloadPDFDistributors() {
            const element = document.getElementById('dr-form-to-distributors');
            const options = {
                margin: 0.5,
                filename: 'DR FORM TO DISTRIBUTORS.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

            html2pdf().set(options).from(element).save();
            setTimeout(() => {
                this.generateReceiptDistributors = false;    
            }, 1000);
            
        },
        
        async getInventory() {
            const res = await fetch(`${base_url}inventory/list?inventory_type=${inventoryType}&sub_inventory_type=${subInventoryType}`);
            this.inventoryList = await res.json();
            this.inventoryList = this.inventoryList.filter(item => {
                return item.current_quantity !== null && Number(item.current_quantity) !== 0;
            });
        },
        async getDistributors() {
            const res = await fetch(`${base_url}distributor/list`);
            this.distributorList = await res.json();
        },
        addItem() {
            if (!this.selectedInventoryId || this.quantity <= 0) {
                alert("Invalid item or quantity.");
                return;
            }

            const item = this.inventoryList.find(i => i.id == this.selectedInventoryId);
            const availableQuantity = item.current_quantity ?? 0;

            const existingItem = this.cart.find(i => i.inventory_id == item.id);
            let newTotalQuantity = this.quantity;

            if (existingItem) {
                // Calculate new total if item already in cart
                newTotalQuantity = existingItem.quantity + this.quantity;
            }

            if (newTotalQuantity > availableQuantity) {
                alert("Total quantity exceeds available stock!");
                return;
            }

            if (existingItem) {
                // Update quantity if exists and within available limit
                existingItem.quantity = newTotalQuantity;
            } else {
                // Add new entry
                this.cart.push({
                    inventory_id: item.id,
                    name: item.name,
                    quantity: this.quantity,
                    remarks: '',
                    unit_cost: parseFloat(this.selectedInventoryPrice),
                    unit: this.selectedUnit
                });
            }

            // Reset
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
        handleDownloads(){
            if (this.cart.length === 0) {
                alert("Cart is empty!");
                return;
            }
            if (this.selectedForms.length === 0) {
                alert("No Selected Form");
                return;
            }            
            
            this.$nextTick(() => {
                if (this.selectedForms.includes('purchaseOrder')) {
                    this.generateReceiptPurchaceOrder = true;
                    this.downloadPDFPurchaseOrder();
                }
                if (this.selectedForms.includes('customerDR')) {
                    this.generateReceiptCustomer = true;
                    this.downloadPDFCustomer();
                }
                if (this.selectedForms.includes('distributorDR')) {
                    this.generateReceiptDistributors = true;
                    this.downloadPDFDistributors();
                }
            });
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
                this.dr_number = response.dr_number;
                alert('POS transaction completed!');
                // this.cart = [];
                // this.type = '';
                // this.distributor_id = '';
                // this.getInventory();
                // location.reload();
            });
        }
    }
}).mount('#pos-app');
</script>

<?= $this->endSection() ?>
