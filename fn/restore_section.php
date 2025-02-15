<?php
session_start();
include '../fn/dbcon.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $section_id = $_POST['section_id'];

    // Restore the section by setting deleted_at to NULL
    $restore_query = "UPDATE sections SET deleted_at = NULL WHERE id = ?";
    $stmt = $con->prepare($restore_query);
    $stmt->bind_param("i", $section_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Section restored successfully.";
    } else {
        $_SESSION['message'] = "Error restoring section: " . $con->error;
    }

    $stmt->close();
}

header('Location: ../admin/manage_sections.php'); // Redirect back to the deleted sections page
exit();
?>
