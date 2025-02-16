<?php
session_start();
include 'dbcon.php'; // Ensure the database connection is included

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve input values and sanitize
    $lrn = trim($_POST['lrn']);
    $password = trim($_POST['password']);

    // Check if fields are not empty
    if (empty($lrn) || empty($password)) {
        $_SESSION['error_message'] = 'Please fill in both fields.';
        header("Location: ../students/student_login.php");
        exit();
    }

    // Prepare and execute SQL statement
    $stmt = $con->prepare("SELECT * FROM students WHERE lrn = ?");
    $stmt->bind_param("s", $lrn); // 's' specifies the variable type => 'string'
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if student exists
    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $student['password'])) {
            // Set session variables
            $_SESSION['student_id'] = $student['id'];
            $_SESSION['lrn'] = $student['lrn'];
            $_SESSION['name'] = $student['first_name'] . ' ' . $student['last_name']; // Full name

            // Redirect to homepage after successful login
            header("Location: ../students/home.php");
            exit();
        } else {
            $_SESSION['error_message'] = 'Invalid password!';
            header("Location: ../students/student_login.php");
            exit();
        }
    } else {
        $_SESSION['error_message'] = 'No account found with that LRN!';
        header("Location: ../students/student_login.php");
        exit();
    }
}
?>
