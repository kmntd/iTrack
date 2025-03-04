<?php
session_start();
if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacher_login.php");
    exit();
}

// Include database connection
include '../fn/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);
    $section_id = intval($_POST['section_id']);
    $teacher_id = $_SESSION['teacher_id'];

    // Validate input
    if (empty($title) || empty($message) || empty($section_id)) {
        echo "<script>alert('All fields are required.');</script>";
    } else {
        
        
        // Insert announcement into the database
        $stmt = $con->prepare("INSERT INTO teacher_announcements (teacher_id, section_id, title, message) VALUES (?, ?, ?, ?)");
        // Correct the types here: 'iiis' means two integers and two strings
        $stmt->bind_param("iiss", $teacher_id, $section_id, $title, $message);

        if ($stmt->execute()) {
            echo "<script>alert('Announcement posted successfully!');</script>";
        } else {
            echo "<script>alert('Failed to post announcement: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    }
}


// Fetch sections assigned to the teacher
$teacher_id = $_SESSION['teacher_id'];
$sectionsQuery = "SELECT DISTINCT s.id, s.section_name 
                  FROM teacher_section ts
                  JOIN sections s ON ts.section_id = s.id 
                  WHERE ts.teacher_id = ?";
$sectionsStmt = $con->prepare($sectionsQuery);
$sectionsStmt->bind_param('i', $teacher_id);
$sectionsStmt->execute();
$sectionsResult = $sectionsStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Announcement</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-gray-200 p-4">
        <h1 class="text-2xl font-bold">Post an Announcement</h1>
        <nav class="mt-2">
            <a href="../fn/teacher_logout.php" class="text-blue-600 hover:underline">Logout</a>
        </nav>
    </header>
    <main class="p-4">
        <form action="" method="POST" class="bg-white p-6 rounded-lg shadow-md">
            <div class="mb-4">
                <label for="section_id" class="block text-gray-700">Select Section:</label>
                <select name="section_id" class="w-full border border-gray-300 rounded p-2" required>
                    <option value="">-- Select Section --</option>
                    <?php while ($section = $sectionsResult->fetch_assoc()): ?>
                        <option value="<?php echo $section['id']; ?>"><?php echo htmlspecialchars($section['section_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="title" class="block text-gray-700">Announcement Title:</label>
                <input type="text" name="title" class="w-full border border-gray-300 rounded p-2" required>
            </div>
            <div class="mb-4">
                <label for="message" class="block text-gray-700">Announcement Message:</label>
                <textarea name="message" rows="5" class="w-full border border-gray-300 rounded p-2" required></textarea>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Post Announcement</button>
        </form>
    </main>
</body>
</html>

<?php
// Close the connection
$sectionsStmt->close();
$con->close();
?>
