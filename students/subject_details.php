<?php
// Include database connection
include '../fn/dbcon.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session to access the logged-in student's ID
session_start();

// Check if the student ID is set in the session
if (!isset($_SESSION['student_id'])) {
    die("Error: You need to log in first.");
}

// Get the student ID from the session
$student_id = $_SESSION['student_id'];

// Fetch the section ID of the student
$sectionStmt = $con->prepare("SELECT section_id FROM student_section WHERE student_id = ?");
$sectionStmt->bind_param('i', $student_id);
$sectionStmt->execute();
$sectionResult = $sectionStmt->get_result();
$section = $sectionResult->fetch_assoc();

if (!$section) {
    die("No section found for student ID: " . htmlspecialchars($student_id));
}

$section_id = $section['section_id'];

// Fetch the subject code from the URL
$subject_code = isset($_GET['subject_code']) ? $_GET['subject_code'] : null;

if (!$subject_code) {
    die("No subject selected.");
}

// Fetch the subject details based on the subject code
$subjectStmt = $con->prepare("
    SELECT s.id, s.subject_name, t.id AS teacher_id, t.first_name, t.last_name 
    FROM teacher_section ts
    JOIN subjects s ON ts.subject_id = s.id
    JOIN teachers t ON ts.teacher_id = t.id
    WHERE ts.section_id = ? AND s.subject_code = ?
");
$subjectStmt->bind_param('is', $section_id, $subject_code);
$subjectStmt->execute();
$subjectResult = $subjectStmt->get_result();

if ($subjectResult->num_rows === 0) {
    die("No subject found for the given subject code: " . htmlspecialchars($subject_code));
}

$subject = $subjectResult->fetch_assoc();
$subject_id = $subject['id'];

// Fetch assignments for the selected subject
$assignmentsStmt = $con->prepare("
    SELECT a.* FROM assignments a
    JOIN subjects s ON a.subject_id = s.id
    JOIN teacher_section ts ON s.id = ts.subject_id
    WHERE ts.section_id = ? AND s.id = ?
");
$assignmentsStmt->bind_param('ii', $section_id, $subject_id);
$assignmentsStmt->execute();
$assignmentsResult = $assignmentsStmt->get_result();
$assignments = $assignmentsResult->fetch_all(MYSQLI_ASSOC);

// Fetch quizzes for the selected subject
$quizzesStmt = $con->prepare("
    SELECT q.* FROM quizzes q
    JOIN subjects s ON q.subject_id = s.id
    JOIN teacher_section ts ON s.id = ts.subject_id
    WHERE ts.section_id = ? AND s.id = ?
");
$quizzesStmt->bind_param('ii', $section_id, $subject_id);
$quizzesStmt->execute();
$quizzesResult = $quizzesStmt->get_result();
$quizzes = $quizzesResult->fetch_all(MYSQLI_ASSOC);

// Function to fetch student score for a specific assignment
function fetchStudentScore($assignment_id, $student_id, $con) {
    $stmt = $con->prepare("SELECT score FROM submissions WHERE assignment_id = ? AND student_id = ?");
    $stmt->bind_param('ii', $assignment_id, $student_id);
    $stmt->execute();
    $scoreResult = $stmt->get_result();
    return $scoreResult->num_rows > 0 ? $scoreResult->fetch_assoc()['score'] : null;
}

// Function to fetch student score for a specific quiz
function fetchQuizScore($quiz_id, $student_id, $con) {
    $stmt = $con->prepare("SELECT score FROM quiz_submissions WHERE quiz_id = ? AND student_id = ?");
    $stmt->bind_param('ii', $quiz_id, $student_id);
    $stmt->execute();
    $scoreResult = $stmt->get_result();
    return $scoreResult->num_rows > 0 ? $scoreResult->fetch_assoc()['score'] : null;
}

// Function to check if the assignment has been submitted
function fetchAssignmentStatus($assignment_id, $student_id, $con) {
    $stmt = $con->prepare("SELECT score FROM submissions WHERE assignment_id = ? AND student_id = ?");
    $stmt->bind_param('ii', $assignment_id, $student_id);
    $stmt->execute();
    $scoreResult = $stmt->get_result();
    return $scoreResult->num_rows > 0 ? 'Submitted' : 'Not submitted';
}

// Function to check if the quiz has been submitted
function fetchQuizStatus($quiz_id, $student_id, $con) {
    $stmt = $con->prepare("SELECT score FROM quiz_submissions WHERE quiz_id = ? AND student_id = ?");
    $stmt->bind_param('ii', $quiz_id, $student_id);
    $stmt->execute();
    $scoreResult = $stmt->get_result();
    return $scoreResult->num_rows > 0 ? 'Submitted' : 'Not submitted';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($subject['subject_name']); ?> - Subject Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100">
<div class="container mx-auto mt-5">
    <h1 class="text-3xl font-semibold mb-5"><?php echo htmlspecialchars($subject['subject_name']); ?></h1>
    <h2 class="text-xl mb-4">Instructor: <?php echo htmlspecialchars($subject['first_name'] . ' ' . $subject['last_name']); ?></h2>

    <h3 class="text-lg font-semibold mt-5">Assignments</h3>
    <?php if (!empty($assignments)): ?>
        <ul class='list-disc pl-5'>
            <?php foreach ($assignments as $assignment): ?>
                <li>
                    <a class='text-blue-500' href="assignment_details.php?id=<?php echo htmlspecialchars($assignment['id']); ?>">
                        <?php echo htmlspecialchars($assignment['assignment_title']); ?>
                    </a>
                    <span class="text-gray-600"> - Due: <?php echo date('Y-m-d H:i', strtotime($assignment['due_date'])); ?></span>
                    <span class="text-gray-600"> - Score: <?php echo fetchStudentScore($assignment['id'], $student_id, $con) ?: 'Not submitted'; ?></span>
                    <span class="text-gray-600"> - Status: <?php echo fetchAssignmentStatus($assignment['id'], $student_id, $con); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No assignments available for this subject.</p>
    <?php endif; ?>

    <h3 class="text-lg font-semibold mt-5">Quizzes</h3>
    <?php if (!empty($quizzes)): ?>
        <ul class='list-disc pl-5'>
            <?php foreach ($quizzes as $quiz): ?>
                <li>
                    <a class='text-blue-500' href="quiz_details.php?id=<?php echo htmlspecialchars($quiz['id']); ?>">
                        <?php echo htmlspecialchars($quiz['title']); ?>
                    </a>
                    <span class="text-gray-600"> - Due: <?php echo date('Y-m-d H:i', strtotime($quiz['due_date'])); ?></span>
                    <span class="text-gray-600"> - Score: <?php echo fetchQuizScore($quiz['id'], $student_id, $con) ?: 'Not submitted'; ?></span>
                    <span class="text-gray-600"> - Status: <?php echo fetchQuizStatus($quiz['id'], $student_id, $con); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No quizzes found for this subject.</p>
    <?php endif; ?>
</div>
</body>
</html>
