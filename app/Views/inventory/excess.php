<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<!-- add for this page only -->
<!-- Tom Select CSS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

<!-- Tom Select JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    let inventorySelect = null;

    document.addEventListener('DOMContentLoaded', function() {
        inventorySelect = new TomSelect('#inventory-id-select', {
            create: false,
            sortField: {
                field: 'text',
                direction: 'asc'
            },
            placeholder: 'Search or select inventory...'
        });
    });
</script>
<!-- add for this page only -->
<nav class="text-sm text-gray-600" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li>
            <a href="<?= base_url('inventory') ?>" class="text-blue-600 hover:underline">Inventory</a>
        </li>
        <li><span class="mx-1 text-gray-400">/</span></li>
        <li>
            <a href="<?= base_url('inventory/' . $inventory_type_parse['id']) ?>" class="text-blue-600 hover:underline"><?= ($inventory_type_parse['name']) ?></a>
        </li>
        <li><span class="mx-1 text-gray-400">/</span></li>
        <li>
            <a href="<?= base_url('inventory/' . $inventory_type_parse['id'] . '/' . $sub_inventory_type_parse['id']) ?>" class="text-blue-600 hover:underline"><?= ($sub_inventory_type_parse['name']) ?></a>
        </li>
        <li><span class="mx-1 text-gray-400">/</span></li>
        <li><span class="">Excess Stock</span></li>
    </ol>
</nav>

<div class="flex items-center justify-between mt-6 mb-4">
    <h1 class="text-2xl font-bold">Excess Stock List</h1>

    <div class="flex gap-2">
        <!-- <button onclick="exportInventory()" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-600">
            Export Inventory
        </button> -->



        <?php if (current_user()['role_id'] == 1 || current_user()['role_id'] == 4) { ?>
            <button onclick="openAddModal()" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">Add Inventory</button>
        <?php } ?>
    </div>
</div>

<form method="get" class="mb-4 flex items-center gap-2">
    <input type="text" name="search" value="<?= esc($search) ?>" placeholder="Search inventory..." class="px-3 py-2 border rounded w-64">

    <input type="hidden" name="inventory_type" value="<?= esc($inventory_type_parse['id']) ?>">
    <input type="hidden" name="sub_inventory_type" value="<?= esc($sub_inventory_type_parse['id']) ?>">
    <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded">Search</button>
</form>
<div class="flex flex-col h-full overflow-auto">

    <table class="min-w-full table-auto border-collapse border border-gray-300">
        <thead>
            <tr>
                <?php if (current_user()['role_id'] == 1 || current_user()['role_id'] == 4) { ?>
                    <th class="sticky top-0 bg-white px-4 py-2 border-b text-left cursor-pointer">Option</th>
                <?php } ?>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left cursor-pointer">Icon</th>
                <!-- <th class="sticky top-0 bg-white px-4 py-2 border-b text-left cursor-pointer">Icon</th> New Column -->
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left cursor-pointer">
                    Inventory Name

                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left cursor-pointer">
                    Unit
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left cursor-pointer">
                    Description
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left cursor-pointer">
                    Current Quantity
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left cursor-pointer">
                    History
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left cursor-pointer">
                    Created At
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left cursor-pointer">
                    Updated At
                </th>
            </tr>
        </thead>
        <tbody id="table-body">
            <?php foreach ($inventory as $index => $sub): ?>
                <!-- Main Row -->

                <tr class="bg-gray-50">
                    <?php if (current_user()['role_id'] == 1 || current_user()['role_id'] == 4): ?>
                        <td class="px-4 py-2 border-b text-left">
                            <button onclick="openEditModal(<?= $sub['excess_id'] ?>)" class="text-yellow-600 hover:underline">Edit</button> |
                            <button onclick="deleteExcessStock(<?= $sub['excess_id'] ?>)" class="text-red-600 hover:underline">Delete</button>
                        </td>
                    <?php endif; ?>

                    <td class="px-4 py-2 border-b text-left">
                        <?php if ($sub['icon'] != null): ?>
                            <img src="<?= base_url('uploads/icons/' . $sub['icon']) ?>" alt="Icon" class="w-6 h-6">
                        <?php else: ?>
                            <span>No Icon</span>
                        <?php endif; ?>
                    </td>

                    <td class="px-4 py-2 border-b text-left cursor-pointer">
                        <?= esc($sub['name']) ?>
                        <?php if (!empty($sub['subinventorydata'])): ?>
                            <button onclick="toggleSubinventory('sub<?= $index ?>')" class="text-xs text-blue-600 underline ml-2">Expand</button>
                        <?php endif; ?>
                    </td>

                    <td class="px-4 py-2 border-b text-left"><?= esc($sub['unit']) ?></td>
                    <td class="px-4 py-2 border-b text-left"><?= esc($sub['description']) ?></td>
                    <td class="px-4 py-2 border-b text-left"><?= esc($sub['quantity']) ?></td>
                    <td class="px-4 py-2 border-b text-left"><?= ($sub['history']) ?></td>
                    <td class="px-4 py-2 border-b text-left">
                        <?= ($sub['excess_created_at'] !== '0000-00-00 00:00:00' && $sub['excess_created_at'] !== '' && $sub['excess_created_at']) ? date('F j, Y h:i a', strtotime($sub['excess_created_at'])) : '' ?>
                    </td>
                    <td class="px-4 py-2 border-b text-left">
                        <?= ($sub['excess_updated_at'] !== '0000-00-00 00:00:00' && $sub['excess_updated_at'] !== '' && $sub['excess_updated_at']) ? date('F j, Y h:i a', strtotime($sub['excess_updated_at'])) : '' ?>
                    </td>
                </tr>
            <?php endforeach; ?>

        </tbody>
    </table>
