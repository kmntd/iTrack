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

// Fetch the quiz ID from the URL
$quiz_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$quiz_id) {
    die("No quiz selected.");
}

// Fetch the quiz details based on the quiz ID
$quizStmt = $con->prepare("
    SELECT q.*, s.subject_name, t.first_name, t.last_name 
    FROM quizzes q
    JOIN subjects s ON q.subject_id = s.id
    JOIN teacher_section ts ON s.id = ts.subject_id
    JOIN teachers t ON ts.teacher_id = t.id
    WHERE q.id = ?
");
$quizStmt->bind_param('i', $quiz_id);
$quizStmt->execute();
$quizResult = $quizStmt->get_result();

if ($quizResult->num_rows === 0) {
    die("No quiz found for the given ID: " . htmlspecialchars($quiz_id));
}

$quiz = $quizResult->fetch_assoc();

// Ensure that quiz details are present
if (!$quiz) {
    die("Error: Unable to fetch quiz details.");
}

// Fetch the student's score for the quiz
function fetchQuizScore($quiz_id, $student_id, $con) {
    $stmt = $con->prepare("SELECT score, submitted_at FROM quiz_submissions WHERE quiz_id = ? AND student_id = ?");
    $stmt->bind_param('ii', $quiz_id, $student_id);
    $stmt->execute();
    $scoreResult = $stmt->get_result();
    return $scoreResult->num_rows > 0 ? $scoreResult->fetch_assoc() : null;
}

$submission = fetchQuizScore($quiz_id, $student_id, $con);
$score = $submission['score'] ?? null;
$submitted_at = $submission['submitted_at'] ?? null;

// Check if the deadline has passed
$currentDateTime = date('Y-m-d H:i:s');
$isDeadlinePassed = strtotime($currentDateTime) > strtotime($quiz['due_date']);

// If deadline passed and the quiz wasn't submitted, insert a zero score with default values for other required fields
if ($isDeadlinePassed && !$submitted_at) {
    $defaultAnswers = ''; // Set a default value for answers, like an empty string if necessary
    
    // Insert with score 0, current time for submitted_at, and default answers
    $stmt = $con->prepare("INSERT INTO quiz_submissions (quiz_id, student_id, score, submitted_at, answers) VALUES (?, ?, 0, ?, ?)");
    $stmt->bind_param('iiss', $quiz_id, $student_id, $currentDateTime, $defaultAnswers);
    $stmt->execute();
    $score = 0; // Set the score to 0 since the deadline is missed
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($quiz['title'] ?? 'Quiz Details'); ?> - Quiz Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100">
<div class="container mx-auto mt-5">
    <h1 class="text-3xl font-semibold mb-5"><?php echo htmlspecialchars($quiz['title'] ?? 'Quiz Title Not Available'); ?></h1>
    <h2 class="text-xl mb-4">Subject: <?php echo htmlspecialchars($quiz['subject_name'] ?? 'Subject Not Available'); ?></h2>
    <h2 class="text-xl mb-4">Instructor: <?php echo htmlspecialchars($quiz['first_name'] . ' ' . $quiz['last_name'] ?? 'Instructor Not Available'); ?></h2>
    
    <div class="bg-white p-5 rounded shadow-md mb-5">
        <h3 class="text-lg font-semibold">Description</h3>
        <p><?php echo htmlspecialchars($quiz['description'] ?? 'No description available.'); ?></p>

        <h3 class="text-lg font-semibold mt-4">Due Date</h3>
        <p><?php echo date('Y-m-d H:i', strtotime($quiz['due_date'])); ?></p>
    </div>

    <div class="bg-white p-5 rounded shadow-md">
        <h3 class="text-lg font-semibold">Your Score</h3>
        <p><?php echo $score !== null ? htmlspecialchars($score) : 'Not submitted'; ?></p>
    </div>

    <?php if ($isDeadlinePassed): ?>
        <!-- Button is unclickable if deadline has passed -->
        <button class="mt-5 inline-block bg-gray-500 text-white py-2 px-4 rounded cursor-not-allowed" disabled>
            Quiz Closed
        </button>
    <?php else: ?>
        <!-- Button is clickable if deadline hasn't passed -->
        <a href="take_quiz.php?id=<?php echo htmlspecialchars($quiz_id); ?>" class="mt-5 inline-block bg-blue-500 text-white py-2 px-4 rounded">
            Take Quiz
        </a>
    <?php endif; ?>
</div>
</body>
</html>
