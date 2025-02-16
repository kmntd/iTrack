<?php
session_start();
if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacher_login.php");
    exit();
}

// Include database connection
include '../fn/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'];
    $section_id = $_POST['section_id'];
    $teacher_id = $_SESSION['teacher_id'];

    // Insert announcement into the database
    $stmt = $con->prepare("INSERT INTO teacher_announcements (teacher_id, section_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $teacher_id, $section_id, $message);

    if ($stmt->execute()) {
        echo "<script>alert('Announcement posted successfully!');</script>";
    } else {
        echo "<script>alert('Failed to post announcement.');</script>";
    }

    $stmt->close();
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
    <header>
        <h1>Post an Announcement</h1>
        <nav>
            <a href="../fn/teacher_logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <form action="" method="POST" class="mt-4">
            <div>
                <label for="section_id">Select Section:</label>
                <select name="section_id" required>
                    <option value="">-- Select Section --</option>
                    <?php while ($section = $sectionsResult->fetch_assoc()): ?>
                        <option value="<?php echo $section['id']; ?>"><?php echo htmlspecialchars($section['section_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label for="message">Announcement Message:</label>
                <textarea name="message" rows="5" required></textarea>
            </div>
            <button type="submit">Post Announcement</button>
        </form>
    </main>
</body>
</html>

<?php
// Close the connection
$sectionsStmt->close();
$con->close();
?>