</div>

<div class="flex justify-between items-center mt-4">
    <div class="text-sm text-gray-700">
        Total of <?= $totalItems ?> items | Page <?= $currentPage ?> of <?= $totalPages ?>
    </div>

    <?php
    // Get current query params and preserve them (e.g., search, order_by)
    $queryParams = $_GET;
    ?>

    <div class="flex gap-2">
        <?php if ($currentPage > 1): ?>
            <?php $queryParams['page_inventory'] = $currentPage - 1; ?>
            <a href="?<?= http_build_query($queryParams) ?>" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-600">Previous</a>
        <?php endif; ?>

        <?php
        $start = max(1, $currentPage - 1);
        $end = min($totalPages, $start + 2);
        if ($end - $start < 2 && $start > 1) {
            $start = max(1, $end - 2);
        }
        ?>

        <?php for ($i = $start; $i <= $end; $i++): ?>
            <?php $queryParams['page_inventory'] = $i; ?>
            <a href="?<?= http_build_query($queryParams) ?>"
                class="px-4 py-2 rounded <?= $i == $currentPage ? 'bg-yellow-600 text-white' : 'bg-white text-black hover:bg-yellow-600 hover:text-white' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
            <?php $queryParams['page_inventory'] = $currentPage + 1; ?>
            <a href="?<?= http_build_query($queryParams) ?>" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-600">Next</a>
        <?php endif; ?>
    </div>
</div>

<!-- Modal for Add/Edit Inventory -->
<div id="inventory-modal" class="flex fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
        <h2 class="text-xl font-bold mb-4" id="modal-title">Add Excess Stock</h2>
        <form id="inventory-form" enctype="multipart/form-data">
            <input type="hidden" id="inventory-id">

            <div class="mb-4" id="inventory-select-wrapper">
                <label for="inventory-id-select" class="block font-medium">Inventory Name</label>
                <select id="inventory-id-select" class="w-full px-3 py-2 border rounded" required>
                    <option value="">Select inventory</option>
                    <?php foreach ($inventory_list as $item): ?>
                        <option value="<?= esc($item['id']) ?>"><?= esc($item['name']) ?> - <?= $item['description'] ?> - (<?= $item['unit'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>


            <div class="mb-4">
                <label for="excess-quantity" class="block font-medium">Quantity</label>
                <input type="number" step="0.01" id="excess-quantity" class="w-full px-3 py-2 border rounded" required>
            </div>

            <div class="mb-4">
                <label for="reason-textarea" class="block font-medium">Reason</label>
                <textarea id="reason-textarea" class="w-full px-3 py-2 border rounded" rows="3" placeholder="Enter reason..." ></textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Open Modal for Add/Edit
    function deleteExcessStock(id) {
        if (!confirm('Are you sure you want to delete this excess stock?')) {
            return;
        }

        fetch(base_url + `inventory/excess/api/delete/${id}`, {
                method: 'DELETE'
            })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    alert('Deleted successfully');
                    location.reload();
                } else {
                    alert('Failed to delete: ' + (response.message || 'Unknown error'));
                }
            })
            .catch(() => {
                alert('Error occurred while deleting');
            });
    }

    function openAddModal() {
        document.getElementById('inventory-id').value = '';
        document.getElementById('excess-quantity').value = '';
        document.getElementById('modal-title').textContent = 'Add Excess Stock';
        document.getElementById('inventory-modal').classList.remove('hidden');

        if (inventorySelect) {
            inventorySelect.clear();
            inventorySelect.enable();
        }

        // Show inventory select for adding
        document.getElementById('inventory-select-wrapper').classList.remove('hidden');

    }



    function openEditModal(id) {
        fetch(base_url + `inventory/excess/api/${id}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('inventory-id').value = data.id;
                document.getElementById('excess-quantity').value = data.quantity;
                document.getElementById('reason-textarea').value = data.reason || ''; // <-- fill reason
                document.getElementById('modal-title').textContent = 'Edit Excess Stock';
                document.getElementById('inventory-modal').classList.remove('hidden');

                if (inventorySelect) {
                    inventorySelect.setValue(data.inventory_id.toString());
                    inventorySelect.disable();
                }

                document.getElementById('inventory-select-wrapper').classList.add('hidden');
            });
    }


    function closeModal() {
        document.getElementById('inventory-modal').classList.add('hidden');
    }

    // Handle Submit Form
    document.getElementById('inventory-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const id = document.getElementById('inventory-id').value;
        const inventory_id = document.getElementById('inventory-id-select').value;
        const quantity = document.getElementById('excess-quantity').value;
        const reason = document.getElementById('reason-textarea').value.trim();


        const formData = new FormData();
        formData.append('inventory_id', inventory_id);
        formData.append('quantity', quantity);
        formData.append('reason', reason);

        const url = id ? base_url + `inventory/excess/api/update/${id}` : base_url + 'inventory/excess/api/store';

        fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(() => location.reload());
    });



    // Export Inventory
    function exportInventory() {
        window.location.href = base_url + 'inventory/export';
    }

    function toggleSubinventory(id, btn) {
        // Toggle visibility of rows with matching id prefix
        document.querySelectorAll(`tr[id^="${id}"]`).forEach(row => {
            row.classList.toggle('hidden');
        });

        // Get the arrow span inside the button
        const arrowSpan = btn.querySelector('.arrow');

        // Check if any of the rows are now visible
        const anyVisible = Array.from(document.querySelectorAll(`tr[id^="${id}"]`))
            .some(row => !row.classList.contains('hidden'));

        // Update arrow symbol
        arrowSpan.innerHTML = anyVisible ? '&darr;' : '&rarr;';
    }
</script>


<?= $this->endSection() ?>