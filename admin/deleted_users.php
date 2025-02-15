<?php
session_start();
include '../fn/dbcon.php'; // Include your database connection

// Retrieve deleted students
$deleted_students_query = "SELECT * FROM students WHERE deleted_at IS NOT NULL";
$deleted_students_result = $con->query($deleted_students_query);

// Retrieve deleted teachers
$deleted_teachers_query = "SELECT * FROM teachers WHERE deleted_at IS NOT NULL";
$deleted_teachers_result = $con->query($deleted_teachers_query);

// Retrieve deleted admins
$deleted_admins_query = "SELECT * FROM admins WHERE deleted_at IS NOT NULL";
$deleted_admins_result = $con->query($deleted_admins_query);

// Retrieve deleted sections
$deleted_sections_query = "SELECT * FROM sections WHERE deleted_at IS NOT NULL";
$deleted_sections_result = $con->query($deleted_sections_query);
?>
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Deleted Users</title>
    <!-- Include any necessary CSS files -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-10">
    <h1 class="text-3xl font-bold mb-6">Deleted Users</h1>

    <h2 class="text-2xl font-semibold mb-4">Deleted Students</h2>
    <table class="min-w-full bg-white border border-gray-200">
        <thead>
            <tr>
                <th class="border px-4 py-2">ID</th>
                <th class="border px-4 py-2">LRN</th>
                <th class="border px-4 py-2">First Name</th>
                <th class="border px-4 py-2">Last Name</th>
                <th class="border px-4 py-2">Email</th>
                <th class="border px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $deleted_students_result->fetch_assoc()): ?>
                <tr>
                    <td class="border px-4 py-2"><?php echo $row['id']; ?></td>
                    <td class="border px-4 py-2"><?php echo $row['lrn']; ?></td>
                    <td class="border px-4 py-2"><?php echo $row['first_name']; ?></td>
                    <td class="border px-4 py-2"><?php echo $row['last_name']; ?></td>
                    <td class="border px-4 py-2"><?php echo $row['email']; ?></td>
                    <td class="border px-4 py-2">
                        <form method="post" action="../fn/restore_student.php" style="display:inline;">
                            <input type="hidden" name="student_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="text-blue-600 hover:underline">Restore</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2 class="text-2xl font-semibold mb-4 mt-8">Deleted Teachers</h2>
    <table class="min-w-full bg-white border border-gray-200">
        <thead>
            <tr>
                <th class="border px-4 py-2">ID</th>
                <th class="border px-4 py-2">First Name</th>
                <th class="border px-4 py-2">Last Name</th>
                <th class="border px-4 py-2">Email</th>
                <th class="border px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $deleted_teachers_result->fetch_assoc()): ?>
                <tr>
                    <td class="border px-4 py-2"><?php echo $row['id']; ?></td>
                    <td class="border px-4 py-2"><?php echo $row['first_name']; ?></td>
                    <td class="border px-4 py-2"><?php echo $row['last_name']; ?></td>
                    <td class="border px-4 py-2"><?php echo $row['email']; ?></td>
                    <td class="border px-4 py-2">
                        <form method="post" action="../fn/restore_teacher.php" style="display:inline;">
                            <input type="hidden" name="teacher_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="text-blue-600 hover:underline">Restore</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2 class="text-2xl font-semibold mb-4 mt-8">Deleted Admins</h2>
    <table class="min-w-full bg-white border border-gray-200">
        <thead>
            <tr>
                <th class="border px-4 py-2">ID</th>
                <th class="border px-4 py-2">Username</th>
                <th class="border px-4 py-2">Name</th>
                <th class="border px-4 py-2">Email</th>
                <th class="border px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $deleted_admins_result->fetch_assoc()): ?>
                <tr>
                    <td class="border px-4 py-2"><?php echo $row['id']; ?></td>
                    <td class="border px-4 py-2"><?php echo $row['username']; ?></td>
                    <td class="border px-4 py-2"><?php echo $row['name']; ?></td>
                    <td class="border px-4 py-2"><?php echo $row['email']; ?></td>
                    <td class="border px-4 py-2">
                        <form method="post" action="restore_admin.php" style="display:inline;">
                            <input type="hidden" name="admin_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="text-blue-600 hover:underline">Restore</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2 class="text-2xl font-semibold mb-4 mt-8">Deleted Sections</h2>
    <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr>
                    <th class="border px-4 py-2">ID</th>
                    <th class="border px-4 py-2">Section</th>
                    <th class="border px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $deleted_sections_result->fetch_assoc()): ?>
                    <tr>
                        <td class="border px-4 py-2"><?php echo $row['id']; ?></td>
                        <td class="border px-4 py-2"><?php echo $row['section_name']; ?></td>
                        <td class="border px-4 py-2">
                            <form method="post" action="../fn/restore_section.php" style="display:inline;">
                                <input type="hidden" name="section_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="text-blue-600 hover:underline">Restore</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
  
</body>
</html>
