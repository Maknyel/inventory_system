<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= global_name() ?></title>
    <link rel="icon" href="<?=base_url('public/images/logo.png')?>" type="image/x-icon">
    <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<script>
    var base_url = `<?=base_url()?>`;
</script>
<body class="flex flex-col h-screen overflow-hidden bg-gray-100 text-gray-900">

    <!-- Header -->
    <header class="bg-black
 text-white p-4">
        <div class="flex items-center justify-start gap-4">
            <!-- Logo or Site Title -->
            
            <div class="flex w-full justify-between items-center">
            <!-- Hamburger Menu for Small Screens -->
                <button id="hamburger" class=" px-2 py-1 text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h1 class="text-xl font-bold"><?= global_name() ?></h1>

                <div class="">
                    <div class="relative inline-block text-left">
                        <!-- Trigger -->
                        <button id="userMenuButton" class="flex items-center space-x-2 focus:outline-none">
                            <?php $user = current_user(); ?>
                            <?php
                                $name = $user['name'] ?? '';
                                $initials = '';
                                if (!empty($name)) {
                                    $parts = explode(' ', $name);
                                    $initials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
                                }
                            ?>
                            <img 
                                src="<?= !empty($user['image_url']) ? base_url(esc($user['image_url'])) : generateInitialsImage($user['name']) ?>"
                                onerror="this.onerror=null; this.src='<?= base_url('public/images/logo.png') ?>';" 
                                alt="Profile Image" 
                                class="w-10 h-10 object-cover rounded-full border"
                            >
                            <span class="font-medium hidden md:inline"><?= esc($user['name']) ?></span>
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="userMenuDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white border rounded shadow z-50">
                            <a href="<?= base_url('profile') ?>" class="block px-4 text-black py-2 text-sm hover:bg-gray-100">Profile</a>
                            <form action="<?= base_url('logout') ?>" method="get">
                                <button type="submit" class="w-full text-left px-4 text-black py-2 text-sm hover:bg-gray-100">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Layout Wrapper: Sidebar + Content -->
    <div class="flex flex-row h-full overflow-hidden">
    
        <!-- Sidebar (hidden by default on mobile, visible on large screens) -->
        <aside id="sidebar" class="hidden flex-col w-64 bg-black text-white p-4 space-y-4">
            <nav>
                <ul class="space-y-2">
                    <li><a href="<?= base_url('') ?>" class="block hover:underline">Home</a></li>
                    <?php if (current_user()['role_id'] == 1 || current_user()['role_id'] == 2 || current_user()['role_id'] == 4) { ?>
                        <li><a href="<?= base_url('inventory') ?>" class="block hover:underline">Inventory</a></li>
                    <?php } ?>
                    <?php if (current_user()['role_id'] == 1 || current_user()['role_id'] == 4) { ?>
                        <!-- <li><a href="<?= base_url('inventory_in') ?>" class="block hover:underline">Inventory In</a></li> -->
                    <?php } ?>
                    <?php if (current_user()['role_id'] == 1 || current_user()['role_id'] == 4) { ?>
                        <!-- <li><a href="<?= base_url('inventory_out') ?>" class="block hover:underline">Inventory Out</a></li> -->
                    <?php } ?>
                    <!-- <li><a href="<?= base_url('inventory_history') ?>" class="block hover:underline">Inventory History</a></li> -->
                    <?php if (current_user()['role_id'] == 1 || current_user()['role_id'] == 4) { ?>
                        <li><a href="<?= base_url('supplier') ?>" class="block hover:underline">Supplier</a></li>
                        <li><a href="<?= base_url('distributor') ?>" class="block hover:underline">Distribution</a></li>
                        <li><a href="<?= base_url('inventory_type') ?>" class="block hover:underline">Inventory Type</a></li>
                        <li><a href="<?= base_url('sub_inventory_type') ?>" class="block hover:underline">Sub Inventory Type</a></li>
                        <li><a href="<?= base_url('users') ?>" class="block hover:underline">User</a></li>
                    <?php } ?>
                    <!-- <li><a href="<?= base_url('profile') ?>" class="block hover:underline">Profile</a></li> -->
                    <!-- <li><a href="<?= base_url('logout') ?>" class="block hover:underline">Logout</a></li> -->
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex flex-col h-full overflow-auto flex-1 p-6">
            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <!-- Footer -->
    <footer class="bg-black
 text-white p-4 text-center">
        &copy; <?= date('Y') ?> <?=global_name()?>. All rights reserved.
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('userMenuButton');
            const dropdown = document.getElementById('userMenuDropdown');

            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdown.classList.toggle('hidden');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', () => {
                dropdown.classList.add('hidden');
            });
        });
        // JavaScript to toggle the sidebar on mobile
        document.getElementById('hamburger').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            // Toggle the hidden class on click (shows or hides sidebar)
            sidebar.classList.toggle('hidden');
        });

        // Optional: Add an event to close the sidebar when a link is clicked (for better UX)
        const sidebarLinks = document.querySelectorAll('#sidebar a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                const sidebar = document.getElementById('sidebar');
                sidebar.classList.add('hidden');  // Hide the sidebar after clicking a link
            });
        });
    </script>

</body>
</html>
