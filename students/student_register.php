<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link rel="stylesheet" href="path/to/your/styles.css"> <!-- Update this path as needed -->
</head>
<body>
    <h1>Student Registration</h1>
    <form >
       
        <input type="text" id="lrn" name="lrn" required>
        <br>
        
        <label >First Name:</label>
        <input type="text" id="first_name" name="first_name" required>
        <br>
        
        <label >Middle Name (optional):</label>
        <input type="text" id="middle_name" name="middle_name">
        <br>
        
        <label >Last Name:</label>
        <input type="text" id="last_name" name="last_name" required>
        <br>
        
        <label >Email:</label>
        <input type="email" id="email" name="email" required>
        <br>
        
        <label >Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        
        <label >Repeat Password:</label>
        <input type="password" id="repeat_password" name="repeat_password" required> <!-- New input -->
        <br>
        
        <label >Date of Birth:</label>
        <input type="date" id="dob" name="dob" required>
        <br>
        
        <input type="submit" value="Register">
    </form>
    <p>Already have an account? <a href="student_login.php">Login here</a>.</p>
</body>
</html>
