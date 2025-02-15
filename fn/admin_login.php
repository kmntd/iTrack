<?php
session_start();
include 'dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Check if admin exists
    $sql = "SELECT * FROM admins WHERE username = ?";
    if ($stmt = $con->prepare($sql)) {
        $stmt->bind_param("s", $username);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $admin = $result->fetch_assoc();

                // Verify password
                if (password_verify($password, $admin['password'])) {
                    // Password is correct, redirect to admin_home.php
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    header("Location: ../admin/admin_home.php");
                    exit();
                } else {
                    // Password is incorrect
                    echo "<script>alert('Invalid password!'); window.location.href = '../admin/admin_login.php';</script>";
                }
            } else {
                // No account with that username
                echo "<script>alert('No account found with that username!'); window.location.href = '../admin/admin_login.php';</script>";
            }
        } else {
            // Error with the query
            echo "<script>alert('Oops! Something went wrong. Please try again later.'); window.location.href = '../admin/admin_login.php';</script>";
        }
    }
}
?>
