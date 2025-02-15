<?php
// Database connection settings
$host = 'localhost'; // Change if necessary
$db = 'gms'; // Change to your database name
$user = 'root'; // Change to your database username
$pass = ''; // Change to your database password

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    // 4. Insert into teachers (without subject_id)
    $teachers = [
        ['Michael', 'Martin', 'Singco Mariscal', 'michael@example.com', password_hash('test', PASSWORD_DEFAULT), '1990-01-15', '123-456-7890', '123 Main St, City, Country', '2020-06-01', 'active'],
        ['Francis', 'Niño', 'Digamo', 'francis@example.com', password_hash('test', PASSWORD_DEFAULT), '1991-02-20', '234-567-8901', '234 Second St, City, Country', '2020-06-01', 'active'],
        ['Raphie', 'Rap', 'Abucay', 'raphie@example.com', password_hash('test', PASSWORD_DEFAULT), '1992-03-25', '345-678-9012', '345 Third St, City, Country', '2020-06-01', 'active']
    ];
    
    $stmt = $pdo->prepare("INSERT INTO teachers (first_name, middle_name, last_name, email, password, dob, phone_number, address, hire_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($teachers as $teacher) {
        $stmt->execute($teacher);
    }
    
    echo "Data inserted successfully!";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the connection
$pdo = null;
?>
