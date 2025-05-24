<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4 p-2">
    <?php foreach ($inventory_type_data as $type): ?>
        <a href="<?= base_url('inventory') ?>/<?= $type['id'] ?>"
           class="block rounded-lg shadow-md bg-yellow-500 text-gray-800 hover:text-white p-4 hover:bg-black transition duration-200">
            <div class="text-lg font-semibold w-full h-full">
                <?= esc($type['name']) ?>
            </div>
        </a>
    <?php endforeach; ?>
</div>





<script>
    
</script>

<?= $this->endSection() ?>
