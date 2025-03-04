<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}
?>




<?php
// Fetch the student's information based on the session variable
$student_id = $_SESSION['student_id'];
$stmt = $con->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    echo "<script>alert('Student not found!'); window.location.href='login.php';</script>";
    exit();
}
?>


<?php
// Fetch the student's information based on the session variable
$student_id = $_SESSION['student_id'];
$stmt = $con->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    echo "<script>alert('Student not found!'); window.location.href='login.php';</script>";
    exit();
}
?>


<?php
// Fetch the student's section ID
$section_query = "SELECT section_id FROM students WHERE id = ?";
$section_stmt = $con->prepare($section_query);
$section_stmt->bind_param("i", $student_id);
$section_stmt->execute();
$section_result = $section_stmt->get_result();
$section = $section_result->fetch_assoc();
?>


<?php
// Initialize subjects variable
$subjects_result = null;
$subjects_count = 0;

if (!empty($section['section_id'])) {
    // Fetch subjects the student is enrolled in based on their section
    $subjects_query = "
        SELECT s.subject_code, s.subject_name, ts.teacher_id 
        FROM subjects s 
        JOIN teacher_section ts ON s.id = ts.subject_id 
        WHERE ts.section_id = ? 
    ";
    $subjects_stmt = $con->prepare($subjects_query);
    $subjects_stmt->bind_param("i", $section['section_id']);
    $subjects_stmt->execute();
    $subjects_result = $subjects_stmt->get_result();
    $subjects_count = $subjects_result->num_rows;
}
?>


<?php
// Fetch the student's grades from assignments
$grades_query = "
    SELECT 
        COALESCE(SUM(sub.score), 0) AS total_assignment_score,
        COALESCE(SUM(a.perfect_score), 0) AS total_assignment_possible_score
    FROM 
        submissions sub
    JOIN 
        assignments a ON sub.assignment_id = a.id
    WHERE 
        sub.student_id = ?";

$grades_stmt = $con->prepare($grades_query);
$grades_stmt->bind_param("i", $student_id);
$grades_stmt->execute();
$grades_result = $grades_stmt->get_result();
$grades = $grades_result->fetch_assoc();
?>


<?php
// Fetch quiz grades
$quiz_query = "
    SELECT 
        COALESCE(SUM(qs.score), 0) AS total_quiz_score,
        COALESCE(SUM(q.perfect_score), 0) AS total_quiz_possible_score
    FROM 
        quiz_submissions qs
    JOIN 
        quizzes q ON qs.quiz_id = q.id
    WHERE 
        qs.student_id = ?";

$quiz_stmt = $con->prepare($quiz_query);
$quiz_stmt->bind_param("i", $student_id);
$quiz_stmt->execute();
$quiz_result = $quiz_stmt->get_result();
$quiz_data = $quiz_result->fetch_assoc();
?>


<?php
// Calculate total scores
$total_assignment_score = $grades['total_assignment_score'];
$total_assignment_possible_score = $grades['total_assignment_possible_score'];

$total_quiz_score = $quiz_data['total_quiz_score'];
$total_quiz_possible_score = $quiz_data['total_quiz_possible_score'];

// Calculate overall scores
$total_score = $total_assignment_score + $total_quiz_score;
$total_possible_score = $total_assignment_possible_score + $total_quiz_possible_score;

// Calculate percentage
$percentage = 0;
if ($total_possible_score > 0) {
    $percentage = ($total_score / $total_possible_score) * 100;
}
?>


<?php
// Fetch the final grades per subject
$final_grades_query = "
    SELECT 
        s.subject_code, 
        s.subject_name,
        COALESCE(SUM(sub.score), 0) AS total_assignment_score,
        COALESCE(SUM(a.perfect_score), 0) AS total_assignment_possible_score,
        COALESCE(SUM(qs.score), 0) AS total_quiz_score,
        COALESCE(SUM(q.perfect_score), 0) AS total_quiz_possible_score
    FROM 
        subjects s
    LEFT JOIN 
        assignments a ON s.id = a.subject_id
    LEFT JOIN 
        submissions sub ON a.id = sub.assignment_id AND sub.student_id = ?
    LEFT JOIN 
        quizzes q ON s.id = q.subject_id
    LEFT JOIN 
        quiz_submissions qs ON q.id = qs.quiz_id AND qs.student_id = ?
    WHERE 
        s.id IN (SELECT subject_id FROM teacher_section WHERE section_id = ?)
    GROUP BY 
        s.id";

