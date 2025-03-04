<?php
include '../fn/dbcon.php';
include '../data/home.php';

// Initialize alerts array
$alerts = [];

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate passwords
    if (!empty($new_password) || !empty($confirm_password)) {
        if ($new_password !== $confirm_password) {
            $alerts[] = "Passwords do not match!";
        } else if (strlen($new_password) < 6) {
            $alerts[] = "Password must be at least 6 characters long!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Grade Monitoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .hidden-message {
            display: none;
        }
    </style>
</head>
<body>

<div class="flex h-screen bg-[#0165DC]">
    <?php include '../component/aside.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <?php include '../component/header.php'; ?>

        <main class="flex-1 p-6 flex flex-col md:flex-row space-x-4">
            <div class="flex-1 mb-4 md:mb-0 bg-white p-6 shadow-lg rounded-lg">
                <h2 class="text-2xl font-bold text-center mb-4">Update Profile</h2>

                <?php if (!empty($alerts)): ?>
                    <div class="flex flex-col space-y-4">
                        <?php foreach ($alerts as $alert): ?>
                            <div class="bg-red-500 text-white p-4 rounded-lg mb-4" role="alert">
                                <?php echo htmlspecialchars($alert); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                        <input type="password" name="new_password" id="new_password" placeholder="New Password" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label for="image_file" class="block text-sm font-medium text-gray-700">Profile Image</label>
                        <input type="file" name="image_file" id="image_file" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-500">
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 rounded-md hover:bg-blue-700 transition">Update</button>
                </form>
            </div>
        </main>
    </div>
</div>

<script>
    const burger = document.getElementById('burger');
    const sidebar = document.getElementById('sidebar');

    if (burger) {
        burger.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });
    }
</script>

</body>
</html>
