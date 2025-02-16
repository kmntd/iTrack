<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Include database connection
include '../fn/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $announcement_message = $_POST['announcement_message'];

    // Insert the announcement into the database
    $stmt = $con->prepare("INSERT INTO admin_announcements (message) VALUES (?)");
    $stmt->bind_param("s", $announcement_message);
    
    if ($stmt->execute()) {
        echo "<script>alert('Announcement created successfully!');</script>";
    } else {
        echo "<script>alert('Error creating announcement!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Announcement</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <h1>Create Announcement</h1>
        <nav>
            <a href="../fn/admin_logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <form method="POST" action="">
            <textarea name="announcement_message" rows="4" cols="50" placeholder="Write your announcement here..." required></textarea>
            <br>
            <button type="submit">Create Announcement</button>
        </form>
    </main>
</body>
</html>
