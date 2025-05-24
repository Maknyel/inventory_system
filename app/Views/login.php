<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <!-- Login Container -->
    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold text-center mb-6">Loginsss</h2>

        <!-- Flash message for errors -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form action="<?=base_url('login')?>" method="POST">
            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" required class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" placeholder="Enter your email">
            </div>

            <!-- Password -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" placeholder="Enter your password">
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-black text-white py-2 rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-black">Login</button>
        </form>

    </div>

</body>
</html>
