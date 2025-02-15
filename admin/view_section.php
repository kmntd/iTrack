<?php
session_start();
include '../fn/dbcon.php'; // Include database connection

// Fetch section details
if (isset($_GET['id'])) {
    $section_id = $_GET['id'];
    $sql = "SELECT * FROM sections WHERE id = $section_id AND deleted_at IS NULL";
    $section_result = $con->query($sql);
    $section = $section_result->fetch_assoc();

    // Fetch students in this section
    $student_sql = "SELECT * FROM students WHERE section_id = $section_id AND deleted_at IS NULL";
    $student_result = $con->query($student_sql);
} else {
    header("Location: manage_sections.php"); // Redirect if no ID is set
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Section</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h2 class="text-xl font-semibold mb-4">Section: <?php echo $section['section_name']; ?></h2>
        
        <h3 class="text-lg font-semibold mb-4">Students in this Section</h3>
        <table class="min-w-full bg-white rounded shadow">
            <thead>
                <tr>
                    <th class="py-2 px-4 border">LRN</th>
                    <th class="py-2 px-4 border">Name</th>
                    <th class="py-2 px-4 border">Email</th>
                    <th class="py-2 px-4 border">Grade Level</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($student_result->num_rows > 0): ?>
                    <?php while($student = $student_result->fetch_assoc()): ?>
                        <tr>
                            <td class="py-2 px-4 border-b"><?php echo $student['lrn']; ?></td>
                            <td class="py-2 px-4 border-b"><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></td>
                            <td class="py-2 px-4 border-b"><?php echo $student['email']; ?></td>
                            <td class="py-2 px-4 border-b"><?php echo $student['grade_level']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="py-2 px-4 text-center">No students found in this section.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="mt-6">
            <a href="manage_sections.php" class="bg-blue-500 text-white rounded p-2">Back to Sections</a>
        </div>
    </div>
</body>
</html>

<?php
$con->close(); // Close database connection
?>
