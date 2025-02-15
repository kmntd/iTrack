<?php
// Include database connection
include '../fn/dbcon.php';

// Start the session
session_start();

// Check if the teacher_id is set in the session
if (!isset($_SESSION['teacher_id'])) {
    // Redirect to login page if not set
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id']; // Now it's safe to use

// Set timezone
date_default_timezone_set('Asia/Manila');

// Fetch subjects assigned to the teacher through sections
$subjectsQuery = "SELECT s.id, s.subject_name 
                  FROM subjects s
                  JOIN teacher_section ts ON s.id = ts.subject_id
                  WHERE ts.teacher_id = ?";
$stmt = $con->prepare($subjectsQuery);
$stmt->bind_param('i', $teacher_id);
$stmt->execute();
$subjectsResult = $stmt->get_result();

// Initialize perfect score to 0
$perfectScore = 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selectedSubjectId = $_POST['subject_id'];
    $selectedSectionId = $_POST['section_id']; // New section_id from the form
    $questions = $_POST['questions'];
    $options = $_POST['options'];
    $correct_answers = $_POST['correct_answers'];
    $dueDate = $_POST['due_date']; // Getting the due date input

    // Calculate perfect score (assuming each question is worth 1 point)
    $perfectScore = count($questions); // Perfect score is equal to the number of questions

    // Insert the quiz into the database, including the perfect score
    $quizQuery = "INSERT INTO quizzes (title, description, created_at, due_date, subject_id, teacher_id, perfect_score, section_id) 
                  VALUES (?, ?, NOW(), ?, ?, ?, ?, ?)";
    $quizStmt = $con->prepare($quizQuery);
    $quizTitle = $_POST['title'];
    $quizDescription = $_POST['description'];
    $quizStmt->bind_param('sssiiii', $quizTitle, $quizDescription, $dueDate, $selectedSubjectId, $teacher_id, $perfectScore, $selectedSectionId);
    $quizStmt->execute();
    $quizId = $con->insert_id; // Get the inserted quiz ID

    // Insert each question
    foreach ($questions as $index => $question) {
        if (!empty($question)) {
            $insertQuestionQuery = "INSERT INTO quiz_questions (quiz_id, question_text) VALUES (?, ?)";
            $insertQuestionStmt = $con->prepare($insertQuestionQuery);
            $insertQuestionStmt->bind_param('is', $quizId, $question);
            $insertQuestionStmt->execute();
            $questionId = $con->insert_id; // Get the inserted question ID

            // Insert options for each question
            foreach ($options[$index] as $optIndex => $option) {
                if (!empty($option)) {
                    $insertOptionQuery = "INSERT INTO quiz_options (question_id, option_text, is_correct) 
                                          VALUES (?, ?, ?)";
                    $is_correct = ($optIndex == $correct_answers[$index]) ? 1 : 0; // Check if the option is correct
                    $insertOptionStmt = $con->prepare($insertOptionQuery);
                    $insertOptionStmt->bind_param('isi', $questionId, $option, $is_correct);
                    $insertOptionStmt->execute();
                }
            }
        }
    }

    // Redirect to the teacher home page after creation
    header("Location: teacher_home.php");
    exit();
}

// Fetch sections based on the selected subject
$sectionsQuery = "SELECT ts.section_id, s.section_name 
                  FROM teacher_section ts
                  JOIN sections s ON ts.section_id = s.id
                  WHERE ts.teacher_id = ?";
$sectionsStmt = $con->prepare($sectionsQuery);
$sectionsStmt->bind_param('i', $teacher_id);
$sectionsStmt->execute();
$sectionsResult = $sectionsStmt->get_result();

