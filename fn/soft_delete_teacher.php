<?php
session_start();
include '../fn/dbcon.php';

if (isset($_POST['teacher_id'])) {
    $teacher_id = intval($_POST['teacher_id']);
    
    // Soft delete the teacher by setting the deleted_at column to the current date and time
    $query = "UPDATE teachers SET deleted_at = NOW() WHERE id = $teacher_id";
    if ($con->query($query) === TRUE) {
        // Redirect back to the manage teachers page
        header("Location: ../admin/manage_teachers.php");
        exit();
    } else {
        echo "Error deleting teacher: " . $con->error;
    }
} else {
    // Redirect back if no teacher_id is set
    header("Location: ../admin/manage_teachers.php");
    exit();
}
?>
