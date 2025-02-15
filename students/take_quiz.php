<?php
// Include database connection
include '../fn/dbcon.php';
session_start();

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    die("You must be logged in to take a quiz.");
}

// Get the student ID from the session
$student_id = $_SESSION['student_id'];

// Get quiz ID from query parameter
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Quiz ID is required.");
}
$quiz_id = intval($_GET['id']); // Ensure quiz ID is treated as an integer

// Fetch the quiz details including deadline
$sql = "SELECT * FROM quizzes WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$quiz_result = $stmt->get_result();

if ($quiz_result->num_rows === 0) {
    die("Quiz not found.");
}

$quiz = $quiz_result->fetch_assoc();

// Check if the current date/time is past the deadline
$current_time = date('Y-m-d H:i:s');

// Handle quiz submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get answers from the form
    if (!isset($_POST['answers'])) {
        die("No answers provided.");
    }
    
    $answers = $_POST['answers']; // This should be an associative array of question_id => answer_id

    // Calculate the score
    $score = 0;
    foreach ($answers as $question_id => $answer_id) {
        // Check if the selected answer is correct
        $correct_sql = "SELECT is_correct FROM quiz_options WHERE id = ? AND question_id = ?";
        $correct_stmt = $con->prepare($correct_sql);
        $correct_stmt->bind_param("ii", $answer_id, $question_id);
        $correct_stmt->execute();
        $correct_result = $correct_stmt->get_result();
        
        if ($correct_result->num_rows > 0) {
            $option = $correct_result->fetch_assoc();
            if ($option['is_correct']) {
                $score++; // Increment score for each correct answer
            }
        }
        $correct_stmt->close();
    }

    // Check if the submission is after the due date
    if ($current_time > $quiz['due_date']) {
        // Submission is late, set score to 0
        $score = 0;
    }

    // Insert into quiz_submissions table
    $sql = "INSERT INTO quiz_submissions (student_id, quiz_id, answers, submitted_at, score) VALUES (?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    
    // Convert answers to JSON format
    $answers_json = json_encode($answers);
    $submission_time = date('Y-m-d H:i:s'); // Current timestamp
    $stmt->bind_param("iissi", $student_id, $quiz_id, $answers_json, $submission_time, $score);
    
    if ($stmt->execute()) {
        header("Location: home.php"); // Redirect to results page
        exit(); // Prevent further script execution
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Display the quiz form
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($quiz['title']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
</head>
<body class="bg-gray-100 p-6">
    <div class="container mx-auto">
        <h1 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($quiz['title']); ?></h1>
        <p class="mb-4"><?php echo htmlspecialchars($quiz['description']); ?></p>

        <form action="" method="POST">
            <?php
            // Fetch quiz questions and display them
            $question_sql = "SELECT * FROM quiz_questions WHERE quiz_id = ?";
            $question_stmt = $con->prepare($question_sql);
            $question_stmt->bind_param("i", $quiz_id);
            $question_stmt->execute();
            $questions_result = $question_stmt->get_result();

            while ($question = $questions_result->fetch_assoc()) {
                echo '<div class="mb-4 border p-4 rounded">';
                echo '<p class="font-medium">' . htmlspecialchars($question['question_text']) . '</p>';

                // Fetch options for the question
                $options_sql = "SELECT * FROM quiz_options WHERE question_id = ?";
                $options_stmt = $con->prepare($options_sql);
                $options_stmt->bind_param("i", $question['id']);
                $options_stmt->execute();
                $options_result = $options_stmt->get_result();

                while ($option = $options_result->fetch_assoc()) {
                    echo '<div>';
                    echo '<input type="radio" name="answers[' . $question['id'] . ']" value="' . $option['id'] . '" required>';
                    echo '<label>' . htmlspecialchars($option['option_text']) . '</label>';
                    echo '</div>';
                }
                echo '</div>';
            }
            ?>
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Submit Quiz</button>
        </form>
    </div>
</body>
</html>
