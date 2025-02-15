<?php
// fn/teacher_logout.php
session_start();
session_destroy(); // Destroy all session data
header("Location: ../teachers/teacher_login.php"); // Redirect to the login page
exit();
?>
