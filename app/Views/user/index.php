<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="flex items-center justify-between mt-6 mb-4">
    <h1 class="text-2xl font-bold">User Management</h1>
    <button onclick="openAddModal()" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">Add User</button>
</div>

<div class="flex flex-col h-full overflow-auto">
    <table class="min-w-full table-auto border-collapse border border-gray-300">
        <thead>
            <tr>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">Actions</th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">Name</th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">Email</th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">Role</th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">Created At</th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">Updated At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td class="px-4 py-2 border-b">
                        <?php if(($user['role_id'] != 1 && current_user()['role_id'] == 1) || (current_user()['role_id'] == 4)){ ?>
                            <button onclick="openEditModal(<?= $user['id'] ?>)" class="text-yellow-600 hover:underline">Edit</button> |
                            <button onclick="deleteUser(<?= $user['id'] ?>)" class="text-red-600 hover:underline">Delete</button>
                        <?php } ?>
                    </td>
                    <td class="px-4 py-2 border-b"><?= esc($user['name']) ?></td>
                    <td class="px-4 py-2 border-b"><?= esc($user['email']) ?></td>
                    <td class="px-4 py-2 border-b"><?= esc($user['role_name']) ?></td>
                    <td class="px-4 py-2 border-b"><?= date('F j, Y h:i a', strtotime($user['created_at'])) ?></td>
                    <td class="px-4 py-2 border-b"><?= date('F j, Y h:i a', strtotime($user['updated_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div id="user-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-md mx-auto">
        <h2 id="modal-title" class="text-xl font-bold mb-4">Add User</h2>
        <form id="user-form">
            <input type="hidden" id="user-id">

            <div class="mb-4">
                <label class="block font-medium">Name</label>
                <input type="text" id="user-name" class="w-full px-3 py-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label class="block font-medium">Email</label>
                <input type="email" id="user-email" class="w-full px-3 py-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label class="block font-medium">Password</label>
                <input type="password" id="user-password" class="w-full px-3 py-2 border rounded">
                <small class="text-gray-500">Leave blank to keep current password (on edit)</small>
            </div>
            <div class="mb-4">
                <label class="block font-medium">Role</label>
                <select id="user-role" class="w-full px-3 py-2 border rounded" required>

                </select>
            </div>
            <div class="mb-4 hidden">
                <label class="block font-medium">Profile Image (URL)</label>
                <input type="text" id="user-image" class="w-full px-3 py-2 border rounded">
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
        document.getElementById('user-form').reset();
        document.getElementById('user-id').value = '';
        document.getElementById('modal-title').textContent = 'Add User';
        document.getElementById('user-modal').classList.remove('hidden');
        loadRolesDropdown();
    }

    function openEditModal(id) {
        fetch(`${base_url}users/api/${id}`)
            .then(res => res.json())
            .then(user => {
                document.getElementById('user-id').value = user.id;
                document.getElementById('user-name').value = user.name;
                document.getElementById('user-email').value = user.email;
                document.getElementById('user-role').value = user.role_id;
                document.getElementById('user-image').value = user.image_url;
                loadRolesDropdown(user.role_id);
                document.getElementById('modal-title').textContent = 'Edit User';
                document.getElementById('user-modal').classList.remove('hidden');
            });
    }

    function closeModal() {
        document.getElementById('user-modal').classList.add('hidden');
    }

    document.getElementById('user-form').addEventListener('submit', function (e) {
        e.preventDefault();
        const id = document.getElementById('user-id').value;
        const payload = {
            name: document.getElementById('user-name').value,
            email: document.getElementById('user-email').value,
            password: document.getElementById('user-password').value,
            role_id: document.getElementById('user-role').value,
            image_url: document.getElementById('user-image').value,
        };

        const url = id ? `${base_url}users/api/update/${id}` : `${base_url}users/api/store`;

        fetch(url, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        }).then(res => res.json())
          .then(() => location.reload());
    });

    function deleteUser(id) {
        if (!confirm('Are you sure you want to delete this user?')) return;
        fetch(`${base_url}users/api/delete/${id}`, {
            method: 'DELETE',
        }).then(() => location.reload());
    }
    

    function loadRolesDropdown(selectedId = null) {
        fetch(base_url + 'api/roles')
            .then(res => res.json())
            .then(roles => {
                const select = document.getElementById('user-role');
                select.innerHTML = ''; // clear options

                roles.forEach(role => {
                    const option = document.createElement('option');
                    option.value = role.id;
                    option.textContent = role.name;
                    if (selectedId && selectedId == role.id) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });
            });
    }
</script>

<?= $this->endSection() ?>
