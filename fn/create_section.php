<?php
session_start();
include 'dbcon.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $section_name = $_POST['section_name'];

    // Insert the new section into the database
    $stmt = $con->prepare("INSERT INTO sections (section_name) VALUES (?)");
    $stmt->bind_param("s", $section_name);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Section added successfully!";
    } else {
        $_SESSION['message'] = "Error adding section: " . $stmt->error;
    }

    $stmt->close();
    header("Location: ../admnin/manage_sections.php"); // Redirect back to the manage sections page
    exit();
}

$con->close(); // Close database connection
?>
