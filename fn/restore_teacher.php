<?php
session_start();
include '../fn/dbcon.php';

if (isset($_POST['teacher_id'])) {
    $teacher_id = intval($_POST['teacher_id']);
    
    // Restore the teacher by setting the deleted_at column to NULL
    $query = "UPDATE teachers SET deleted_at = NULL WHERE id = $teacher_id";
    if ($con->query($query) === TRUE) {
        // Redirect back to the deleted teachers page
        header("Location: ../admin/manage_teachers.php");
        exit();
    } else {
        echo "Error restoring teacher: " . $con->error;
    }
} else {
    // Redirect back if no teacher_id is set
    header("Location: ../admin/manage_teachers.php");
    exit();
}
?>
