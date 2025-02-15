<?php
session_start();
if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacher_login.php");
    exit();
}

// Include database connection
include '../fn/dbcon.php';

// Get subject ID from the URL
if (!isset($_GET['subject_id'])) {
    echo "Invalid Subject.";
    exit();
}
$subject_id = intval($_GET['subject_id']);
$teacher_id = $_SESSION['teacher_id'];

// Fetch subject details
$subjectQuery = "SELECT subject_name FROM subjects WHERE id = ?";
$subjectStmt = $con->prepare($subjectQuery);
$subjectStmt->bind_param('i', $subject_id);
$subjectStmt->execute();
$subjectResult = $subjectStmt->get_result();

if ($subjectResult->num_rows == 0) {
    echo "Subject not found.";
    exit();
}

$subject = $subjectResult->fetch_assoc();
$subject_name = htmlspecialchars($subject['subject_name']);

echo "<h1>Subject: $subject_name</h1>";

// Fetch assignments for the subject created by the teacher
$assignmentsQuery = "SELECT a.id, a.assignment_title, a.due_date, a.created_at 
                     FROM assignments a 
                     WHERE a.subject_id = ? AND a.teacher_id = ?";
$stmt = $con->prepare($assignmentsQuery);
$stmt->bind_param('ii', $subject_id, $teacher_id);
$stmt->execute();
$assignmentsResult = $stmt->get_result();

// Fetch quizzes for the subject created by the teacher
$quizzesQuery = "SELECT q.id, q.title, q.created_at 
                 FROM quizzes q 
                 WHERE q.subject_id = ? AND q.teacher_id = ?";
$quizStmt = $con->prepare($quizzesQuery);
$quizStmt->bind_param('ii', $subject_id, $teacher_id);
$quizStmt->execute();
$quizzesResult = $quizStmt->get_result();
?>

<a href="teacher_home.php">Back to Home</a>

<h2 class="mt-4 text-lg font-semibold">My Assignments</h2>

<?php if ($assignmentsResult->num_rows > 0): ?>
    <table class="min-w-full bg-white border border-gray-300 mt-2">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Assignment Title</th>
                <th class="py-2 px-4 border-b">Due Date</th>
                <th class="py-2 px-4 border-b">Created At</th>
                <th class="py-2 px-4 border-b">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($assignment = $assignmentsResult->fetch_assoc()): ?>
                <tr>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($assignment['assignment_title']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($assignment['due_date']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($assignment['created_at']); ?></td>
                    <td class="py-2 px-4 border-b">
                        <a href="view_submissions.php?assignment_id=<?php echo $assignment['id']; ?>" class="text-blue-500">View Submissions</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No assignments found for this subject.</p>
<?php endif; ?>

<h2 class="mt-4 text-lg font-semibold">My Quizzes</h2>

<?php if ($quizzesResult->num_rows > 0): ?>
    <table class="min-w-full bg-white border border-gray-300 mt-2">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Quiz Title</th>
                <th class="py-2 px-4 border-b">Created At</th>
                <th class="py-2 px-4 border-b">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($quiz = $quizzesResult->fetch_assoc()): ?>
                <tr>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($quiz['title']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($quiz['created_at']); ?></td>
                    <td class="py-2 px-4 border-b">
                        <a href="view_quiz_submissions.php?quiz_id=<?php echo $quiz['id']; ?>" class="text-blue-500">View Submissions</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No quizzes found for this subject.</p>
<?php endif; ?>

<?php
// Close statements and connection
$stmt->close();
$quizStmt->close();
$con->close();
?>
