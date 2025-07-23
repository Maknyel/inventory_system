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
                                    onclick="markAsViewed(<?= $note['id'] ?>)"
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

<script>
    function markAsViewed(notificationId) {
        fetch(base_url+'api/notifications/view/' + notificationId, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: notificationId })
        })
        .then(response => {
            if (response.ok) {
                // Redirect to a view page or simply reload the page
                window.location.reload(); // or redirect to detail page
            } else {
                alert('Failed to mark as viewed.');
            }
        });
    }
</script>

<?= $this->endSection() ?>
