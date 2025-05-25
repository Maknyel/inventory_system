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
      <span class="text-black"><?=($sub_inventory_type_parse['name'])?></span>
    </li>
  </ol>
</nav>

<div class="flex items-center justify-between mt-6 mb-4">
    <h1 class="text-2xl font-bold">Inventory List</h1>
    <div class="flex items-center gap-2">
        <a href="<?= base_url('inventory_in?inventory_type=' . $inventory_type_parse['id'] . '&sub_inventory_type=' . $sub_inventory_type_parse['id']) ?>"
            class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
            IN
        </a>
        <a href="<?= base_url('inventory_out?inventory_type=' . $inventory_type_parse['id'] . '&sub_inventory_type=' . $sub_inventory_type_parse['id']) ?>"
            class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition">
            OUT
        </a>
        <a href="<?= base_url('inventory_out_pos?inventory_type=' . $inventory_type_parse['id'] . '&sub_inventory_type=' . $sub_inventory_type_parse['id']) ?>"
            class="px-3 py-1 bg-black text-white rounded hover:bg-black transition">
            POS
        </a>
        <?php if($sub_inventory_type_parse['has_reeturn'] != 0){ ?>
            <a href="<?= base_url('inventory_return?inventory_type=' . $inventory_type_parse['id'] . '&sub_inventory_type=' . $sub_inventory_type_parse['id']) ?>"
                class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">
                Return
            </a>
            <a href="<?= base_url('inventory_return_history?inventory_type=' . $inventory_type_parse['id'] . '&sub_inventory_type=' . $sub_inventory_type_parse['id']) ?>"
                class="px-3 py-1 bg-white text-black rounded hover:bg-white transition">
                Return History
            </a>
        <?php } ?>
        <a href="<?= base_url('inventory_history_filter?inventory_type=' . $inventory_type_parse['id'] . '&sub_inventory_type=' . $sub_inventory_type_parse['id']) ?>"
            class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 transition">
            History
        </a>
    </div>
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
                    <a href="?search=<?= esc($search) ?>&orderby=inventory.name&orderdir=<?= $orderby == 'inventory.name' && $orderdir == 'asc' ? 'desc' : 'asc' ?>">
                        Inventory Name
                        <i class="ion-<?= ($orderby == 'inventory.name' && $orderdir == 'asc') ? 'arrow-up-b' : 'arrow-down-b' ?> <?= $orderby == 'inventory.name' ? 'text-black' : 'text-gray-400' ?>"></i>
                    </a>
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left cursor-pointer">
                    <a href="?search=<?= esc($search) ?>&orderby=inventory.unit&orderdir=<?= $orderby == 'inventory.unit' && $orderdir == 'asc' ? 'desc' : 'asc' ?>">
                        Unit
                        <i class="ion-<?= ($orderby == 'inventory.unit' && $orderdir == 'asc') ? 'arrow-up-b' : 'arrow-down-b' ?> <?= $orderby == 'inventory.unit' ? 'text-black' : 'text-gray-400' ?>"></i>
                    </a>
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left cursor-pointer">
                    <a href="?search=<?= esc($search) ?>&orderby=inventory.description&orderdir=<?= $orderby == 'inventory.description' && $orderdir == 'asc' ? 'desc' : 'asc' ?>">
                        Description
                        <i class="ion-<?= ($orderby == 'inventory.description' && $orderdir == 'asc') ? 'arrow-up-b' : 'arrow-down-b' ?> <?= $orderby == 'inventory.description' ? 'text-black' : 'text-gray-400' ?>"></i>
                    </a>
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left cursor-pointer">
                    <a href="?search=<?= esc($search) ?>&orderby=inventory.reordering_level&orderdir=<?= $orderby == 'inventory.reordering_level' && $orderdir == 'asc' ? 'desc' : 'asc' ?>">
                        Reordering Level
                        <i class="ion-<?= ($orderby == 'inventory.reordering_level' && $orderdir == 'asc') ? 'arrow-up-b' : 'arrow-down-b' ?> <?= $orderby == 'inventory.reordering_level' ? 'text-black' : 'text-gray-400' ?>"></i>
                        
                    </a>
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left cursor-pointer">
                    <a href="?search=<?= esc($search) ?>&orderby=inventory.current_quantity&orderdir=<?= $orderby == 'inventory.current_quantity' && $orderdir == 'asc' ? 'desc' : 'asc' ?>">
                        Current Quantity
                        <i class="ion-<?= ($orderby == 'inventory.current_quantity' && $orderdir == 'asc') ? 'arrow-up-b' : 'arrow-down-b' ?> <?= $orderby == 'inventory.current_quantity' ? 'text-black' : 'text-gray-400' ?>"></i>
                        
                    </a>
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left cursor-pointer">
                    <a href="?search=<?= esc($search) ?>&orderby=inventory.current_price&orderdir=<?= $orderby == 'inventory.current_price' && $orderdir == 'asc' ? 'desc' : 'asc' ?>">
                        Current Price
                        <i class="ion-<?= ($orderby == 'inventory.current_price' && $orderdir == 'asc') ? 'arrow-up-b' : 'arrow-down-b' ?> <?= $orderby == 'inventory.current_price' ? 'text-black' : 'text-gray-400' ?>"></i>
                        
                    </a>
                    
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left cursor-pointer">
                    <a href="?search=<?= esc($search) ?>&orderby=inventory.created_at&orderdir=<?= $orderby == 'inventory.created_at' && $orderdir == 'asc' ? 'desc' : 'asc' ?>">
                        Created At
                        <i class="ion-<?= ($orderby == 'inventory.created_at' && $orderdir == 'asc') ? 'arrow-up-b' : 'arrow-down-b' ?> <?= $orderby == 'inventory.created_at' ? 'text-black' : 'text-gray-400' ?>"></i>
                        
                    </a>
                    
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left cursor-pointer">
                    <a href="?search=<?= esc($search) ?>&orderby=inventory.updated_at&orderdir=<?= $orderby == 'inventory.updated_at' && $orderdir == 'asc' ? 'desc' : 'asc' ?>">
                        Updated At
                        <i class="ion-<?= ($orderby == 'inventory.updated_at' && $orderdir == 'asc') ? 'arrow-up-b' : 'arrow-down-b' ?> <?= $orderby == 'inventory.updated_at' ? 'text-black' : 'text-gray-400' ?>"></i>                        
                    </a>
                    
                </th>
            </tr>
        </thead>
        <tbody id="table-body">
            <?php foreach ($inventory as $item): ?>
                <tr>
                    
                    <?php if (current_user()['role_id'] == 1 || current_user()['role_id'] == 4) { ?>
                        <td class="px-4 py-2 border-b text-left">
                            <button onclick="openEditModal(<?= $item['id'] ?>)" class="text-yellow-600 hover:underline">Edit</button> |
                            <button onclick="deleteInventory(<?= $item['id'] ?>)" class="text-red-600 hover:underline">Delete</button>
                        </td>
                    <?php } ?>
                    
                    <td class="px-4 py-2 border-b text-left">
                        <?php if ($item['icon'] != null){ ?>
                            <img src="<?= base_url('uploads/icons/'.$item['icon']) ?>" alt="Icon" class="w-6 h-6">
                        <?php }else{ ?>
                            <span>No Icon</span>
                        <?php } ?>
                    </td>
                    <td class="px-4 py-2 border-b text-left cursor-pointer"><?= $item['name'] ?></td>
                    <td class="px-4 py-2 border-b text-left cursor-pointer"><?= $item['unit'] ?></td>
                    <td class="px-4 py-2 border-b text-left cursor-pointer"><?= $item['description'] ?></td>
                    <td class="px-4 py-2 border-b text-left cursor-pointer"><?= $item['reordering_level'] ?></td>
                    <td class="px-4 py-2 border-b text-left cursor-pointer"><?= $item['current_quantity'] ?></td>
                    <td class="px-4 py-2 border-b text-left cursor-pointer"><?= $item['current_price'] ?></td>
                    <td class="px-4 py-2 border-b text-left cursor-pointer">
                        <?php if ($item['created_at'] != '0000-00-00 00:00:00'): ?>
                            <?= date('F j, Y h:i a', strtotime($item['created_at'])) ?>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-2 border-b text-left cursor-pointer">
                        <?php if ($item['updated_at'] != '0000-00-00 00:00:00'): ?>
                            <?= date('F j, Y h:i a', strtotime($item['updated_at'])) ?>
                        <?php endif; ?>
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
        <h2 class="text-xl font-bold mb-4" id="modal-title">Add Inventory</h2>
        <div class="flex flex-col">
            <label for="file">Choose File:</label>
            <input type="file" name="file" id="file" accept=".png,.jpg,.jpeg,.svg">
        </div>
        <form id="inventory-form" enctype="multipart/form-data"> <!-- Added enctype -->
            <input type="hidden" id="inventory-id">
            <input type="hidden" id="inventory-icon" name="inventory-icon">
            <!-- Icon Upload -->
            <!-- <div class="mb-4 hidden">
                <label for="inventory-icon" class="block font-medium">Icon</label>
                <input type="file" id="inventory-icon" name="inventory-icon" class="w-full px-3 py-2 border rounded">
            </div> -->

            <div class="mb-4">
                <label for="inventory-name" class="block font-medium">Name</label>
                <input type="text" id="inventory-name" class="w-full px-3 py-2 border rounded">
            </div>
            <div class="mb-4">
                <label for="inventory-unit" class="block font-medium">Unit</label>
                <input type="text" id="inventory-unit" class="w-full px-3 py-2 border rounded">
            </div>
            <div class="mb-4">
                <label for="inventory-description" class="block font-medium">Description</label>
                <textarea id="inventory-description" class="w-full px-3 py-2 border rounded"></textarea>
            </div>
            <div class="mb-4">
                <label for="inventory-reordering-level" class="block font-medium">Reordering Level</label>
                <input type="number" id="inventory-reordering-level" class="w-full px-3 py-2 border rounded">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('file').addEventListener('change', function () {
        const fileInput = this;
        const formData = new FormData();
        formData.append('file', fileInput.files[0]);

        fetch("<?= base_url('upload/doUpload') ?>", {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Set the hidden input value with the uploaded file name
                document.getElementById('inventory-icon').value = data.filename;

                alert('File uploaded successfully!');
            } else {
                alert('Upload failed: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error uploading file:', error);
            alert('An unexpected error occurred.');
        });
    });
    // Open Modal for Add/Edit
    function openAddModal() {
        document.getElementById('inventory-id').value = '';
        document.getElementById('inventory-name').value = '';
        document.getElementById('inventory-unit').value = '';
        document.getElementById('inventory-icon').value = '';
        
        document.getElementById('inventory-description').value = '';
        document.getElementById('inventory-reordering-level').value = '';
        document.getElementById('inventory-icon').value = ''; // Reset icon input
        document.getElementById('modal-title').textContent = 'Add Inventory';
        document.getElementById('inventory-modal').classList.remove('hidden');
    }

    function openEditModal(id) {
        fetch(base_url + `inventory/api/${id}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('inventory-id').value = data.id;
                document.getElementById('inventory-name').value = data.name;
                document.getElementById('inventory-unit').value = data.unit;
                document.getElementById('inventory-icon').value = data.icon;
                document.getElementById('inventory-description').value = data.description;
                document.getElementById('inventory-reordering-level').value = data.reordering_level;
                document.getElementById('modal-title').textContent = 'Edit Inventory';
                document.getElementById('inventory-modal').classList.remove('hidden');
            });
    }

    function closeModal() {
        document.getElementById('inventory-modal').classList.add('hidden');
    }

    // Handle Submit Form
    document.getElementById('inventory-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const id = document.getElementById('inventory-id').value;
        const name = document.getElementById('inventory-name').value;
        const unit = document.getElementById('inventory-unit').value;
        const icon = document.getElementById('inventory-icon').value;
        const description = document.getElementById('inventory-description').value;
        const inventory_type = `<?=$inventory_type_parse['id']?>`;
        const sub_inventory_type = `<?=$sub_inventory_type_parse['id']?>`;
        const reordering_level = document.getElementById('inventory-reordering-level').value;
        
        const formData = new FormData();
        formData.append('name', name);
        formData.append('unit', unit);
        formData.append('icon', icon);
        formData.append('description', description);
        formData.append('inventory_type', inventory_type);
        formData.append('sub_inventory_type', sub_inventory_type);
        formData.append('reordering_level', reordering_level);
        
        const url = id ? base_url + `inventory/api/update/${id}` : base_url + 'inventory/api/store';
        const method = id ? 'POST' : 'POST';

        fetch(url, {
            method: method,
            body: formData
        })
        .then(res => res.json())
        .then(() => location.reload());
    });

    // Delete Inventory Item
    function deleteInventory(id) {
        if (!confirm('Are you sure?')) return;
        fetch(base_url + `inventory/api/delete/${id}`, { method: 'DELETE' })
            .then(() => location.reload());
    }

    // Export Inventory
    function exportInventory() {
        window.location.href = base_url + 'inventory/export';
    }
</script>

<?= $this->endSection() ?>
