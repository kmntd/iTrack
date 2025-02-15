<?php
session_start();
include '../fn/dbcon.php'; // Include database connection

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $teacher_id = $_POST['teacher_id'];
    $section_id = $_POST['section_id'];
    $year_level = $_POST['year_level'];
    $subject_id = $_POST['subject_id'];
    
    // Check if we are editing an existing assignment
    if (isset($_POST['edit_id']) && !empty($_POST['edit_id'])) {
        $edit_id = $_POST['edit_id'];
        
        // Prepare the update statement
        $stmt_update = $con->prepare("UPDATE teacher_section SET teacher_id = ?, section_id = ?, year_level = ?, subject_id = ? WHERE id = ?");
        $stmt_update->bind_param("iiiii", $teacher_id, $section_id, $year_level, $subject_id, $edit_id);
        
        // Execute the statement and check for errors
        if ($stmt_update->execute()) {
            header("Location: ../admin/assign_teachers.php?updated=1");
            exit();
        } else {
            echo "Error updating assignment: " . $stmt_update->error;
        }
        
        $stmt_update->close();
    } else {
        // Prepare the insert statement
        $stmt_insert = $con->prepare("INSERT INTO teacher_section (teacher_id, section_id, year_level, subject_id) VALUES (?, ?, ?, ?)");
        $stmt_insert->bind_param("iisi", $teacher_id, $section_id, $year_level, $subject_id);
        
        // Execute the statement and check for errors
        if ($stmt_insert->execute()) {
            header("Location: ../admin/assign_teachers.php?success=1");
            exit();
        } else {
            echo "Error creating assignment: " . $stmt_insert->error;
        }
        
        $stmt_insert->close();
    }
}

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    // Prepare the delete statement for soft delete
    $stmt_delete = $con->prepare("UPDATE teacher_section SET deleted_at = NOW() WHERE id = ?");
    $stmt_delete->bind_param("i", $delete_id);
    
    // Execute the delete statement and check for errors
    if ($stmt_delete->execute()) {
        header("Location: ../admin/assign_teachers.php?deleted=1");
        exit();
    } else {
        echo "Error deleting assignment: " . $stmt_delete->error;
    }
    
    $stmt_delete->close();
}



$con->close(); // Close database connection
?>
