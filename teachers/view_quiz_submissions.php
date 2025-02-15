<?php
session_start();
if (!isset($_SESSION['teacher_id'])) {
    header("Location: teachers/teacher_login.php");
    exit();
}

// Include the database connection
include '../fn/dbcon.php'; // Ensure this path is correct based on your directory structure

// Get the quiz ID from the query parameter
$quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;

// Check if quiz ID is valid
if ($quiz_id <= 0) {
    echo "Invalid Quiz ID.";
    exit();
}

// SQL to fetch all students and their quiz submissions (if any)
$sql = "
    SELECT s.id AS student_id, s.first_name, s.last_name, qs.score 
    FROM students s 
    LEFT JOIN quiz_submissions qs ON s.id = qs.student_id AND qs.quiz_id = ?
";

// Prepare and execute the statement
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$result = $stmt->get_result();

// Output the quiz submissions
?>
<h1>Quiz Submissions</h1>
<table>
    <thead>
        <tr>
            <th>Student Name</th>
            <th>Score</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?></td>
                <td><?php echo htmlspecialchars($row['score'] !== null ? $row['score'] : 'Not Submitted'); ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php
// Close statement and connection
$stmt->close();
$con->close();
?>