$final_grades_stmt = $con->prepare($final_grades_query);
$final_grades_stmt->bind_param("iii", $student_id, $student_id, $section['section_id']);
$final_grades_stmt->execute();
$final_grades_result = $final_grades_stmt->get_result();
?>


<?php
// Prepare data for Chart.js
$final_grades_data = [];
while ($row = $final_grades_result->fetch_assoc()) {
    $total_score = $row['total_assignment_score'] + $row['total_quiz_score'];
    $total_possible_score = $row['total_assignment_possible_score'] + $row['total_quiz_possible_score'];
    $final_percentage = ($total_possible_score > 0) ? ($total_score / $total_possible_score) * 100 : 0;

    $final_grades_data[] = [
        'subject' => $row['subject_code'] . ' - ' . $row['subject_name'],
        'final_grade' => number_format($final_percentage, 2)
    ];
}
?>


<?php 
// Fetch announcements for admins
$admin_announcements = [];

// Query to fetch admin announcements
$announcements_query = "SELECT message, created_at FROM admin_announcements ORDER BY created_at DESC";
$announcements_stmt = $con->prepare($announcements_query);
$announcements_stmt->execute();
$announcements_result = $announcements_stmt->get_result();

while ($row = $announcements_result->fetch_assoc()) {
    $row['created_at'] = date('m/d/Y h:i A', strtotime($row['created_at']));
    $admin_announcements[] = $row;
}

$announcements_stmt->close();



?>

<?php 
$announcements = [];
if ($section) {
    // Fetch announcements for the student's section with teacher's name
    $announcements_query = "SELECT ta.message, ta.created_at, 
                            CONCAT(t.first_name, ' ', t.last_name) AS teacher_name
                            FROM teacher_announcements ta 
                            JOIN teachers t ON ta.teacher_id = t.id
                            WHERE ta.section_id = ? 
                            ORDER BY ta.created_at DESC";
    $announcements_stmt = $con->prepare($announcements_query);
    $announcements_stmt->bind_param("i", $section['section_id']);
    $announcements_stmt->execute();
    $announcements_result = $announcements_stmt->get_result();

    while ($row = $announcements_result->fetch_assoc()) {
        // Format the created_at time to include date and time in 12-hour format
        $row['created_at'] = date('m/d/Y h:i A', strtotime($row['created_at']));
        $announcements[] = $row;
    }

    $announcements_stmt->close();
}
?>

<?php
function getStudentLastName($con, $student_id) {
    $last_name_query = "SELECT last_name FROM students WHERE id = ?";
    $last_name_stmt = $con->prepare($last_name_query);
    $last_name_stmt->bind_param("i", $student_id);
    $last_name_stmt->execute();
    $last_name_stmt->bind_result($last_name);
    $last_name_stmt->fetch();
    $last_name_stmt->close();

    return $last_name;
}

// Fetch the last name
$last_name = getStudentLastName($con, $student_id);
?>


<?php
// Fetch the student's information based on the session variable
$student_id = $_SESSION['student_id'];
$stmt = $con->prepare("SELECT first_name, middle_name, last_name, lrn FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
    $first_name = $student['first_name'];
    $middle_name = $student['middle_name'];
    $last_name = $student['last_name'];
    $lrn = $student['lrn'];
} else {
    echo "<script>alert('Student not found!'); window.location.href='student_login.php';</script>";
    exit();
}

?>

<?php 
// Check if the user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch the student's section ID
$section_query = "SELECT section_id FROM students WHERE id = ?";
$section_stmt = $con->prepare($section_query);
$section_stmt->bind_param("i", $student_id);
$section_stmt->execute();
$section_result = $section_stmt->get_result();
$section = $section_result->fetch_assoc();

if (!$section) {
    echo "<script>alert('Section not found!'); window.location.href='student_login.php';</script>";
    exit();
}

$section_id = $section['section_id'];

// Fetch teachers for the section
$teachers_query = "
    SELECT t.id AS teacher_id, CONCAT(t.first_name, ' ', t.last_name) AS name
    FROM teacher_section ts
    JOIN teachers t ON ts.teacher_id = t.id
    WHERE ts.section_id = ?
