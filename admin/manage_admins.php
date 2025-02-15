<?php
session_start();
include '../fn/dbcon.php'; // Include your database connection

// Fetch all admin accounts from the database
$query = "SELECT * FROM admins";
$result = $con->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admin Accounts</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold mb-4">Manage Admin Accounts</h1>

        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-2 px-4 border-b">ID</th>
                    <th class="py-2 px-4 border-b">Username</th>
                    <th class="py-2 px-4 border-b">Name</th>
                    <th class="py-2 px-4 border-b">Email</th>
                    <th class="py-2 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($admin = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="py-2 px-4 border-b"><?php echo $admin['id']; ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($admin['username']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($admin['name']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($admin['email']); ?></td>
                            <td class="py-2 px-4 border-b">
                                <a href="edit_admin.php?id=<?php echo $admin['id']; ?>" class="text-blue-500 hover:underline">Edit</a>
                                <a href="delete_admin.php?id=<?php echo $admin['id']; ?>" class="text-red-500 hover:underline">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="py-2 px-4 border-b text-center">No admin accounts found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="mt-4">
            <a href="add_admin.php" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Add New Admin</a>
        </div>
    </div>

</body>
</html>

<?php
$con->close(); // Close the database connection
?>
