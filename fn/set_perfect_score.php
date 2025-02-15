<?php
session_start();
if (!isset($_SESSION['teacher_id'])) {
    header("Location: teachers/teacher_login.php");
    exit();
}

// Include the database connection
include '../fn/dbcon.php'; // Adjust the path as needed

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $assignment_id = $_POST['assignment_id'];
    $perfect_score = $_POST['perfect_score'];

    // Check if a perfect score already exists for the assignment
    $stmt = $con->prepare("SELECT * FROM perfect_scores WHERE assignment_id = ?");
    $stmt->bind_param("i", $assignment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update existing perfect score in perfect_scores table
        $stmt = $con->prepare("UPDATE perfect_scores SET perfect_score = ? WHERE assignment_id = ?");
        $stmt->bind_param("ii", $perfect_score, $assignment_id);
    } else {
        // Insert new perfect score into perfect_scores table
        $stmt = $con->prepare("INSERT INTO perfect_scores (assignment_id, perfect_score) VALUES (?, ?)");
        $stmt->bind_param("ii", $assignment_id, $perfect_score);
    }

    // Execute the statement for perfect scores
    if ($stmt->execute()) {
        // Update the perfect score in the submissions table
        $stmt = $con->prepare("UPDATE submissions SET perfect_score = ? WHERE assignment_id = ?");
        $stmt->bind_param("ii", $perfect_score, $assignment_id);
        if ($stmt->execute()) {
            // Update the perfect score in the assignments table as well
            $stmt = $con->prepare("UPDATE assignments SET perfect_score = ? WHERE id = ?");
            $stmt->bind_param("ii", $perfect_score, $assignment_id);
            if ($stmt->execute()) {
                // Redirect back to the view submissions page
                header("Location: ../teachers/view_submissions.php?assignment_id=" . $assignment_id);
                exit();
            } else {
                echo "Error updating perfect score in assignments: " . $stmt->error;
            }
        } else {
            echo "Error updating perfect score in submissions: " . $stmt->error;
        }
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Close the database connection
$stmt->close();
$con->close();
?>
