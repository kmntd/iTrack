<?php
session_start();
include '../fn/dbcon.php'; // Include database connection

// Handle section deletion
if (isset($_GET['delete'])) {
    $section_id = $_GET['delete'];
    $query = "UPDATE sections SET deleted_at = NOW() WHERE id = $section_id";
    $con->query($query);
    header("Location: manage_sections.php"); // Redirect after deletion
}

// Fetch sections with student count
$sql = "SELECT s.id, s.section_name, COUNT(st.id) as student_count 
        FROM sections s 
        LEFT JOIN students st ON s.id = st.section_id 
        WHERE s.deleted_at IS NULL 
        GROUP BY s.id";

$result = $con->query($sql);
?>

<?php if (isset($_SESSION['message'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <?php echo $_SESSION['message']; ?>
        <?php unset($_SESSION['message']); ?>
    </div>
<?php endif; ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sections</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        function editSection(id, name) {
            // Logic for opening an edit modal or redirecting to edit page
            const sectionName = prompt("Edit Section Name:", name);
            if (sectionName !== null) {
                window.location.href = `edit_section.php?id=${id}&name=${encodeURIComponent(sectionName)}`;
            }
        }
    </script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h2 class="text-xl font-semibold mb-4">Manage Sections</h2>
        
        <table class="min-w-full bg-white rounded shadow">
            <thead>
                <tr>
                    <th class="py-2 px-4 border">ID</th>
                    <th class="py-2 px-4 border">Section Name</th>
                    <th class="py-2 px-4 border">Number of Students</th>
                    <th class="py-2 px-4 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="py-2 px-4 border-b"><?php echo $row['id']; ?></td>
                            <td class="py-2 px-4 border-b"><?php echo $row['section_name']; ?></td>
                            <td class="py-2 px-4 border-b"><?php echo $row['student_count']; ?></td>
                            <td class="py-2 px-4 border-b">
                                <button onclick="editSection(<?php echo $row['id']; ?>, '<?php echo $row['section_name']; ?>')" class="text-blue-500 hover:text-blue-700">Edit</button>
                                <a href="view_section.php?id=<?php echo $row['id']; ?>" class="text-green-500 hover:text-green-700 ml-4">View</a>
                                <a href="?delete=<?php echo $row['id']; ?>" class="text-red-500 hover:text-red-700 ml-4" onclick="return confirm('Are you sure you want to delete this section?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="py-2 px-4 text-center">No sections found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="mt-6">
            <h3 class="text-lg font-semibold">Add New Section</h3>
            <form action="../fn/create_section.php" method="POST" class="mt-4">
                <input type="text" name="section_name" placeholder="Section Name" required class="border rounded p-2" />
                <button type="submit" class="bg-blue-500 text-white rounded p-2 ml-4">Add Section</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php
$con->close(); // Close database connection
?>
