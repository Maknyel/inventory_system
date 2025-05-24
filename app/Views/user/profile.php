<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- 1. Edit User Info -->
    <div class="bg-white p-6 shadow rounded">
        <h3 class="text-xl font-bold mb-4">Edit User Info</h3>
        <form id="editUserForm" class="space-y-4">
            <input type="hidden" name="id" value="<?= esc($user['id']) ?>">

            <div>
                <label class="block text-sm font-medium">Name</label>
                <input type="text" name="name" value="<?= esc($user['name']) ?>" class="w-full p-2 border rounded" required>
            </div>

            <div>
                <label class="block text-sm font-medium">Email</label>
                <input type="email" name="email" value="<?= esc($user['email']) ?>" class="w-full p-2 border rounded" required>
            </div>

            <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-600">
                Save Changes
            </button>

            <p id="editUserMsg" class="text-sm mt-2"></p>
        </form>
    </div>

    <!-- 2. Change Profile Image -->
    <div class="bg-white p-6 shadow rounded">
        <h3 class="text-xl font-bold mb-4">Change Profile Image</h3>

        <!-- Current Profile Image Preview -->
        <?php if (!empty($user['image_url'])): ?>
            <div class="mb-4">
                <img src="<?= base_url(esc($user['image_url'])) ?>" 
                    alt="Profile Image" 
                    class="w-32 h-32 object-cover rounded-full mx-auto border" />
            </div>
        <?php endif; ?>
        
        <form id="uploadImageForm" class="space-y-4" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= esc($user['id']) ?>">

            <div>
                <label class="block text-sm font-medium">Select Image</label>
                <input type="file" name="image" accept="image/*" class="w-full p-2 border rounded" required>
            </div>

            <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                Upload Image
            </button>

            <p id="uploadImageMsg" class="text-sm mt-2"></p>
        </form>
    </div>

    <!-- 3. Edit Password -->
    <div class="bg-white p-6 shadow rounded">
        <h3 class="text-xl font-bold mb-4">Change Password</h3>
        <form id="changePasswordForm" class="space-y-4">
            <input type="hidden" name="id" value="<?= esc($user['id']) ?>">

            <div>
                <label class="block text-sm font-medium">Current Password</label>
                <input type="password" name="current_password" class="w-full p-2 border rounded" required>
            </div>

            <div>
                <label class="block text-sm font-medium">New Password</label>
                <input type="password" name="new_password" class="w-full p-2 border rounded" required>
            </div>

            <div>
                <label class="block text-sm font-medium">Confirm New Password</label>
                <input type="password" name="confirm_password" class="w-full p-2 border rounded" required>
            </div>

            <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                Update Password
            </button>

            <p id="changePasswordMsg" class="text-sm mt-2"></p>
        </form>
    </div>

</div>

<!-- JavaScript with fetch -->
<script>
document.getElementById('editUserForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    const res = await fetch('<?= base_url('profile/update') ?>', {
        method: 'POST',
        body: formData,
    });

    const result = await res.json();
    const msg = document.getElementById('editUserMsg');
    msg.textContent = result.message;
    msg.className = result.status === 'success' ? 'text-yellow-600' : 'text-red-600';
});

document.getElementById('uploadImageForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    const res = await fetch('<?= base_url('profile/uploadImage') ?>', {
        method: 'POST',
        body: formData,
    });

    const result = await res.json();
    const msg = document.getElementById('uploadImageMsg');
    msg.textContent = result.message;
    msg.className = result.status === 'success' ? 'text-yellow-600' : 'text-red-600';
    setTimeout(() => {
        location.reload();
    }, 1000);
});

document.getElementById('changePasswordForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    const res = await fetch('<?= base_url('profile/changePassword') ?>', {
        method: 'POST',
        body: formData,
    });

    const result = await res.json();
    const msg = document.getElementById('changePasswordMsg');
    msg.textContent = result.message;
    msg.className = result.status === 'success' ? 'text-yellow-600' : 'text-red-600';
});
</script>

<?= $this->endSection() ?>
