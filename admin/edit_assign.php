<?php
session_start();
include '../fn/dbcon.php'; // Include database connection

// Check if an ID is provided for editing
if (isset($_GET['id'])) {
    $edit_id = $_GET['id'];
    
    // Fetch the assignment to edit
    $edit_query = "SELECT * FROM teacher_section WHERE id = ?";
    $edit_stmt = $con->prepare($edit_query);
    $edit_stmt->bind_param("i", $edit_id);
    $edit_stmt->execute();
    $assignment_to_edit = $edit_stmt->get_result()->fetch_assoc();
    $edit_stmt->close();
    
    // If no assignment found, redirect or show an error
    if (!$assignment_to_edit) {
        header("Location: assign_teachers.php?error=not_found");
        exit();
    }
} else {
    header("Location: assign_teachers.php");
    exit();
}

// Fetch all teachers, sections, and subjects
$teachers_query = "SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM teachers";
$teachers_result = $con->query($teachers_query);

$sections_query = "SELECT id, section_name FROM sections";
$sections_result = $con->query($sections_query);

$subjects_query = "SELECT id, subject_name FROM subjects";
$subjects_result = $con->query($subjects_query);

// Handle the form submission for updating
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $teacher_id = $_POST['teacher_id'];
    $section_id = $_POST['section_id'];
    $year_level = $_POST['year_level'];
    $subject_id = $_POST['subject_id'];

    // Prepare the update SQL statement
    $stmt_update = $con->prepare("UPDATE teacher_section SET teacher_id = ?, section_id = ?, year_level = ?, subject_id = ? WHERE id = ?");
    $stmt_update->bind_param("iiiii", $teacher_id, $section_id, $year_level, $subject_id, $edit_id);

    if ($stmt_update->execute()) {
        header("Location: assign_teachers.php?updated=1");
        exit();
    } else {
        echo "Error: " . $stmt_update->error;
    }
    $stmt_update->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Assignment</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h2 class="text-xl font-semibold mb-4">Edit Assignment</h2>

        <form method="post" action="">
            <div class="mb-4">
                <label for="teacher_id" class="block text-sm font-medium">Select Teacher:</label>
                <select name="teacher_id" id="teacher_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
                    <option value="">Select a teacher</option>
                    <?php while ($teacher = $teachers_result->fetch_assoc()): ?>
                        <option value="<?= $teacher['id'] ?>" <?= $assignment_to_edit['teacher_id'] == $teacher['id'] ? 'selected' : '' ?>>
                            <?= $teacher['name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="section_id" class="block text-sm font-medium">Select Section:</label>
                <select name="section_id" id="section_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
                    <option value="">Select a section</option>
                    <?php while ($section = $sections_result->fetch_assoc()): ?>
                        <option value="<?= $section['id'] ?>" <?= $assignment_to_edit['section_id'] == $section['id'] ? 'selected' : '' ?>>
                            <?= $section['section_name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="year_level" class="block text-sm font-medium">Select Year Level:</label>
                <select name="year_level" id="year_level" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
                    <option value="">Select Year Level</option>
                    <option value="1" <?= $assignment_to_edit['year_level'] == 1 ? 'selected' : '' ?>>1st Year</option>
                    <option value="2" <?= $assignment_to_edit['year_level'] == 2 ? 'selected' : '' ?>>2nd Year</option>
                    <option value="3" <?= $assignment_to_edit['year_level'] == 3 ? 'selected' : '' ?>>3rd Year</option>
                    <option value="4" <?= $assignment_to_edit['year_level'] == 4 ? 'selected' : '' ?>>4th Year</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="subject_id" class="block text-sm font-medium">Select Subject:</label>
                <select name="subject_id" id="subject_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
                    <option value="">Select a subject</option>
                    <?php while ($subject = $subjects_result->fetch_assoc()): ?>
                        <option value="<?= $subject['id'] ?>" <?= $assignment_to_edit['subject_id'] == $subject['id'] ? 'selected' : '' ?>>
                            <?= $subject['subject_name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update Assignment</button>
        </form>
    </div>
</body>
</html>

<?php
$con->close(); // Close database connection
?>
