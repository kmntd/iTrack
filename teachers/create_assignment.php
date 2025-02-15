<?php
// Start the session
session_start();

// Include database connection
include '../fn/dbcon.php';

$teacher_id = $_SESSION['teacher_id']; // Ensure the teacher ID is set in the session

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $assignment_title = $_POST['assignment_title'];
    $subject_id = $_POST['subject_id'];
    $section_id = $_POST['section_id']; // Get section_id from the form
    $description = $_POST['description']; // Get description from the form
    $due_datetime = $_POST['due_datetime']; // Get the due date and time from the form
    $assignment_type = $_POST['assignment_type'];
    $perfect_score = $_POST['perfect_score'] ?? 100; // Get perfect score or default to 100

    // Check if the due date has already passed
    $currentDateTime = new DateTime();
    $dueDateTime = new DateTime($due_datetime);

    if ($dueDateTime < $currentDateTime) {
        echo "<p>Error: The due date and time must be in the future.</p>";
    } else {
        // Insert the new assignment into the database
        $insertQuery = "INSERT INTO assignments (assignment_title, description, section_id, subject_id, teacher_id, due_date, type, perfect_score) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $con->prepare($insertQuery);
        $stmt->bind_param('ssiiissi', $assignment_title, $description, $section_id, $subject_id, $teacher_id, $due_datetime, $assignment_type, $perfect_score);

        if ($stmt->execute()) {
            echo "<p>Assignment created successfully!</p>";
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }

        // Close the statement
        $stmt->close();
    }
}

// Fetch subjects and sections assigned to the teacher from teacher_section
$subjectsQuery = "SELECT s.id, s.subject_name, sec.section_name, ts.section_id
                  FROM teacher_section ts
                  JOIN subjects s ON ts.subject_id = s.id
                  JOIN sections sec ON ts.section_id = sec.id
                  WHERE ts.teacher_id = ?";
$stmt = $con->prepare($subjectsQuery);
$stmt->bind_param('i', $teacher_id);
$stmt->execute();
$subjectsResult = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Assignment</title>
</head>
<body>
    <h1>Create Assignment</h1>
    <form method="POST" action="">
        <label for="assignment_title">Assignment Title:</label>
        <input type="text" id="assignment_title" name="assignment_title" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description"></textarea>

        <label for="subject_id">Select Subject:</label>
        <select id="subject_id" name="subject_id" required>
            <option value="">--Select Subject--</option>
            <?php 
            // Loop through the results to display subjects
            while ($subject = $subjectsResult->fetch_assoc()): ?>
                <option value="<?php echo $subject['id']; ?>">
                    <?php echo htmlspecialchars($subject['subject_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="section_id">Select Section:</label>
        <select id="section_id" name="section_id" required>
            <option value="">--Select Section--</option>
            <?php
          
            $stmt->execute(); 
            $sectionsResult = $stmt->get_result();
            while ($section = $sectionsResult->fetch_assoc()): ?>
                <option value="<?php echo $section['section_id']; ?>">
                    <?php echo htmlspecialchars($section['section_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="due_datetime">Due Date and Time:</label>
        <input type="datetime-local" id="due_datetime" name="due_datetime" required>

        <label for="assignment_type">Assignment Type:</label>
        <select id="assignment_type" name="assignment_type" required>
            <option value="file_upload">File Upload</option>
            <option value="quiz">Quiz</option>
        </select>

        <label for="perfect_score">Perfect Score:</label>
        <input type="number" id="perfect_score" name="perfect_score" value="100" min="0">

        <button type="submit">Create Assignment</button>
    </form>
</body>
</html>

<?php
// Close the database connection
$con->close();
?>
