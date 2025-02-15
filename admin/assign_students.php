<?php
session_start();
include '../fn/dbcon.php'; // Include database connection

// Fetch all active students
$students_query = "SELECT * FROM students WHERE status = 'active'"; // Display only active students
$students_result = $con->query($students_query);

// Fetch all sections
$sections_query = "SELECT * FROM sections";
$sections_result = $con->query($sections_query);

// Fetch all subjects
$subjects_query = "SELECT * FROM subjects WHERE deleted_at IS NULL"; // Exclude soft-deleted subjects
$subjects_result = $con->query($subjects_query);

// Handle assignment form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['assign_students'])) {
        $student_ids = $_POST['student_ids']; // Array of selected student IDs
        $section_id = $_POST['section_id'];
        $subject_id = $_POST['subject_id'];
        $year_level = $_POST['year_level']; // Add year_level

        if (!empty($student_ids) && !empty($section_id) && !empty($subject_id) && !empty($year_level)) {
            // Prepare SQL statement to insert into `student_section` table
            $student_section_insert_stmt = $con->prepare("
                INSERT INTO student_section (student_id, section_id, year_level, subject_id) 
                VALUES (?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE section_id = VALUES(section_id), year_level = VALUES(year_level), subject_id = VALUES(subject_id)
            ");

            // Loop through selected students and insert/update `student_section`
            foreach ($student_ids as $student_id) {
                $student_section_insert_stmt->bind_param("iiii", $student_id, $section_id, $year_level, $subject_id);
                $student_section_insert_stmt->execute();
            }

            $student_section_insert_stmt->close();

            $_SESSION['message'] = 'Students assigned to section and subject successfully!';
        } else {
            $_SESSION['error'] = 'Please select students, a section, subject, and year level.';
        }
    }
}
?>

<?php
    // Handle delete action
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_student'])) {
        $delete_id = $_POST['delete_id'];

        // Soft delete query
        $deleteQuery = "UPDATE students SET status = 'inactive' WHERE id = ?";
        $stmt = $con->prepare($deleteQuery);
        $stmt->bind_param('i', $delete_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = 'Student deleted successfully!';
            echo "<script>location.href='';</script>"; // Refresh the page
        } else {
            $_SESSION['error'] = 'Error deleting student.';
        }

        $stmt->close();
    }
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Students</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h2 class="text-xl font-semibold mb-4">Assign Students to Section and Subject</h2>

        <!-- Display messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="bg-green-500 text-white p-2 mb-4"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-500 text-white p-2 mb-4"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- Assignment Form -->
        <form method="post" action="assign_students.php" class="mb-6">
            <div class="mb-4">
                <label for="section_id" class="block text-sm font-medium">Select Section:</label>
                <select name="section_id" id="section_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
                    <option value="">Select a section</option>
                    <?php while ($section = $sections_result->fetch_assoc()): ?>
                        <option value="<?php echo $section['id']; ?>"><?php echo $section['section_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="subject_id" class="block text-sm font-medium">Select Subject:</label>
                <select name="subject_id" id="subject_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
                    <option value="">Select a subject</option>
                    <?php while ($subject = $subjects_result->fetch_assoc()): ?>
                        <option value="<?php echo $subject['id']; ?>"><?php echo $subject['subject_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-4">
            <label for="year_level" class="block text-sm font-medium">Select Year Level:</label>
<select name="year_level" id="year_level" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
    <option value="">Select Year Level</option>
    <?php for ($i = 1; $i <= 4; $i++): ?>
        <option value="<?= $i ?>" <?= isset($assignment_to_edit) && $assignment_to_edit['year_level'] == $i ? 'selected' : '' ?>>Year<?= $i ?></option>
    <?php endfor; ?>
</select>
</div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Select Students:</label>
                <div class="max-h-64 overflow-y-scroll border p-4 rounded-md">
                    <?php while ($student = $students_result->fetch_assoc()): ?>
                        <div class="flex items-center mb-2">
                            <input type="checkbox" name="student_ids[]" value="<?php echo $student['id']; ?>" class="mr-2">
                            <label><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></label>
                        </div>
                    <?php endwhile; ?>
                </div>
                <small class="text-gray-500">Select as many students as you want to assign.</small>
            </div>

            <button type="submit" name="assign_students" class="bg-blue-500 text-white px-4 py-2 rounded">Assign Students</button>
        </form>

        <!-- Display Student Assignment Table -->
        <div class="mt-6">
            <h2 class="text-xl font-semibold mb-4">Student Assignments</h2>
            <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-2 px-4 border">Student Name</th>
                    <th class="py-2 px-4 border">Section</th>
                    <th class="py-2 px-4 border">Subject</th>
                    <th class="py-2 px-4 border">Year Level</th>
                    <th class="py-2 px-4 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $assignments_query = "
                SELECT s.id, s.first_name, s.last_name, sec.section_name, sub.subject_name, ss.year_level
                FROM students AS s
                LEFT JOIN student_section AS ss ON s.id = ss.student_id
                LEFT JOIN sections AS sec ON ss.section_id = sec.id
                LEFT JOIN subjects AS sub ON ss.subject_id = sub.id
                WHERE s.status = 'active'
            ";
            
                $assignments_result = $con->query($assignments_query);
                while ($assignment = $assignments_result->fetch_assoc()): ?>
                    <tr>
                        <td class="py-2 px-4 border"><?php echo $assignment['first_name'] . ' ' . $assignment['last_name']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $assignment['section_name']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $assignment['subject_name']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $assignment['year_level']; ?></td>
                        <td class="py-2 px-4 border">
                            <!-- Edit Button, redirects to edit_student.php with the student ID -->
                            <a href="edit_student.php?id=<?php echo $assignment['id']; ?>" class="text-blue-500 hover:underline">Edit</a>
                            |
                            <!-- Delete Button, handled internally -->
                            <form method="POST" action="" style="display:inline;">
                                <input type="hidden" name="delete_id" value="<?php echo $assignment['id']; ?>">
                                <button type="submit" name="delete_student" class="text-red-500 hover:underline" onclick="return confirm('Are you sure you want to delete this student?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    </div>
</body>
</html>

<?php
$con->close(); // Close database connection
?>
