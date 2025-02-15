<?php
session_start();
if (!isset($_SESSION['teacher_id'])) {
    header("Location: teachers/teacher_login.php");
    exit();
}

// Include the database connection
include '../fn/dbcon.php'; // Ensure this path is correct based on your directory structure

// Fetch the list of students who submitted for the assignment
$assignment_id = $_GET['assignment_id']; // Get the assignment ID from the query parameter
$student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null; // Get the student ID (if set)

// Fetch all students who submitted
$students_stmt = $con->prepare("SELECT DISTINCT t.id AS student_id, t.first_name, t.last_name 
                                FROM submissions s 
                                INNER JOIN students t ON s.student_id = t.id 
                                WHERE s.assignment_id = ?");
$students_stmt->bind_param("i", $assignment_id);
$students_stmt->execute();
$students_result = $students_stmt->get_result();

// SQL to fetch submissions, with optional filtering by student
$sql = "SELECT s.id AS submission_id, s.score, s.file_path, s.submission_time, 
               t.first_name, t.last_name, a.perfect_score 
        FROM submissions s 
        INNER JOIN students t ON s.student_id = t.id 
        INNER JOIN assignments a ON s.assignment_id = a.id 
        WHERE s.assignment_id = ?";

// If a specific student is selected, add filtering by student
if ($student_id) {
    $sql .= " AND s.student_id = ?";
}

// Prepare the SQL query
$stmt = $con->prepare($sql);
if ($student_id) {
    $stmt->bind_param("ii", $assignment_id, $student_id);
} else {
    $stmt->bind_param("i", $assignment_id);
}
$stmt->execute();
$result = $stmt->get_result();

// Fetch perfect score directly from assignments table
$assignment_stmt = $con->prepare("SELECT perfect_score FROM assignments WHERE id = ?");
$assignment_stmt->bind_param("i", $assignment_id);
$assignment_stmt->execute();
$assignment_result = $assignment_stmt->get_result();
$assignment_row = $assignment_result->fetch_assoc();
$perfect_score = $assignment_row ? $assignment_row['perfect_score'] : 100; // Default to 100 if not set
?>

<h1>View Submissions</h1>

<!-- Filter by student dropdown -->
<form method="GET" action="view_submissions.php">
    <input type="hidden" name="assignment_id" value="<?php echo htmlspecialchars($assignment_id); ?>">
    <label for="student_id">Filter by Student:</label>
    <select name="student_id" onchange="this.form.submit()">
        <option value="">All Students</option>
        <?php while ($student = $students_result->fetch_assoc()): ?>
            <option value="<?php echo htmlspecialchars($student['student_id']); ?>" 
                <?php echo $student_id == $student['student_id'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($student['first_name'] . " " . $student['last_name']); ?>
            </option>
        <?php endwhile; ?>
    </select>
</form>

<!-- Section for entering perfect score -->
<form method="POST" action="../fn/set_perfect_score.php">
    <input type="hidden" name="assignment_id" value="<?php echo htmlspecialchars($assignment_id); ?>">
    <label for="perfect_score">Perfect Score:</label>
    <input type="number" name="perfect_score" value="<?php echo htmlspecialchars($perfect_score); ?>" required>
    <button type="submit">Set Perfect Score</button>
</form>

<table>
    <thead>
        <tr>
            <th>Student Name</th>
            <th>File</th>
            <th>Submission Time</th>
            <th>Score</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Reset the result pointer to the beginning
        $result->data_seek(0);
        while ($submission = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($submission['first_name'] . " " . $submission['last_name']); ?></td>
                <td><a href="../students/uploads/<?php echo htmlspecialchars($submission['file_path']); ?>" target="_blank">View File</a></td>
                <td><?php echo htmlspecialchars($submission['submission_time']); ?></td>
                <td>
                    <form class="submit-score-form" method="POST" action="../fn/grade_submission.php">
                        <input type="hidden" name="submission_id" value="<?php echo htmlspecialchars($submission['submission_id']); ?>">
                        <input type="number" name="score" value="<?php echo htmlspecialchars($submission['score']); ?>" required>
                </td>
                <td>
                    <button type="submit">Submit Score</button>
                </td>
                    </form>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
