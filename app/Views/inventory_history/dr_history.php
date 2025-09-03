<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="flex items-center justify-between mt-6 mb-4">

    <h1 class="text-2xl font-bold">DR History</h1>
    <button class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700" onclick="downloadCSV()">Export to CSV</button>

</div>




<form id="filterForm" class="mb-4 flex flex-row items-center gap-2 flex-1 w-full h-10">
    <div class="flex flex-row items-center gap-2 flex-1 w-full overflow-auto h-16 px-4">

        <input type="text" name="search_dr" placeholder="Search DR" class="px-3 py-2 border rounded w-64" />
        <input type="text" name="search_dt" placeholder="Search Delivered To" class="px-3 py-2 border rounded w-64" />
        <input type="text" name="search_address" placeholder="Search Address" class="px-3 py-2 border rounded w-64" />
        <input type="text" name="search_ref" placeholder="Search REF. P.O." class="px-3 py-2 border rounded w-64" />

        

        <!-- ✅ Add these date inputs -->
        <input type="date" name="start_date" class="px-3 py-2 border rounded" />
        <input type="date" name="end_date" class="px-3 py-2 border rounded" />


        <select name="number_per_page" class="px-3 py-2 border rounded">
            <option value="10" selected>10 per page</option>
            <option value="25">25 per page</option>
            <option value="50">50 per page</option>
            <option value="100">100 per page</option>
            <option value="100">500 per page</option>
            <option value="100">1000 per page</option>
        </select>
    </div>
    <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded">Filter</button>
</form>

<div class="flex flex-col h-full overflow-auto">
    <table id="myTable" class="min-w-full table-auto border-collapse border border-gray-300">
        <thead>
            <tr>
                <!-- <th class="sticky z-10 top-0 bg-white px-4 py-2 border-b text-left">#</th> -->
                <th class="sticky z-10 top-0 bg-white px-4 py-2 border-b text-left">
                    Action

                </th>
                <th class="sticky z-10 top-0 bg-white px-4 py-2 border-b text-left">
                    DR Number

                </th>
                <th class="sticky z-10 top-0 bg-white px-4 py-2 border-b text-left">
                    Delivered To
                </th>
                <th class="sticky z-10 top-0 bg-white px-4 py-2 border-b text-left">
                    Address
                </th>
                <th class="sticky z-10 top-0 bg-white px-4 py-2 border-b text-left">
                    REF. P.O.
                </th>
                <th class="sticky z-10 top-0 bg-white px-4 py-2 border-b text-left">
                    Date
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
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('id')) {
            document.getElementById('filterForm').style.display = 'none';
        }
    });
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

        const search_dt = formData.get('search_dt') || '';
        const search_address = formData.get('search_address') || '';
        const search_ref = formData.get('search_ref') || '';
        const search_dr = formData.get('search_dr') || '';
        const start_date = formData.get('start_date') || '';

        const end_date = formData.get('end_date') || '';


        const number_per_page = formData.get('number_per_page') || 10;
        
        const urlParams = new URLSearchParams(window.location.search);
        const url = new URL(base_url + 'api/inventory-history-dr');
        url.searchParams.set('search_dr', search_dr);

        url.searchParams.set('number_per_page', number_per_page);
        url.searchParams.set('page', page);
        url.searchParams.set('start_date', start_date);
        url.searchParams.set('end_date', end_date);
        url.searchParams.set('search_dt', search_dt);
        url.searchParams.set('search_address', search_address);
        url.searchParams.set('search_ref', search_ref);


        const response = await fetch(url);
        const result = await response.json();

        const tableBody = document.getElementById('inventoryTableBody');
        tableBody.innerHTML = ''; // clear old rows

        result.data.forEach((recordv2, index) => {
            const drRowId = `sub-rows-${index}`;

            // DR Header Row with Toggle Button
            const row = document.createElement('tr');
            row.classList.add('bg-gray-100');
            let baseUrl = "<?= base_url('print') ?>";
            row.innerHTML = `
                <td class=" bg-[#FFFFFF80] px-4 py-2 border-b font-bold">
                    <button onclick="window.open('${baseUrl}/purchase-order/${recordv2.id}')" class="text-yellow-600 hover:underline">Purchase Order</button> |
                    <button onclick="window.open('${baseUrl}/form-customer/${recordv2.id}')" class="text-yellow-600 hover:underline">DR Form to Customer</button> |
                    <button onclick="window.open('${baseUrl}/form-distributor/${recordv2.id}')" class="text-yellow-600 hover:underline">DR Form to Distributors</button> |
                </td>
                <td class=" bg-[#FFFFFF80] px-4 py-2 border-b font-bold">
                    ${recordv2.dr_number}
                </td>

                <td class=" bg-[#FFFFFF80] px-4 py-2 border-b font-bold">
                    ${(recordv2.name)?recordv2.name:''}
                </td>

                <td class=" bg-[#FFFFFF80] px-4 py-2 border-b font-bold">
                    ${(recordv2.address)?recordv2.address:''}
                </td>

                <td class=" bg-[#FFFFFF80] px-4 py-2 border-b font-bold">
                    ${(recordv2.ref_po_number)?recordv2.ref_po_number:''}
                </td>

                <td class=" bg-[#FFFFFF80] px-4 py-2 border-b font-bold">
                    ${(recordv2.created_at != '0000-00-00 00:00:00')?formatDate(recordv2.created_at):''}
                </td>
            `;

            tableBody.appendChild(row);


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



    function renderPagination({
        total,
        total_pages,
        current_page
    }) {
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

        [
            'search',
            'number_per_page', 
            'search_dt',
            'search_address',
            'search_ref'
        ].forEach(name => {
            if (params.has(name)) {
                form.elements[name].value = params.get(name);
            }
        });
    }



    function formatDate(dateString) {
        const options = {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        return new Date(dateString).toLocaleDateString(undefined, options);
    }

    function downloadCSV() {
        const table = document.getElementById("myTable");
        let csv = [];

        for (let row of table.rows) {
            // Skip rows that contain the "exclude" class
            if ([...row.classList].includes("main-group-data")) continue;

            const cols = Array.from(row.cells).map(cell => `"${cell.innerText.trim()}"`);
            csv.push(cols.join(","));
        }

        const csvContent = csv.join("\n");

        const blob = new Blob([csvContent], {
            type: 'text/csv;charset=utf-8;'
        });
        const url = URL.createObjectURL(blob);

        const a = document.createElement("a");
        a.href = url;
        a.download = "table-data.csv";
        a.click();
        URL.revokeObjectURL(url);
    }
</script>
<?= $this->endSection() ?>