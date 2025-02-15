
<?php
include '../fn/dbcon.php';
// Assuming you have already established a database connection in $con

$students = [
    ['lrn' => '123456789012', 'first_name' => 'John', 'last_name' => 'Doe', 'email' => 'john.doe@example.com', 'dob' => '2005-01-15'],
    ['lrn' => '123456789013', 'first_name' => 'Jane', 'last_name' => 'Smith', 'email' => 'jane.smith@example.com', 'dob' => '2005-02-20'],
    ['lrn' => '123456789014', 'first_name' => 'Mark', 'last_name' => 'Johnson', 'email' => 'mark.johnson@example.com', 'dob' => '2005-03-25'],
    ['lrn' => '123456789015', 'first_name' => 'Emily', 'last_name' => 'Davis', 'email' => 'emily.davis@example.com', 'dob' => '2005-04-30'],
    ['lrn' => '123456789016', 'first_name' => 'Michael', 'last_name' => 'Wilson', 'email' => 'michael.wilson@example.com', 'dob' => '2005-05-05'],
    ['lrn' => '123456789017', 'first_name' => 'Jessica', 'last_name' => 'Taylor', 'email' => 'jessica.taylor@example.com', 'dob' => '2005-06-10'],
    ['lrn' => '123456789018', 'first_name' => 'David', 'last_name' => 'Moore', 'email' => 'david.moore@example.com', 'dob' => '2005-07-15'],
    ['lrn' => '123456789019', 'first_name' => 'Sarah', 'last_name' => 'Anderson', 'email' => 'sarah.anderson@example.com', 'dob' => '2005-08-20'],
    ['lrn' => '123456789020', 'first_name' => 'James', 'last_name' => 'Thomas', 'email' => 'james.thomas@example.com', 'dob' => '2005-09-25'],
    ['lrn' => '123456789021', 'first_name' => 'Laura', 'last_name' => 'Jackson', 'email' => 'laura.jackson@example.com', 'dob' => '2005-10-30'],
    ['lrn' => '123456789022', 'first_name' => 'Daniel', 'last_name' => 'White', 'email' => 'daniel.white@example.com', 'dob' => '2005-11-05'],
    ['lrn' => '123456789023', 'first_name' => 'Samantha', 'last_name' => 'Harris', 'email' => 'samantha.harris@example.com', 'dob' => '2005-12-10'],
    ['lrn' => '123456789024', 'first_name' => 'Chris', 'last_name' => 'Martin', 'email' => 'chris.martin@example.com', 'dob' => '2006-01-15'],
    ['lrn' => '123456789025', 'first_name' => 'Anna', 'last_name' => 'Thompson', 'email' => 'anna.thompson@example.com', 'dob' => '2006-02-20'],
    ['lrn' => '123456789026', 'first_name' => 'Andrew', 'last_name' => 'Garcia', 'email' => 'andrew.garcia@example.com', 'dob' => '2006-03-25'],
    ['lrn' => '123456789027', 'first_name' => 'Megan', 'last_name' => 'Martinez', 'email' => 'megan.martinez@example.com', 'dob' => '2006-04-30'],
    ['lrn' => '123456789028', 'first_name' => 'Joshua', 'last_name' => 'Robinson', 'email' => 'joshua.robinson@example.com', 'dob' => '2006-05-05'],
    ['lrn' => '123456789029', 'first_name' => 'Ashley', 'last_name' => 'Clark', 'email' => 'ashley.clark@example.com', 'dob' => '2006-06-10'],
    ['lrn' => '123456789030', 'first_name' => 'Brian', 'last_name' => 'Rodriguez', 'email' => 'brian.rodriguez@example.com', 'dob' => '2006-07-15'],
];

foreach ($students as $student) {
    // Hash the password "test"
    $hashed_password = password_hash('test', PASSWORD_DEFAULT);

    $sql = "INSERT INTO students (lrn, first_name, middle_name, last_name, email, password, dob, grade_level, section_id) VALUES 
    ('{$student['lrn']}', '{$student['first_name']}', NULL, '{$student['last_name']}', '{$student['email']}', '$hashed_password', '{$student['dob']}', NULL, NULL)";

    if ($con->query($sql) === TRUE) {
        echo "New record created successfully for {$student['first_name']} {$student['last_name']}<br>";
    } else {
        echo "Error: " . $sql . "<br>" . $con->error;
    }
}

$con->close();
?>
