<?php
session_start();
include '../fn/dbcon.php'; // Include database connection

// Handle teacher registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_teacher'])) {
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name']; // Optional
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashing password
    $dob = $_POST['dob'];
    $subject_id = intval($_POST['subject_id']);
    $phone_number = $_POST['phone_number']; // Optional
    $address = $_POST['address']; // Optional
    $hire_date = $_POST['hire_date']; // Optional

    // Prepare SQL statement
    $sql = "INSERT INTO teachers (first_name, middle_name, last_name, email, password, dob, subject_id, phone_number, address, hire_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    // Prepare statement
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssssssssss", $first_name, $middle_name, $last_name, $email, $password, $dob, $subject_id, $phone_number, $address, $hire_date);
    
    // Execute statement and check for success
    if ($stmt->execute()) {
        $message = "Teacher registered successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close(); // Close the prepared statement
}
?>