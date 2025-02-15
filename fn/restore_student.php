<?php
session_start();
include '../fn/dbcon.php';

if (isset($_POST['student_id'])) {
    $student_id = intval($_POST['student_id']);

    // Restore the student by setting the deleted_at column to NULL
    $query = "UPDATE students SET deleted_at = NULL WHERE id = $student_id";
    if ($con->query($query) === TRUE) {
        $_SESSION['message'] = "Student restored successfully.";
    } else {
        $_SESSION['error'] = "Error restoring student: " . $con->error;
    }
} else {
    $_SESSION['error'] = "No student ID provided.";
}

// Redirect back to the deleted users page
header("Location: ../admin/manage_students.php");
exit();
?>
