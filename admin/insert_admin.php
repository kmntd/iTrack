<?php
include '../fn/dbcon.php';

$hashed_password = password_hash('admin', PASSWORD_DEFAULT);

$sql = "INSERT INTO admins (username, name, email, password) VALUES 
('admin', 'Admin', 'admin@example.com', '$hashed_password')";

if ($con->query($sql) === TRUE) {
    echo "Admin account created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $con->error;
}
?>
