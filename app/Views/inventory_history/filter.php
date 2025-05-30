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
            <a href="<?=base_url('inventory/'.$inventory_type_parse['id'].'/'.$sub_inventory_type_parse['id'])?>" class="text-blue-600 hover:underline"><?=($sub_inventory_type_parse['name'])?></a>
        </li>
        <li><span class="mx-1 text-gray-400">/</span></li>
        <li><span class="">Inventory History</span></li>
    </ol>
</nav>

<div class="flex items-center justify-between mt-6 mb-4">
    
    <h1 class="text-2xl font-bold">Inventory History</h1>
    <button class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700" onclick="downloadCSV()">Export to CSV</button>

</div>



<form method="get" class="mb-4 flex items-center gap-2">
    <input type="text" name="search" value="<?= esc($search ?? '') ?>" placeholder="Search history..." class="px-3 py-2 border rounded w-64">
    <input type="hidden" name="inventory_type" value="<?=$inventory_type_parse['id']?>">
    <input type="hidden" name="sub_inventory_type" value="<?=$sub_inventory_type_parse['id']?>">
    <select name="in_out" class="px-3 py-2 border rounded">
        <option value="">All</option>
        <option value="IN" <?= $in_out === 'IN' ? 'selected' : '' ?>>IN</option>
        <option value="OUT" <?= $in_out === 'OUT' ? 'selected' : '' ?>>OUT</option>
    </select>

    <select name="number_per_page" class="px-3 py-2 border rounded">
        <?php foreach ([2, 5, 10, 25, 50, 100] as $num): ?>
            <option value="<?= $num ?>" <?= $number_per_page == $num ? 'selected' : '' ?>><?= $num ?> per page</option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded">Filter</button>
</form>

