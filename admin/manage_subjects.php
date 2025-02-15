<?php
session_start();
include '../fn/dbcon.php'; // Include database connection

// Fetch all subjects
$subjects_query = "SELECT * FROM subjects";
$subjects_result = $con->query($subjects_query);

// Handle subject form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_subject'])) {
        $subject_name = $_POST['subject_name'];
        $subject_code = $_POST['subject_code'];

        // Prepare the SQL statement
        $stmt = $con->prepare("INSERT INTO subjects (subject_name, subject_code) VALUES (?, ?)");
        $stmt->bind_param("ss", $subject_name, $subject_code);

        // Execute the statement
        if ($stmt->execute()) {
            echo "<script>alert('Subject added successfully.'); window.location.href = 'manage_subjects.php';</script>";
        } else {
            echo "<script>alert('Error adding subject.');</script>";
        }

        $stmt->close();
    } elseif (isset($_POST['delete_subject'])) {
        $subject_id = $_POST['subject_id'];

        // Prepare the SQL statement for soft delete
        $stmt = $con->prepare("UPDATE subjects SET deleted_at = NOW() WHERE id = ?");
        $stmt->bind_param("i", $subject_id);

        // Execute the statement
        if ($stmt->execute()) {
            echo "<script>alert('Subject deleted successfully.');</script>";
        } else {
            echo "<script>alert('Error deleting subject.');</script>";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subjects</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h2 class="text-xl font-semibold mb-4">Manage Subjects</h2>

        <!-- Subject Addition Form -->
        <form method="post" action="manage_subjects.php">
            <div class="mb-4">
                <label for="subject_name" class="block text-sm font-medium">Subject Name:</label>
                <input type="text" name="subject_name" id="subject_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
            </div>
            <div class="mb-4">
                <label for="subject_code" class="block text-sm font-medium">Subject Code:</label>
                <input type="text" name="subject_code" id="subject_code" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
            </div>
            <button type="submit" name="add_subject" class="bg-blue-500 text-white px-4 py-2 rounded">Add Subject</button>
        </form>

        <!-- Table for displaying subjects -->
        <h2 class="text-xl font-semibold mb-4 mt-6">Existing Subjects</h2>
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr>
                    <th class="border px-4 py-2">ID</th>
                    <th class="border px-4 py-2">Subject Name</th>
                    <th class="border px-4 py-2">Subject Code</th>
                    <th class="border px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $subjects_result->fetch_assoc()): ?>
                <tr>
                    <td class="border px-4 py-2"><?php echo $row['id']; ?></td>
                    <td class="border px-4 py-2"><?php echo htmlspecialchars($row['subject_name']); ?></td>
                    <td class="border px-4 py-2"><?php echo htmlspecialchars($row['subject_code']); ?></td>
                    <td class="border px-4 py-2">
                        <!-- Soft delete the subject -->
                        <form method="post" action="manage_subjects.php" style="display:inline;">
                            <input type="hidden" name="subject_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete_subject" class="text-red-500 hover:underline">Delete</button>
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
