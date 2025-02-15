<?php
session_start();
include '../fn/dbcon.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the student ID from the POST request
    $student_id = $_POST['student_id'];

    // Prepare the SQL statement to set the deleted_at timestamp
    $query = "UPDATE students SET deleted_at = NOW() WHERE id = ?";
    
    if ($stmt = $con->prepare($query)) {
        // Bind the parameter
        $stmt->bind_param("i", $student_id);

        // Execute the statement
        if ($stmt->execute()) {
            // Redirect back to the student list page with a success message
            $_SESSION['message'] = "Student has been successfully soft deleted.";
            header("Location: ../admin/student_list.php");
            exit();
        } else {
            // Redirect back with an error message
            $_SESSION['error'] = "Error deleting student. Please try again.";
            header("Location: ../admin/student_list.php");
            exit();
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        // Handle error in preparing the statement
        $_SESSION['error'] = "Error preparing statement.";
        header("Location: ../admin/student_list.php");
        exit();
    }
}

// Close database connection
$con->close();
?>
