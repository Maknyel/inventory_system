<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

    <div class="flex items-center justify-between mt-6 mb-4">
        <h1 class="text-2xl font-bold">Distributor List</h1>
        <div class="flex gap-2">
            <!-- <button onclick="exportDitributor()" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-600">
                Export Distributors
            </button> -->
            <button onclick="openAddModal()" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">Add Distributor</button>
        </div>
    </div>
    <div class="flex flex-col h-full overflow-auto">
        <table class="min-w-full table-auto border-collapse border border-gray-300">
            <thead>
                <tr>
                    <th class="sticky top-0 bg-white px-4 py-2 border-b text-left cursor-pointer">Option</th>
                    <th class="sticky top-0 bg-white px-4 py-2 border-b text-left cursor-pointer">Distributor Name</th>
                    <th class="sticky top-0 bg-white px-4 py-2 border-b text-left cursor-pointer">Description</th>
                    <th class="sticky top-0 bg-white px-4 py-2 border-b text-left cursor-pointer">Type</th>
                    <th class="sticky top-0 bg-white px-4 py-2 border-b text-left cursor-pointer">Created At</th>
                    <th class="sticky top-0 bg-white px-4 py-2 border-b text-left cursor-pointer">Updated At</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <?php foreach ($distributors as $distributor): ?>
                
                    <tr>
                        <td class="px-4 py-2 border-b text-left">
                            <button onclick="openEditModal(<?= $distributor['id'] ?>)" class="text-yellow-600 hover:underline">Edit</button>
                            |
                            <button onclick="deleteDistributor(<?= $distributor['id'] ?>)" class="text-red-600 hover:underline">Delete</button>
                        </td>
                        <td class="px-4 py-2 border-b text-left cursor-pointer">
                            <?=$distributor['name']?>
                        </td>
                        <td class="px-4 py-2 border-b text-left cursor-pointer">
                            <?=$distributor['description']?>
                        </td>
                        <td class="px-4 py-2 border-b text-left cursor-pointer">
                            <?=$distributor['type']?>
                        </td>
                        <td class="px-4 py-2 border-b text-left cursor-pointer">
                            <?php if($distributor['created_at'] != '0000-00-00 00:00:00'){ ?>
                                <?=date('F j, Y h:i a', strtotime($distributor['created_at']))?>
                            <?php } ?>
                        </td>
                        <td class="px-4 py-2 border-b text-left cursor-pointer">
                            <?php if($distributor['updated_at'] != '0000-00-00 00:00:00'){ ?>
                                <?=date('F j, Y h:i a', strtotime($distributor['updated_at']))?>
                            <?php } ?>
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
                <a href="?page_distributor=<?= $currentPage - 1 ?>" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-600">Previous</a>
            <?php endif; ?>

            <?php
                // Range logic for pagination numbers (max 3 shown)
                $start = max(1, $currentPage - 1);
                $end = min($totalPages, $start + 2);

                // Adjust if at the end
                if ($end - $start < 2 && $start > 1) {
                    $start = max(1, $end - 2);
                }
            ?>

            <?php for ($i = $start; $i <= $end; $i++): ?>
                <a href="?page_distributor=<?= $i ?>"
                class="px-4 py-2 rounded <?= $i == $currentPage ? 'bg-yellow-600 text-white' : 'bg-white text-black hover:bg-yellow-600 hover:text-white' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
                <a href="?page_distributor=<?= $currentPage + 1 ?>" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-600">Next</a>
            <?php endif; ?>
        </div>
    </div>
    







    <!-- modal -->
    <!-- Add/Edit Modal -->
    <div id="distributor-modal" class="flex fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
            <h2 class="text-xl font-bold mb-4" id="modal-title">Add Distributor</h2>
            <form id="distributor-form">
                <input type="hidden" id="distributor-id">
                <div class="mb-4">
                    <label for="distributor-name" class="block font-medium">Name</label>
                    <input type="text" id="distributor-name" class="w-full px-3 py-2 border rounded">
                </div>
                <div class="mb-4">
                    <label for="distributor-description" class="block font-medium">Description</label>
                    <textarea id="distributor-description" class="w-full px-3 py-2 border rounded"></textarea>
                </div>
                <div class="mb-4">
                    <label for="distributor-type" class="block font-medium">Type</label>
                    <select id="distributor-type" class="w-full px-3 py-2 border rounded">
                        <option value=""></option>
                        <option value="Online">Online</option>
                        <option value="Distributor">Distributor</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded">Save</button>
                </div>
            </form>
        </div>
    </div>


    <!-- end of modal -->


<script>
    

    function openAddModal() {
        document.getElementById('distributor-id').value = '';
        document.getElementById('distributor-name').value = '';
        document.getElementById('distributor-type').value = '';
        document.getElementById('distributor-description').value = '';
        document.getElementById('modal-title').textContent = 'Add Distributor';
        document.getElementById('distributor-modal').classList.remove('hidden');
    }

    function openEditModal(id) {
        fetch(base_url+`inventorydistributor/api/${id}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('distributor-id').value = data.id;
                document.getElementById('distributor-name').value = data.name;
                document.getElementById('distributor-type').value = data.type;
                document.getElementById('distributor-description').value = data.description;
                document.getElementById('modal-title').textContent = 'Edit Distributor';
                document.getElementById('distributor-modal').classList.remove('hidden');
            });
    }

    function closeModal() {
        document.getElementById('distributor-modal').classList.add('hidden');
    }

    document.getElementById('distributor-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const id = document.getElementById('distributor-id').value;
        const name = document.getElementById('distributor-name').value;
        const type = document.getElementById('distributor-type').value;
        const description = document.getElementById('distributor-description').value;

        const url = id ? base_url+`inventorydistributor/api/update/${id}` : base_url+'inventorydistributor/api/store';
        const method = id ? 'POST' : 'POST';

        fetch(url, {
            method: method,
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ name, type, description })
        })
        .then(res => res.json())
        .then(() => location.reload());
    });

    function deleteDistributor(id) {
        if (!confirm('Are you sure?')) return;
        fetch(base_url+`inventorydistributor/api/delete/${id}`, { method: 'DELETE' })
            .then(() => location.reload());
    }
    function exportDitributor() {
        window.location.href = base_url + 'inventorydistributor/export';
    }
</script>

<?= $this->endSection() ?>