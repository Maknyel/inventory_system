<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Notifications</h1>

    <div class="bg-white rounded-2xl shadow p-6">
        <?php if (!empty($notifications)): ?>
            <ul class="divide-y divide-gray-200">
                <?php foreach ($notifications as $note): ?>
                    <li class="py-4 <?= $note['is_viewed'] == 0 ? 'bg-gray-100' : '' ?>">
                        <div class="p-4 flex justify-between items-start space-x-3">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 2a6 6 0 00-6 6v2.586L2.707 13.88a1 1 0 001.414 1.414L6 13.414V16a2 2 0 002 2h4a2 2 0 002-2v-2.586l1.879 1.88a1 1 0 001.415-1.415L16 10.586V8a6 6 0 00-6-6z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-700">
                                        <?= esc($note['created_by_name']) ?>: <?= esc($note['text']) ?>
                                    </p>
                                    <p class="text-xs text-gray-400"><?= date('F j, Y, g:i a', strtotime($note['created_at'])) ?></p>
                                </div>
                            </div>
                            <div>
                                <button 
                                    onclick='markAsViewed(<?= json_encode($note) ?>)'
                                    class="text-sm text-blue-600 hover:underline"
                                >View</button>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-gray-500">No notifications available.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Modal -->
<div id="notificationModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
        <button onclick="closeModal()" class="absolute top-2 right-3 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        <h2 class="text-xl font-bold mb-4 text-gray-800">Notification Details</h2>

        <div class="text-sm text-gray-700 space-y-2">
            <p><strong>From:</strong> <span id="modalUser"></span></p>
            <p><strong>Date:</strong> <span id="modalDate"></span></p>
            <p><strong>Inventory Name:</strong> <span id="modalInventoryName"></span></p>
            <p><strong>Description:</strong> <span id="modalDescription"></span></p>
            <p><strong>Unit:</strong> <span id="modalUnit"></span></p>
            <p><strong>Quantity:</strong> <span id="modalQuantity"></span></p>
            <p><strong>Adjustment:</strong> <span id="modalAdjustment"></span></p>
        </div>

        <div class="mt-6 flex justify-end gap-4">
            <button 
                id="acceptBtn"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm"
            >Accept Request</button>

            <button 
                id="cancelBtn"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm"
            >Cancel Request</button>

            <button 
                id="showDataBtn"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm"
            >Show Data</button>
        </div>
    </div>
</div>


<script>
    let currentNotification = null;
    function markAsViewed(notification) {
        fetch(`${base_url}api/notifications/view/${notification.id}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: notification.id })
        }).then(response => {
            if (response.ok) {
                currentNotification = notification;
                showModal(notification);
            } else {
                alert('Failed to mark as viewed.');
            }
        });
    }

    function showModal(notification) {
        document.getElementById('modalUser').textContent = notification.created_by_name;
        document.getElementById('modalDate').textContent = new Date(notification.created_at).toLocaleString();
        document.getElementById('modalInventoryName').textContent = notification.inventory_name ?? '—';
        document.getElementById('modalDescription').textContent = notification.inventory_description ?? '—';
        document.getElementById('modalUnit').textContent = notification.inventory_unit ?? '—';
        document.getElementById('modalQuantity').textContent = notification.inventory_quantity ?? '—';
        document.getElementById('modalAdjustment').textContent = notification.text ?? '—';

        document.getElementById('notificationModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('notificationModal').classList.add('hidden');
    }

    // Cancel button click
    document.getElementById('cancelBtn').addEventListener('click', function() {
        if (!currentNotification) return;

        fetch(`${base_url}api/notifications/cancel/${currentNotification.id}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                alert('Request cancelled.');
                closeModal();
                location.reload();
            } else {
                alert('Failed to accept request.');
            }
        });
    });

    // Accept button click
    document.getElementById('acceptBtn').addEventListener('click', function() {
        if (!currentNotification) return;

        fetch(`${base_url}api/notifications/accept/${currentNotification.id}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                alert('Request accepted.');
                closeModal();
                location.reload();
            } else {
                alert('Failed to accept request.');
            }
        });
    });

    // Show Data button click
    document.getElementById('showDataBtn').addEventListener('click', function() {
        if (!currentNotification || !currentNotification.inventory_id) {
            alert('No inventory linked.');
            return;
        }

        // Redirect to inventory detail page
        if(currentNotification.column_to_be_updated == 'quantity'){
            window.open(`${base_url}inventory_history?id=${currentNotification.inventory_history_id}`);
        }
        
    });
</script>

<?= $this->endSection() ?>
