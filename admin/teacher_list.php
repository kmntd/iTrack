<?php
// Include database connection
include '../fn/dbcon.php';

// Fetch teachers from the database
$sql = "SELECT t.id, t.first_name, t.middle_name, t.last_name, t.email, 
               t.dob, t.phone_number, t.address, t.hire_date, t.status 
        FROM teachers t";
$result = $con->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add/Edit/Delete Teachers</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<div class="container mx-auto p-6">
    <h1 class="text-3xl font-semibold mb-4">Add/Edit/Delete Teachers</h1>
    

    <!-- Teachers Table -->
    <table class="min-w-full bg-white border border-gray-300">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b text-left">First Name</th>
                <th class="py-2 px-4 border-b text-left">Middle Name</th>
                <th class="py-2 px-4 border-b text-left">Last Name</th>
                <th class="py-2 px-4 border-b text-left">Email</th>
                <th class="py-2 px-4 border-b text-left">DOB</th>
                <th class="py-2 px-4 border-b text-left">Phone Number</th>
                <th class="py-2 px-4 border-b text-left">Address</th>
                <th class="py-2 px-4 border-b text-left">Hire Date</th>
                <th class="py-2 px-4 border-b text-left">Status</th>
                <th class="py-2 px-4 border-b text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td class="py-2 px-4 border-b"><?php echo $row['first_name']; ?></td>
            <td class="py-2 px-4 border-b"><?php echo $row['middle_name']; ?></td>
            <td class="py-2 px-4 border-b"><?php echo $row['last_name']; ?></td>
            <td class="py-2 px-4 border-b"><?php echo $row['email']; ?></td>
            <td class="py-2 px-4 border-b"><?php echo $row['dob']; ?></td>
            <td class="py-2 px-4 border-b"><?php echo $row['phone_number']; ?></td>
            <td class="py-2 px-4 border-b"><?php echo $row['address']; ?></td>
            <td class="py-2 px-4 border-b"><?php echo $row['hire_date']; ?></td>
            <td class="py-2 px-4 border-b"><?php echo $row['status']; ?></td>
            <td class="py-2 px-4 border-b">
                <a href="../fn/edit_teacher.php?id=<?php echo $row['id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a>
                <form method="post" action="../fn/soft_delete_teacher.php" style="display:inline;">
                    <input type="hidden" name="teacher_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" class="text-red-500 hover:text-red-700 ml-4" onclick="return confirm('Are you sure you want to delete this teacher?');">Delete</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="10" class="py-2 px-4 text-center">No teachers found.</td>
    </tr>
<?php endif; ?>

              
        </tbody>
    </table>
</div>

</body>
</html>
