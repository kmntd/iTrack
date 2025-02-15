<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Registration</title>
    <link rel="stylesheet" href="style.css"> <!-- Optional: Add your CSS file -->
</head>
<body>
    <h1>Teacher Registration</h1>
    <form action="../fn/teacher_register.php" method="post">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required>

        <label for="middle_name">Middle Name (Optional):</label>
        <input type="text" id="middle_name" name="middle_name">

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <label for="subject_id">Subject:</label>
        <select id="subject_id" name="subject_id" required>
            <option value="1">Database</option>
            <option value="2">Computer Networks</option>
            <option value="3">Computer Programming</option>
            <!-- Add more subjects as necessary -->
        </select>

        <input type="submit" name="register" value="Register">
    </form>

    <p>Already have an account? <a href="teacher_login.php">Login</a></p>
</body>
</html>