// Prepare sections for display in dropdown
$sectionsOptions = '';
while ($section = $sectionsResult->fetch_assoc()) {
    $sectionsOptions .= '<option value="' . $section['section_id'] . '">' . htmlspecialchars($section['section_name']) . '</option>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Quiz</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <h1 class="text-xl font-bold mb-4">Create Quiz</h1>
    <form action="" method="POST" id="quiz-form">
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2" for="title">Quiz Title</label>
            <input type="text" name="title" id="title" class="border rounded w-full p-2" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2" for="description">Quiz Description</label>
            <textarea name="description" id="description" class="border rounded w-full p-2"></textarea>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2" for="subject_id">Select Subject</label>
            <select name="subject_id" id="subject_id" class="border rounded w-full p-2" required onchange="updateSections()">
                <option value="">--Select Subject--</option>
                <?php while ($subject = $subjectsResult->fetch_assoc()) : ?>
                    <option value="<?php echo $subject['id']; ?>"><?php echo htmlspecialchars($subject['subject_name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2" for="section_id">Select Section</label>
            <select name="section_id" id="section_id" class="border rounded w-full p-2" required>
                <option value="">--Select Section--</option>
                <?php echo $sectionsOptions; ?>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2" for="due_date">Quiz Deadline (Due Date & Time)</label>
            <input type="datetime-local" name="due_date" id="due_date" class="border rounded w-full p-2" required>
        </div>

        <div id="questions-container">
            <h2 class="text-lg font-semibold mb-2">Questions (Perfect Score: <span id="perfect-score"><?php echo $perfectScore; ?></span>)</h2>
            <div class="question mb-4 border p-4 rounded">
                <label class="block text-sm font-medium mb-2" for="question_0">Question</label>
                <input type="text" name="questions[]" id="question_0" class="border rounded w-full p-2" required>
                <h3 class="text-sm font-medium mt-2">Options</h3>
                <div class="options">
                    <input type="text" name="options[0][]" class="border rounded w-full p-2 mb-2" placeholder="Option 1" required>
                    <input type="text" name="options[0][]" class="border rounded w-full p-2 mb-2" placeholder="Option 2" required>
                    <input type="text" name="options[0][]" class="border rounded w-full p-2 mb-2" placeholder="Option 3" required>
                    <input type="text" name="options[0][]" class="border rounded w-full p-2 mb-2" placeholder="Option 4" required>
                </div>
                <label class="block text-sm font-medium mt-2">Correct Answer</label>
                <select name="correct_answers[]" class="border rounded w-full p-2" required>
                    <option value="0">Option 1</option>
                    <option value="1">Option 2</option>
                    <option value="2">Option 3</option>
                    <option value="3">Option 4</option>
                </select>
                <button type="button" class="remove-question bg-red-500 text-white px-2 py-1 rounded mt-2">Remove</button>
            </div>
        </div>

        <button type="button" id="add-question" class="bg-blue-500 text-white px-4 py-2 rounded">Add Question</button>
        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Create Quiz</button>
    </form>

    <script>
        function updatePerfectScore() {
            const questionsCount = document.querySelectorAll('.question').length;
            document.getElementById('perfect-score').innerText = questionsCount;
        }

        document.getElementById('add-question').addEventListener('click', function() {
            const questionCount = document.querySelectorAll('.question').length;
            const questionContainer = document.createElement('div');
            questionContainer.className = 'question mb-4 border p-4 rounded';
            questionContainer.innerHTML = `
                <label class="block text-sm font-medium mb-2" for="question_${questionCount}">Question</label>
                <input type="text" name="questions[]" id="question_${questionCount}" class="border rounded w-full p-2" required>
                <h3 class="text-sm font-medium mt-2">Options</h3>
                <div class="options">
                    <input type="text" name="options[${questionCount}][]" class="border rounded w-full p-2 mb-2" placeholder="Option 1" required>
                    <input type="text" name="options[${questionCount}][]" class="border rounded w-full p-2 mb-2" placeholder="Option 2" required>
                    <input type="text" name="options[${questionCount}][]" class="border rounded w-full p-2 mb-2" placeholder="Option 3" required>
                    <input type="text" name="options[${questionCount}][]" class="border rounded w-full p-2 mb-2" placeholder="Option 4" required>
                </div>
                <label class="block text-sm font-medium mt-2">Correct Answer</label>
                <select name="correct_answers[]" class="border rounded w-full p-2" required>
                    <option value="0">Option 1</option>
                    <option value="1">Option 2</option>
                    <option value="2">Option 3</option>
                    <option value="3">Option 4</option>
                </select>
                <button type="button" class="remove-question bg-red-500 text-white px-2 py-1 rounded mt-2">Remove</button>
            `;
            document.getElementById('questions-container').appendChild(questionContainer);
            updatePerfectScore();
        });

        document.getElementById('questions-container').addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-question')) {
                event.target.closest('.question').remove();
                updatePerfectScore();
            }
        });

        // Function to update sections based on the selected subject
        function updateSections() {
            const subjectId = document.getElementById('subject_id').value;
            // Here, you would normally make an AJAX call to fetch sections based on the selected subject
            // For demonstration, let's assume sections are already fetched in PHP
        }
    </script>
</body>
</html>
