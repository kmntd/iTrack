<?php
session_start();
include '../fn/dbcon.php'; // Include database connection

// Get student ID from URL
$student_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($student_id == 0) {
    $_SESSION['error'] = 'Invalid student ID.';
    header('Location: assign_students.php');
    exit;
}

// Fetch student details
$student_query = "SELECT * FROM students WHERE id = ?";
$stmt = $con->prepare($student_query);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch all sections
$sections_query = "SELECT * FROM sections";
$sections_result = $con->query($sections_query);

// Fetch all subjects
$subjects_query = "SELECT * FROM subjects WHERE deleted_at IS NULL"; // Exclude soft-deleted subjects
$subjects_result = $con->query($subjects_query);

// Fetch current student section and subject data from student_section table
$student_section_query = "
    SELECT ss.section_id, ss.subject_id, ss.year_level 
    FROM student_section AS ss
    WHERE ss.student_id = ?
";
$stmt = $con->prepare($student_section_query);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$student_section = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $section_id = $_POST['section_id'];
    $subject_id = $_POST['subject_id'];
    $year_level = $_POST['year_level'];

    if (!empty($section_id) && !empty($subject_id) && !empty($year_level)) {
        // Update the student_section table
        $update_query = "
            INSERT INTO student_section (student_id, section_id, year_level, subject_id) 
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE section_id = VALUES(section_id), year_level = VALUES(year_level), subject_id = VALUES(subject_id)
        ";
        $stmt = $con->prepare($update_query);
        $stmt->bind_param("iiii", $student_id, $section_id, $year_level, $subject_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = 'Student details updated successfully!';
            header("Location: assign_students.php");
            exit;
        } else {
            $_SESSION['error'] = 'Failed to update student details.';
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = 'Please select section, subject, and year level.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h2 class="text-xl font-semibold mb-4">Edit Student Details: <?php echo $student['first_name'] . ' ' . $student['last_name']; ?></h2>

        <!-- Display messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="bg-green-500 text-white p-2 mb-4"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-500 text-white p-2 mb-4"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- Edit Form -->
        <form method="post" action="">
            <div class="mb-4">
                <label for="section_id" class="block text-sm font-medium">Select Section:</label>
                <select name="section_id" id="section_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
                    <option value="">Select a section</option>
                    <?php while ($section = $sections_result->fetch_assoc()): ?>
                        <option value="<?php echo $section['id']; ?>" <?php echo ($student_section['section_id'] == $section['id']) ? 'selected' : ''; ?>>
                            <?php echo $section['section_name']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="subject_id" class="block text-sm font-medium">Select Subject:</label>
                <select name="subject_id" id="subject_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
                    <option value="">Select a subject</option>
                    <?php while ($subject = $subjects_result->fetch_assoc()): ?>
                        <option value="<?php echo $subject['id']; ?>" <?php echo ($student_section['subject_id'] == $subject['id']) ? 'selected' : ''; ?>>
                            <?php echo $subject['subject_name']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="year_level" class="block text-sm font-medium">Select Year Level:</label>
                <select name="year_level" id="year_level" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
                    <option value="">Select Year Level</option>
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <option value="<?= $i ?>" <?= ($student_section['year_level'] == $i) ? 'selected' : ''; ?>>Year <?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update Student</button>
        </form>
    </div>
</body>
</html>

<?php
$con->close(); // Close database connection
?>
