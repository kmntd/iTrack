<?php
session_start();
include '../fn/dbcon.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the assignment ID from the POST request
    $assignment_id = $_POST['assignment_id'];

    // Prepare the SQL statement for soft delete
    $stmt = $con->prepare("UPDATE student_sections SET deleted_at = NOW() WHERE id = ?");
    $stmt->bind_param("i", $assignment_id);

    // Execute the statement
    if ($stmt->execute()) {
        // Optionally set a success message in session and redirect or return to the previous page
        $_SESSION['message'] = "Student assignment removed successfully.";
        header("Location: ../path/to/assign_student_to_section.php"); // Redirect to the appropriate page
        exit();
    } else {
        // Error handling
        $_SESSION['error'] = "Error removing student assignment.";
        header("Location: ../path/to/assign_student_to_section.php");
        exit();
    }

    $stmt->close();
} else {
    // If not a POST request, redirect or handle accordingly
    header("Location: ../path/to/assign_student_to_section.php");
    exit();
}

$con->close(); // Close database connection
?>
