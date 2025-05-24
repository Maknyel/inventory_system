<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="flex items-center justify-between mt-6 mb-4">
    <h1 class="text-2xl font-bold">Sub Inventory Types</h1>
    <div class="flex gap-2">
        <!-- <button onclick="exportSubInventoryTypes()" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">Export</button> -->
        <button onclick="openAddModal()" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">Add Sub Inventory Type</button>
    </div>
</div>

<div class="flex flex-col h-full overflow-auto">
    <table class="min-w-full table-auto border-collapse border border-gray-300">
        <thead>
            <tr>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">Actions</th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left" >Name</th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left" >Description</th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left" >Inventory Type</th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left" >Has Purpose</th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left" >Has Distributor</th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left" >Has Return</th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left" >Created At</th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left" >Updated At</th>
            </tr>
        </thead>
        <tbody id="table-body">
            <?php foreach ($types as $type): ?>
                <tr>
                    <td class="px-4 py-2 border-b">
                        <button onclick="openEditModal(<?= $type['id'] ?>)" class="text-yellow-600 hover:underline">Edit</button>
                        |
                        <button onclick="deleteSubInventoryType(<?= $type['id'] ?>)" class="text-red-600 hover:underline">Delete</button>
                    </td>
                    <td class="px-4 py-2 border-b"><?= esc($type['name']) ?></td>
                    <td class="px-4 py-2 border-b"><?= esc($type['description']) ?></td>
                    <td class="px-4 py-2 border-b"><?= esc($type['inventory_type_name']) ?></td>

                    <td class="px-4 py-2 border-b"><?= ($type['has_purpose'] != 0)?"Yes":"No" ?> </td>
                    <td class="px-4 py-2 border-b"><?= ($type['has_distributor'] != 0)?"Yes":"No" ?> </td>
                    <td class="px-4 py-2 border-b"><?= ($type['has_reeturn'] != 0)?"Yes":"No" ?> </td>
                    <td class="px-4 py-2 border-b">
                        <?= ($type['created_at'] !== '0000-00-00 00:00:00' && $type['created_at']) ? date('F j, Y h:i a', strtotime($type['created_at'])) : '' ?>
                    </td>
                    <td class="px-4 py-2 border-b">
                        <?= ($type['updated_at'] !== '0000-00-00 00:00:00' && $type['updated_at']) ? date('F j, Y h:i a', strtotime($type['updated_at'])) : '' ?>
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
    <div class="flex gap-2">
        <?php if ($currentPage > 1): ?>
            <a href="?page_sub_inventory_type=<?= $currentPage - 1 ?>" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-600">Previous</a>
        <?php endif; ?>

        <?php
            $start = max(1, $currentPage - 1);
            $end = min($totalPages, $start + 2);
            if ($end - $start < 2 && $start > 1) {
                $start = max(1, $end - 2);
            }
        ?>

        <?php for ($i = $start; $i <= $end; $i++): ?>
            <a href="?page_sub_inventory_type=<?= $i ?>" class="px-4 py-2 rounded <?= $i == $currentPage ? 'bg-yellow-600 text-white' : 'bg-white text-black hover:bg-yellow-600 hover:text-white' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
            <a href="?page_sub_inventory_type=<?= $currentPage + 1 ?>" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-600">Next</a>
        <?php endif; ?>
    </div>
</div>

