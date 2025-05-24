<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Inventory Count -->
        <div class="bg-white rounded-2xl shadow p-4">
            <h2 class="text-sm text-gray-500">Total Inventory Items</h2>
            <p class="text-3xl font-bold text-yellow-600"><?= esc(inventory_item_count() ?? '0') ?></p>
        </div>

        <!-- Low Stock Count -->
        <div class="bg-white rounded-2xl shadow p-4">
            <h2 class="text-sm text-gray-500">Low Stock Items</h2>
            <p class="text-3xl font-bold text-red-600"><?= esc(low_stock_inventory_count() ?? '0') ?></p>
        </div>

        <!-- Total Suppliers -->
        <div class="bg-white rounded-2xl shadow p-4">
            <h2 class="text-sm text-gray-500">Suppliers</h2>
            <p class="text-3xl font-bold text-yellow-600"><?= esc(inventory_supplier_count() ?? '0') ?></p>
        </div>

        <!-- Users -->
        <div class="bg-white rounded-2xl shadow p-4">
            <h2 class="text-sm text-gray-500">System Users</h2>
            <p class="text-3xl font-bold text-yellow-600"><?= esc(users_count() ?? '0') ?></p>
        </div>
    </div>

    <?php $lowStockItems = get_low_stock_items(); ?>

    <div class="bg-white rounded-2xl shadow p-6">
        <h2 class="text-xl font-semibold text-red-600 mb-4">Low Stock Items</h2>

        <?php if (count($lowStockItems) > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Icon</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Qty</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reordering Level</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($lowStockItems as $item): ?>
                            <tr>
                                <td class="px-4 py-2">
                                    <?php if (!empty($item['icon'])): ?>
                                        <img src="<?= base_url('uploads/icons/'.$item['icon']) ?>" alt="icon" class="h-8 w-8 object-cover rounded-full">
                                    <?php else: ?>
                                        <span class="text-gray-400">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-800"><?= esc($item['name']) ?></td>
                                <td class="px-4 py-2 text-sm text-gray-600"><?= esc($item['description']) ?></td>
                                <td class="px-4 py-2 text-sm text-red-600 font-semibold"><?= esc($item['current_quantity']) ?? '0' ?></td>
                                <td class="px-4 py-2 text-sm text-gray-700"><?= esc($item['current_price']) ?></td>
                                <td class="px-4 py-2 text-sm text-gray-700"><?= esc($item['reordering_level']) ?></td>
                                <td class="px-4 py-2 text-sm text-gray-700"><?= esc($item['inventory_type_name']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-gray-500">No low stock items found.</p>
        <?php endif; ?>
    </div>


    
</div>

<?= $this->endSection() ?>
