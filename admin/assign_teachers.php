<?php
session_start();
include '../fn/dbcon.php'; // Include database connection

// Fetch all teachers, sections, and subjects
$teachers_result = $con->query("SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM teachers");
$sections_result = $con->query("SELECT id, section_name FROM sections");
$subjects_result = $con->query("SELECT id, subject_name FROM subjects");

// Handle delete request internally
if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];

    // Prepare the delete statement for soft delete
    $stmt_delete = $con->prepare("UPDATE teacher_section SET deleted_at = NOW() WHERE id = ?");
    $stmt_delete->bind_param("i", $delete_id);

    // Execute the delete statement and check for errors
    if ($stmt_delete->execute()) {
        header("Location: assign_teacherS.php?deleted=1");
        exit();
    } else {
        echo "Error deleting assignment: " . $stmt_delete->error;
    }

    $stmt_delete->close();
}

// Fetch assigned teachers who have not been soft deleted
$assigned_query = "
    SELECT ts.id, CONCAT(t.first_name, ' ', t.last_name) AS teacher_name, 
           s.section_name, ts.year_level, sub.subject_name
    FROM teacher_section ts
    JOIN teachers t ON ts.teacher_id = t.id
    JOIN sections s ON ts.section_id = s.id
    JOIN subjects sub ON ts.subject_id = sub.id
    WHERE ts.deleted_at IS NULL"; 
$assigned_result = $con->query($assigned_query);

// Fetch the assignment details for editing
$assignment_to_edit = null;
if (isset($_GET['edit_id'])) {
    $edit_stmt = $con->prepare("SELECT * FROM teacher_section WHERE id = ?");
    $edit_stmt->bind_param("i", $_GET['edit_id']);
    $edit_stmt->execute();
    $assignment_to_edit = $edit_stmt->get_result()->fetch_assoc();
    $edit_stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Teachers to Sections</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h2 class="text-xl font-semibold mb-4">Assign Teacher to Section</h2>

        <!-- Teacher Assignment Form -->
        <form method="post" action="../fn/assign_teacher_process.php">
            <input type="hidden" name="edit_id" value="<?= $assignment_to_edit['id'] ?? '' ?>">
            <div class="mb-4">
                <label for="teacher_id" class="block text-sm font-medium">Select Teacher:</label>
                <select name="teacher_id" id="teacher_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
                    <option value="">Select a teacher</option>
                    <?php while ($teacher = $teachers_result->fetch_assoc()): ?>
                        <option value="<?= $teacher['id'] ?>" <?= isset($assignment_to_edit) && $assignment_to_edit['teacher_id'] == $teacher['id'] ? 'selected' : '' ?>>
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
                        <option value="<?= $section['id'] ?>" <?= isset($assignment_to_edit) && $assignment_to_edit['section_id'] == $section['id'] ? 'selected' : '' ?>>
                            <?= $section['section_name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="year_level" class="block text-sm font-medium">Select Year Level:</label>
                <select name="year_level" id="year_level" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
                    <option value="">Select Year Level</option>
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <option value="<?= $i ?>" <?= isset($assignment_to_edit) && $assignment_to_edit['year_level'] == $i ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="subject_id" class="block text-sm font-medium">Select Subject:</label>
                <select name="subject_id" id="subject_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
                    <option value="">Select a subject</option>
                    <?php while ($subject = $subjects_result->fetch_assoc()): ?>
                        <option value="<?= $subject['id'] ?>" <?= isset($assignment_to_edit) && $assignment_to_edit['subject_id'] == $subject['id'] ? 'selected' : '' ?>>
                            <?= $subject['subject_name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded"><?= isset($assignment_to_edit) ? 'Update Assignment' : 'Assign Teacher' ?></button>
        </form>

        <!-- Table for displaying teacher assignments -->
        <h2 class="text-xl font-semibold mb-4">Assigned Teachers to Sections</h2>
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2">Teacher Name</th>
                    <th class="px-4 py-2">Section</th>
                    <th class="px-4 py-2">Year Level</th>
                    <th class="px-4 py-2">Subject</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($assignment = $assigned_result->fetch_assoc()): ?>
                    <tr>
                        <td class="border px-4 py-2"><?= $assignment['teacher_name'] ?></td>
                        <td class="border px-4 py-2"><?= $assignment['section_name'] ?></td>
                        <td class="border px-4 py-2"><?= $assignment['year_level'] ?></td>
                        <td class="border px-4 py-2"><?= $assignment['subject_name'] ?></td>
                        <td class="border px-4 py-2">
                            <a href="edit_assign.php?id=<?= $assignment['id'] ?>" class="text-blue-500">Edit</a>
                            <!-- Internal form for soft delete -->
                            <form method="post" action="" style="display:inline;">
                                <input type="hidden" name="delete_id" value="<?= $assignment['id'] ?>">
                                <button type="submit" class="text-red-500 ml-2">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$con->close(); // Close database connection
?>
