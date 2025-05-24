<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<nav class="text-sm text-gray-600" aria-label="Breadcrumb">
  <ol class="flex items-center space-x-2">
    <li>
      <a href="<?=base_url('inventory')?>" class="text-blue-600 hover:underline">Inventory</a>
    </li>
    <li><span class="mx-1 text-gray-400">/</span></li>
    <li>
      <span class="text-black"><?=($inventory_type_data['name'])?></span>
    </li>
  </ol>
</nav>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4 p-2 m-auto">
    <?php foreach ($sub_inventory_type_data as $type): ?>
        <a href="<?= base_url('inventory') ?>/<?= $type['inventory_type_id'] ?>/<?= $type['id'] ?>"
           class="block rounded-lg shadow-md bg-yellow-500 text-gray-800 hover:text-white p-4 py-8 hover:bg-black transition duration-200">
            <div class="text-center text-lg font-semibold w-full h-full">
                <?= esc($type['name']) ?>
            </div>
        </a>
    <?php endforeach; ?>
</div>





<script>
    
</script>

<?= $this->endSection() ?>
