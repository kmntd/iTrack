<?php
session_start();
if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacher_login.php");
    exit();
}

// Include database connection
include '../fn/dbcon.php';

// Display teacher's name and ID
echo "<h1>Welcome, " . htmlspecialchars($_SESSION['teacher_name']) . "</h1>";
echo "<p>Your Teacher ID: " . htmlspecialchars($_SESSION['teacher_id']) . "</p>";

// Fetch sections assigned to the teacher
$teacher_id = $_SESSION['teacher_id'];
$sectionsQuery = "SELECT DISTINCT ts.section_id, s.section_name 
                  FROM teacher_section ts
                  JOIN sections s ON ts.section_id = s.id 
                  WHERE ts.teacher_id = ? AND ts.deleted_at IS NULL"; // Ensure only active assignments are fetched
$sectionsStmt = $con->prepare($sectionsQuery);
$sectionsStmt->bind_param('i', $teacher_id);
$sectionsStmt->execute();
$sectionsResult = $sectionsStmt->get_result();

// Fetch all results as an associative array
$sectionsArray = $sectionsResult->fetch_all(MYSQLI_ASSOC);


// Fetch announcements
$announcements_query = "SELECT * FROM admin_announcements ORDER BY created_at DESC";
$announcements_stmt = $con->prepare($announcements_query);
$announcements_stmt->execute();
$announcements_result = $announcements_stmt->get_result();

echo "<h3 class='text-xl font-semibold mb-4'>Announcements</h3>";
if ($announcements_result->num_rows > 0) {
    while ($announcement = $announcements_result->fetch_assoc()) {
        echo "<div class='bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4' role='alert'>";
        echo "<p><strong>Announcement:</strong> " . htmlspecialchars($announcement['message']) . "</p>";
        echo "<p><small>Posted on: " . htmlspecialchars($announcement['created_at']) . "</small></p>";
        echo "</div>";
    }
} else {
    echo "<p>No announcements available.</p>";
}


// Display Sections
echo "<h2>Assigned Sections</h2>";

if (count($sectionsArray) > 0): ?>
    <ul>
        <?php foreach ($sectionsArray as $section): ?>
            <li>
                <strong><?php echo htmlspecialchars($section['section_name']); ?></strong>
                <ul>
                    <?php
                    // Fetch subjects for this section
                    $subjectsQuery = "SELECT s.id, s.subject_name 
                                      FROM teacher_section ts
                                      JOIN subjects s ON ts.subject_id = s.id 
                                      WHERE ts.teacher_id = ? AND ts.section_id = ? AND ts.deleted_at IS NULL";
                    $subjectsStmt = $con->prepare($subjectsQuery);
                    $subjectsStmt->bind_param('ii', $teacher_id, $section['section_id']);
                    $subjectsStmt->execute();
                    $subjectsResult = $subjectsStmt->get_result();
                    
                    // Fetch all results as an associative array
                    $subjectsArray = $subjectsResult->fetch_all(MYSQLI_ASSOC);

                    if (count($subjectsArray) > 0): 
                        foreach ($subjectsArray as $subject): ?>
                            <li>
                                <a href="subject_details.php?subject_id=<?php echo $subject['id']; ?>">
                                    <?php echo htmlspecialchars($subject['subject_name']); ?>
                                </a>
                            </li>
                        <?php endforeach; 
                    else: ?>
                        <li>No subjects assigned to this section.</li>
                    <?php endif; 
                    ?>
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No sections assigned.</p>
<?php endif; ?>

<a href="create_quiz.php">Create Quiz</a>
<a href="create_assignment.php">Create Assignment</a>
<a href="../fn/teacher_logout.php">Logout</a>

<?php
// Close statements and connection
$sectionsStmt->close();
$con->close();
?>
