<?php
include '../fn/dbcon.php';

$sql = "INSERT INTO sections (section_name) VALUES 
        ('A'), 
        ('B'), 
        ('C')";

if ($con->query($sql) === TRUE) {
    echo "Sections added successfully";
} else {
    echo "Error: " . $sql . "<br>" . $con->error;
}
?>
