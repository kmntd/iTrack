<?php
session_start();
include '../fn/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $submissionId = $_POST['submission_id'];
    $score = $_POST['score'];
    $perfectScore = $_POST['perfect_score']; // Get the perfect score from the form

    // Update the submission in the database
    $updateQuery = "UPDATE submissions SET score = ?, perfect_score = ? WHERE id = ?";
    $stmt = $con->prepare($updateQuery);
    $stmt->bind_param('ddi', $score, $perfectScore, $submissionId);
    
    if ($stmt->execute()) {
        echo "Score submitted successfully.";
    } else {
        echo "Error submitting score: " . $stmt->error;
    }
    
    $stmt->close();
    $con->close();
    header("Location: ../teachers/teacher_home.php"); // Redirect back to teacher home after submission
    exit();
} else {
    // Redirect to an error page or show a message if accessed directly
    header("Location: ../teachers/teacher_home.php");
    exit();
}
?>
