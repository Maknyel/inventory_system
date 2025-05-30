<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="flex items-center justify-between mt-6 mb-4">
    
    <h1 class="text-2xl font-bold">Inventory History</h1>
    <button class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700" onclick="downloadCSV()">Export to CSV</button>

</div>

    


<form id="filterForm" class="mb-4 flex items-center gap-2">
    <input type="text" name="search" placeholder="Search Name..." class="px-3 py-2 border rounded w-64" />
    <input type="text" name="search_dr" placeholder="Search DR..." class="px-3 py-2 border rounded w-64" />
    
    <select name="in_out" class="px-3 py-2 border rounded">
        <option value="">All</option>
        <option value="in">IN</option>
        <option value="out">OUT</option>
    </select>
    
    <select name="number_per_page" class="px-3 py-2 border rounded">
        <option value="5">5 per page</option>
        <option value="10" selected>10 per page</option>
        <option value="25">25 per page</option>
        <option value="50">50 per page</option>
        <option value="100">100 per page</option>
    </select>
    <select name="order_by" class="px-3 py-2 border rounded">
        <option value="created_at" selected>Sort by Created At</option>
        <option value="name">Sort by Name</option>
        <option value="price">Sort by Price</option>
        <option value="quantity">Sort by Quantity</option>
        <!-- add more as you want -->
    </select>

    <select name="order_dir" class="px-3 py-2 border rounded">
        <option value="asc">Ascending</option>
        <option value="desc" selected>Descending</option>
    </select>
    <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded">Filter</button>
</form>

<div class="flex flex-col h-full overflow-auto">
    <table id="myTable" class="min-w-full table-auto border-collapse border border-gray-300">
        <thead>
            <tr>
                <!-- <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">#</th> -->
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                    Name
                  
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                    Description
                  
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                        Price
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                        Quantity
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                        Inventory Type
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                        Sub Inventory Type
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                        Supplier
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                        Distributor
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                        Purpose
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                        Return Qty
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                        Remarks
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                        In/Out
                </th>
                
                
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                        Created At
                </th>
                <th class="sticky top-0 bg-white px-4 py-2 border-b text-left">
                        Updated At
                </th>
            </tr>
        </thead>
        <tbody id="inventoryTableBody"></tbody>
    </table>
