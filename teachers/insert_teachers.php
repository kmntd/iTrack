<?php
// Include database connection
include '../fn/dbcon.php'; // Adjust the path as needed

// Hash the passwords
$hashed_password1 = password_hash('test', PASSWORD_DEFAULT);
$hashed_password2 = password_hash('test', PASSWORD_DEFAULT);
$hashed_password3 = password_hash('test', PASSWORD_DEFAULT);

// SQL insert command
$sql = "INSERT INTO teachers (first_name, middle_name, last_name, email, password, dob, subject_id, phone_number, address, hire_date, status) VALUES 
('Michael', 'Martin', 'Singco Mariscal', 'michael@example.com', '$hashed_password1', '1990-01-15', 1, '123-456-7890', '123 Main St, City, Country', '2020-06-01', 'active'),
('Francis', 'Niño', 'Digamo', 'francis@example.com', '$hashed_password2', '1991-02-20', 2, '234-567-8901', '234 Second St, City, Country', '2020-06-01', 'active'),
('Raphie', 'Rap', 'Abucay', 'raphie@example.com', '$hashed_password3', '1992-03-25', 3, '345-678-9012', '345 Third St, City, Country', '2020-06-01', 'active')";

// Execute the query
if ($con->query($sql) === TRUE) {
    echo "New records created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $con->error;
}
?>