<div class="flex flex-col h-full overflow-auto">
    <table id="myTable" class="min-w-full table-auto border-collapse border border-gray-300">
        <thead>
            <tr>
                <!-- <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">#</th> -->
                 <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                    <a href="?in_out=<?= esc($in_out) ?>&number_per_page=<?= esc($number_per_page) ?>&search=<?= esc($search) ?>&inventory_type=<?=$inventory_type_parse['id']?>&sub_inventory_type=<?=$sub_inventory_type_parse['id']?>&orderby=inventory_history.in_out&orderdir=<?= $orderby == 'inventory_history.in_out' && $orderdir == 'asc' ? 'desc' : 'asc' ?>">
                        In/Out
                        <i class="ion-<?= ($orderby == 'inventory_history.in_out' && $orderdir == 'asc') ? 'arrow-up-b' : 'arrow-down-b' ?> <?= $orderby == 'inventory_history.in_out' ? 'text-black' : 'text-gray-400' ?>"></i>
                    </a>
                </th>

                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                    <a href="?in_out=<?= esc($in_out) ?>&number_per_page=<?= esc($number_per_page) ?>&search=<?= esc($search) ?>&inventory_type=<?=$inventory_type_parse['id']?>&sub_inventory_type=<?=$sub_inventory_type_parse['id']?>&orderby=inventory_history.name&orderdir=<?= $orderby == 'inventory_history.name' && $orderdir == 'asc' ? 'desc' : 'asc' ?>">
                        Name
                        <i class="ion-<?= ($orderby == 'inventory_history.name' && $orderdir == 'asc') ? 'arrow-up-b' : 'arrow-down-b' ?> <?= $orderby == 'inventory_history.name' ? 'text-black' : 'text-gray-400' ?>"></i>
                    </a>
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                    <a href="?in_out=<?= esc($in_out) ?>&number_per_page=<?= esc($number_per_page) ?>&search=<?= esc($search) ?>&inventory_type=<?=$inventory_type_parse['id']?>&sub_inventory_type=<?=$sub_inventory_type_parse['id']?>&orderby=inventory_history.description&orderdir=<?= $orderby == 'inventory_history.description' && $orderdir == 'asc' ? 'desc' : 'asc' ?>">
                        Description
                        <i class="ion-<?= ($orderby == 'inventory_history.description' && $orderdir == 'asc') ? 'arrow-up-b' : 'arrow-down-b' ?> <?= $orderby == 'inventory_history.description' ? 'text-black' : 'text-gray-400' ?>"></i>
                    </a>
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                    <a href="?in_out=<?= esc($in_out) ?>&number_per_page=<?= esc($number_per_page) ?>&search=<?= esc($search) ?>&inventory_type=<?=$inventory_type_parse['id']?>&sub_inventory_type=<?=$sub_inventory_type_parse['id']?>&orderby=inventory_history.price&orderdir=<?= $orderby == 'inventory_history.price' && $orderdir == 'asc' ? 'desc' : 'asc' ?>">
                        Price
                        <i class="ion-<?= ($orderby == 'inventory_history.price' && $orderdir == 'asc') ? 'arrow-up-b' : 'arrow-down-b' ?> <?= $orderby == 'inventory_history.price' ? 'text-black' : 'text-gray-400' ?>"></i>
                    </a>
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                    <a href="?in_out=<?= esc($in_out) ?>&number_per_page=<?= esc($number_per_page) ?>&search=<?= esc($search) ?>&inventory_type=<?=$inventory_type_parse['id']?>&sub_inventory_type=<?=$sub_inventory_type_parse['id']?>&orderby=inventory_history.name&orderdir=<?= $orderby == 'inventory_history.quantity' && $orderdir == 'asc' ? 'desc' : 'asc' ?>">
                        Quantity
                        <i class="ion-<?= ($orderby == 'inventory_history.quantity' && $orderdir == 'asc') ? 'arrow-up-b' : 'arrow-down-b' ?> <?= $orderby == 'inventory_history.quantity' ? 'text-black' : 'text-gray-400' ?>"></i>
                    </a>
                </th>
                
                
                
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                    <a href="?in_out=<?= esc($in_out) ?>&number_per_page=<?= esc($number_per_page) ?>&search=<?= esc($search) ?>&inventory_type=<?=$inventory_type_parse['id']?>&sub_inventory_type=<?=$sub_inventory_type_parse['id']?>&orderby=inventory_type_name&orderdir=<?= $orderby == 'inventory_type_name' && $orderdir == 'asc' ? 'desc' : 'asc' ?>">
                        Inventory Type
                        <i class="ion-<?= ($orderby == 'inventory_type_name' && $orderdir == 'asc') ? 'arrow-up-b' : 'arrow-down-b' ?> <?= $orderby == 'inventory_type_name' ? 'text-black' : 'text-gray-400' ?>"></i>
                    </a>
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                    <a href="?in_out=<?= esc($in_out) ?>&number_per_page=<?= esc($number_per_page) ?>&search=<?= esc($search) ?>&inventory_type=<?=$inventory_type_parse['id']?>&sub_inventory_type=<?=$sub_inventory_type_parse['id']?>&orderby=sub_inventory_type_name&orderdir=<?= $orderby == 'sub_inventory_type_name' && $orderdir == 'asc' ? 'desc' : 'asc' ?>">
                        Sub Inventory Type
                        <i class="ion-<?= ($orderby == 'sub_inventory_type_name' && $orderdir == 'asc') ? 'arrow-up-b' : 'arrow-down-b' ?> <?= $orderby == 'sub_inventory_type_name' ? 'text-black' : 'text-gray-400' ?>"></i>
                    </a>
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                    <a href="?in_out=<?= esc($in_out) ?>&number_per_page=<?= esc($number_per_page) ?>&search=<?= esc($search) ?>&inventory_type=<?=$inventory_type_parse['id']?>&sub_inventory_type=<?=$sub_inventory_type_parse['id']?>&orderby=supplier_name&orderdir=<?= $orderby == 'supplier_name' && $orderdir == 'asc' ? 'desc' : 'asc' ?>">
                        Supplier
                        <i class="ion-<?= ($orderby == 'supplier_name' && $orderdir == 'asc') ? 'arrow-up-b' : 'arrow-down-b' ?> <?= $orderby == 'supplier_name' ? 'text-black' : 'text-gray-400' ?>"></i>
                    </a>
                </th>

                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                    <a href="?in_out=<?= esc($in_out) ?>&number_per_page=<?= esc($number_per_page) ?>&search=<?= esc($search) ?>&inventory_type=<?=$inventory_type_parse['id']?>&sub_inventory_type=<?=$sub_inventory_type_parse['id']?>&orderby=distributor_name&orderdir=<?= $orderby == 'distributor_name' && $orderdir == 'asc' ? 'desc' : 'asc' ?>">
                        Distributor
                        <i class="ion-<?= ($orderby == 'distributor_name' && $orderdir == 'asc') ? 'arrow-up-b' : 'arrow-down-b' ?> <?= $orderby == 'distributor_name' ? 'text-black' : 'text-gray-400' ?>"></i>
                    </a>
                </th>
                

                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                    <a href="?in_out=<?= esc($in_out) ?>&number_per_page=<?= esc($number_per_page) ?>&search=<?= esc($search) ?>&inventory_type=<?=$inventory_type_parse['id']?>&sub_inventory_type=<?=$sub_inventory_type_parse['id']?>&orderby=customer_own_distribution&orderdir=<?= $orderby == 'customer_own_distribution' && $orderdir == 'asc' ? 'desc' : 'asc' ?>">
                        Purpose
                        <i class="ion-<?= ($orderby == 'customer_own_distribution' && $orderdir == 'asc') ? 'arrow-up-b' : 'arrow-down-b' ?> <?= $orderby == 'customer_own_distribution' ? 'text-black' : 'text-gray-400' ?>"></i>
                    </a>
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                    <a href="?in_out=<?= esc($in_out) ?>&number_per_page=<?= esc($number_per_page) ?>&search=<?= esc($search) ?>&inventory_type=<?=$inventory_type_parse['id']?>&sub_inventory_type=<?=$sub_inventory_type_parse['id']?>&orderby=inventory_history.return_quantity&orderdir=<?= $orderby == 'inventory_history.return_quantity' && $orderdir == 'asc' ? 'desc' : 'asc' ?>">
                        Return Qty
                        <i class="ion-<?= ($orderby == 'inventory_history.return_quantity' && $orderdir == 'asc') ? 'arrow-up-b' : 'arrow-down-b' ?> <?= $orderby == 'inventory_history.return_quantity' ? 'text-black' : 'text-gray-400' ?>"></i>
                    </a>
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                    <a href="?in_out=<?= esc($in_out) ?>&number_per_page=<?= esc($number_per_page) ?>&search=<?= esc($search) ?>&inventory_type=<?=$inventory_type_parse['id']?>&sub_inventory_type=<?=$sub_inventory_type_parse['id']?>&orderby=inventory_history.remarks&orderdir=<?= $orderby == 'inventory_history.remarks' && $orderdir == 'asc' ? 'desc' : 'asc' ?>">
                        Remarks
                        <i class="ion-<?= ($orderby == 'inventory_history.remarks' && $orderdir == 'asc') ? 'arrow-up-b' : 'arrow-down-b' ?> <?= $orderby == 'inventory_history.remarks' ? 'text-black' : 'text-gray-400' ?>"></i>
                    </a>
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                    <a href="?in_out=<?= esc($in_out) ?>&number_per_page=<?= esc($number_per_page) ?>&search=<?= esc($search) ?>&inventory_type=<?=$inventory_type_parse['id']?>&sub_inventory_type=<?=$sub_inventory_type_parse['id']?>&orderby=inventory_history.created_at&orderdir=<?= $orderby == 'inventory_history.created_at' && $orderdir == 'asc' ? 'desc' : 'asc' ?>">
                        Created At
                        <i class="ion-<?= ($orderby == 'inventory_history.created_at' && $orderdir == 'asc') ? 'arrow-up-b' : 'arrow-down-b' ?> <?= $orderby == 'inventory_history.created_at' ? 'text-black' : 'text-gray-400' ?>"></i>
                        
                    </a>
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                    <a href="?in_out=<?= esc($in_out) ?>&number_per_page=<?= esc($number_per_page) ?>&search=<?= esc($search) ?>&inventory_type=<?=$inventory_type_parse['id']?>&sub_inventory_type=<?=$sub_inventory_type_parse['id']?>&orderby=inventory_history.updated_at&orderdir=<?= $orderby == 'inventory_history.updated_at' && $orderdir == 'asc' ? 'desc' : 'asc' ?>">
                        Updated At
                        <i class="ion-<?= ($orderby == 'inventory_history.updated_at' && $orderdir == 'asc') ? 'arrow-up-b' : 'arrow-down-b' ?> <?= $orderby == 'inventory_history.updated_at' ? 'text-black' : 'text-gray-400' ?>"></i>
                        
                    </a>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($inventory_history as $record): ?>
                <tr>
                    <!-- <td class="px-4 py-2 border-b"><?= $record['id'] ?></td> -->
                    <td class="px-4 py-2 border-b"><?= esc($record['in_out']) ?></td>
                    <td class="px-4 py-2 border-b"><?= esc($record['name']) ?></td>
                    <td class="px-4 py-2 border-b"><?= esc($record['description']) ?></td>
                    <td class="px-4 py-2 border-b">₱ <?= number_format($record['price'], 2) ?></td>
                    <td class="px-4 py-2 border-b"><?= $record['quantity'] ?></td>
                    
                    
                    <td class="px-4 py-2 border-b"><?= $record['inventory_type_name'] ?></td>
                    <td class="px-4 py-2 border-b"><?= $record['sub_inventory_type_name'] ?></td>
                    <td class="px-4 py-2 border-b"><?= $record['supplier_name'] ?></td>
                    <td class="px-4 py-2 border-b"><?= $record['distributor_name'] ?></td>
                    

                    <td class="px-4 py-2 border-b">
                        <?php
                            if($record['customer_own_distribution'] == 'For Own Consumption'){
                                echo 'For Own Consumption';
                            }else if($record['customer_own_distribution'] == 'For Distribution'){
                                echo 'For Customer Distribution';
                            }else{

                            }
                        ?>
                    </td>
                    <td class="px-4 py-2 border-b"><?= $record['return_quantity'] ?></td>
                    <td class="px-4 py-2 border-b"><?= esc($record['remarks']) ?></td>
                    
                    <td class="px-4 py-2 border-b"><?= date('M d, Y h:i A', strtotime($record['created_at'])) ?></td>
                    <td class="px-4 py-2 border-b"><?= date('M d, Y h:i A', strtotime($record['updated_at'])) ?></td>
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
            <?php $queryParams['page_inventory_history'] = $currentPage - 1; ?>
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
            <?php $queryParams['page_inventory_history'] = $i; ?>
            <a href="?<?= http_build_query($queryParams) ?>"
            class="px-4 py-2 rounded <?= $i == $currentPage ? 'bg-yellow-600 text-white' : 'bg-white text-black hover:bg-yellow-600 hover:text-white' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
            <?php $queryParams['page_inventory_history'] = $currentPage + 1; ?>
            <a href="?<?= http_build_query($queryParams) ?>" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-600">Next</a>
        <?php endif; ?>
    </div>
</div>
<script>
    function downloadCSV() {
        const table = document.getElementById("myTable");
        let csv = [];
        for (let row of table.rows) {
            let cols = Array.from(row.cells).map(cell => `"${cell.innerText.trim()}"`);
            csv.push(cols.join(","));
        }
        const csvContent = csv.join("\n");
        
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);

        const a = document.createElement("a");
        a.href = url;
        a.download = "table-data.csv";
        a.click();
        URL.revokeObjectURL(url);
    }
</script>
<?= $this->endSection() ?>