</div>
<div class="flex items-center justify-between w-full">
    <div id="paginationSummary" class="text-gray-600 mb-2 text-center"></div>
    <div id="pagination" class="flex justify-center gap-2 mt-4"></div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        populateFormFromURL();
        loadTable(1);

        // Optional: add event listener for your filter form
        document.querySelector('form').addEventListener('submit', (e) => {
            e.preventDefault();
            loadTable();
        });
    });

    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault(); // prevent full page reload

        // When filter form submits, load page 1 with new filters
        loadTable(1);
    });

    async function loadTable(page = 1) {
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);

        const search = formData.get('search') || '';
        const search_dr = formData.get('search_dr') || '';
        const in_out = formData.get('in_out') || '';
        const number_per_page = formData.get('number_per_page') || 10;
        const order_by = formData.get('order_by') || 'created_at';
        const order_dir = formData.get('order_dir') || 'desc';

        const url = new URL(base_url + 'api/inventory-history');
        url.searchParams.set('search', search);
        url.searchParams.set('search_dr', search_dr);
        url.searchParams.set('in_out', in_out);
        url.searchParams.set('number_per_page', number_per_page);
        url.searchParams.set('page', page);
        url.searchParams.set('order_by', order_by);
        url.searchParams.set('order_dir', order_dir);

        const response = await fetch(url);
        const result = await response.json();

        const tableBody = document.getElementById('inventoryTableBody');
        tableBody.innerHTML = ''; // clear old rows

        result.data.forEach((recordv2, index) => {
            const drRowId = `sub-rows-${index}`;

            // DR Header Row with Toggle Button
            const row = document.createElement('tr');
            row.classList.add('bg-gray-100');
            row.innerHTML = `
                <td class="bg-[#FFFFFF80] cursor-pointer px-4 py-2 border-b font-bold" colspan="14">
                    <button onclick="toggleSubRows('${drRowId}', this, '${recordv2.dr_number}')" class="mr-2 text-yellow-600 hover:underline">
                        &rarr; DR Number: ${recordv2.dr_number}
                    </button>
                    
                </td>
            `;
            tableBody.appendChild(row);

            // Sub Rows (initially hidden)
            recordv2.sub.forEach(sub => {
                const subRow = document.createElement('tr');
                subRow.classList.add('sub-row', drRowId, 'hidden'); // toggle this class
                subRow.innerHTML = `
                    <td class="px-4 py-2 border-b">${sub.name}</td>
                    <td class="px-4 py-2 border-b">${sub.description}</td>
                    <td class="px-4 py-2 border-b">₱ ${parseFloat(sub.price).toFixed(2)}</td>
                    <td class="px-4 py-2 border-b">${sub.quantity}</td>
                    <td class="px-4 py-2 border-b">${sub.inventory_type_name ?? ""}</td>
                    <td class="px-4 py-2 border-b">${sub.sub_inventory_type_name ?? ""}</td>
                    <td class="px-4 py-2 border-b">${sub.supplier_name ?? ''}</td>
                    <td class="px-4 py-2 border-b">${sub.distributor_name ?? ''}</td>
                    
                    <td class="px-4 py-2 border-b">${((sub.customer_own_distribution=='For Own Consumption')?'For Own Consumption':((sub.customer_own_distribution=='For Distribution')?'For Customer Distribution':''))}</td>

                    <td class="px-4 py-2 border-b">${sub.return_quantity}</td>                    
                    <td class="px-4 py-2 border-b">${sub.remarks}</td>
                    <td class="px-4 py-2 border-b">${sub.in_out}</td>
                    
                    <td class="px-4 py-2 border-b">${formatDate(sub.created_at)}</td>
                    <td class="px-4 py-2 border-b">${formatDate(sub.updated_at)}</td>
                `;
                tableBody.appendChild(subRow);
            });
        });

        renderPagination(result.pagination);
    }

    function toggleSubRows(className, toggleButton, dr_number) {
        const rows = document.querySelectorAll(`.${className}`);
        const isHidden = rows[0]?.classList.contains('hidden');

        rows.forEach(row => row.classList.toggle('hidden'));

        if (toggleButton) {
            toggleButton.innerHTML = `${isHidden ? '&darr;' : '&rarr;'}` + ` DR Number: ${dr_number}`;
            // toggleButton.nextSibling.textContent = ;
        }
    }



    function renderPagination({ total, total_pages, current_page }) {
        const pagination = document.getElementById('pagination');
        const summary = document.getElementById('paginationSummary');

        pagination.innerHTML = ''; // clear existing

        summary.textContent = `Total of ${total} items | Page ${current_page} of ${total_pages}`;

        // Previous button
        const prevBtn = document.createElement('button');
        prevBtn.textContent = 'Previous';
        prevBtn.disabled = current_page === 1;
        prevBtn.className = `px-4 py-2 rounded ${prevBtn.disabled ? 'bg-gray-300 cursor-not-allowed' : 'bg-yellow-600 text-white hover:bg-yellow-700'}`;
        prevBtn.onclick = () => {
            if (current_page > 1) loadTable(current_page - 1);
        };
        pagination.appendChild(prevBtn);

        // Page numbers
        for (let i = 1; i <= total_pages; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.textContent = i;
            pageBtn.className = `px-4 py-2 rounded ${i === current_page ? 'bg-yellow-600 text-white' : 'bg-white text-black hover:bg-yellow-600 hover:text-white'}`;
            pageBtn.onclick = () => loadTable(i);
            pagination.appendChild(pageBtn);
        }

        // Next button
        const nextBtn = document.createElement('button');
        nextBtn.textContent = 'Next';
        nextBtn.disabled = current_page === total_pages;
        nextBtn.className = `px-4 py-2 rounded ${nextBtn.disabled ? 'bg-gray-300 cursor-not-allowed' : 'bg-yellow-600 text-white hover:bg-yellow-700'}`;
        nextBtn.onclick = () => {
            if (current_page < total_pages) loadTable(current_page + 1);
        };
        pagination.appendChild(nextBtn);
    }

    function populateFormFromURL() {
        const params = new URLSearchParams(window.location.search);
        const form = document.getElementById('filterForm');

        ['search', 'in_out', 'number_per_page', 'order_by', 'order_dir'].forEach(name => {
            if (params.has(name)) {
                form.elements[name].value = params.get(name);
            }
        });
    }



    function formatDate(dateString) {
        const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute:'2-digit' };
        return new Date(dateString).toLocaleDateString(undefined, options);
    }
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