";
$teachers_stmt = $con->prepare($teachers_query);
$teachers_stmt->bind_param("i", $section_id);
$teachers_stmt->execute();
$teachers_result = $teachers_stmt->get_result();

// Fetch the teachers into an array
$teachers = [];
while ($teacher = $teachers_result->fetch_assoc()) {
    $teachers[] = $teacher;
}

// Proceed with fetching assignments and quizzes as per your previous code
$assignments_and_quizzes = [];
foreach ($teachers as $teacher) {
    $teacher_id = $teacher['teacher_id'];

    // Fetch assignments for this teacher
    $assignments_query = "
        SELECT a.assignment_title, a.description, a.due_date, 
               COALESCE(sub.score, 0) AS score
        FROM assignments a
        LEFT JOIN submissions sub ON a.id = sub.assignment_id AND sub.student_id = ?
        WHERE a.teacher_id = ? AND a.section_id = ?
    ";
    $assignments_stmt = $con->prepare($assignments_query);
    $assignments_stmt->bind_param("iii", $student_id, $teacher_id, $section_id);
    $assignments_stmt->execute();
    $assignments_result = $assignments_stmt->get_result();

    // Fetch quizzes for this teacher
    $quizzes_query = "
        SELECT q.title, q.description, q.due_date, 
               COALESCE(qs.score, 0) AS score
        FROM quizzes q
        LEFT JOIN quiz_submissions qs ON q.id = qs.quiz_id AND qs.student_id = ?
        WHERE q.teacher_id = ? AND q.section_id = ?
    ";
    $quizzes_stmt = $con->prepare($quizzes_query);
    $quizzes_stmt->bind_param("iii", $student_id, $teacher_id, $section_id);
    $quizzes_stmt->execute();
    $quizzes_result = $quizzes_stmt->get_result();

    // Store assignments and quizzes for this teacher
    $assignments_and_quizzes[$teacher_id] = [
        'assignments' => $assignments_result->fetch_all(MYSQLI_ASSOC),
        'quizzes' => $quizzes_result->fetch_all(MYSQLI_ASSOC),
    ];
}
?>

<?php

// Fetch admin announcements
$admin_announcements = [];
$announcements_query = "SELECT message, created_at FROM admin_announcements ORDER BY created_at DESC";
$announcements_stmt = $con->prepare($announcements_query);
$announcements_stmt->execute();
$announcements_result = $announcements_stmt->get_result();

while ($row = $announcements_result->fetch_assoc()) {
    $row['created_at'] = date('m/d/Y h:i A', strtotime($row['created_at']));
    $admin_announcements[] = $row;
}

$announcements_stmt->close();
?>

<?php

// Fetch the student ID from the session
$student_id = $_SESSION['student_id'] ?? null;

// Set the default image path
$image_path = "../students/profile/default.jpg"; // Default image path

if ($student_id) {
    // Prepare the SQL query to fetch the student's image
    $sql = "SELECT image FROM students WHERE id = ?";
    if ($stmt = $con->prepare($sql)) {
        $stmt->bind_param('i', $student_id);
        $stmt->execute();
        $stmt->bind_result($uploaded_image);
        $stmt->fetch();
        $stmt->close();

        // If an uploaded image exists, use it
        if (!empty($uploaded_image)) {
            $image_path = $uploaded_image; // Set to the uploaded image path
        }
    } else {
        // Handle potential SQL errors
        echo "Error preparing statement: " . $con->error;
    }
}
?>


<?php 
// Fetch subjects the student is enrolled in based on their section
$subjects_query = "
    SELECT s.subject_code, s.subject_name, ts.teacher_id, 
           CONCAT(t.first_name, ' ', t.last_name) AS teacher_name 
    FROM subjects s 
    JOIN teacher_section ts ON s.id = ts.subject_id 
    JOIN teachers t ON ts.teacher_id = t.id
    WHERE ts.section_id = ? 
";
$subjects_stmt = $con->prepare($subjects_query);
$subjects_stmt->bind_param("i", $section['section_id']);
$subjects_stmt->execute();
$subjects_result = $subjects_stmt->get_result();
$subjects_count = $subjects_result->num_rows;

?>