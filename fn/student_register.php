<?php
session_start();
include 'dbcon.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $last_name = $_POST['last_name'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $lrn = $_POST['lrn'];
    $dob = $_POST['dob'];
    
    $email = $_POST['email'];
    $password = $_POST['password'];
    $repeat_password = $_POST['repeat_password']; // New field for confirmation
    

    // Check if the passwords match
    if ($password !== $repeat_password) {
        echo "<script>alert('Passwords do not match!'); window.location.href='../students/student_register.php';</script>";
        exit();
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL statement to insert the new student
    $stmt = $con->prepare("INSERT INTO students (lrn, first_name, middle_name, last_name, email, password, dob) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $lrn, $first_name, $middle_name, $last_name, $email, $hashed_password, $dob);

    // Execute the statement and check for errors
    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href='../students/student_login.php';</script>";
    } else {
        echo "<script>alert('Oops! Something went wrong. Please try again later.'); window.location.href='../students/student_register.php';</script>";
    }

    $stmt->close(); // Close the statement
    $con->close();  // Close the database connection
}
?>
