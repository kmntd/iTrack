<?php
// Include database connection
include '../fn/dbcon.php';
session_start();

// Set the timezone to the Philippines
date_default_timezone_set('Asia/Manila');

// Check if assignment_id is set in the URL
if (!isset($_GET['id'])) {
    die("Error: Assignment ID not provided.");
}

$assignment_id = $_GET['id'];
$student_id = $_SESSION['student_id']; // Assuming you store the student ID in the session

// Fetch the assignment details including perfect_score
$assignmentQuery = "SELECT a.assignment_title, a.due_date, a.type, s.subject_name, a.created_at, a.perfect_score FROM assignments a 
                    JOIN subjects s ON a.subject_id = s.id 
                    WHERE a.id = ?";
$stmt = $con->prepare($assignmentQuery);
$stmt->bind_param('i', $assignment_id);
$stmt->execute();
$assignmentResult = $stmt->get_result();

if ($assignmentResult->num_rows > 0) {
    $assignment = $assignmentResult->fetch_assoc();
} else {
    echo "<p>No assignment found.</p>";
    exit;
}

// Get current date and time
$currentDateTime = date('Y-m-d H:i:s');
$dueDateTime = $assignment['due_date'];

// Check if the student has already submitted the assignment
function fetchAssignmentSubmission($assignment_id, $student_id, $con) {
    $stmt = $con->prepare("SELECT submission_time, file_name FROM submissions WHERE assignment_id = ? AND student_id = ?");
    $stmt->bind_param('ii', $assignment_id, $student_id);
    $stmt->execute();
    $submissionResult = $stmt->get_result();
    return $submissionResult->num_rows > 0 ? $submissionResult->fetch_assoc() : null;
}

$submission = fetchAssignmentSubmission($assignment_id, $student_id, $con); // Pass the connection here
$submitted_time = $submission['submission_time'] ?? null;

// Handle file upload if the form is submitted and the deadline has not passed
if ($_SERVER["REQUEST_METHOD"] == "POST" && $assignment['type'] == 'file_upload') {
    if ($currentDateTime > $dueDateTime) {
        echo "<p style='color: red;'>Cannot submit. The deadline has passed.</p>";
    } else {
        $uploadDir = 'uploads/'; // Ensure this directory exists
        $fileName = basename($_FILES['file_upload']['name']);
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['file_upload']['tmp_name'], $uploadFile)) {
            // Use the current time as the submission time
            $submissionTime = date('Y-m-d H:i:s');

            // Insert submission details into the database
            $insertQuery = "INSERT INTO submissions (assignment_id, student_id, file_name, submission_time, perfect_score) VALUES (?, ?, ?, ?, ?)";
            $insertStmt = $con->prepare($insertQuery);
            $insertStmt->bind_param('iissi', $assignment_id, $student_id, $fileName, $submissionTime, $assignment['perfect_score']);
            $insertStmt->execute();

            if ($insertStmt->affected_rows > 0) {
                echo "<script>alert('File uploaded successfully!'); window.location.href = window.location.href;</script>";
            } else {
                echo "<p>Error recording submission.</p>";
            }
        } else {
            echo "<p>Error uploading file.</p>";
        }
    }
}

// Handle case where the deadline has passed and the assignment wasn't submitted
if ($currentDateTime > $dueDateTime && !$submitted_time) {
    // Insert a record indicating a late submission with a score of 0
    $insertQuery = "INSERT INTO submissions (assignment_id, student_id, file_name, submission_time, perfect_score) VALUES (?, ?, NULL, ?, 0)";
    $insertStmt = $con->prepare($insertQuery);
    $insertStmt->bind_param('iis', $assignment_id, $student_id, $currentDateTime);
    $insertStmt->execute();

    if ($insertStmt->affected_rows > 0) {
        echo "<p style='color: red;'>Deadline has passed. Your submission is recorded with a score of 0.</p>";
    } else {
        echo "<p>Error recording late submission.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($assignment['assignment_title']); ?></title>
</head>
<body>
    <h1><?php echo htmlspecialchars($assignment['assignment_title']); ?></h1>
    <p>Due Date: <?php echo htmlspecialchars($assignment['due_date']); ?></p>
    <p>Date Submitted: <?php echo $submitted_time ? htmlspecialchars($submitted_time) : 'Not submitted'; ?></p>
    <p>Subject: <?php echo htmlspecialchars($assignment['subject_name']); ?></p>

    <?php if ($assignment['type'] == 'file_upload'): ?>
        <?php if ($currentDateTime > $dueDateTime): ?>
            <p style="color: red;">Cannot submit. The deadline has passed.</p>
        <?php else: ?>
            <h2>Upload Your File</h2>
            <form method="POST" enctype="multipart/form-data">
                <label for="file_upload">Choose file:</label>
                <input type="file" id="file_upload" name="file_upload" required>
                <button type="submit">Upload</button>
            </form>
        <?php endif; ?>
    <?php elseif ($assignment['type'] == 'quiz'): ?>
        <!-- Quiz handling logic here -->
    <?php endif; ?>
</body>
</html>
