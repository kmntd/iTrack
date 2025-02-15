
<?php
$host = 'localhost';  // Or your database host
$dbname = 'gms';  // Replace with your actual database name
$username = 'root';  // Replace with your DB username
$password = '';  // Replace with your DB password

// Create connection
$con = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
?>
