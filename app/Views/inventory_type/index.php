<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="flex items-center justify-between mt-6 mb-4">
    <h1 class="text-2xl font-bold">Inventory Types</h1>
    <div class="flex gap-2">
        <!-- <button onclick="exportInventoryTypes()" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">Export</button> -->
        <button onclick="openAddModal()" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">Add Inventory Type</button>
    </div>
</div>

<div class="flex flex-col h-full overflow-auto">
    <table class="min-w-full table-auto border-collapse border border-gray-300">
        <thead>
            <tr>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">Actions</th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left" >Name</th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left" >Description</th>
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
                        <button onclick="deleteInventoryType(<?= $type['id'] ?>)" class="text-red-600 hover:underline">Delete</button>
                    </td>
                    <td class="px-4 py-2 border-b"><?= esc($type['name']) ?></td>
                    <td class="px-4 py-2 border-b"><?= esc($type['description']) ?></td>
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
            <a href="?page_inventory_type=<?= $currentPage - 1 ?>" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-600">Previous</a>
        <?php endif; ?>

        <?php
            $start = max(1, $currentPage - 1);
            $end = min($totalPages, $start + 2);
            if ($end - $start < 2 && $start > 1) {
                $start = max(1, $end - 2);
            }
        ?>

        <?php for ($i = $start; $i <= $end; $i++): ?>
            <a href="?page_inventory_type=<?= $i ?>" class="px-4 py-2 rounded <?= $i == $currentPage ? 'bg-yellow-600 text-white' : 'bg-white text-black hover:bg-yellow-600 hover:text-white' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
            <a href="?page_inventory_type=<?= $currentPage + 1 ?>" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-600">Next</a>
        <?php endif; ?>
    </div>
</div>

<!-- Modal -->
<div id="inventorytype-modal" class="fixed flex inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
        <h2 class="text-xl font-bold mb-4" id="modal-title">Add Inventory Type</h2>
        <form id="inventorytype-form">
            <input type="hidden" id="inventorytype-id">
            <div class="mb-4">
                <label for="inventorytype-name" class="block font-medium">Name</label>
                <input type="text" id="inventorytype-name" class="w-full px-3 py-2 border rounded">
            </div>
            <div class="mb-4">
                <label for="inventorytype-description" class="block font-medium">Description</label>
                <textarea id="inventorytype-description" class="w-full px-3 py-2 border rounded"></textarea>
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
        document.getElementById('inventorytype-id').value = '';
        document.getElementById('inventorytype-name').value = '';
        document.getElementById('inventorytype-description').value = '';
        document.getElementById('modal-title').textContent = 'Add Inventory Type';
        document.getElementById('inventorytype-modal').classList.remove('hidden');
    }

    function openEditModal(id) {
        fetch(base_url + `inventorytype/api/${id}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('inventorytype-id').value = data.id;
                document.getElementById('inventorytype-name').value = data.name;
                document.getElementById('inventorytype-description').value = data.description;
                document.getElementById('modal-title').textContent = 'Edit Inventory Type';
                document.getElementById('inventorytype-modal').classList.remove('hidden');
            });
    }

    function closeModal() {
        document.getElementById('inventorytype-modal').classList.add('hidden');
    }

    document.getElementById('inventorytype-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('inventorytype-id').value;
        const name = document.getElementById('inventorytype-name').value;
        const description = document.getElementById('inventorytype-description').value;
        const url = id ? base_url + `inventorytype/api/update/${id}` : base_url + 'inventorytype/api/store';

        fetch(url, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ name, description })
        })
        .then(res => res.json())
        .then(() => location.reload());
    });

    function deleteInventoryType(id) {
        if (!confirm('Are you sure?')) return;
        fetch(base_url + `inventorytype/api/delete/${id}`, { method: 'DELETE' })
            .then(() => location.reload());
    }

    function exportInventoryTypes() {
        window.location.href = base_url + 'inventorytype/export';
    }

    
</script>

<?= $this->endSection() ?>
