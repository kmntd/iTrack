<?php
include '../fn/dbcon.php';

// Check if subject_id is provided
if (isset($_GET['subject_id'])) {
    $subjectId = $_GET['subject_id'];

    $query = "SELECT ts.section_id, s.section_name 
              FROM teacher_section ts 
              JOIN sections s ON ts.section_id = s.id 
              WHERE ts.subject_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $subjectId);
    $stmt->execute();
    $result = $stmt->get_result();

    $sections = [];
    while ($row = $result->fetch_assoc()) {
        $sections[] = $row;
    }

    // Return sections as JSON
    echo json_encode($sections);
}
?>