<!-- Modal -->
<div id="subinventorytype-modal" class="fixed flex inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
        <h2 class="text-xl font-bold mb-4" id="modal-title">Add Sub Inventory Type</h2>
        <form id="subinventorytype-form">
            <input type="hidden" id="subinventorytype-id">
            <div class="mb-4">
                <label for="subinventorytype-name" class="block font-medium">Name</label>
                <input type="text" id="subinventorytype-name" class="w-full px-3 py-2 border rounded">
            </div>
            <div class="mb-4">
                <label for="subinventorytype-description" class="block font-medium">Description</label>
                <textarea id="subinventorytype-description" class="w-full px-3 py-2 border rounded"></textarea>
            </div>
            <div class="mb-4">
                <label for="subinventorytype-inventorytype" class="block font-medium">Inventory Type</label>
                <select id="subinventorytype-inventorytype" class="w-full px-3 py-2 border rounded" required>
                    <?php foreach ($inventory_type as $type): ?>
                        <option value="<?= esc($type['id']) ?>">
                            <?= ($type['name']) ?>
                        </option>
                    <?php endforeach; ?>    
                </select>
            </div>

            <div class="mb-4">
                <label for="subinventorytype-has_purpose" class="block font-medium">Has Purpose</label>
                <select id="subinventorytype-has_purpose" class="w-full px-3 py-2 border rounded" required>
                    <option value="1">Yes</option>
                    <option value="0">No</option>    
                </select>
            </div>

            <div class="mb-4">
                <label for="subinventorytype-has_distributor" class="block font-medium">Has Distributor</label>
                <select id="subinventorytype-has_distributor" class="w-full px-3 py-2 border rounded" required>
                    <option value="1">Yes</option>
                    <option value="0">No</option>    
                </select>
            </div>

            <div class="mb-4">
                <label for="subinventorytype-has_reeturn" class="block font-medium">Has Return</label>
                <select id="subinventorytype-has_reeturn" class="w-full px-3 py-2 border rounded" required>
                    <option value="1">Yes</option>
                    <option value="0">No</option>    
                </select>
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('subinventorytype-id').value = '';
        document.getElementById('subinventorytype-name').value = '';
        document.getElementById('subinventorytype-inventorytype').value = '';
        document.getElementById('subinventorytype-has_purpose').value = 0;
        document.getElementById('subinventorytype-has_distributor').value = 0;
        document.getElementById('subinventorytype-has_reeturn').value = 0;
        document.getElementById('subinventorytype-description').value = '';
        document.getElementById('modal-title').textContent = 'Add Sub Inventory Type';
        document.getElementById('subinventorytype-modal').classList.remove('hidden');
    }

    function openEditModal(id) {
        fetch(base_url + `subinventorytype/api/${id}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('subinventorytype-id').value = data.id;
                document.getElementById('subinventorytype-name').value = data.name;
                document.getElementById('subinventorytype-inventorytype').value = data.inventory_type_id;
                document.getElementById('subinventorytype-has_purpose').value = data.has_purpose;
                document.getElementById('subinventorytype-has_distributor').value = data.has_distributor;
                document.getElementById('subinventorytype-has_reeturn').value = data.has_reeturn;
                
                document.getElementById('subinventorytype-description').value = data.description;
                document.getElementById('modal-title').textContent = 'Edit Sub Inventory Type';
                document.getElementById('subinventorytype-modal').classList.remove('hidden');
            });
    }

    function closeModal() {
        document.getElementById('subinventorytype-modal').classList.add('hidden');
    }

    document.getElementById('subinventorytype-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('subinventorytype-id').value;
        const name = document.getElementById('subinventorytype-name').value;
        const inventory_type_id = document.getElementById('subinventorytype-inventorytype').value;
        const has_purpose = document.getElementById('subinventorytype-has_purpose').value;
        const has_distributor = document.getElementById('subinventorytype-has_distributor').value;
        const has_reeturn = document.getElementById('subinventorytype-has_reeturn').value;
        const description = document.getElementById('subinventorytype-description').value;
        const url = id ? base_url + `subinventorytype/api/update/${id}` : base_url + 'subinventorytype/api/store';

        fetch(url, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ name, inventory_type_id, has_purpose, has_distributor, has_reeturn, description })
        })
        .then(res => res.json())
        .then(() => location.reload());
    });

    function deleteSubInventoryType(id) {
        if (!confirm('Are you sure?')) return;
        fetch(base_url + `subinventorytype/api/delete/${id}`, { method: 'DELETE' })
            .then(() => location.reload());
    }

    function exportSubInventoryTypes() {
        window.location.href = base_url + 'subinventorytype/export';
    }

    
</script>

<?= $this->endSection() ?>
