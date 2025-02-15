<?php
session_start();
include 'dbcon.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the email and password from the POST request
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL statement to prevent SQL injection
    $stmt = $con->prepare("SELECT * FROM teachers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a teacher with the provided email exists
    if ($result->num_rows > 0) {
        $teacher = $result->fetch_assoc();
        
        // Verify the password
        if (password_verify($password, $teacher['password'])) {
            // Password is correct, set session variables
            $_SESSION['teacher_id'] = $teacher['id'];
            $_SESSION['teacher_name'] = $teacher['first_name'] . ' ' . $teacher['last_name'];
            // Redirect to teacher home page
            header("Location: ../teachers/teacher_home.php");
            exit();
        } else {
            echo "<script>alert('Invalid password!'); window.location.href='../teachers/teacher_login.php';</script>";
        }
    } else {
        echo "<script>alert('No account found with that email!'); window.location.href='../teachers/teacher_login.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request method!'); window.location.href='../teachers/teacher_login.php';</script>";
}
?>
