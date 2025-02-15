<?php
session_start();
include '../fn/dbcon.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $assignment_id = $_POST['assignment_id'];

    // Soft delete the assignment by setting the deleted_at timestamp
    $soft_delete_query = "UPDATE teacher_section SET deleted_at = NOW() WHERE id = '$assignment_id'";
    if ($con->query($soft_delete_query) === TRUE) {
        echo "Assignment soft-deleted successfully!";
        header("Location: ../admin/assign_teachers.php"); // Redirect back to the assignment page
    } else {
        echo "Error: " . $soft_delete_query . "<br>" . $con->error;
    }
}

$con->close(); // Close database connection
?>
