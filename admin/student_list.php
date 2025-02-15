<?php
session_start();
include '../fn/dbcon.php'; // Include database connection

// Fetch sections to create a separate table for each
$sqlSections = "SELECT * FROM sections"; // Adjust if you have specific criteria
$sectionsResult = $con->query($sqlSections);

// Fetch students for displaying, ensuring we only select those who are active
$sqlStudents = "SELECT s.*, sec.section_name FROM students s LEFT JOIN sections sec ON s.section_id = sec.id WHERE s.status = 'active' ORDER BY sec.section_name";
$resultStudents = $con->query($sqlStudents);

// Group students by section
$studentsBySection = [];
while ($student = $resultStudents->fetch_assoc()) {
    $studentsBySection[$student['section_id']][] = $student;
}

// Fetch students without a section
$sqlStudentsWithoutSection = "SELECT * FROM students WHERE section_id IS NULL AND status = 'active'";
$studentsWithoutSectionResult = $con->query($sqlStudentsWithoutSection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h2 class="text-xl font-semibold mb-4">Existing Students</h2>
        
        <?php while ($section = $sectionsResult->fetch_assoc()): ?>
            <h3 class="text-lg font-semibold mt-6"><?php echo htmlspecialchars($section['section_name']); ?></h3>
            <table class="min-w-full bg-white rounded shadow mb-4">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border">LRN</th>
                        <th class="py-2 px-4 border">Name</th>
                        <th class="py-2 px-4 border">Email</th>
                        <th class="py-2 px-4 border">Grade Level</th>
                        <th class="py-2 px-4 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($studentsBySection[$section['id']])): ?>
                        <?php foreach ($studentsBySection[$section['id']] as $student): ?>
                            <tr>
                                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($student['lrn']); ?></td>
                                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($student['email']); ?></td>
                                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($student['grade_level']); ?></td>
                                <td class="py-2 px-4 border-b">
                                    <a href="edit_student.php?id=<?php echo $student['id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a>
                                    <form method="post" action="../fn/soft_delete_student.php" style="display:inline;">
                                        <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                        <button type="submit" class="text-red-500 hover:text-red-700 ml-4" onclick="return confirm('Are you sure you want to delete this student?');">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="py-2 px-4 text-center">No students found in this section.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endwhile; ?>

        <!-- Section for students without a section -->
        <h3 class="text-lg font-semibold mt-6">Students Without a Section</h3>
<table class="min-w-full bg-white rounded shadow mb-4">
    <thead>
        <tr>
            <th class="py-2 px-4 border">LRN</th>
            <th class="py-2 px-4 border">Name</th>
            <th class="py-2 px-4 border">Email</th>
            <th class="py-2 px-4 border">Grade Level</th>
            <th class="py-2 px-4 border">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($studentsWithoutSectionResult->num_rows > 0): ?>
            <?php while ($studentWithoutSection = $studentsWithoutSectionResult->fetch_assoc()): ?>
                <tr>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($studentWithoutSection['lrn']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($studentWithoutSection['first_name'] . ' ' . $studentWithoutSection['last_name']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($studentWithoutSection['email']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($studentWithoutSection['grade_level']); ?></td>
                    <td class="py-2 px-4 border-b">
                        <a href="edit_student.php?id=<?php echo $studentWithoutSection['id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a>
                        <form method="post" action="../fn/soft_delete_student.php" style="display:inline;">
                            <input type="hidden" name="student_id" value="<?php echo $studentWithoutSection['id']; ?>">
                            <button type="submit" class="text-red-500 hover:text-red-700 ml-4" onclick="return confirm('Are you sure you want to delete this student?');">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="py-2 px-4 text-center">No students without a section found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

    </div>
</body>
</html>

<?php
$con->close(); // Close database connection
?>
