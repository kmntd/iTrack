<?php
// Include database connection
include '../fn/dbcon.php';

// Fetch teachers from the database
$sql = "SELECT t.id, t.first_name, t.middle_name, t.last_name, t.email, 
               t.dob, t.phone_number, t.address, t.hire_date, t.status 
        FROM teachers t";
$result = $con->query($sql);

// Soft delete teacher
if (isset($_POST['delete_teacher'])) {
    $teacher_id = intval($_POST['teacher_id']);
    $query = "UPDATE teachers SET deleted_at = NOW() WHERE id = $teacher_id";
    if ($con->query($query) === TRUE) {
        echo "<script>alert('Teacher deleted successfully.');</script>";
    } else {
        echo "<script>alert('Error deleting teacher: " . $con->error . "');</script>";
    }
}

// Restore teacher
if (isset($_POST['restore_teacher'])) {
    $teacher_id = intval($_POST['teacher_id']);
    $query = "UPDATE teachers SET deleted_at = NULL WHERE id = $teacher_id";
    if ($con->query($query) === TRUE) {
        echo "<script>alert('Teacher restored successfully.');</script>";
    } else {
        echo "<script>alert('Error restoring teacher: " . $con->error . "');</script>";
    }
}

// Retrieve active teachers (soft delete logic)
$query = "SELECT * FROM teachers WHERE deleted_at IS NULL";
$result = $con->query($query);

// Optional: Retrieve deleted teachers
$deleted_query = "SELECT * FROM teachers WHERE deleted_at IS NOT NULL";
$deleted_result = $con->query($deleted_query);
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
    
    <!-- Add Teacher Form -->
    <form action="../fn/teacher_register.php" method="POST" class="bg-white p-4 rounded-lg shadow">
            <div class="grid grid-cols-2 gap-4">
                <input type="text" name="first_name" placeholder="First Name" required class="border p-2 rounded">
                <input type="text" name="middle_name" placeholder="Middle Name" class="border p-2 rounded">
                <input type="text" name="last_name" placeholder="Last Name" required class="border p-2 rounded">
                <input type="email" name="email" placeholder="Email" required class="border p-2 rounded">
                <input type="password" name="password" placeholder="Password" required class="border p-2 rounded">
                <input type="date" name="dob" required class="border p-2 rounded">
                <select name="subject_id" required class="border p-2 rounded">
                    <option value="">Select Subject</option>
                    <?php
                    // Fetch subjects for dropdown
                    $subject_sql = "SELECT * FROM subjects"; // Assuming you have a subjects table
                    $subject_result = $con->query($subject_sql);
                    while ($subject = $subject_result->fetch_assoc()) {
                        echo "<option value='{$subject['id']}'>{$subject['subject_name']}</option>";
                    }
                    ?>
                </select>
                <input type="text" name="phone_number" placeholder="Phone Number" class="border p-2 rounded">
                <input type="text" name="address" placeholder="Address" class="border p-2 rounded">
                <input type="date" name="hire_date" class="border p-2 rounded">
            </div>
            <button type="submit" class="mt-4 bg-blue-500 text-white py-2 px-4 rounded">Register Teacher</button>
        </form>

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
